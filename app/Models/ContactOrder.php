<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContactOrderMail;

class ContactOrder extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'user_type',
        'message',
    ];

    protected static function booted(): void
    {
        static::created(function (ContactOrder $order) {
            // Gửi email vào hàng đợi (nếu đã cấu hình queue)
            Mail::to(env('CONTACT_ORDER'))
                ->queue(new \App\Mail\NewContactOrderMail($order));
        });
    }
}
    
