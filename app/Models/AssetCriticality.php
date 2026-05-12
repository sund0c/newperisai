<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetCriticality extends Model
{
    use HasUuids;

    protected $table = 'asset_criticalities';

    protected $fillable = [
        'asset_id',
        'confidentiality',
        'integrity',
        'availability',
        'kritikalitas',
        'assessed_by',
    ];

    protected $casts = [
        'confidentiality' => 'integer',
        'integrity'       => 'integer',
        'availability'    => 'integer',
        'kritikalitas'    => 'integer',
        'assessed_by'     => 'integer',
    ];

    // ── Label maps ────────────────────────────────────────────

    public static array $CIA_OPTIONS = [
        'C' => [
            ['value' => '1', 'label' => 'Rendah: Tidak berdampak — informasi memang bersifat publik atau tidak sensitif'],
            ['value' => '2', 'label' => 'Sedang: Menimbulkan kerugian reputasi atau gangguan operasional internal'],
            ['value' => '3', 'label' => 'Tinggi: Menimbulkan kerugian serius — menyangkut data pribadi, keuangan, rahasia jabatan, atau keamanan negara'],
        ],
        'I' => [
            ['value' => '1', 'label' => 'Rendah: Tidak berdampak — kesalahan mudah dideteksi dan dikoreksi tanpa konsekuensi'],
            ['value' => '2', 'label' => 'Sedang: Menyebabkan kesalahan proses atau data yang perlu upaya koreksi'],
            ['value' => '3', 'label' => 'Tinggi: Menyebabkan kerugian besar, keputusan salah, atau gangguan layanan utama'],
        ],
        'A' => [
            ['value' => '1', 'label' => 'Rendah: Tidak berdampak — dapat ditunda atau digantikan sementara tanpa konsekuensi'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan namun masih ada alternatif atau solusi sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting atau operasional utama pemerintahan'],
        ],
    ];

    public static array $LEVEL_LABELS = [
        1 => 'Rendah',
        2 => 'Sedang',
        3 => 'Tinggi',
    ];

    public static array $LEVEL_COLORS = [
        1 => 'green',   // Rendah
        2 => 'amber',   // Sedang
        3 => 'red',     // Tinggi
    ];

    // ── Computed ──────────────────────────────────────────────

    /**
     * Hitung kritikalitas = nilai tertinggi dari C, I, A
     */
    public static function computeKritikalitas(int $c, int $i, int $a): int
    {
        return max($c, $i, $a);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getLevelLabelAttribute(): string
    {
        return self::$LEVEL_LABELS[$this->kritikalitas] ?? '-';
    }

    public function getLevelColorAttribute(): string
    {
        return self::$LEVEL_COLORS[$this->kritikalitas] ?? 'gray';
    }

    // ── Relations ─────────────────────────────────────────────

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}
