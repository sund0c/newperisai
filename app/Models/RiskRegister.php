<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiskRegister extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'kode_rr',
        'asset_id',
        'tahunaktif_id',
        'opd_id',
        'versi',
        'status',
        'keterangan',
        'dibuat_oleh',
        'difinalisasi_oleh',
        'difinalisasi_at',
    ];

    protected $casts = [
        'difinalisasi_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function tahunaktif()
    {
        return $this->belongsTo(Tahunaktif::class);
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function items()
    {
        return $this->hasMany(RiskRegisterItem::class)->orderBy('risk_no');
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function difinalisasiOleh()
    {
        return $this->belongsTo(User::class, 'difinalisasi_oleh');
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isFinal(): bool
    {
        return $this->status === 'final';
    }

    public static function generateKode(string $tahun, string $opdPrefix): string
    {
        $prefix = 'RR-' . $tahun . '-' . strtoupper($opdPrefix);

        return \DB::transaction(function () use ($prefix) {
            $last = self::withTrashed()
                ->where('kode_rr', 'like', $prefix . '-%')
                ->lockForUpdate()
                ->count();

            $seq = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
            return $prefix . '-' . $seq;
        });
    }
}
