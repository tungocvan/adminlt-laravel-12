<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserOption extends Model
{
    use HasFactory;

    protected $table = 'user_options';
    protected $primaryKey = 'option_id';

    protected $fillable = [
        'user_id',
        'option_name',
        'option_value',
        'autoload',
    ];

    protected $casts = [
        'option_value' => 'array',
    ];

    /**
     * Mỗi option thuộc về 1 user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
