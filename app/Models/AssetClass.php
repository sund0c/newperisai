<?php
// app/Models/AssetClass.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AssetClass extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['kode', 'nama', 'deskripsi', 'is_active', 'urutan'];

    protected $casts = ['is_active' => 'boolean'];

    public function subclasses()
    {
        return $this->hasMany(AssetSubclass::class)->orderBy('urutan');
    }

    public function vulnerabilitySets()
    {
        return $this->hasMany(VulnerabilitySet::class, 'scope_id')
            ->where('scope_type', 'global_class');
    }

    public function activeVulnerabilitySet()
    {
        return $this->hasOne(VulnerabilitySet::class, 'scope_id')
            ->where('scope_type', 'global_class')
            ->where('is_active', true);
    }
}
