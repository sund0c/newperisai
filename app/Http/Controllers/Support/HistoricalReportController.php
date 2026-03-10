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
            'title'             => 'required|string|max:255',
            'affected_system'   => 'nullable|string|max:255',
            'severity_reporter' => 'required|in:critical,high,medium,low',
            'reported_at'       => 'required|date|before_or_equal:today',
            'validation_result' => 'required|in:valid,invalid,duplicate',
            'severity_verified' => 'required_if:validation_result,valid|nullable|in:critical,high,medium,low',
            'certificate'       => 'nullable|file|mimes:pdf|max:5120',
            'admin_notes'       => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $user) {
            $ticketNumber = Report::generateHistoricalTicketNumber();
            $reportedAt   = $request->reported_at;

            // Upload sertifikat jika ada
            $certPath         = null;
            $certOriginalName = null;
            if ($request->hasFile('certificate') && $request->validation_result === 'valid') {
                $file             = $request->file('certificate');
                $certOriginalName = $file->getClientOriginalName();
                $storedName       = Str::uuid() . '.pdf';

                // Simpan dulu dengan ID sementara, update setelah create
                $certPath = '__pending__' . $storedName;
            }

            $report = Report::create([
                'ticket_number'      => $ticketNumber,
                'user_id'            => $user->id,
                'title'              => strip_tags($request->title),
                'description' => '',
                'affected_system'    => $request->affected_system,
                'poc_video_url'      => '',
                'severity_reporter'  => $request->severity_reporter,
                'severity_verified'  => $request->validation_result === 'valid' ? $request->severity_verified : null,
                'status'             => 'closed',
                'validation_result'  => $request->validation_result,
                'closed_reason'      => $request->admin_notes,
                'admin_notes'        => $request->admin_notes,
                'handled_by'         => auth()->id(),
                'handled_at'         => $reportedAt,
                'validated_at'       => $reportedAt,
                'certificated_at'    => ($certPath || $request->validation_result === 'valid') ? $reportedAt : null,
                'closed_at'          => $reportedAt,
                'is_historical'      => true,
                'created_at'         => $reportedAt,
                'updated_at'         => $reportedAt,
            ]);

            // Upload sertifikat dengan ID yang sudah ada
            if ($request->hasFile('certificate') && $request->validation_result === 'valid') {
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
