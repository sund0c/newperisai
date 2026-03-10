<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeUserNotification extends Notification //implements ShouldQueue
{
    // use Queueable;

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
            ->subject('Akun Anda di Sistem Aduan CSIRT Provinsi Bali versi baru telah dibuat')
            ->greeting('Om Suastiastu, Yth. ' . $this->user->name . '!')
            ->line('Atas nama Pemerintah Provinsi Bali, kami sampaikan terimakasih atas kontrubusi Anda selama ini telah ikut menjaga ruang siber Pemprov Bali dengan aktif
            memberikan laporan kerentanan / insiden siber pada aset-aset TIK milik Pemerintah Provinsi Bali. Untuk memberikan pelayanan aduan kerentanan/insiden siber aset yang lebih baik lagi, kami telah meluncurkan Sistem Aduan CSIRT Provinsi Bali versi baru ini dimana
            akun email Anda sudah langsung kami daftarkan.')
            ->line('Berikut informasi login Anda:')
            ->line('**Email:** ' . $this->user->email)
            ->line('**Password:** ' . $this->plainPassword)
            ->action('Login Sekarang', url('/login'))
            ->line('Demi keamanan akun Anda, **Anda wajib mengganti password** segera setelah login pertama.')
            ->line('Jika Anda merasa tidak pernah mendaftarkan akun di Sistem Aduan CSIRT Provinsi Bali, atau tidak berkenan menggunakannya, silahkan abaikan email ini atau kontak ke csirt@baliprov.go.id.')
            ->salutation('Om Santih,Santih,Santih Om - hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
    }
}
