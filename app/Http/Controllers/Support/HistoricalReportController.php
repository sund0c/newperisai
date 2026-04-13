<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HistoricalReportController extends Controller
{
    public function create(User $user)
    {
        abort_unless($user->hasRole('public'), 403);
        abort_unless($user->email_verified_at !== null, 403, 'User belum verifikasi email.');

        return view('support.users.historical-create', compact('user'));
    }

    public function store(Request $request, User $user)
    {
        abort_unless($user->hasRole('public'), 403);
        abort_unless($user->email_verified_at !== null, 403, 'User belum verifikasi email.');

        $request->validate([
            // --- Field aktif ---
            'reported_at'       => 'required|date|before_or_equal:today',
            'validation_result' => 'required|in:valid,invalid,duplicate',
            'certificate'       => 'nullable|file|mimes:pdf|max:5120',

            // --- Field dinonaktifkan di form (tidak dikirim), tetap didefinisikan
            //     agar tidak ada mass-assignment tak terduga dari luar ---
            // 'title'             => tidak divalidasi, diisi default di bawah
            // 'affected_system'   => tidak divalidasi, diisi null
            // 'severity_reporter' => tidak divalidasi, diisi default
            // 'severity_verified' => tidak divalidasi, diisi null
            // 'admin_notes'       => tidak divalidasi, diisi null
        ]);

        DB::transaction(function () use ($request, $user) {
            $ticketNumber = Report::generateHistoricalTicketNumber();
            $reportedAt   = $request->reported_at;

            $report = Report::create([
                'ticket_number'      => $ticketNumber,
                'user_id'            => $user->id,
                'title'              => '(historis)',        // default karena field dinonaktifkan
                'description'        => '',
                'affected_system'    => null,               // field dinonaktifkan
                'poc_video_url'      => '',
                'severity_reporter'  => 'low',              // default karena field dinonaktifkan
                'severity_verified'  => null,               // field dinonaktifkan
                'status'             => 'closed',
                'validation_result'  => $request->validation_result,
                'closed_reason'      => null,               // field dinonaktifkan
                'admin_notes'        => null,               // field dinonaktifkan
                'handled_by'         => auth()->id(),
                'handled_at'         => $reportedAt,
                'validated_at'       => $reportedAt,
                'certificated_at'    => $request->validation_result === 'valid' ? $reportedAt : null,
                'closed_at'          => $reportedAt,
                'is_historical'      => true,
                'created_at'         => $reportedAt,
                'updated_at'         => $reportedAt,
            ]);

            // Upload sertifikat — hanya jika hasil valid DAN file benar-benar ada
            if ($request->validation_result === 'valid' && $request->hasFile('certificate')) {
                $file       = $request->file('certificate');
                $storedName = Str::uuid() . '.pdf';
                $path       = "certificates/{$report->id}/{$storedName}";

                Storage::disk('local')->put($path, file_get_contents($file));

                $report->update([
                    'certificate_file'          => $path,
                    'certificate_file_original' => $file->getClientOriginalName(),
                ]);
            }

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'historical_report_created',
                'model_type' => 'Report',
                'model_id'   => $report->id,
                'new_values' => [
                    'ticket_number'     => $ticketNumber,
                    'user_id'           => $user->id,
                    'validation_result' => $request->validation_result,
                ],
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('support.users.show', $user)
            ->with('success', 'Tiket historis berhasil ditambahkan.');
    }
}
