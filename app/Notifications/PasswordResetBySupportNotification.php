<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetBySupportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // $plainPassword DIHAPUS dari constructor.
    // Password tidak pernah di-generate atau dikirim via email.
    // Email ini hanya notifikasi — link reset dikirim oleh Laravel Password Broker secara terpisah.
    public function __construct(
        public User $user
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Permintaan Reset Password — CSIRT Provinsi Bali')
            ->greeting('Om Suastiastu, Yth. ' . $this->user->name . '!')
            ->line('Administrator sistem telah meminta reset password untuk akun Anda di Sistem Aduan CSIRT Provinsi Bali.')
            ->line('Anda akan segera menerima **email terpisah** berisi link reset password.')
            ->line('Link tersebut hanya berlaku selama **15 menit** dan hanya bisa digunakan satu kali.')
            ->line('Setelah reset, Anda akan diminta membuat password baru yang kuat.')
            ->line('Jika Anda tidak merasa meminta reset ini atau ada aktivitas mencurigakan, segera hubungi kami di csirt@baliprov.go.id.')
            ->salutation('Om Santih, Santih, Santih Om — hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
    }
}
