<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Events\AlertUserCreated;

class AlertUser extends Model
{
    use HasFactory;
    protected $table = 'alert_users';
    protected $fillable = [
        'user_id',
        'title',
        'content',
        'is_read',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($alert) {
            event(new AlertUserCreated($alert));
        });
    }
}
 