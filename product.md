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

## 3. DATABASE SCHEMA (BẮT BUỘC BÁM SÁT)

### 3.1 wp_products
```php
- id
- title (string, indexed)
- slug (string, unique)
- short_description (nullable)
- description (longText, nullable)
- regular_price (decimal 10,2, nullable)
- sale_price (decimal 10,2, nullable)
- image (string, nullable)
- gallery (json, nullable)
- tags (json, nullable)
- created_at
- updated_at
```

### 3.2 carts
```php
- id
- session_id
- user_id (nullable)
- created_at
- updated_at
```

### 3.3 cart_items
```php
- id
- cart_id
- product_id
- price
- quantity
- total
- created_at
- updated_at
```

### 3.4 orders
```php
- id
- user_id (nullable)
- order_code (unique)
- customer_name
- customer_phone
- customer_email (nullable)
- customer_address
- note (nullable)
- subtotal
- discount (default 0)
- total
- status (pending, confirmed, shipping, completed, cancelled)
- created_at
- updated_at
```

### 3.5 order_items
```php
- id
- order_id
- product_id
- product_name
- price
- quantity
- total
- created_at
- updated_at
```

---

## 4. MODELS & DOMAIN LOGIC

### 4.1 WpProduct
**Vị trí:** `Modules/Website/Models/WpProduct.php`

- Cast:
  - gallery → array
  - tags → array

- Accessor:
  - final_price
  - discount_percent

- Relationship:
```php
belongsToMany(Category::class)
```

### 4.2 Cart, CartItem
- Quan hệ: Cart hasMany CartItem
- Lưu session-based cart

### 4.3 Order, OrderItem
- Order hasMany OrderItem
- OrderItem belongsTo WpProduct

---

## 5. ROUTES (MODULE WEBSITE)
**File:** `Modules/Website/routes/web.php`

Prefix: `/website`

| URL | Chức năng |
|----|---------|
| /products | Danh sách sản phẩm |
| /products/{slug} | Chi tiết sản phẩm |
| /cart | Giỏ hàng |
| /checkout | Thanh toán |
| /order-success/{code} | Thành công |

---

## 6. CONTROLLERS (PAGE ENTRY)

### ProductController
- index()
- show($slug)

### CartController
- index()

### CheckoutController
- index()
- process()
- success($orderCode)

⚠️ Controller chỉ điều hướng view, **KHÔNG xử lý business logic**

---

## 7. LIVEWIRE COMPONENTS (CORE LOGIC)

### Products
- ProductList
- ProductDetail

### Cart
- Cart/AddToCart
- Cart/CartList

### Checkout
- Checkout/CheckoutForm
- Checkout/OrderSummary

Render view bắt buộc:
```php
return view('Website::livewire.component-name');
```

---

## 8. LAYOUT WEBSITE (BẮT BUỘC DÙNG)

**File có sẵn:**
```
Modules/Website/resources/views/layouts/website.blade.php
```

⚠️ KHÔNG tạo layout mới
⚠️ KHÔNG nhúng lại Bootstrap

---

## 9. UI / UX / SEO
- Grid Bootstrap 4.6.1
- number_format cho giá
- Ảnh fallback
- Badge giảm giá chỉ hiển thị khi có sale
- URL SEO theo slug

---

## 10. CHECKOUT FLOW (PRODUCTION READY)

1. Add to Cart
2. Xem Cart
3. Checkout Form
4. Tạo Order + OrderItems
5. Clear Cart
6. Redirect Order Success

---

## 11. QUY TRÌNH SINH CODE TUẦN TỰ (CỰC KỲ QUAN TRỌNG)

### BƯỚC 1: MIGRATIONS
1. wp_products
2. carts
3. cart_items
4. orders
5. order_items

### BƯỚC 2: MODELS
1. WpProduct
2. Cart
3. CartItem
4. Order
5. OrderItem

### BƯỚC 3: SEEDER
- WpProductSeeder (10–20 sản phẩm mẫu)

### BƯỚC 4: ROUTES
- products → cart → checkout → success

### BƯỚC 5: CONTROLLERS
- ProductController
- CartController
- CheckoutController

### BƯỚC 6: LIVEWIRE
- ProductList
- ProductDetail
- Cart Components
- Checkout Components

### BƯỚC 7: VIEWS
- products/index
- products/show
- cart/index
- checkout/index
- checkout/success

---

## 12. CẤU TRÚC MODULE BẮT BUỘC
```txt
Modules/
└── Website/
    ├── Http/Controllers/
    ├── Livewire/
    ├── Models/
    ├── database/
    ├── resources/views/
    └── routes/web.php
```

---

## 13. OUTPUT AI PHẢI TRẢ RA
- Code hoàn chỉnh
- Copy chạy ngay Laravel 12
- Không thiếu file
- Không phá kiến trúc Module
- Giải thích rõ từng bước

---

✅ PROMPT NÀY DÙNG ĐỂ SINH CODE ECOMMERCE CHUẨN PRODUCTION

