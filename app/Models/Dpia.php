<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Dpia extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'dpias';

    protected $fillable = [
        'tahunaktif_id',
        'opd_id',
        'ropa_activity_id',
        'kode',
        'nama_aktivitas',
        'penanggung_jawab',
        'ppd',
        'tanggal_penyusunan',
        'versi',
        'konsultasi_stakeholder',
        'kriteria_risiko',
        'evaluasi_residual',
        'kesimpulan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_penyusunan' => 'date',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function tahunAktif(): BelongsTo
    {
        return $this->belongsTo(TahunAktif::class, 'tahunaktif_id');
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class, 'opd_id');
    }

    public function ropaActivity(): BelongsTo
    {
        return $this->belongsTo(RopaActivity::class, 'ropa_activity_id');
    }

    public function thresholds(): HasMany
    {
        return $this->hasMany(DpiaThreshold::class, 'dpia_id')
            ->orderBy('indikator');
    }

    public function tim(): HasMany
    {
        return $this->hasMany(DpiaTim::class, 'dpia_id')
            ->orderBy('urutan');
    }

    public function risikos(): HasMany
    {
        return $this->hasMany(DpiaRisiko::class, 'dpia_id')
            ->orderBy('urutan');
    }

    // ── Helpers ─────────────────────────────────────────────────

    public static function generateKode(): string
    {
        return DB::transaction(function () {
            $last = static::withTrashed()
                ->lockForUpdate()
                ->orderByRaw("CAST(SUBSTRING(kode, 6) AS UNSIGNED) DESC")
                ->value('kode');

            $next = $last ? (int) substr($last, 5) + 1 : 1;
            return 'DPIA-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }

    public function isEditable(): bool
    {
        $tahunAktif = TahunAktif::where('is_active', true)->value('id');
        return (string) $this->tahunaktif_id === (string) $tahunAktif;
    }

    /**
     * Sync threshold otomatis dari indikator risiko RoPA.
     * Indikator yang tercentang di RoPA → terpenuhi = true.
     * Keterangan tetap dipertahankan jika sudah diisi sebelumnya.
     */
    public function syncThresholdsFromRopa(): void
    {
        $ropa = $this->ropaActivity()->with('riskIndicators')->first();
        if (!$ropa) return;

        $terpenuhi = $ropa->riskIndicators->pluck('indikator')->toArray();

        foreach (DpiaThreshold::INDIKATOR_LABELS as $indikator => $label) {
            $existing = $this->thresholds()->where('indikator', $indikator)->first();
            if ($existing) {
                // Update terpenuhi, pertahankan keterangan
                $existing->update(['terpenuhi' => in_array($indikator, $terpenuhi)]);
            } else {
                $this->thresholds()->create([
                    'indikator'   => $indikator,
                    'terpenuhi'   => in_array($indikator, $terpenuhi),
                    'keterangan'  => null,
                ]);
            }
        }
    }
}
