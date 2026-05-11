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
            ['value' => '1', 'label' => 'Rendah: Informasi bersifat umum dan dapat diakses publik tanpa risiko'],
            ['value' => '2', 'label' => 'Sedang: Mengandung informasi internal yang perlu dijaga dari pihak luar'],
            ['value' => '3', 'label' => 'Tinggi: Mengandung data rahasia, pribadi, atau strategis; kebocoran menimbulkan dampak serius'],
        ],
        'I' => [
            ['value' => '1', 'label' => 'Rendah: Tidak Signifikan'],
            ['value' => '2', 'label' => 'Sedang: Dapat menyebabkan kesalahan data atau proses, namun masih bisa dikoreksi.'],
            ['value' => '3', 'label' => 'Tinggi: Dapat menyebabkan kerugian besar, gangguan operasional, atau kesalahan mengambil keputusan'],
        ],
        'A' => [
            ['value' => '1', 'label' => 'Rendah: Tidak signifikan, masih dapat ditunda'],
            ['value' => '2', 'label' => 'Sedang: Menghambat sebagian kegiatan, tetapi masih ada alternatif sementara'],
            ['value' => '3', 'label' => 'Tinggi: Menghentikan layanan penting/operasional utama'],
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
