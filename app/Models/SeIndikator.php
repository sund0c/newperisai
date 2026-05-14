<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeIndikator extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'se_indikators';

    protected $fillable = [
        'se_version_id',
        'urutan',
        'pertanyaan',
        'keterangan',
        'pilihan_1',
        'pilihan_2',
        'pilihan_3',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(SeVersion::class, 'se_version_id');
    }

    public function jawabans(): HasMany
    {
        return $this->hasMany(SePenilaianJawaban::class, 'se_indikator_id');
    }

    /**
     * Nilai otomatis by position: pilihan_1=1, pilihan_2=2, pilihan_3=3
     */
    public function getPilihanArray(): array
    {
        return [
            ['label' => $this->pilihan_1, 'nilai' => 1],
            ['label' => $this->pilihan_2, 'nilai' => 2],
            ['label' => $this->pilihan_3, 'nilai' => 3],
        ];
    }
}
