<?php

namespace App\Mail;

use App\Models\ContactSeller;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class NewContactSellerMail extends Mailable
{
    use Queueable, SerializesModels;

    

    protected int $sellerId;


    public function __construct(ContactSeller $seller)
    {
            
               
        $this->sellerId = $seller->id;
        
        
    }
    public function build()
    {
        $seller = ContactSeller::findOrFail($this->sellerId);

        $email = $this->subject("Liên hệ bán hàng từ {$seller->name}")
                    ->markdown('emails.contact-seller', ['seller' => $seller]);

        $files = json_decode($seller->files ?? '[]', true);

        foreach ($files as $file) {
            $filePath = storage_path('app/public/' . ltrim($file, '/'));
            if (file_exists($filePath)) {
                $email->attach($filePath, [
                    'as' => basename($filePath),
                    'mime' => File::mimeType($filePath),
                ]);
            }
        }

        return $email;
    }

    
}
