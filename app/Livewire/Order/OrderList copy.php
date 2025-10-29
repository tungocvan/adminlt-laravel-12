<?php

namespace App\Livewire\Order;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Medicine;
use App\Models\User;

class OrderList extends Component
{
    use WithPagination;

    public $formVisible = false;
    public $orderId = null;
    public $email = '';
    public $status = 'pending';
    public $order_note = '';
    public $admin_note = '';
    public $productSearch = '';
    public $selectedProductIds = []; 
    public $selectedProducts = []; // ['productId' => ['title','dvt','quy_cach','quantity','don_gia','total']]
    public $selectAllProducts = false;
    public $total = 0;
    protected $paginationTheme = 'bootstrap';
    protected $rules = [
        'email' => 'required|email',
    ];


    // Clear search (gọi từ nút X)
    public function clearProductSearch()
    {
        $this->productSearch = '';
        // không xóa selectedProducts ở đây — giữ selection
    }

    // Khi user tick/untick checkbox, Livewire sẽ gọi updatedSelectedProductIds
    public function updatedSelectedProductIds($value)
    {
        // $value là mảng id hiện tại
        // 1) Thêm id mới vào selectedProducts nếu chưa có
        foreach ($value as $id) {
            if (!isset($this->selectedProducts[$id])) {
                $product = \App\Models\Product::find($id);
                if ($product) {
                    $this->selectedProducts[$id] = [
                        'don_gia' => $product->don_gia ?? 0,
                        'quantity' => 1,
                    ];
                }
            }
        }

        // 2) Loại bỏ những id bị uncheck khỏi selectedProducts
        foreach (array_keys($this->selectedProducts) as $existingId) {
            if (!in_array($existingId, $value)) {
                unset($this->selectedProducts[$existingId]);
            }
        }

        $this->recalculateTotal();
    }

    protected function recalculateTotal()
    {
        $total = 0;
        foreach ($this->selectedProducts as $p) {
            $total += ($p['don_gia'] ?? 0) * ($p['quantity'] ?? 1);
        }
        $this->total = $total;
    }

    public function render()
    {
        $query = Order::query()->orderByDesc('id');
        $orders = $query->paginate(10);
    
        $productsQuery = Medicine::query();
        if ($this->productSearch) {
            $productsQuery->where(function($q){
                $q->where('ten_hoat_chat', 'like', '%'.$this->productSearch.'%')
                  ->orWhere('ten_biet_duoc', 'like', '%'.$this->productSearch.'%');
            });
        }
    
        $products = $productsQuery->orderBy('ten_hoat_chat')->get();
    
        $total = 0;
        foreach ($this->selectedProducts as $item) {
            $total += ($item['quantity'] ?? 1) * ($item['don_gia'] ?? 0);
        }
    
        return view('livewire.order.order-list', [
            'orders' => $orders,
            'filteredProducts' => $products,
            'total' => $total,
        ]);
    }
    
    public function updatedProductSearch()
    {
        // Mỗi khi tìm kiếm thay đổi, reset selectAll và selectedProducts
        $this->selectAllProducts = false;
        $this->selectedProducts = [];
    }


    public function showForm($id = null)
    {
        $this->resetForm();
        $this->formVisible = true;

        if($id){
            $order = Order::find($id);
            if($order){
                $this->orderId = $order->id;
                $this->email = $order->email;
                $this->status = $order->status;
                $this->order_note = $order->order_note;
                $this->admin_note = $order->admin_note;

                $this->selectedProducts = [];
                foreach($order->order_detail as $item){
                    $this->selectedProducts[$item['id']] = [
                        'title' => $item['title'] ?? 'Chưa có tên',
                        'dvt' => $item['dvt'] ?? '-',
                        'quy_cach' => $item['quy_cach'] ?? '-',
                        'quantity' => $item['quantity'] ?? 1,
                        'don_gia' => $item['don_gia'] ?? 0,
                        'total' => $item['total'] ?? (($item['quantity'] ?? 1) * ($item['don_gia'] ?? 0)),
                    ];
                }
            }
        }
    }

    public function hideForm()
    {
        $this->formVisible = false;
    }

    public function updatedSelectAllProducts($value)
    {
        $products = Medicine::query()
            ->when($this->productSearch, fn($q)=>$q->where('ten_hoat_chat','like','%'.$this->productSearch.'%')
                ->orWhere('ten_biet_duoc','like','%'.$this->productSearch.'%'))
            ->get();

        if($value){
            foreach($products as $p){
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
    }

    public function toggleProduct($id)
    {
        if(isset($this->selectedProducts[$id])){
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
    }

    public function incrementQuantity($id)
    {
        if(isset($this->selectedProducts[$id])){
            $this->selectedProducts[$id]['quantity']++;
            $this->selectedProducts[$id]['total'] = $this->selectedProducts[$id]['quantity'] * $this->selectedProducts[$id]['don_gia'];
        }
    }

    public function decrementQuantity($id)
    {
        if(isset($this->selectedProducts[$id])){
            $this->selectedProducts[$id]['quantity'] = max(1, $this->selectedProducts[$id]['quantity'] - 1);
            $this->selectedProducts[$id]['total'] = $this->selectedProducts[$id]['quantity'] * $this->selectedProducts[$id]['don_gia'];
        }
    }

    public function saveOrder()
    {
        $this->validate();

        if(!$this->selectedProducts){
            $this->addError('selectedProducts','Bạn phải chọn sản phẩm.');
            return;
        }

        $user = User::where('email',$this->email)->first();
        $userId = $user->id ?? null;

        $orderDetail = [];
        $total = 0;
        foreach($this->selectedProducts as $id=>$item){
            $orderDetail[] = [
                'id' => $id,
                'title' => $item['title'] ?? 'Chưa có tên',
                'dvt' => $item['dvt'] ?? '-',
                'quy_cach' => $item['quy_cach'] ?? '-',
                'quantity' => $item['quantity'] ?? 1,
                'don_gia' => $item['don_gia'] ?? 0,
                'total' => $item['total'] ?? (($item['quantity'] ?? 1) * ($item['don_gia'] ?? 0)),
            ];
            $total += ($item['quantity'] ?? 1) * ($item['don_gia'] ?? 0);
        }

        $data = [
            'email' => $this->email,
            'user_id' => $userId,
            'status' => $this->status,
            'order_note' => $this->order_note,
            'admin_note' => $this->admin_note,
            'order_detail' => $orderDetail,
            'total' => $total,
        ];

        if($this->orderId){
            $order = Order::find($this->orderId);
            $order->update($data);
        } else {
            $order = Order::create($data);
        }

        $this->hideForm();
        $this->resetForm();
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
    }
    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);

        if (!$order) {
            $this->addError('deleteError', 'Đơn hàng không tồn tại.');
            return;
        }

        try {
            $order->delete();
            session()->flash('message', "Đơn hàng #{$orderId} đã xóa thành công.");
        } catch (\Exception $e) {
            $this->addError('deleteError', 'Xóa đơn hàng thất bại: ' . $e->getMessage());
        }
    }

}
