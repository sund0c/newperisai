<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Report $report,
        public string $oldStatus
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->report->status_label;
        $ticketNumber = $this->report->ticket_number;

        $message = (new MailMessage)
            ->subject("Update Laporan {$ticketNumber} - {$statusLabel}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Status laporan Anda telah diperbarui.")
            ->line("**Nomor Tiket:** {$ticketNumber}")
            ->line("**Judul:** {$this->report->title}")
            ->line("**Status Baru:** {$statusLabel}");

        // Pesan kontekstual per status
        match ($this->report->status) {
            'processing'  => $message->line("Laporan Anda sedang dalam proses verifikasi kelengkapan oleh tim CSIRT Bali."),
            'validated'   => $message->line("Laporan Anda telah divalidasi. Tim CSIRT Bali akan segera menindaklanjuti."),
            'certificate' => $message->line("Selamat! Laporan Anda telah diverifikasi dan e-Sertifikat sedang dalam proses penerbitan."),
            'closed'      => $message->line("Laporan Anda telah selesai ditangani. Terima kasih atas kontribusi Anda dalam menjaga keamanan siber Provinsi Bali."),
            default       => $message->line("Tim CSIRT Bali akan segera menindaklanjuti laporan Anda."),
        };

        if ($this->report->admin_notes) {
            $message->line("**Catatan dari Tim CSIRT:** {$this->report->admin_notes}");
        }

        return $message
            ->action('Lihat Status Laporan', url('/laporan'))
            ->line("Jika ada pertanyaan, silakan hubungi tim CSIRT Bali.")
            ->salutation("Salam,\nTim CSIRT Provinsi Bali");
    }
}
