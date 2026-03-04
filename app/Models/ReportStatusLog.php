<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStatusLog extends Model
{
    public $timestamps = false; // hanya pakai created_at

    protected $fillable = [
        'report_id',
        'status',
        'changed_by',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function changer()
    {
        return $this->belongsTo(User::class, 'changed_by')->withTrashed();
    }

    public function getStatusLabelAttribute(): string
    {
        return Report::statusLabel()[$this->status] ?? $this->status;
    }
}
