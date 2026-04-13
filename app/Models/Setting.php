<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public    $incrementing = false;

    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);
        return $setting ? $setting->value : $default;
    }

    /**
     * Set nilai setting. Insert jika belum ada, update jika sudah ada.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key'   => $key],
            ['value' => $value]
        );
    }

    /**
     * Cek apakah maintenance mode aktif.
     */
    public static function maintenanceActive(): bool
    {
        return (bool) static::get('maintenance_mode', false);
    }
}
