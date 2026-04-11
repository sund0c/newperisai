<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use App\Http\Middleware\SandidataMiddleware;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    // ── Jumlah bulan sebelum password wajib diganti ─────────────────────
    const PASSWORD_EXPIRY_MONTHS = 2;

    // ── Jumlah password lama yang tidak boleh digunakan kembali ─────────
    const PASSWORD_HISTORY_LIMIT = 2;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'organization',
        'google2fa_secret',
        'google2fa_enabled',
        'is_active',
        'must_change_password',
        'password_changed_at',
        'last_login_at',
        'last_login_ip',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret', // NEVER expose 2FA secret via JSON/API
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'password_changed_at' => 'datetime',
            'password'           => 'hashed',
            'google2fa_enabled'  => 'boolean',
            'is_active'          => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    // ════════════════════════════════════════════════════════════════════
    // RELASI
    // ════════════════════════════════════════════════════════════════════

    public function passwordHistories()
    {
        return $this->hasMany(PasswordHistory::class)->latest('created_at');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ════════════════════════════════════════════════════════════════════
    // HELPER ROLE
    // ════════════════════════════════════════════════════════════════════

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isSupport(): bool
    {
        return $this->hasRole('support');
    }

    public function isPublic(): bool
    {
        return $this->hasRole('public');
    }

    // ════════════════════════════════════════════════════════════════════
    // PASSWORD ROTATION
    // ════════════════════════════════════════════════════════════════════

    /**
     * Cek apakah password sudah kadaluarsa (> 2 bulan).
     * Jika password_changed_at null, dianggap set saat akun dibuat (created_at).
     */
    public function isPasswordExpired(): bool
    {
        $reference = $this->password_changed_at ?? $this->created_at;

        if (!$reference) {
            return false;
        }

        return $reference->addMonths(self::PASSWORD_EXPIRY_MONTHS)->isPast();
    }

    /**
     * Hitung sisa hari sebelum password expired.
     * Return: int hari (bisa negatif jika sudah lewat)
     */
    public function daysUntilPasswordExpiry(): int
    {
        $reference = $this->password_changed_at ?? $this->created_at;

        if (!$reference) {
            return self::PASSWORD_EXPIRY_MONTHS * 30;
        }

        $expiryDate = $reference->copy()->addMonths(self::PASSWORD_EXPIRY_MONTHS);

        return (int) now()->diffInDays($expiryDate, false);
    }

    /**
     * Tanggal kadaluarsa password.
     */
    public function passwordExpiresAt(): Carbon
    {
        $reference = $this->password_changed_at ?? $this->created_at ?? now();
        return $reference->copy()->addMonths(self::PASSWORD_EXPIRY_MONTHS);
    }

    /**
     * Cek apakah password baru pernah digunakan sebelumnya.
     * Membandingkan dengan PASSWORD_HISTORY_LIMIT entri terakhir.
     *
     * @param string $plainPassword Password baru (belum di-hash)
     * @return bool true = pernah dipakai, false = aman digunakan
     */
    public function isPasswordReused(string $plainPassword): bool
    {
        return $this->passwordHistories()
            ->latest('created_at')
            ->limit(self::PASSWORD_HISTORY_LIMIT)
            ->get()
            ->contains(fn($history) => Hash::check($plainPassword, $history->password));
    }

    /**
     * Simpan password lama ke history, lalu update password user.
     * Otomatis hapus history yang melebihi PASSWORD_HISTORY_LIMIT.
     *
     * @param string $newHashedPassword Password baru yang sudah di-hash
     */
    public function rotatePassword(string $newHashedPassword): void
    {
        // 1. Simpan password LAMA ke history (sebelum diganti)
        if ($this->password) {
            PasswordHistory::create([
                'user_id'  => $this->id,
                'password' => $this->password, // sudah bcrypt, langsung simpan
            ]);
        }

        // 2. Hapus history lama jika melebihi limit (keep N terbaru)
        $historyIds = $this->passwordHistories()
            ->pluck('id')
            ->skip(self::PASSWORD_HISTORY_LIMIT - 1); // -1 karena kita baru tambah 1

        if ($historyIds->isNotEmpty()) {
            PasswordHistory::whereIn('id', $historyIds)->delete();
        }

        // 3. Update password & reset timer rotasi
        $this->update([
            'password'           => $newHashedPassword,
            'password_changed_at' => now(),
            'must_change_password' => false,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════
    // 2FA HELPERS
    // ════════════════════════════════════════════════════════════════════

    /**
     * Apakah 2FA sudah diaktifkan untuk user ini?
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->google2fa_enabled && !empty($this->google2fa_secret);
    }

    /**
     * Ambil secret 2FA (didekripsi).
     * Return null jika belum setup.
     */
    public function getTwoFactorSecret(): ?string
    {
        if (!$this->google2fa_secret) {
            return null;
        }

        try {
            return decrypt($this->google2fa_secret);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Report::class, 'user_id');
    }


    public function getPhoneAttribute($value): string
    {
        return SandidataMiddleware::decryptValue($value ?? '');
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->email_verified_at !== null;
    }
}
