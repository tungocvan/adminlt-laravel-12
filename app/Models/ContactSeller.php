<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewContactSellerMail;



class ContactSeller extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'name',
        'email',
        'subject',
        'message',
        'files',
    ];

    protected $casts = [
        'files' => 'array', // JSON -> array tự động
    ];


   
//    protected static function booted(): void
//     {
//         static::created(function (ContactSeller $seller) {
//             try {
//                 \Mail::to(env('CONTACT_ORDER'))
//                     ->send(new \App\Mail\NewContactSellerMail($seller)); // 👈 send trực tiếp

//                 \Log::info("✅ Mail sent with possible attachment for seller id={$seller->id}");
//             } catch (\Exception $e) {
//                 \Log::error("❌ Mail send failed: " . $e->getMessage());
//             }
//         });
//     }

    protected static function booted(): void
    {
        static::created(function (ContactSeller $seller) {
            Mail::to(env('CONTACT_ORDER'))
                    ->queue(new \App\Mail\NewContactSellerMail($seller));
        });
    }

}
