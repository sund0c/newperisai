<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\CsirtProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // ════════════════════════════════════════════════════════════════
    // INDEX — daftar semua tiket (belum selesai di atas, selesai di bawah)
    // ════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Report::with(['reporter', 'latestStatusLog', 'csirtProcess'])
            ->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter severity
        if ($request->filled('severity')) {
            $query->where(function ($q) use ($request) {
                $q->where('severity_verified', $request->severity)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('severity_verified')
                            ->where('severity_reporter', $request->severity);
                    });
            });
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('ticket_number', 'like', '%' . $request->search . '%')
                    ->orWhere('title', 'like', '%' . $request->search . '%');
            });
        }

        // Tiket aktif (belum closed) di atas, closed di bawah
        $query->orderByRaw("FIELD(status, 'submitted', 'validated', 'certificate', 'closed')");

        if ($request->filled('result')) {
            $query->where('validation_result', $request->result);
        }

        $reports = $query->paginate(20)->withQueryString();


        $totalAll       = Report::count();
        $totalValid     = Report::where('validation_result', 'valid')->count();
        $totalInvalid   = Report::where('validation_result', 'invalid')->count();
        $totalDuplicate = Report::where('validation_result', 'duplicate')->count();

        return view('support.reports.index', compact(
            'reports',
            'totalAll',
            'totalValid',
            'totalInvalid',
            'totalDuplicate'
        ));
    }

    // ════════════════════════════════════════════════════════════════
    // SHOW — detail tiket
    // ════════════════════════════════════════════════════════════════

    public function show(Report $report)
    {
        $report->load([
            'reporter',
            'handler',
            'attachments',
            'images',
            'documents',
            'statusLogs.changer',
            'csirtProcess.handler',
        ]);

        return view('support.reports.show', compact('report'));
    }

    // ════════════════════════════════════════════════════════════════
    // VALIDASI — ubah status submitted → validated
    // ════════════════════════════════════════════════════════════════

    public function startValidation(Request $request, Report $report)
    {
        abort_if($report->status !== 'submitted', 403, 'Tiket tidak dalam status yang bisa divalidasi.');

        $report->update([
            'status'       => 'validated',
            'handled_by'   => auth()->id(),
            'handled_at'   => now(),
            'validated_at' => now(),
            'admin_notes'  => 'Tiket mulai divalidasi oleh support.',
        ]);

        $report->reporter->notify(
            new \App\Notifications\ReportStatusChangedNotification($report, 'validated')
        );

        return back()->with('success', 'Tiket berhasil divalidasi.');
    }

    // ════════════════════════════════════════════════════════════════
    // SET RESULT — VALID / INVALID / DUPLICATE
    // ════════════════════════════════════════════════════════════════

    public function setResult(Request $request, Report $report)
    {
        \Log::info('setResult called', ['report_id' => $report->id, 'result' => $request->result]);

        abort_if($report->status !== 'validated', 403, 'Tiket harus dalam status Divalidasi.');

        $request->validate([
            'result'                 => 'required|in:valid,invalid,duplicate',
            'notes'                  => 'nullable|string|max:1000',
            'incident_type_verified' => 'required|in:data_breach,web_defacement,ransomware,phishing,malicious_software,exploit,account_hijacking,advanced_persistence_threat,peringatan_keamanan,lainnya',
            'severity_verified'      => 'nullable|in:critical,high,medium,low',
        ]);

        try {
            DB::transaction(function () use ($request, $report) {
                \Log::info('inside transaction', ['result' => $request->result]);

                if ($request->result === 'valid') {
                    $request->validate([
                        'severity_verified' => 'required|in:critical,high,medium,low',
                    ]);

                    $report->update([
                        'status'                 => 'certificate',
                        'validation_result'      => 'valid',
                        'incident_type_verified' => $request->incident_type_verified, // ← tambah ini
                        'severity_verified'      => $request->severity_verified,
                        'admin_notes'            => $request->notes,
                        'certificated_at'        => null,
                    ]);
                    \Log::info('report updated');

                    // Buat record proses CSIRT
                    CsirtProcess::create([
                        'report_id'   => $report->id,
                        'status'      => 'notified',
                        'notified_at' => now(),
                    ]);
                    \Log::info('csirt process created');

                    // Kirim notifikasi ke semua user role CSIRT
                    $this->notifyCsirtTeam($report);
                } else {
                    // INVALID atau DUPLICATE → langsung closed
                    $report->update([
                        'status'                 => 'closed',
                        'validation_result'      => $request->result,
                        'incident_type_verified' => $request->incident_type_verified, // ← tambah ini juga
                        'closed_reason'          => $request->notes,
                        'admin_notes'            => $request->notes,
                        'closed_at'              => now(),
                    ]);

                    // Kirim email ke pelapor
                    $this->sendClosedEmail($report);
                }
            });
        } catch (\Exception $e) {
            \Log::error('setResult error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }

        $label = Report::validationResultLabel()[$request->result];
        return back()->with('success', "Tiket ditandai sebagai {$label}.");
    }

    // ════════════════════════════════════════════════════════════════
    // UPLOAD CERTIFICATE — upload e-certificate PDF
    // ════════════════════════════════════════════════════════════════

    public function uploadCertificate(Request $request, Report $report)
    {
        abort_if($report->status !== 'certificate', 403, 'Tiket tidak dalam status e-Sertifikat.');
        abort_if($report->validation_result !== 'valid', 403, 'Tiket harus berstatus Valid.');

        $request->validate([
            'certificate' => 'required|file|mimes:pdf|max:5120', // maks 5MB
        ]);

        DB::transaction(function () use ($request, $report) {
            $file     = $request->file('certificate');
            $uuid     = Str::uuid();
            $filename = $uuid . '.pdf';
            $path     = "certificates/{$report->id}/{$filename}";

            Storage::disk('local')->put($path, file_get_contents($file));

            // Hapus file lama jika ada
            if ($report->certificate_file) {
                Storage::disk('local')->delete($report->certificate_file);
            }

            $report->update([
                'certificate_file'          => $path,
                'certificate_file_original' => $file->getClientOriginalName(),
                'status'                    => 'closed',
                'certificated_at'           => now(),
                'closed_at'                 => now(),
                'admin_notes'               => 'e-Sertifikat diterbitkan dan laporan ditutup.',
            ]);

            // Kirim email ke pelapor dengan link download sertifikat
            $this->sendCertificateEmail($report);
        });

        return back()->with('success', 'e-Sertifikat berhasil diupload dan tiket ditutup.');
    }

    // ════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════════

    private function notifyCsirtTeam(Report $report): void
    {
        $csirtUsers = \App\Models\User::role('csirt')->where('is_active', true)->get();

        foreach ($csirtUsers as $user) {
            $user->notify(new \App\Notifications\CsirtTicketNotification($report));
        }
    }

    private function sendClosedEmail(Report $report): void
    {
        $report->reporter->notify(
            new \App\Notifications\ReportClosedNotification($report)
        );
    }

    private function sendCertificateEmail(Report $report): void
    {
        $report->reporter->notify(
            new \App\Notifications\ReportCertificateNotification($report)
        );
    }
}
