<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportCertificateNotification extends Notification implements ShouldQueue
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
            ->subject("[CSIRT Bali] e-Sertifikat Tersedia — {$this->report->ticket_number}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Kami dengan bangga menginformasikan bahwa laporan kerentanan Anda telah selesai diproses.")
            ->line("**Nomor Tiket:** {$this->report->ticket_number}")
            ->line("**Judul:** {$this->report->title}")
            ->line("**Tanggal Selesai:** {$this->report->closed_at->format('d M Y, H:i')} WITA")
            ->line("Sebagai bentuk apresiasi atas kontribusi Anda, kami menerbitkan **e-Sertifikat** yang dapat Anda unduh melalui tautan berikut.")
            ->action('Unduh e-Sertifikat', route('public.reports.show', $this->report))
            ->line("Terima kasih telah berkontribusi dalam menjaga keamanan sistem informasi Pemerintah Provinsi Bali.")
            ->line("Partisipasi aktif Anda sangat berarti bagi keamanan siber daerah kita.")
            ->salutation("Hormat kami,\nTim CSIRT Provinsi Bali");
    }
}
