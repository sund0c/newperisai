<?php
// app/Models/PasswordHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordHistory extends Model
{
    // Tabel ini hanya punya created_at, tidak perlu updated_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'password',
    ];

    protected $hidden = [
        'password', // jangan pernah expose hash lama
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
