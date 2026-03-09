<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'title',
        'description',
        'affected_system',
        'poc_video_url',
        'severity_reporter',
        'severity_verified',
        'status',
        'validation_result',
        'closed_reason',
        'certificate_file',
        'certificate_file_original',
        'admin_notes',
        'handled_by',
        'handled_at',
        'validated_at',
        'certificated_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'handled_at'      => 'datetime',
            'validated_at'    => 'datetime',
            'certificated_at' => 'datetime',
            'closed_at'       => 'datetime',
        ];
    }

    // ════════════════════════════════════════════════════════════════
    // BOOT
    // ════════════════════════════════════════════════════════════════

    protected static function booted(): void
    {
        static::created(function (Report $report) {
            ReportStatusLog::create([
                'report_id'  => $report->id,
                'status'     => $report->status,
                'changed_by' => Auth::id(),
                'notes'      => null,
            ]);
        });

        static::updated(function (Report $report) {
            if ($report->wasChanged('status')) {
                ReportStatusLog::create([
                    'report_id'  => $report->id,
                    'status'     => $report->status,
                    'changed_by' => Auth::id(),
                    'notes'      => $report->admin_notes,
                ]);
            }
        });
    }

    // ════════════════════════════════════════════════════════════════
    // TICKET NUMBER GENERATOR
    // ════════════════════════════════════════════════════════════════

    public static function generateTicketNumber(): string
    {
        $year   = now()->format('Y');
        $prefix = "BALIPROV-CSIRT-{$year}-";

        $last = static::withTrashed()
            ->where('ticket_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('ticket_number')
            ->first();

        $nextNumber = $last
            ? (int) substr($last->ticket_number, -4) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    // ════════════════════════════════════════════════════════════════
    // RELASI
    // ════════════════════════════════════════════════════════════════

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by')->withTrashed();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReportAttachment::class)->where('type', 'image');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ReportAttachment::class)->where('type', 'document');
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(ReportStatusLog::class)->orderBy('created_at');
    }

    public function latestStatusLog(): HasOne
    {
        return $this->hasOne(ReportStatusLog::class)->latestOfMany('created_at');
    }

    public function csirtProcess(): HasOne
    {
        return $this->hasOne(CsirtProcess::class);
    }

    // ════════════════════════════════════════════════════════════════
    // STATUS HELPERS
    // ════════════════════════════════════════════════════════════════

    public static function statusFlow(): array
    {
        return ['submitted', 'validated', 'certificate', 'closed'];
    }

    public static function statusLabel(): array
    {
        return [
            'submitted'   => 'Diterima',
            'validated'   => 'Divalidasi',
            'certificate' => 'e-Sertifikat',
            'closed'      => 'Selesai',
        ];
    }

    public static function statusColor(): array
    {
        return [
            'submitted'   => 'blue',
            'validated'   => 'purple',
            'certificate' => 'indigo',
            'closed'      => 'green',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColor()[$this->status] ?? 'gray';
    }

    // ════════════════════════════════════════════════════════════════
    // VALIDATION RESULT HELPERS
    // ════════════════════════════════════════════════════════════════

    public static function validationResultLabel(): array
    {
        return [
            'valid'     => 'Valid',
            'invalid'   => 'Tidak Valid',
            'duplicate' => 'Duplikat',
        ];
    }

    public static function validationResultColor(): array
    {
        return [
            'valid'     => 'green',
            'invalid'   => 'red',
            'duplicate' => 'yellow',
        ];
    }

    public function getValidationResultLabelAttribute(): ?string
    {
        return $this->validation_result
            ? (self::validationResultLabel()[$this->validation_result] ?? $this->validation_result)
            : null;
    }

    public function getValidationResultColorAttribute(): string
    {
        return self::validationResultColor()[$this->validation_result] ?? 'gray';
    }

    public function isValid(): bool
    {
        return $this->validation_result === 'valid';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function hasCertificate(): bool
    {
        return !empty($this->certificate_file);
    }

    // ════════════════════════════════════════════════════════════════
    // SEVERITY HELPERS
    // ════════════════════════════════════════════════════════════════

    public static function severityLabel(): array
    {
        return [
            'critical' => 'Sangat Berbahaya',
            'high'     => 'Berbahaya',
            'medium'   => 'Cukup Berbahaya',
            'low'      => 'Perlu Diperhatikan',
        ];
    }

    public static function severityColor(): array
    {
        return [
            'critical' => 'red',
            'high'     => 'orange',
            'medium'   => 'yellow',
            'low'      => 'green',
        ];
    }

    public function getEffectiveSeverityAttribute(): string
    {
        return $this->severity_verified ?? $this->severity_reporter;
    }

    public function getEffectiveSeverityLabelAttribute(): string
    {
        return self::severityLabel()[$this->effective_severity] ?? $this->effective_severity;
    }

    public function getStatusStepAttribute(): int
    {
        return array_search($this->status, self::statusFlow()) + 1;
    }

    public function nextStatus(): ?string
    {
        $flow         = self::statusFlow();
        $currentIndex = array_search($this->status, $flow);
        return $flow[$currentIndex + 1] ?? null;
    }
}
