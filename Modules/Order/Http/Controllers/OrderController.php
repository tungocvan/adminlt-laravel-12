<?php

namespace Modules\Order\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
         $this->middleware('permission:order-list|order-create|order-edit|order-delete', ['only' => ['index','show']]);
         $this->middleware('permission:order-create', ['only' => ['create','store']]);
         $this->middleware('permission:order-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('Order::index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->status === 'pending') {
            $order->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);
        }
        
        $user = $order->user; // 💡 gọi quan hệ luôn

        return view('Order::show', compact('order','user'));
    }

    public function edit(Order $order)
    {
        return view('Order::edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('order.index')->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('order.index')->with('success', 'Đã xóa đơn hàng!');
    }

    public function print(Order $order,$type = 'order_print')
    {
        //dd($order);
        // Lấy thông tin user từ email
        
        $user = $order->user;

        // Trả về view in đơn hàng
        return view('Order::print', compact('order', 'user','type'));
    }

    public function exportPdf(Order $order, $type = 'order_pdf')
    {
        $view =  "Order::$type";        
        $pdf = Pdf::loadView($view, compact('order'))->setPaper('a4', 'portrait');

        $fileName = strtoupper($type) . "_Order_{$order->id}.pdf";
        return $pdf->download($fileName);
    }
}
