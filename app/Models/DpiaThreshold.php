<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpiaThreshold extends Model
{
    use HasUuids;

    protected $table = 'dpia_thresholds';

    protected $fillable = [
        'dpia_id',
        'indikator',
        'terpenuhi',
        'keterangan',
    ];

    protected $casts = [
        'terpenuhi' => 'boolean',
    ];

    public const INDIKATOR_LABELS = [
        'keputusan_otomatis' => 'Pengambilan keputusan otomatis yang berdampak hukum signifikan',
        'data_spesifik'      => 'Pemrosesan data pribadi spesifik',
        'skala_besar'        => 'Pemrosesan skala besar (> 1.000 subjek)',
        'evaluasi_penskoran' => 'Evaluasi / penskoran / pemantauan sistematis',
        'pencocokan_data'    => 'Pencocokan atau penggabungan dataset besar',
        'teknologi_baru'     => 'Penggunaan teknologi baru dalam pemrosesan',
        'membatasi_hak'      => 'Pemrosesan yang berpotensi membatasi hak subjek data',
    ];

    public function dpia(): BelongsTo
    {
        return $this->belongsTo(Dpia::class, 'dpia_id');
    }

    public function getLabelAttribute(): string
    {
        return self::INDIKATOR_LABELS[$this->indikator] ?? $this->indikator;
    }
}
