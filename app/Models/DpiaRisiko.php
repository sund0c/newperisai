<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpiaRisiko extends Model
{
    use HasUuids;

    protected $table = 'dpia_risikos';

    protected $fillable = [
        'dpia_id',
        'ancaman',
        'likelihood',
        'dampak',
        'level',
        'referensi_mitigasi',
        'residual_technical',
        'residual_privacy',
        'residual_organizational',
    ];

    public const LEVEL_MATRIX = [
        'Tinggi' => ['Rendah' => 'Sedang', 'Sedang' => 'Tinggi', 'Tinggi' => 'Tinggi'],
        'Sedang' => ['Rendah' => 'Rendah', 'Sedang' => 'Sedang', 'Tinggi' => 'Tinggi'],
        'Rendah' => ['Rendah' => 'Rendah', 'Sedang' => 'Rendah', 'Tinggi' => 'Sedang'],
    ];

    public static function computeLevel(string $likelihood, string $dampak): string
    {
        return self::LEVEL_MATRIX[$likelihood][$dampak] ?? 'Sedang';
    }

    public function dpia(): BelongsTo
    {
        return $this->belongsTo(Dpia::class, 'dpia_id');
    }
}
