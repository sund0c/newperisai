<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Report $report,
        public string $newStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = \App\Models\Report::statusLabel()[$this->newStatus] ?? $this->newStatus;

        $message = (new MailMessage)
            ->subject('Pembaruan Status Laporan ' . $this->report->ticket_number . ' — CSIRT Provinsi Bali')
            ->greeting('Om Suastiastu, Yth. ' . $notifiable->name . '!')
            ->line('Status laporan Anda telah diperbarui.')
            ->line('**Nomor Tiket:** ' . $this->report->ticket_number)
            ->line('**Judul:** ' . $this->report->title)
            ->line('**Status Terbaru:** ' . $statusLabel);

        // Pesan kontekstual per status
        match ($this->newStatus) {
            'validated' => $message
                ->line('Laporan Anda sedang dalam proses validasi oleh Tim CSIRT Provinsi Bali. Kami akan segera menentukan hasil validasi dan menginformasikannya kepada Anda.'),
            default => $message
                ->line('Silakan pantau perkembangan laporan Anda melalui sistem kami.'),
        };

        return $message
            ->action('Pantau Status Laporan', route('public.reports.show', $this->report))
            ->salutation('Om Santih, Santih, Santih Om — hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
    }
}
