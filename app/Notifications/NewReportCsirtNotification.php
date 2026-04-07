<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReportCsirtNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Report $report) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Pastikan relasi reporter ter-load (penting saat dijalankan via queue)
        if (! $this->report->relationLoaded('reporter')) {
            $this->report->load('reporter');
        }

        $severityLabels = \App\Models\Report::severityLabel();
        $severityLabel  = $severityLabels[$this->report->severity_reporter] ?? $this->report->severity_reporter;

        $incidentLabels = [
            'data_breach_pdp'           => 'Data Pribadi Bocor (UU PDP)',
            'data_breach'               => 'Data Breach (Non PDP)',
            'web_defacement'            => 'Web Defacement',
            'ransomware'                => 'Ransomware',
            'phishing'                  => 'Phishing',
            'malicious_software'        => 'Malicious Software',
            'exploit'                   => 'Exploit',
            'account_hijacking'         => 'Account Hijacking',
            'advanced_persistence_threat' => 'Advanced Persistence Threat',
            'peringatan_keamanan'       => 'Peringatan Keamanan',
            'lainnya'                   => 'Lain-lain',
        ];
        $incidentLabel = $incidentLabels[$this->report->incident_type_reporter] ?? $this->report->incident_type_reporter;

        return (new MailMessage)
            ->subject('[CSIRT ALERT] Laporan Baru Masuk — ' . $this->report->ticket_number)
            ->greeting('Notifikasi Laporan Baru')
            ->line('Telah diterima laporan insiden/kerentanan siber baru yang memerlukan tindak lanjut.')
            ->line('---')
            ->line('**Nomor Tiket:** ' . $this->report->ticket_number)
            ->line('**Pelapor:** ' . ($this->report->reporter->name ?? '-') . ' (' . ($this->report->reporter->email ?? '-') . ')')
            ->line('**Judul:** ' . $this->report->title)
            ->line('**Jenis Insiden:** ' . $incidentLabel)
            ->line('**Severity (Pelapor):** ' . $severityLabel)
            ->line('**Sistem Terdampak:** ' . ($this->report->affected_system ?? '-'))
            ->line('**Deskripsi:** ' . ($this->report->description ?? '-'))
            ->line('**Link Video PoC:** ' . $this->report->poc_video_url)
            ->line('**Waktu Masuk:** ' . $this->report->created_at->setTimezone('Asia/Makassar')->format('d M Y, H:i') . ' WITA')
            ->action('Buka Laporan di Dashboard', route('support.reports.show', $this->report))
            ->line('Segera lakukan verifikasi dan tindak lanjut sesuai SOP BALIPROV-CSIRT.')
            ->salutation('BALIPROV-CSIRT Notification System #jagaRuangSiber');
    }
}
