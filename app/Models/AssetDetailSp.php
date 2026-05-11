<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssetDetailSp extends Model
{
    use SoftDeletes;
    protected $table = 'asset_detail_sp';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'asset_id',
        'merk',
        'model',
        'serial_number',
        'kapasitas',
        'tahun_perolehan',
        'kondisi',
        'lokasi_fisik',
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
