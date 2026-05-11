<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssetDetailPk extends Model
{
    use SoftDeletes;
    protected $table = 'asset_detail_pk';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'asset_id',
        'merk',
        'model',
        'serial_number',
        'tahun_perolehan',
        'kondisi',
        'lokasi_fisik',
        'ip_address',
        'spesifikasi',
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
