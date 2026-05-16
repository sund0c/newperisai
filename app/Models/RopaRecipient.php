<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RopaRecipient extends Model
{
    use HasUuids;

    protected $table = 'ropa_recipients';

    protected $fillable = [
        'ropa_activity_id',
        'profil_penerima',
        'tipe',
        'peran',
        'kontak_pic',
        'tujuan_pengiriman',
        'jenis_data_dikirim',
        'mekanisme_pengiriman',
    ];

    public const PERAN_LABELS = [
        'pengendali'         => 'Pengendali',
        'pengendali_bersama' => 'Pengendali Bersama',
        'prosesor'           => 'Prosesor',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }
}
