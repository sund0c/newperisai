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
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Akun Anda di Sistem Aduan CSIRT Provinsi Bali Telah Dibuat')
            ->greeting('Om Suastiastu, Yth. ' . $this->user->name . '!')
            ->line('Atas nama Pemerintah Provinsi Bali, kami sampaikan terima kasih atas kontribusi Anda selama ini dalam menjaga ruang siber Pemprov Bali.')
            ->line('Untuk memberikan pelayanan aduan kerentanan/insiden siber yang lebih baik, kami telah meluncurkan Sistem Aduan CSIRT Provinsi Bali versi baru. Akun Anda telah kami daftarkan dengan email: **' . $this->user->email . '**')
            ->line('Anda akan segera menerima **email terpisah** berisi link untuk membuat password Anda. Link tersebut hanya berlaku selama **15 menit**.')
            //->action('Login Sekarang', url('/login'))
            ->line('Setelah membuat password, Anda dapat langsung login dan menggunakan sistem.')
            ->line('Jika Anda merasa tidak pernah mendaftarkan akun atau tidak berkenan menggunakannya, silakan abaikan email ini atau hubungi kami di csirt@baliprov.go.id.')
            ->salutation('Om Santih, Santih, Santih Om — hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
    }
}
