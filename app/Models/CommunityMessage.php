<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityMessage extends Model
{
    protected $fillable = ['user_id','message'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
