<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportReceivedNotification extends Notification implements ShouldQueue
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
            ->subject('Laporan Anda Telah Diterima — ' . $this->report->ticket_number)
            ->greeting('Om Suastiastu, Yth. ' . $notifiable->name . '!')
            ->line('Terima kasih telah melaporkan kerentanan/insiden siber kepada CSIRT Provinsi Bali.')
            ->line('Laporan Anda telah kami terima dengan detail sebagai berikut:')
            ->line('**Nomor Tiket:** ' . $this->report->ticket_number)
            ->line('**Judul:** ' . $this->report->title)
            ->line('**Sistem Terdampak:** ' . ($this->report->affected_system ?? '-'))
            ->line('**Tingkat Dampak:** ' . (\App\Models\Report::severityLabel()[$this->report->severity_reporter] ?? $this->report->severity_reporter))
            ->action('Pantau Status Laporan', route('public.reports.show', $this->report))
            ->line('Tim CSIRT Provinsi Bali akan segera meninjau laporan Anda. Anda dapat memantau perkembangan status laporan melalui tautan di atas.')
            ->line('Simpan nomor tiket Anda sebagai referensi: **' . $this->report->ticket_number . '**')
            ->salutation('Om Santih, Santih, Santih Om — hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
    }
}
