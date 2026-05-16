<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RopaSubjectRight extends Model
{
    use HasUuids;

    protected $table = 'ropa_subject_rights';

    protected $fillable = [
        'ropa_activity_id',
        'pasal',
        'nama_hak',
    ];

    protected $casts = ['pasal' => 'integer'];

    public const HAK = [
        5  => 'Hak mendapatkan informasi pemrosesan data pribadi',
        6  => 'Hak memutakhirkan data pribadinya',
        7  => 'Hak akses dan mendapatkan salinan',
        8  => 'Hak mengakhiri pemrosesan, menghapus, dan/atau memusnahkan',
        9  => 'Hak menarik persetujuan',
        10 => 'Hak keberatan atas pemrosesan otomatis (Automated Decision Making)',
        11 => 'Hak menunda atau membatasi pemrosesan',
        12 => 'Hak atas gugatan ganti rugi',
        13 => 'Hak interoperabilitas',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }
}
