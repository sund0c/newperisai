<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AssetDetailSk extends Model
{
    use SoftDeletes;
    protected $table = 'asset_detail_sk';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'asset_id',
        'jabatan',
        'unit_kerja',
        'no_hp',
        'email',
        'tipe',
        'akses_sistem',
        'tgl_kontrak_berakhir',
    ];
    protected $casts = ['tgl_kontrak_berakhir' => 'date'];

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
