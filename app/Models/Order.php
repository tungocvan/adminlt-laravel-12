<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Mail\OrderCreatedMail;
use Illuminate\Support\Facades\Mail;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',      
        'order_detail',
        'total',
        'status',
    ];

    protected $casts = [
        'order_detail' => 'array', // Tự động decode JSON thành mảng PHP
    ];

    // Khi đơn hàng tạo thành công, tự động gửi email
    protected static function booted()
    {
        static::created(function ($order) {
            if($order->status == "pending"){
                Mail::to($order->email)->send(new OrderCreatedMail($order));
            }
            
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

}
