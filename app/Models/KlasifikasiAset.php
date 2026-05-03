<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KlasifikasiAset extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'klasifikasi_asets';

    protected $fillable = [
        'klasifikasiaset',
        'kodeklas',
    ];

    public function subklasifikasi()
    {
        return $this->hasMany(SubKlasifikasiAset::class, 'klasifikasi_aset_id');
    }
}
