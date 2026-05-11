<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssetDetailPl extends Model
{
    use SoftDeletes;
    protected $table = 'asset_detail_pl';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'asset_id',
        'url',
        'versi',
        'lisensi',
        'tgl_lisensi_berakhir',
        'vendor',
        'lead_developer',
        'platform',
        'lokasi_hosting',
        'nama_server_lainnya',
        'nama_server',
    ];
    protected $casts = ['tgl_lisensi_berakhir' => 'date'];

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
