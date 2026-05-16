<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RopaPersonalDataType extends Model
{
    use HasUuids;

    protected $table = 'ropa_personal_data_types';

    protected $fillable = [
        'ropa_activity_id',
        'is_spesifik',
        'jenis_data',
    ];

    protected $casts = [
        'is_spesifik' => 'boolean',
    ];

    public const UMUM = [
        'Nama lengkap',
        'NIK / nomor identitas',
        'Tempat & tanggal lahir',
        'Jenis kelamin',
        'Kewarganegaraan',
        'Agama',
        'Status perkawinan',
        'Alamat domisili',
        'Nomor telepon',
        'Alamat email',
        'Riwayat pendidikan',
        'Riwayat pekerjaan',
    ];

    public const SPESIFIK = [
        'Data & informasi kesehatan',
        'Data biometrik',
        'Data genetika',
        'Catatan kejahatan',
        'Data anak',
        'Data keuangan pribadi',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }
}
