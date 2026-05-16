<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RopaLegalBasis extends Model
{
    use HasUuids;

    protected $table = 'ropa_legal_bases';

    protected $fillable = [
        'ropa_activity_id',
        'dasar_pemrosesan',
        'keterangan',
    ];

    public const LABELS = [
        'consent'                 => 'Persetujuan eksplisit subjek data (Consent)',
        'contractual'             => 'Pemenuhan kewajiban perjanjian (Contractual)',
        'legal_obligation'        => 'Kewajiban hukum pengendali (Legal Obligation)',
        'vital_interests'         => 'Perlindungan kepentingan vital subjek (Vital Interests)',
        'public_interests'        => 'Kepentingan umum / pelayanan publik (Public Interests)',
        'legitimate_interests'    => 'Kepentingan sah lainnya (Legitimate Interests)',
        'keseimbangan_kepentingan'=> 'Keseimbangan kepentingan Pengendali dan hak Subjek Data Pribadi',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }
}
