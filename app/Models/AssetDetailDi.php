<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssetDetailDi extends Model
{
    use SoftDeletes;
    protected $table = 'asset_detail_di';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'asset_id',
        'bentuk',
        'lokasi_fisik',
        'lokasi_elektronik',
        'format',
        'klasifikasi_data',
        'retensi',
        'enkripsi',
        'metode_enkripsi',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->id = (string) Str::uuid());
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
