<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeUserMail;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\Filterable;
use App\Models\Traits\AutoParseDates;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, Filterable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'is_admin',
        'birthdate',
        'google_id',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (User $user) {
            Mail::to($user->email)
                ->queue(new WelcomeUserMail($user));
        });

        static::deleting(function ($user) {
            $user->roles()->detach();
            $user->permissions()->detach();
        });
    }

    public function phone(): HasOne
    {
        return $this->hasOne(Phone::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // Keyword search
    public function scopeKeyword($query, $keyword)
    {
        $query->where(function ($q) use ($keyword) {
            $q->where('name', 'like', "%{$keyword}%")
              ->orWhere('email', 'like', "%{$keyword}%")
              ->orWhere('username', 'like', "%{$keyword}%");
        });
    }
}
