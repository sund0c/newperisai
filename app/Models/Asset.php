<?php
// app/Models/Asset.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AssetCriticality;

class Asset extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'tahunaktif_id',     // ← tambah ini
        'opd_id',
        'sub_klasifikasi_id',
        'kode_aset',
        'nama_aset',
        'keterangan',
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

    public function tahunAktif()
    {
        return $this->belongsTo(TahunAktif::class, 'tahunaktif_id');
    }

    public function detailPl()
    {
        return $this->hasOne(AssetDetailPl::class, 'asset_id');
    }

    public function detailPk()
    {
        return $this->hasOne(AssetDetailPk::class, 'asset_id');
    }

    public function detailSp()
    {
        return $this->hasOne(AssetDetailSp::class, 'asset_id');
    }

    public function detailSk()
    {
        return $this->hasOne(AssetDetailSk::class, 'asset_id');
    }

    public function detailDi()
    {
        return $this->hasOne(AssetDetailDi::class, 'asset_id');
    }

    public function detail()
    {
        $kode = strtolower($this->subKlasifikasi?->klasifikasi?->kodeklas ?? '');
        return match ($kode) {
            'pl'    => $this->detailPl,
            'pk'    => $this->detailPk,
            'sp'    => $this->detailSp,
            'sk'    => $this->detailSk,
            'di'    => $this->detailDi,
            default => null,
        };
    }

    public function criticality(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AssetCriticality::class, 'asset_id');
    }

    public function iiv(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\AssetIiv::class, 'asset_id');
    }
}
