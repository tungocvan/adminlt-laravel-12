<?php

namespace App\Livewire\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
//use App\Models\Order;
use Modules\Order\Models\Order;
use App\Models\Medicine;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\MedicineStock;

class OrderList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $formVisible = false;
    public $orderId = null;
    public $email = '';
    public $status = 'pending';
    public $customers = [];
    public $selectAll = false;
    public $selectedOrder = [];
    public $customer_id = null;
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

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            // Lấy tất cả order hiện tại (trên page hiện tại)
            $this->selectedOrder = Order::latest()->pluck('id')->toArray();
        } else {
            $this->selectedOrder = [];
        }
    }
    // Xóa các order đã chọn
    public function deleteSelectedOrders()
    {
        if (empty($this->selectedOrder)) {
            $this->addError('deleteError', 'Bạn chưa chọn đơn hàng nào.');
            return;
        }

        \DB::transaction(function () {
            $orders = Order::whereIn('id', $this->selectedOrder)->get();

            foreach ($orders as $order) {
                if ($order->link_download) {
                    Storage::disk('public')->delete($order->link_download);
                }
                $order->delete();
            }
        });

        session()->flash('message', count($this->selectedOrder) . ' đơn hàng đã được xóa thành công.');

        $this->selectedOrder = [];
        $this->selectAll = false;
        $this->resetPage();
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
                $q->where('ten_hoat_chat', 'like', "%{$this->productSearch}%")->orWhere('ten_biet_duoc', 'like', "%{$this->productSearch}%");
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
            $q->where('ten_hoat_chat', 'like', "%{$this->productSearch}%")->orWhere('ten_biet_duoc', 'like', "%{$this->productSearch}%");
        })
            ->where('id', $id)
            ->exists();

        if (!$visible) {
            return;
        }

        if (isset($this->selectedProducts[$id])) {
            unset($this->selectedProducts[$id]);
        } else {
            $show_lot_input = MedicineStock::getNearestExpiry($id);
            // lấy số lô và hạn dùng gần nhất
            $so_lo = $show_lot_input['so_lo'];
            $han_dung = $show_lot_input['han_dung'];
            $so_luong_ton = $show_lot_input['so_luong'];
           
            $p = Medicine::find($id);
            $this->selectedProducts[$id] = [
                'title' => $p->ten_hoat_chat ?? 'Chưa có tên',
                'dvt' => $p->don_vi_tinh ?? '-',
                'quy_cach' => $p->quy_cach_dong_goi ?? '-',
                'quantity' => 1,
                'don_gia' => $p->don_gia ?? 0,
                'total' => $p->don_gia ?? 0,
                'so_lo' => $so_lo,
                'han_dung' => $han_dung,
                'so_luong_ton' => $so_luong_ton,
                'show_lot_input' => false,
            ];
        }

        $this->recalculateTotal();
    }

    public function toggleLotInput($id)
    {
        // Lấy tất cả các lô theo hạn dùng tăng dần
        $stocks = MedicineStock::where('medicine_id', $id)
            ->orderBy('han_dung', 'asc')
            ->get(['so_lo', 'han_dung', 'so_luong']);

        if ($stocks->isEmpty()) {
            // Không có lô nào
            $this->dispatch('alert', type: 'error', message: 'Thuốc này không còn tồn kho!');
            return;
        }

        // Nếu sản phẩm chưa được chọn → KHÔNG xử lý
        if (!isset($this->selectedProducts[$id])) {
            return;
        }

        // Toggle hiển thị combobox
        $current = $this->selectedProducts[$id]['show_lot_input'] ?? false;
        $this->selectedProducts[$id]['show_lot_input'] = !$current;

        // Gán danh sách lô
        $this->selectedProducts[$id]['available_stocks'] = $stocks;

        // Khi bật combobox (true) → gán lô đầu tiên mặc định
        if ($this->selectedProducts[$id]['show_lot_input']) {
            $first = $stocks->first();

            $this->selectedProducts[$id]['so_lo'] = $first->so_lo;
            $this->selectedProducts[$id]['han_dung'] = \Carbon\Carbon::parse($first->han_dung)->format('Y-m-d');
            $this->selectedProducts[$id]['so_luong_ton'] = $first->so_luong;
        }
    }

    public function updatedSelectedProducts($value, $name)
    {
        // Phân tích path property
        $parts = explode('.', $name);

        if (count($parts) === 2) {
            // Ví dụ: 5.quantity
            [$productId, $field] = $parts;
        } elseif (count($parts) === 3) {
            // Ví dụ: selectedProducts.5.quantity
            [$prefix, $productId, $field] = $parts;
        } else {
            return;
        }

        if (!isset($this->selectedProducts[$productId])) {
            return;
        }

        $product = &$this->selectedProducts[$productId];

        // ===============================
        // Xử lý khi thay đổi số lượng
        // ===============================
        if ($field === 'quantity') {
            $qty = (int) $value;
            $maxQty = $product['so_luong_ton'] ?? 9999;

            if ($qty > $maxQty) {
                $product['quantity'] = $maxQty;
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'error',
                    'message' => "Số lượng vượt tồn kho! Tối đa: {$maxQty}",
                ]);
            } elseif ($qty < 1) {
                $product['quantity'] = 1;
            } else {
                $product['quantity'] = $qty;
            }

            $this->updateProductTotal($productId);
        }

        // ===============================
        // Xử lý khi thay đổi số lô
        // ===============================
        if ($field === 'so_lo') {
            $stock = collect($product['available_stocks'] ?? [])->firstWhere('so_lo', $value);

            if ($stock) {
                // Cập nhật hạn dùng và tồn kho theo lô mới
                $product['han_dung'] = \Carbon\Carbon::parse($stock['han_dung'])->format('Y-m-d');
                $product['so_luong_ton'] = $stock['so_luong'];

                // Nếu quantity hiện tại vượt tồn kho mới → điều chỉnh
                if ($product['quantity'] > $stock['so_luong']) {
                    $product['quantity'] = $stock['so_luong'];
                    $this->dispatchBrowserEvent('alert', [
                        'type' => 'error',
                        'message' => "Số lượng giảm xuống tồn kho hiện tại: {$stock['so_luong']}",
                    ]);
                }

                $this->updateProductTotal($productId);
            }
        }
    }

    /* =========================
     * QUANTITY CONTROL
     * ========================= */
    public function incrementQuantity($id)
    {
        if (isset($this->selectedProducts[$id])) {
            $this->selectedProducts[$id]['quantity']++;
            // nếu $this->selectedProducts[$id]['quantity'] > $this->selectedProducts[$id]['so_luong_ton'] báo không đủ số lượng
            $this->updateProductTotal($id);
        }
    }

    public function decrementQuantity($id)
    {
        //dd($id);
        if (isset($this->selectedProducts[$id])) {
            $this->selectedProducts[$id]['quantity'] = max(1, $this->selectedProducts[$id]['quantity'] - 1);
            // dd($this->selectedProducts[$id]['so_luong_ton']);
            // nếu $this->selectedProducts[$id]['quantity'] > $this->selectedProducts[$id]['so_luong_ton'] báo không đủ số lượng
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
        $this->resetForm();

        $this->email = Auth::user()->email;
        $this->customer_id = Auth::user()->id;
        $this->customers = User::select('id', 'username')->where('referral_code', $this->email)->get();
        $this->formVisible = true;

        if (!$id) {
            return;
        }

        $order = Order::find($id);
        if (!$order) {
            return;
        }

        $this->orderId = $order->id;
        $this->email = $order->email;
        $this->status = $order->status;
        $this->customer_id = $order->customer_id;
        $this->order_note = $order->order_note;
        $this->admin_note = $order->admin_note;

        $this->selectedProducts = [];

        $orderDetails = is_array($order->order_detail) ? $order->order_detail : json_decode($order->order_detail, true);

        foreach ($orderDetails as $item) {
            $product_id = $item['product_id'];

            // Lấy các lô còn tồn kho
            $stocks = \App\Models\MedicineStock::where('medicine_id', $product_id)
                ->orderBy('han_dung', 'asc')
                ->get(['so_lo', 'han_dung', 'so_luong']);

            // Nếu lô cũ đã hết tồn kho, vẫn đưa vào danh sách để hiển thị
            if (!empty($item['so_lo']) && !empty($item['han_dung'])) {
                $exists = $stocks->firstWhere('so_lo', $item['so_lo']);
                if (!$exists) {
                    $stocks->prepend(
                        (object) [
                            'so_lo' => $item['so_lo'],
                            'han_dung' => $item['han_dung'],
                            'so_luong' => $item['quantity'] ?? 0,
                        ],
                    );
                }
            }

            // Lấy mặc định stock nếu chưa có số lô/hạn dùng
            $defaultStock = $stocks->first();

            $this->selectedProducts[$product_id] = [
                'title' => $item['title'] ?? 'Chưa có tên',
                'dvt' => $item['dvt'] ?? '-',
                'quy_cach' => $item['quy_cach'] ?? '-',
                'quantity' => $item['quantity'] ?? 1,
                'don_gia' => $item['don_gia'] ?? 0,
                'total' => $item['total'] ?? ($item['quantity'] ?? 1) * ($item['don_gia'] ?? 0),
                'so_lo' => $item['so_lo'] ?: $defaultStock->so_lo ?? '',
                'han_dung' => isset($item['han_dung']) ? \Carbon\Carbon::parse($item['han_dung'])->format('Y-m-d') : '',
                'show_lot_input' => true,
                'available_stocks' => $stocks,
            ];
        }

        $this->recalculateTotal();
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

        if (!$user) {
            $this->addError('email', 'Người dùng không tồn tại.');
            return;
        }

        \DB::beginTransaction();

        try {
            $orderDetail = [];
            $total = 0;

            foreach ($this->selectedProducts as $id => $item) {
                $qty = (int) ($item['quantity'] ?? 1);
                $price = (float) ($item['don_gia'] ?? 0);
                $so_lo = $item['so_lo'] ?? null;
                $han_dung = $item['han_dung'] ?? null;

                // Kiểm tra tồn kho nếu có lô/hạn dùng
                if ($so_lo && $han_dung) {
                    $stock = MedicineStock::where('medicine_id', $id)->where('so_lo', $so_lo)->where('han_dung', $han_dung)->first();

                    if (!$stock) {
                        throw new \Exception("Lô {$so_lo} của thuốc {$item['title']} không tồn tại!");
                    }

                    if ($stock->so_luong < $qty) {
                        throw new \Exception("Không đủ tồn kho cho thuốc {$item['title']} lô {$so_lo}. Tồn kho hiện tại: {$stock->so_luong}");
                    }

                    // Giảm tồn kho
                    $stock->so_luong -= $qty;
                    if ($stock->so_luong <= 0) {
                        $stock->status = 'empty';
                        $stock->so_luong = 0;
                    }
                    $stock->save();
                }

                // Thêm vào chi tiết đơn
                $orderDetail[] = [
                    'product_id' => $id,
                    'title' => $item['title'] ?? 'Chưa có tên',
                    'dvt' => $item['dvt'] ?? '-',
                    'quy_cach' => $item['quy_cach'] ?? '-',
                    'quantity' => $qty,
                    'don_gia' => $price,
                    'total' => $qty * $price,
                    'so_lo' => $so_lo,
                    'han_dung' => $han_dung,
                ];

                $total += $qty * $price;
            }

            // Dữ liệu order
            $data = [
                'email' => $this->email,
                'customer_id' => $this->customer_id,
                'user_id' => $user->id,
                'status' => $this->status,
                'order_note' => $this->order_note,
                'admin_note' => $this->admin_note,
                'order_detail' => $orderDetail,
                'total' => $total,
            ];

            if ($this->orderId) {
                // Cập nhật
                Order::find($this->orderId)?->update($data);
            } else {
                // Tạo mới
                Order::create($data);
            }

            \DB::commit();

            session()->flash('message', 'Đơn hàng đã được lưu thành công.');

            $this->hideForm();
            $this->resetForm();
            $this->resetPage();
        } catch (\Exception $e) {
            \DB::rollBack();
            $this->addError('saveError', 'Lỗi khi lưu đơn hàng: ' . $e->getMessage());
        }
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

        if ($this->orderId) {
            // Nếu đang sửa đơn: chỉ hiển thị sản phẩm đã chọn
            $products = Medicine::whereIn('id', array_keys($this->selectedProducts))
                ->orderBy('ten_hoat_chat')
                ->get();
        } else {
            // Trường hợp tạo mới hoặc search bình thường
            $products = Medicine::query()->when($this->productSearch, fn($q) => $q->where('ten_hoat_chat', 'like', "%{$this->productSearch}%")->orWhere('ten_biet_duoc', 'like', "%{$this->productSearch}%"))->orderBy('ten_hoat_chat')->get();
        }

        return view('livewire.order.order-list', [
            'orders' => $orders,
            'filteredProducts' => $products,
            'total' => $this->total,
        ]);
    }
}
