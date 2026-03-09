<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CsirtTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Report $report) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[CSIRT Bali] Tiket Valid Memerlukan Mitigasi — {$this->report->ticket_number}")
            ->greeting("Yth. Tim CSIRT,")
            ->line("Telah diterima laporan kerentanan yang telah divalidasi dan memerlukan tindakan mitigasi.")
            ->line("**Nomor Tiket:** {$this->report->ticket_number}")
            ->line("**Judul:** {$this->report->title}")
            ->line("**Dampak:** {$this->report->effective_severity_label}")
            ->line("**Sistem Terdampak:** " . ($this->report->affected_system ?? '-'))
            ->line("**Tanggal Laporan:** {$this->report->created_at->format('d M Y, H:i')} WITA")
            ->action('Lihat Detail & Mulai Proses', route('csirt.reports.show', $this->report))
            ->line("Segera lakukan proses mitigasi sesuai prosedur yang berlaku.")
            ->salutation("Salam,\nSistem CSIRT Provinsi Bali");
    }
}
