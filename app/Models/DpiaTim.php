<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpiaTim extends Model
{
    use HasUuids;

    protected $table = 'dpia_tim';

    protected $fillable = [
        'dpia_id',
        'nama_anggota',
        'peran',
        'urutan',
    ];

    public function dpia(): BelongsTo
    {
        return $this->belongsTo(Dpia::class, 'dpia_id');
    }
}
