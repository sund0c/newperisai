<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RopaAsset extends Model
{
    use HasUuids;

    protected $table = 'ropa_assets';

    protected $fillable = [
        'ropa_activity_id',
        'asset_instance_id',
        'nama_manual',
        'peran_aset',
    ];

    public const PERAN_LABELS = [
        'primer'      => 'Primer',
        'pendukung'   => 'Pendukung',
        'penyimpanan' => 'Penyimpanan',
        'transmisi'   => 'Transmisi',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_instance_id');
    }

    public function getNamaAttribute(): string
    {
        return $this->asset?->nama_aset ?? $this->nama_manual ?? '-';
    }
}
