<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Verifikasi Email ─────────────────────────────────────────
        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('Verifikasi Alamat Email — CSIRT Provinsi Bali')
                ->greeting('Om Suastiastu, Yth. ' . $notifiable->name . '!')
                ->line('Terima kasih telah mendaftar di Sistem Aduan CSIRT Provinsi Bali.')
                ->line('Klik tombol di bawah untuk memverifikasi alamat email Anda.')
                ->action('Verifikasi Email Saya', $url)
                ->line('Tautan ini akan kedaluwarsa dalam **60 menit**.')
                ->line('Jika Anda tidak mendaftar akun ini, abaikan email ini.')
                ->salutation('Om Santih,Santih,Santih Om - hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
        });

        // ── Reset Password ───────────────────────────────────────────
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('Permintaan Reset Password — CSIRT Provinsi Bali')
                ->greeting('Om Suastiastu, Yth. ' . $notifiable->name . '!')
                ->line('Kami menerima permintaan reset password untuk akun Anda.')
                ->action('Reset Password', $url)
                ->line('Tautan ini akan kedaluwarsa dalam **60 menit**.')
                ->line('Jika Anda tidak meminta reset password, abaikan email ini. Password Anda tidak akan berubah.')
                ->salutation('Om Santih,Santih,Santih Om - hormat kami, BALIPROV-CSIRT #jagaRuangSiber');
        });
    }
}
