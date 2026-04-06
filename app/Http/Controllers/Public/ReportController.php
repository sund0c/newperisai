<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Report;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Statistik hasil validasi
        $totalAll    = Report::where('user_id', $userId)->count();
        $totalValid  = Report::where('user_id', $userId)->where('validation_result', 'valid')->count();
        $totalInvalid   = Report::where('user_id', $userId)->where('validation_result', 'invalid')->count();
        $totalDuplicate = Report::where('user_id', $userId)->where('validation_result', 'duplicate')->count();

        $query = Report::where('user_id', $userId)
            ->with(['latestStatusLog', 'documents'])
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter hasil validasi
        if ($result = $request->get('result')) {
            $query->where('validation_result', $result);
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('public.reports.index', compact(
            'reports',
            'totalAll',
            'totalValid',
            'totalInvalid',
            'totalDuplicate'
        ));
    }

    public function create()
    {
        return view('public.reports.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['required', 'string', 'min:30'],
            'affected_system' => ['nullable', 'string', 'max:255'],
            'poc_video_url'   => ['required', 'url', 'max:500'],
            'severity'        => ['required', 'in:critical,high,medium,low'],
            'incident_type'   => ['required', 'in:data_breach_pdp,data_breach,web_defacement,ransomware,phishing,malicious_software,exploit,account_hijacking,advanced_persistence_threat,peringatan_keamanan,lainnya'],
            'incident_type_other' => ['required_if:incident_type,lainnya', 'nullable', 'string', 'max:255'],
            // 'poc_images'      => ['required', 'array', 'min:1', 'max:3'],
            // 'poc_images.*'    => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            // 'poc_document'    => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'description.min'     => 'Deskripsi minimal 30 karakter.',
            'poc_video_url.url'   => 'Link video PoC harus berupa URL yang valid.',
            'incident_type.required' => 'Jenis insiden wajib dipilih.',
            // 'poc_images.required' => 'Minimal 1 screenshot wajib diunggah.',
            // 'poc_images.min'      => 'Minimal 1 screenshot wajib diunggah.',
            // 'poc_images.max'      => 'Maksimal 3 screenshot yang dapat diunggah.',
            // 'poc_images.*.image'  => 'File harus berupa gambar.',
            // 'poc_images.*.mimes'  => 'Format gambar harus JPG atau PNG.',
            // 'poc_images.*.max'    => 'Ukuran gambar maksimal 5MB.',
            // 'poc_document.mimes'  => 'Dokumen harus berformat PDF.',
            // 'poc_document.max'    => 'Ukuran dokumen maksimal 10MB.',
        ]);

        DB::transaction(function () use ($request) {
            $user         = auth()->user();
            $ticketNumber = Report::generateTicketNumber();

            $report = Report::create([
                'ticket_number'     => $ticketNumber,
                'user_id'           => $user->id,
                'title'             => strip_tags($request->title),
                'description'       => strip_tags($request->description),
                'affected_system'   => $request->affected_system ? strip_tags($request->affected_system) : null,
                'poc_video_url'     => $request->poc_video_url,
                'severity_reporter' => $request->severity,
                'incident_type_reporter' => $request->incident_type,
                'status'            => 'submitted',
            ]);

            // Upload semua gambar
            // foreach ($request->file('poc_images') as $image) {
            //     $this->storeAttachment($image, $report, 'image');
            // }

            // Upload PDF jika ada
            // if ($request->hasFile('poc_document')) {
            //     $this->storeAttachment($request->file('poc_document'), $report, 'document');
            // }

            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'report_created',
                'new_values' => [
                    'ticket_number' => $ticketNumber,
                    'title'         => $report->title,
                    'severity'      => $report->severity_reporter,
                    'incident_type'      => $report->incident_type_reporter,

                    // 'has_document'  => $request->hasFile('poc_document'),
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            $user->notify(new \App\Notifications\ReportReceivedNotification($report));
        });

        return redirect()->route('public.reports.index')
            ->with('success', 'Laporan berhasil dikirim! Tim CSIRT Provinsi Bali akan segera menindaklanjuti.');
    }

    public function show(Report $report)
    {
        abort_if($report->user_id !== auth()->id(), 403);

        $report->load(['attachments', 'handler', 'statusLogs.changer']);

        return view('public.reports.show', compact('report'));
    }

    // ════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ════════════════════════════════════════════════════════════════

    private function storeAttachment($file, Report $report, string $type): void
    {
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path       = "reports/{$report->id}/{$type}/{$storedName}";

        Storage::disk('local')->put($path, file_get_contents($file));

        ReportAttachment::create([
            'report_id'     => $report->id,
            'type'          => $type,
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => $storedName,
            'disk'          => 'local',
            'path'          => $path,
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
        ]);
    }
}
