<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderCreatedMail;
use App\Mail\OrderConfirmedMail; // tạo mail mới kèm link PDF
use App\Models\User;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'user_id',
        'order_detail',
        'order_note',
        'admin_note',
        'total',
        'status',
        'link_download'
    ];

    protected $casts = [
        'order_detail' => 'array', // tự động decode JSON thành mảng
    ];

    protected static function booted()
    {
        // 🔹 Khi tạo đơn hàng mới
        static::created(function ($order) {
            if ($order->status == "pending") {
            //    Mail::to($order->email)->send(new OrderCreatedMail($order));
                Mail::to($order->email)->queue(new OrderCreatedMail($order));
            
            }
        });

        // 🔹 Khi cập nhật đơn hàng
        static::updated(function ($order) {
            // Chỉ xử lý khi confirmed và chưa có link_download
            if ($order->status === 'confirmed' && !$order->link_download) {

                // -----------------------------
                // 1️⃣ Tạo thư mục theo ngày
                // -----------------------------
                $dateFolder = now()->format('Y-m-d');
                $folderPath = "orders/{$dateFolder}";
                if (!Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->makeDirectory($folderPath);
                }

                // -----------------------------
                // 2️⃣ Tạo PDF
                // -----------------------------
                try {
                    $pdf = Pdf::loadView('pdf.order', [
                        'order' => $order,
                        'details' => $order->order_detail,
                    ]);

                    $fileName = 'order_' . $order->id . '_' . time() . '.pdf';
                    $filePath = "{$folderPath}/{$fileName}";

                    Storage::disk('public')->put($filePath, $pdf->output());

                    // -----------------------------
                    // 3️⃣ Cập nhật link_download
                    // -----------------------------
                    $order->link_download = $filePath;
                    //$order->link_download = asset("storage/{$filePath}");
                    $order->saveQuietly(); // tránh loop updated

                } catch (\Exception $e) {
                    \Log::error('Tạo PDF thất bại: ' . $e->getMessage());
                }

                // -----------------------------
                // 4️⃣ Gửi email xác nhận kèm link PDF
                // -----------------------------
                try {
                   //Mail::to($order->email)->send(new OrderConfirmedMail($order));
                   Mail::to($order->email)->queue(new OrderConfirmedMail($order));
                } catch (\Exception $e) {
                    \Log::error('Gửi email thất bại: ' . $e->getMessage());
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
