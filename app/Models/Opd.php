<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opd extends Model
{
    use SoftDeletes;
    // HasUuids dihapus — sekarang pakai auto-increment biasa

    protected $fillable = [
        'id',        // ← allow mass assign karena ID dari SSO nanti
        'kode_opd',
        'namaopd',
    ];

    public $incrementing = false; // ID di-set manual dari SSO, bukan auto
}
