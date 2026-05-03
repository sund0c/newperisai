<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class SubKlasifikasiAset extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'sub_klasifikasi_asets';

    protected $fillable = [
        'klasifikasi_aset_id',
        'subklasifikasiaset',
        'penjelasan',
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(KlasifikasiAset::class, 'klasifikasi_aset_id');
    }
}
