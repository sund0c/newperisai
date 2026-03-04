<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'admin_notes',
        'handled_by',
        'handled_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'handled_at' => 'datetime',
            'closed_at'  => 'datetime',
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

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by')->withTrashed();
    }

    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function images()
    {
        return $this->hasMany(ReportAttachment::class)->where('type', 'image');
    }

    public function documents()
    {
        return $this->hasMany(ReportAttachment::class)->where('type', 'document');
    }

    public function statusLogs()
    {
        return $this->hasMany(ReportStatusLog::class)->orderBy('created_at');
    }

    public function latestStatusLog()
    {
        return $this->hasOne(ReportStatusLog::class)->latestOfMany('created_at');
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
