<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetIiv extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'asset_iivs';

    protected $fillable = [
        'asset_id',
        'dampak_operasional',
        'dampak_data_informasi',
        'dampak_finansial',
        'dampak_umum',
        'dampak_ketergantungan',
        'nilai_iiv',
        'assessed_by',
    ];

    protected $casts = [
        'dampak_operasional'     => 'integer',
        'dampak_data_informasi'  => 'integer',
        'dampak_finansial'       => 'integer',
        'dampak_umum'            => 'integer',
        'dampak_ketergantungan'  => 'integer',
        'nilai_iiv'              => 'integer',
    ];

    // ── Konstanta nilai ──────────────────────────────────────────

    const MINOR    = 1;
    const TERBATAS = 2;
    const KRITIS   = 3;

    /**
     * Label untuk setiap nilai numerik.
     */
    public static function labelMap(): array
    {
        return [
            self::MINOR    => 'MINOR',
            self::TERBATAS => 'TERBATAS',
            self::KRITIS   => 'KRITIS',
        ];
    }

    /**
     * Pilihan jawaban per dimensi (untuk view/form).
     * Kunci dimensi → array pilihan.
     */
    public static function options(): array
    {
        return [
            'dampak_operasional' => [
                [
                    'value' => self::KRITIS,
                    'label' => 'KRITIS',
                    'desc'  => 'Menimbulkan gangguan pada skala Nasional; layanan pemerintahan terhenti total atau tidak dapat diakses secara luas; pemulihan membutuhkan waktu lebih dari 24 jam.',
                ],
                [
                    'value' => self::TERBATAS,
                    'label' => 'TERBATAS',
                    'desc'  => 'Menimbulkan gangguan terbatas pada layanan utama lingkup Provinsi Bali; sebagian fungsi layanan masih dapat berjalan; pemulihan dapat diselesaikan dalam waktu kurang dari 24 jam.',
                ],
                [
                    'value' => self::MINOR,
                    'label' => 'MINOR',
                    'desc'  => 'Gangguan sangat kecil atau tidak berdampak signifikan terhadap operasional; layanan tetap berjalan normal; tidak memerlukan eskalasi pemulihan khusus.',
                ],
            ],

            'dampak_data_informasi' => [
                [
                    'value' => self::KRITIS,
                    'label' => 'KRITIS',
                    'desc'  => 'Berpotensi mengakibatkan kebocoran, kerusakan, atau hilangnya data strategis/rahasia negara berskala nasional; dapat mengancam keamanan nasional atau kedaulatan informasi.',
                ],
                [
                    'value' => self::TERBATAS,
                    'label' => 'TERBATAS',
                    'desc'  => 'Berpotensi mengakibatkan kebocoran atau kerusakan data internal pemerintah lingkup provinsi; berdampak pada privasi individu atau integritas data layanan publik daerah.',
                ],
                [
                    'value' => self::MINOR,
                    'label' => 'MINOR',
                    'desc'  => 'Tidak berdampak signifikan terhadap kerahasiaan, integritas, atau ketersediaan data; data yang terpengaruh bersifat publik atau tidak sensitif.',
                ],
            ],

            'dampak_finansial' => [
                [
                    'value' => self::KRITIS,
                    'label' => 'KRITIS',
                    'desc'  => 'Berpotensi mengakibatkan kerugian finansial yang sangat besar pada skala nasional (APBN), termasuk denda regulasi berat, tuntutan hukum massal, atau kerugian ekonomi yang meluas.',
                ],
                [
                    'value' => self::TERBATAS,
                    'label' => 'TERBATAS',
                    'desc'  => 'Berpotensi mengakibatkan kerugian finansial yang signifikan pada skala daerah (APBD Provinsi/Kabupaten); memerlukan alokasi anggaran pemulihan atau kompensasi khusus.',
                ],
                [
                    'value' => self::MINOR,
                    'label' => 'MINOR',
                    'desc'  => 'Kerugian finansial yang dapat diabaikan atau sangat kecil; dapat diselesaikan melalui anggaran operasional rutin tanpa eskalasi khusus.',
                ],
            ],

            'dampak_umum' => [
                [
                    'value' => self::KRITIS,
                    'label' => 'KRITIS',
                    'desc'  => 'Berpotensi menimbulkan kegaduhan massal berskala provinsi atau nasional; dapat memicu kepanikan publik, demonstrasi, atau keresahan sosial yang meluas; merusak kepercayaan masyarakat terhadap pemerintah secara luas.',
                ],
                [
                    'value' => self::TERBATAS,
                    'label' => 'TERBATAS',
                    'desc'  => 'Berpotensi menimbulkan kegaduhan atau ketidaknyamanan terbatas pada kelompok/individu tertentu; tidak bersifat massal; dampak sosial lebih kecil dari skala provinsi; dapat dikelola melalui komunikasi publik biasa.',
                ],
                [
                    'value' => self::MINOR,
                    'label' => 'MINOR',
                    'desc'  => 'Tidak menimbulkan kegaduhan publik; masyarakat umum tidak terpengaruh atau dampak sangat terlokalisir dan dapat diselesaikan secara internal.',
                ],
            ],

            'dampak_ketergantungan' => [
                [
                    'value' => self::KRITIS,
                    'label' => 'KRITIS',
                    'desc'  => 'Aset ini menjadi tulang punggung bagi banyak sistem/layanan lain; kegagalan aset ini akan memicu kegagalan berantai (cascading failure) pada infrastruktur kritikal lainnya di tingkat nasional atau lintas sektor.',
                ],
                [
                    'value' => self::TERBATAS,
                    'label' => 'TERBATAS',
                    'desc'  => 'Beberapa sistem/layanan di lingkup provinsi bergantung pada aset ini; kegagalan akan mengganggu layanan terkait tetapi tidak memicu kegagalan masif; pemulihan dapat dilakukan secara bertahap.',
                ],
                [
                    'value' => self::MINOR,
                    'label' => 'MINOR',
                    'desc'  => 'Sedikit atau tidak ada sistem lain yang bergantung pada aset ini; kegagalan bersifat terisolasi dan tidak berdampak pada infrastruktur lain.',
                ],
            ],
        ];
    }

    /**
     * Hitung nilai IIV berdasarkan ke-5 dimensi.
     * Logika: max(5 dimensi)
     *   → Ada satu KRITIS (3)   = KRITIS
     *   → Ada satu TERBATAS (2) = TERBATAS
     *   → Semua MINOR (1)       = MINOR
     */
    public static function computeNilaiIiv(
        int $operasional,
        int $dataInformasi,
        int $finansial,
        int $umum,
        int $ketergantungan
    ): int {
        return max($operasional, $dataInformasi, $finansial, $umum, $ketergantungan);
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getNilaiIivLabelAttribute(): string
    {
        return self::labelMap()[$this->nilai_iiv] ?? '-';
    }

    public function getNilaiIivBadgeClassAttribute(): string
    {
        return match ($this->nilai_iiv) {
            self::KRITIS   => 'badge-kritis',
            self::TERBATAS => 'badge-terbatas',
            default        => 'badge-minor',
        };
    }

    // ── Relasi ───────────────────────────────────────────────────

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}
