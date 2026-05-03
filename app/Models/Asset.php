<?php
// app/Models/Asset.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'opd_id',
        'sub_klasifikasi_id',
        'kode_aset',
        'nama_aset',
        'created_by',
        'updated_by',
    ];

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function subKlasifikasi()
    {
        return $this->belongsTo(SubKlasifikasiAset::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // public function instances()
    // {
    //     return $this->hasMany(AssetInstance::class);
    // }
}
