<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class Period extends Model
{
    use HasUuids;

    protected $table = 'asset_periods'; // ← tambahkan ini

    protected $fillable = [
        'tahun',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tahun'     => 'integer',
    ];

    public function assetInstances()
    {
        return $this->hasMany(AssetInstance::class, 'period_id');
    }
}
