<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class PostCreate
{
    use Dispatchable, SerializesModels;

    public $post;

    /**
     * Create a new event instance.
     */
    public function __construct($post)
    {
        $this->post = $post;

        // 👉 Khi event được khởi tạo, tự động gửi sang NodeJS
        $this->sendToNode($post);
    }

    protected function sendToNode($post)
    {
        try {
            Http::post("http://127.0.0.1:6001/post-create", [
                "id"         => $post->id,
                "title"      => $post->title,
                "body"       => $post->body,
                "user_id"    => $post->user_id,
                "created_at" => $post->created_at->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            \Log::error("❌ PostCreate event failed to send to NodeJS: " . $e->getMessage());
        }
    }
}
