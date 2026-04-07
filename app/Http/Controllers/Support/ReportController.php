<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\CsirtProcess;
use App\Models\DpoProcess;
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
            'dpoProcess.handler',
            'dpoProcess.activityLogs.logger',
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

        $isValid         = $request->result === 'valid';
        $needsFileUpload = $isValid && !$report->validation_file;

        $request->validate([
            'result'                 => 'required|in:valid,invalid,duplicate',
            'notes'                  => 'nullable|string|max:1000',
            'incident_type_verified' => 'required|in:data_breach_pdp,data_breach,web_defacement,ransomware,phishing,malicious_software,exploit,account_hijacking,advanced_persistence_threat,peringatan_keamanan,lainnya',
            'severity_verified'      => 'nullable|in:critical,high,medium,low',
        ], [
            'validation_file.required' => 'Laporan Validasi wajib diupload untuk menandai tiket sebagai Valid.',
            'validation_file.mimes'    => 'Laporan Validasi harus berupa file PDF.',
            'validation_file.max'      => 'Ukuran file tidak boleh lebih dari 2MB.',
        ]);

        // Validasi file terpisah — hanya jika ada file yang diupload atau memang wajib
        if ($needsFileUpload) {
            $request->validate([
                'validation_file' => 'required|file|mimes:pdf|max:2048',
            ], [
                'validation_file.required' => 'Laporan Validasi wajib diupload untuk menandai tiket sebagai Valid.',
                'validation_file.mimes'    => 'Laporan Validasi harus berupa file PDF.',
                'validation_file.max'      => 'Ukuran file tidak boleh lebih dari 2MB.',
            ]);
        } elseif ($request->hasFile('validation_file')) {
            $request->validate([
                'validation_file' => 'file|mimes:pdf|max:2048',
            ], [
                'validation_file.mimes' => 'Laporan Validasi harus berupa file PDF.',
                'validation_file.max'   => 'Ukuran file tidak boleh lebih dari 2MB.',
            ]);
        }

        // Upload laporan validasi — sekali upload, tidak bisa diganti
        if ($request->hasFile('validation_file')) {
            if ($report->validation_file) {
                return back()->withErrors(['validation_file' => 'Laporan validasi sudah pernah diupload dan tidak dapat diganti.']);
            }

            $file = $request->file('validation_file');
            $path = "validation/{$report->id}/" . \Illuminate\Support\Str::uuid() . '.pdf';
            Storage::disk('local')->put($path, file_get_contents($file));

            $report->update([
                'validation_file'          => $path,
                'validation_file_original' => $file->getClientOriginalName(),
            ]);
        }

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
                        'incident_type_verified' => $request->incident_type_verified,
                        'severity_verified'      => $request->severity_verified,
                        'admin_notes'            => $request->notes,
                        'certificated_at'        => null,
                    ]);
                    \Log::info('report updated');

                    CsirtProcess::create([
                        'report_id'   => $report->id,
                        'status'      => 'notified',
                        'notified_at' => now(),
                    ]);
                    \Log::info('csirt process created');

                    $this->notifyCsirtTeam($report);

                    if ($request->incident_type_verified === 'data_breach_pdp') {
                        DpoProcess::create([
                            'report_id'   => $report->id,
                            'status'      => 'notified',
                            'notified_at' => now(),
                        ]);
                        $this->notifyDpoTeam($report);
                    }
                } else {
                    $report->update([
                        'status'                 => 'closed',
                        'validation_result'      => $request->result,
                        'incident_type_verified' => $request->incident_type_verified,
                        'closed_reason'          => $request->notes,
                        'admin_notes'            => $request->notes,
                        'closed_at'              => now(),
                    ]);

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


    // ReportController@showValidationFile
    public function showValidationFile(Report $report)
    {
        abort_if(!$report->validation_file, 404, 'File tidak tersedia.');
        abort_unless(Storage::disk('local')->exists($report->validation_file), 404, 'File tidak ditemukan.');

        $filename = $report->validation_file_original ?? 'laporan-validasi.pdf';

        return Storage::disk('local')->response(
            $report->validation_file,
            $filename,
            ['Content-Type' => 'application/pdf', 'Cache-Control' => 'private, no-store']
        );
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

    private function notifyDpoTeam(Report $report): void
    {
        $dpoUsers = \App\Models\User::role('Dpo')->where('is_active', true)->get();

        foreach ($dpoUsers as $user) {
            $user->notify(new \App\Notifications\DpoTicketNotification($report));
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
