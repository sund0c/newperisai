<?php

namespace App\Enums;

enum JenisPeriode: string
{
    case ASET         = 'aset';
    case MANRISK      = 'manrisk';
    case DPIA         = 'dpia';
    case ITSA         = 'itsa';
    case ROPA         = 'ropa';
    // tambah jenis baru cukup di sini

    public function label(): string
    {
        return match ($this) {
            self::ASET         => 'Pemutakhiran Aset',
            self::MANRISK => 'Manajemen Risiko Keamanan',
            self::DPIA         => 'DPIA',
            self::ITSA        => 'ITSA',
            self::ROPA         => 'ROPA',
        };
    }
}
