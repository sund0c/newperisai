<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Middleware\SandidataMiddleware;

class DpoActivityLog extends Model
{
    protected $fillable = [
        'dpo_process_id',
        'logged_by',
        'type',
        'title',
        'body',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function process()
    {
        return $this->belongsTo(DpoProcess::class, 'dpo_process_id');
    }

    public function logger()
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    // Ikon & warna per type — dipakai di blade
    public static function typeConfig(): array
    {
        return [
            'update'       => ['label' => 'Update',        'color' => 'blue',   'icon' => 'refresh'],
            'notification' => ['label' => 'Notifikasi',    'color' => 'yellow', 'icon' => 'mail'],
            'coordination' => ['label' => 'Koordinasi',    'color' => 'purple', 'icon' => 'users'],
            'technical'    => ['label' => 'Teknis',        'color' => 'red',    'icon' => 'code'],
            'other'        => ['label' => 'Lainnya',       'color' => 'gray',   'icon' => 'dots'],
        ];
    }

    public function getTitleAttribute($value): string
    {
        return SandidataMiddleware::decryptValue($value ?? '');
    }

    public function getBodyAttribute($value): string
    {
        return SandidataMiddleware::decryptValue($value ?? '');
    }
}
