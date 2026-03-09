<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportClosedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Report $report) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isInvalid   = $this->report->validation_result === 'invalid';
        $isDuplicate = $this->report->validation_result === 'duplicate';

        if ($isDuplicate) {
            $subject     = "[CSIRT Bali] Laporan Duplikat — {$this->report->ticket_number}";
            $headline    = "Laporan Anda Tercatat sebagai Duplikat";
            $explanation = "Setelah dilakukan pengecekan oleh tim kami, laporan yang Anda kirimkan memiliki kesamaan dengan laporan yang telah kami terima sebelumnya.";
        } else {
            $subject     = "[CSIRT Bali] Laporan Tidak Dapat Diproses — {$this->report->ticket_number}";
            $headline    = "Laporan Anda Tidak Dapat Diproses";
            $explanation = "Setelah dilakukan pengecekan oleh tim kami, laporan yang Anda kirimkan tidak memenuhi kriteria untuk diproses lebih lanjut.";
        }

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Yth. {$notifiable->name},")
            ->line("Terima kasih telah melaporkan kerentanan kepada CSIRT Provinsi Bali.")
            ->line("**{$headline}**")
            ->line("**Nomor Tiket:** {$this->report->ticket_number}")
            ->line("**Judul:** {$this->report->title}")
            ->line($explanation);

        if ($this->report->closed_reason) {
            $mail->line("**Keterangan:** {$this->report->closed_reason}");
        }

        return $mail
            ->line("Jika Anda memiliki pertanyaan, silakan hubungi tim kami melalui halaman laporan.")
            ->action('Lihat Status Laporan', route('public.reports.index'))
            ->line("Kami menghargai kontribusi Anda dalam menjaga keamanan siber Provinsi Bali.")
            ->salutation("Hormat kami,\nTim CSIRT Provinsi Bali");
    }
}
