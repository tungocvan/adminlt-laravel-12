# 🛒 PROMPT XÂY DỰNG WEBSITE ECOMMERCE CHUẨN PRODUCTION

## Laravel 12 + Livewire 3.1 + Bootstrap 4.6.1 (Modules/Website)

---

## 1. VAI TRÒ AI (BẮT BUỘC TUÂN THỦ)
Bạn là **Senior Laravel Developer**, chuyên sâu:
- Laravel **12**
- Livewire **3.1** (class-based, **KHÔNG dùng Volt**)
- Bootstrap **4.6.1**
- Kiến trúc **Modules**, clean code, production-ready

Nhiệm vụ: xây dựng **Website Ecommerce hoàn chỉnh** theo mô hình Module, gồm:
- Product (Listing + Detail)
- Cart (Giỏ hàng)
- Checkout
- Order Success

---

## 2. TECH STACK & NGUYÊN TẮC CỐT LÕI
- Laravel **12**
- Livewire **3.1**
- Bootstrap **4.6.1** (đã có sẵn trong layout)
- Chuẩn **MVC + Livewire**
- Không hardcode dữ liệu
- Không dùng JSON fake
- Lấy dữ liệu **trực tiếp từ database**
- Code clean, dễ mở rộng
- Tuân thủ **Modules/Website**

---
👉 “Sinh BƯỚC 1: toàn bộ migrations theo prompt”
Table	Mô tả	Status
wp_products	Bảng sản phẩm	✅
carts	Giỏ hàng theo session	✅
cart_items	Chi tiết giỏ hàng	✅
wp_orders	Đơn hàng	✅
order_items	Chi tiết đơn hàng	✅

Schema::create('wp_products', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();          // Tên sản phẩm
            $table->string('slug')->unique();          // Slug
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();

            $table->decimal('regular_price', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();

            $table->string('image')->nullable();       // Ảnh chính
            $table->json('gallery')->nullable();       // Ảnh phụ
            $table->json('tags')->nullable();          // Tags dạng JSON

            $table->timestamps();
        });
    
      Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();
        });

   Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')
                  ->constrained('carts')
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->constrained('wp_products')
                  ->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            // Đảm bảo mỗi sản phẩm chỉ xuất hiện 1 lần trong 1 cart
            $table->unique(['cart_id', 'product_id']);
        });

Schema::create('wp_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('customer_address');
            $table->text('note')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', [
                'pending',
                'confirmed',
                'shipping',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->timestamps();

            // ⚠️ Không dùng foreign key nếu bảng users chưa có
            // $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            $table->index('customer_phone');
            $table->index('status');
        });
Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('wp_orders')  // ← ĐỔI TỪ 'orders' THÀNH 'wp_orders'
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('wp_products')
                  ->nullOnDelete();
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

SƠ ĐỒ QUAN HỆ:
┌─────────────────┐
│   wp_products   │
└────────┬────────┘
         │
    ┌────┴────┐
    ▼         ▼
┌────────┐  ┌─────────────┐
│ carts  │  │   orders    │
└───┬────┘  └──────┬──────┘
    │              │
    ▼              ▼
┌────────────┐  ┌─────────────┐
│ cart_items │  │ order_items │
└────────────┘  └─────────────┘

👉 “Sinh BƯỚC 2: Models + relationships + accessors”
Modules/
└── Website/
    └── Models/
        ├── WpProduct.php
        ├── Cart.php
        ├── CartItem.php
        ├── Order.php
        └── OrderItem.php
Model	Table	Features	Status
WpProduct	wp_products	Accessors, Scopes, Casts	✅
Cart	carts	Helper methods, Session-based	✅
CartItem	cart_items	Quantity management	✅
Order	wp_orders	Status constants, Order code	✅
OrderItem	order_items	Product snapshot	✅        

👉 “Sinh BƯỚC 3: Seeder dữ liệu mẫu”
    dữ liệu tương chưng, truyền biến count để nhận sp cần , đơn giản, ảnh dùng tượng trưng thôi !
👉 “Sinh BƯỚC 4: Routes + Controllers”

BẢNG TÓM TẮT ROUTES
Method	URI	Name	Controller@Action	Mô tả
GET	/website	website.home	Redirect	Trang chủ → Products
GET	/website/products	website.products.index	ProductController@index	Danh sách sản phẩm
GET	/website/products/{slug}	website.products.show	ProductController@show	Chi tiết sản phẩm
GET	/website/cart	website.cart.index	CartController@index	Giỏ hàng
GET	/website/checkout	website.checkout.index	CheckoutController@index	Trang thanh toán
POST	/website/checkout	website.checkout.process	CheckoutController@process	Xử lý đặt hàng
GET	/website/order-success/{code}	website.order.success	CheckoutController@success	Đặt hàng thành công

 TỔNG KẾT CONTROLLERS
Controller	Method	Route	Chức năng
ProductController	index()	GET /products	Render view danh sách
show($slug)	GET /products/{slug}	Render view chi tiết
CartController	index()	GET /cart	Render view giỏ hàng
CheckoutController	index()	GET /checkout	Render view checkout
process()	POST /checkout	Xử lý đặt hàng (fallback)
success($code)	GET /order-success/{code}	Render view thành công

👉 “Sinh BƯỚC 5: Livewire ProductList & ProductDetail & Cart, Checkout & Order flow”
Modules/
└── Website/
    └── Livewire/
        ├── Products/
        │   ├── ProductList.php
        │   └── ProductDetail.php
        ├── Cart/
        │   ├── AddToCart.php
        │   ├── CartList.php
        │   └── CartIcon.php
        └── Checkout/
            ├── CheckoutForm.php
            └── OrderSummary.php


👉 Làm như vậy thì AI không bao giờ sinh thiếu file hoặc lệch kiến trúc.