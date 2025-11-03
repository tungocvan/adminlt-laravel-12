<?php
 
namespace App\Livewire\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
//use Modules\Order\Models\Order;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class OrderList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $formVisible = false;
    public $orderId = null;
    public $email = '';
    public $status = 'pending';
    public $order_note = '';
    public $admin_note = '';

    // Product selection
    public $productSearch = '';
    public $selectAllProducts = false;
    public $selectedProducts = []; // [id => ['title','dvt','quy_cach','quantity','don_gia','total']]
    public $total = 0;

    protected $rules = [
        'email' => 'required|email',
    ];

    /* =========================
     * PRODUCT SEARCH + SELECT
     * ========================= */
    public function updatedProductSearch()
    {
       // $this->selectAllProducts = false;
        // $this->selectedProducts = [];
    }

    public function clearProductSearch()
    {
        $this->productSearch = '';
   
    }

    public function updatedSelectAllProducts($value)
    {
       
        $query = Medicine::query();
      
        if ($this->productSearch) {
            $query->where(function ($q) {
                $q->where('ten_hoat_chat', 'like', "%{$this->productSearch}%")
                  ->orWhere('ten_biet_duoc', 'like', "%{$this->productSearch}%");
            });
        }

        $products = $query->get();

        if ($value) {
            foreach ($products as $p) {
                $this->selectedProducts[$p->id] = [
                    'title' => $p->ten_hoat_chat ?? 'Chưa có tên',
                    'dvt' => $p->don_vi_tinh ?? '-',
                    'quy_cach' => $p->quy_cach_dong_goi ?? '-',
                    'quantity' => $this->selectedProducts[$p->id]['quantity'] ?? 1,
                    'don_gia' => $p->don_gia ?? 0,
                    'total' => ($p->don_gia ?? 0) * ($this->selectedProducts[$p->id]['quantity'] ?? 1),
                ];
            }
        } else {
            $this->selectedProducts = [];
        }
       
        $this->recalculateTotal();
    }
    public function toggleProduct($id)
    {
        $visible = Medicine::when($this->productSearch, function ($q) {
            $q->where('ten_hoat_chat', 'like', "%{$this->productSearch}%")
              ->orWhere('ten_biet_duoc', 'like', "%{$this->productSearch}%");
        })->where('id', $id)->exists();
    
        if (!$visible) return;
    
        if (isset($this->selectedProducts[$id])) {
            unset($this->selectedProducts[$id]);
        } else {
            $p = Medicine::find($id);
            $this->selectedProducts[$id] = [
                'title' => $p->ten_hoat_chat ?? 'Chưa có tên',
                'dvt' => $p->don_vi_tinh ?? '-',
                'quy_cach' => $p->quy_cach_dong_goi ?? '-',
                'quantity' => 1,
                'don_gia' => $p->don_gia ?? 0,
                'total' => $p->don_gia ?? 0,
            ];
        }
    
        $this->recalculateTotal();
    }
    
    

    /* =========================
     * QUANTITY CONTROL
     * ========================= */
    public function incrementQuantity($id)
    {
        if (isset($this->selectedProducts[$id])) {
            $this->selectedProducts[$id]['quantity']++;
            $this->updateProductTotal($id);
        }
    }

    public function decrementQuantity($id)
    {
        if (isset($this->selectedProducts[$id])) {
            $this->selectedProducts[$id]['quantity'] = max(1, $this->selectedProducts[$id]['quantity'] - 1);
            $this->updateProductTotal($id);
        }
    }

    private function updateProductTotal($id)
    {
        $p = &$this->selectedProducts[$id];
        $p['total'] = $p['quantity'] * $p['don_gia'];
        $this->recalculateTotal();
    }

    private function recalculateTotal()
    {
        $this->total = collect($this->selectedProducts)->sum(fn($p) => ($p['quantity'] ?? 1) * ($p['don_gia'] ?? 0));
    }

    /* =========================
     * FORM CONTROL
     * ========================= */
    public function showForm($id = null)
    {
        //dd($id);
        $this->resetForm();
        $this->formVisible = true;
        
        if ($id) {
            $order = Order::find($id);
            if ($order) {
                $this->orderId = $order->id;
                $this->email = $order->email;
                $this->status = $order->status;
                $this->order_note = $order->order_note;
                $this->admin_note = $order->admin_note;

                $this->selectedProducts = [];
                foreach ($order->order_detail as $item) {
                   // dd($item);
                   $product_id = $item['product_id'];
                    $this->selectedProducts[$product_id] = [
                        'title' => $item['title'] ?? 'Chưa có tên',
                        'dvt' => $item['dvt'] ?? '-',
                        'quy_cach' => $item['quy_cach'] ?? '-',
                        'quantity' => $item['quantity'] ?? 1,
                        'don_gia' => $item['don_gia'] ?? 0,
                        'total' => $item['total'] ?? (($item['quantity'] ?? 1) * ($item['don_gia'] ?? 0)),
                    ];
                }

                $this->recalculateTotal();
            }
        }
    }

    public function hideForm()
    {
        $this->formVisible = false;
    }

    private function resetForm()
    {
        $this->orderId = null;
        $this->email = '';
        $this->status = 'pending';
        $this->order_note = '';
        $this->admin_note = '';
        $this->selectedProducts = [];
        $this->selectAllProducts = false;
        $this->total = 0;
    }

    /* =========================
     * SAVE / DELETE ORDER
     * ========================= */
    public function saveOrder()
    {
        $this->validate();

        if (empty($this->selectedProducts)) {
            $this->addError('selectedProducts', 'Bạn phải chọn ít nhất 1 sản phẩm.');
            return;
        }

        $user = User::where('email', $this->email)->first();

        $orderDetail = [];
        $total = 0;
        foreach ($this->selectedProducts as $id => $item) {
            $qty = $item['quantity'] ?? 1;
            $price = $item['don_gia'] ?? 0;

            $orderDetail[] = [
                'product_id' => $id,
                'title' => $item['title'] ?? 'Chưa có tên',
                'dvt' => $item['dvt'] ?? '-',
                'quy_cach' => $item['quy_cach'] ?? '-',
                'quantity' => $qty,
                'don_gia' => $price,
                'total' => $qty * $price,
            ];

            $total += $qty * $price;
        }

        $data = [
            'email' => $this->email,
            'user_id' => $user->id ?? null,
            'status' => $this->status,
            'order_note' => $this->order_note,
            'admin_note' => $this->admin_note,
            'order_detail' => $orderDetail,
            'total' => $total,
        ];

        $this->orderId
            ? Order::find($this->orderId)?->update($data)
            : Order::create($data);

        session()->flash('message', 'Đơn hàng đã được lưu thành công.');

        $this->hideForm();
        $this->resetForm();
        $this->resetPage();
    }

    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->addError('deleteError', 'Đơn hàng không tồn tại.');
            return;
        }

        try {
            if ($order->link_download) {
                Storage::disk('public')->delete($order->link_download);
            }
            $order->delete();
            session()->flash('message', "Đơn hàng #{$orderId} đã xóa thành công.");
        } catch (\Exception $e) {
            $this->addError('deleteError', 'Xóa đơn hàng thất bại: ' . $e->getMessage());
        }
    }

    /* =========================
     * RENDER
     * ========================= */
    public function render()
    {
        $orders = Order::latest()->paginate(10);

        $products = Medicine::query()
            ->when($this->productSearch, fn($q) =>
                $q->where('ten_hoat_chat', 'like', "%{$this->productSearch}%")
                  ->orWhere('ten_biet_duoc', 'like', "%{$this->productSearch}%"))
            ->orderBy('ten_hoat_chat')
            ->get();

        return view('livewire.order.order-list', [
            'orders' => $orders,
            'filteredProducts' => $products,
            'total' => $this->total,
        ]);
    }
}
