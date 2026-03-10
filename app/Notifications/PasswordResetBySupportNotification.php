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

    public function __construct(
        public User $user,
        public string $plainPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Password Akun Anda Telah Direset — CSIRT Provinsi Bali')
            ->greeting('Om Suastiastu, Yth. ' . $this->user->name . '!')
            ->line('Sesuai permintaan Anda, password akun Anda di Sistem Aduan CSIRT Provinsi Bali telah **direset oleh administrator**')
            ->line('Berikut informasi login baru Anda:')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Password Baru:** ' . $this->plainPassword)
            ->action('Login Sekarang', url('/login'))
            ->line('Demi keamanan akun Anda, **Anda wajib mengganti password** segera setelah login.')
            ->line('Jika Anda merasa tidak meminta reset password ini, segera hubungi tim kami di csirt@baliprov.go.id.')
            ->salutation('Om Santih, Santih, Santih Om — hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
    }
}
