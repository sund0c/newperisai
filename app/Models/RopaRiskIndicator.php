<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RopaRiskIndicator extends Model
{
    use HasUuids;

    protected $table = 'ropa_risk_indicators';

    protected $fillable = [
        'ropa_activity_id',
        'indikator',
    ];

    // 7 indikator risiko tinggi Pasal 34 ayat 2 UU PDP
    public const LABELS = [
        'keputusan_otomatis' => 'Pengambilan keputusan otomatis berdampak hukum signifikan',
        'data_spesifik'      => 'Pemrosesan data pribadi spesifik',
        'skala_besar'        => 'Pemrosesan skala besar (> 1.000 subjek)',
        'evaluasi_penskoran' => 'Evaluasi, penskoran, atau pemantauan sistematis',
        'pencocokan_data'    => 'Pencocokan atau penggabungan kelompok data',
        'teknologi_baru'     => 'Penggunaan teknologi baru dalam pemrosesan',
        'membatasi_hak'      => 'Pemrosesan yang membatasi hak subjek data',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->indikator] ?? $this->indikator;
    }
}
