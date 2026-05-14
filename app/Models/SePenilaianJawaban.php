<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SePenilaianJawaban extends Model
{
    use HasUuids;

    protected $table = 'se_penilaian_jawabans';

    protected $fillable = [
        'se_penilaian_id',
        'se_indikator_id',
        'urutan_indikator',
        'pertanyaan_snapshot',
        'pilihan_dipilih',
        'nilai_dipilih',
        'catatan_jawaban',
    ];

    protected $casts = [
        'urutan_indikator' => 'integer',
        'nilai_dipilih'    => 'integer',
    ];

    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(SePenilaian::class, 'se_penilaian_id');
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(SeIndikator::class, 'se_indikator_id');
    }
}
