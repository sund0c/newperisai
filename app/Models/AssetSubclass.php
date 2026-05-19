<?php
// app/Models/AssetSubclass.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssetSubclass extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['asset_class_id', 'kode', 'nama', 'deskripsi', 'is_active', 'urutan'];

    protected $casts = ['is_active' => 'boolean'];

    public function assetClass()
    {
        return $this->belongsTo(AssetClass::class);
    }

    public function vulnerabilitySets()
    {
        return $this->hasMany(VulnerabilitySet::class, 'scope_id')
            ->where('scope_type', 'subclass');
    }

    public function activeVulnerabilitySet()
    {
        return $this->hasOne(VulnerabilitySet::class, 'scope_id')
            ->where('scope_type', 'subclass')
            ->where('is_active', true);
    }
}
