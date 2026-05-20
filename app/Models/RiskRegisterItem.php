<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RiskRegisterItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'risk_register_id',
        'risk_no',
        'jenis_risiko',
        'ancaman',
        'kerawanan',
        'kategori',
        'dampak_detail',
        'area_dampak',
        'vulnerability_item_id',
        'kontrol_saat_ini',
        'rencana_aksi',
        'inherent_dampak',
        'inherent_kemungkinan',
        'inherent_skor',
        'inherent_level',
        'keputusan_penanganan',
        'prioritas_risiko',
        'opsi_penanganan',
        'keluaran',
        'target_jadwal',
        'penanggung_jawab',
        'ada_residual_risk',
        'residual_dampak',
        'residual_kemungkinan',
        'residual_skor',
        'residual_level',
        'residual_status',
        'rencana_kontrol_tambahan',
        'risk_owner',
    ];

    protected $casts = [
        'area_dampak'      => 'array',
        'ada_residual_risk' => 'boolean',
    ];

    public function riskRegister()
    {
        return $this->belongsTo(RiskRegister::class);
    }

    public function vulnerabilityItem()
    {
        return $this->belongsTo(VulnerabilityItem::class);
    }

    /**
     * Matrix skor risiko 5×5 (non-linear sesuai standar Pemprov Bali).
     * Index: [kemungkinan-1][dampak-1]
     */
    public static function hitungSkor(int $kemungkinan, int $dampak): int
    {
        $matrix = [
            // D:  1   2   3   4   5
            [1,  3,  5,  8, 20], // K=1 Hampir Tidak Terjadi
            [2,  7, 11, 13, 21], // K=2 Jarang Terjadi
            [4, 10, 14, 17, 22], // K=3 Kadang-Kadang
            [6, 12, 16, 19, 24], // K=4 Sering Terjadi
            [9, 15, 18, 23, 25], // K=5 Hampir Pasti
        ];

        return $matrix[$kemungkinan - 1][$dampak - 1];
    }

    public static function skorKeLevel(int $skor): string
    {
        return match (true) {
            $skor <= 5  => 'Sangat Rendah',
            $skor <= 10 => 'Rendah',
            $skor <= 15 => 'Sedang',
            $skor <= 20 => 'Tinggi',
            default     => 'Sangat Tinggi',
        };
    }

    public static function labelKemungkinan(int $val): string
    {
        return match ($val) {
            1 => 'Hampir Tidak Terjadi',
            2 => 'Jarang Terjadi',
            3 => 'Kadang-Kadang Terjadi',
            4 => 'Sering Terjadi',
            5 => 'Hampir Pasti Terjadi',
            default => '-',
        };
    }

    public static function labelDampak(int $val): string
    {
        return match ($val) {
            1 => 'Tidak Signifikan',
            2 => 'Kurang Signifikan',
            3 => 'Cukup Signifikan',
            4 => 'Signifikan',
            5 => 'Sangat Signifikan',
            default => '-',
        };
    }
}
