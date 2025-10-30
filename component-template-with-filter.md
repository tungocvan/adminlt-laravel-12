# 🧩 TEMPLATE MÔ TẢ COMPONENT LARAVEL + LIVEWIRE (BẢN CÓ LỌC DANH MỤC)

> 💡 Chỉ cần copy mẫu này, điền thông tin vào các chỗ `[ ... ]` rồi gửi cho ChatGPT để mình viết component, view, route hoặc logic hoàn chỉnh cho bạn.

---

## 1️⃣. Thông tin Model

- **Model tên:** `[Tên Model, ví dụ: Product]`
- **Bảng trong DB:** `[Tên bảng nếu khác, ví dụ: wp_products]`
- **Các trường chính:**
  ```
  id, title, slug, category_id, image, price, status, created_at, updated_at
  ```
- **Quan hệ (nếu có):**
  - `[Tên quan hệ]()` → `[Loại quan hệ]` (`belongsTo`, `hasMany`, v.v.)
  - Ví dụ:
    - `category()` → `belongsTo(Category::class)`
    - `categories()` → `belongsToMany(Category::class)`

---

## 2️⃣. Mục tiêu component

> Mô tả component cần làm gì:
- Quản lý sản phẩm (thêm, sửa, xóa, lọc theo danh mục, tìm kiếm)
- Upload ảnh có preview
- Lưu nhiều danh mục (checkbox)
- Phân trang, lọc, sắp xếp

---

## 3️⃣. Giao diện mong muốn

- Giao diện: `[AdminLTE / TailwindCSS / Bootstrap / Custom]`
- Bảng danh sách: `[Có/Không]`
- Form thêm/sửa: `[Có/Không]`
- Form hiển thị dạng: `[Tab panel / accordion / inline form]`
- Không dùng modal: `[Đúng/Sai]`
- Có upload ảnh: `[Có/Không]`
- Có preview ảnh: `[Có/Không]`
- Chọn danh mục cha dạng cây checkbox: `[Có/Không]`
- Khi bấm Hủy → quay lại danh sách: `[Đúng/Sai]`
- **🆕 Có lọc theo danh mục:** `[Có/Không]`

---

## 4️⃣. Lọc & tìm kiếm

> 💡 Nếu có lọc theo danh mục hoặc từ khóa, mô tả rõ ở đây.

- Có dropdown chọn danh mục để lọc: `[Có/Không]`
- Có thể chọn nhiều danh mục để lọc: `[Có/Không]`
- Có tìm kiếm theo tên / slug / mô tả: `[Có/Không]`
- Khi thay đổi bộ lọc → tự động cập nhật danh sách (không reload trang): `[Có/Không]`

---

## 5️⃣. Luồng xử lý & hành vi đặc biệt

- Khi chọn danh mục cha → tự động chọn tất cả con
- Khi lưu → kiểm tra trùng `slug`
- Khi sửa → giữ nguyên ảnh cũ nếu không thay
- Khi hủy → reset form và ẩn form
- Khi lưu → thông báo `toastr` hoặc `session()->flash`
- Khi xóa → xác nhận bằng `confirm()` hoặc `sweetalert`

---

## 6️⃣. Kết quả bạn muốn mình xuất ra

- [x] Component Livewire (`app/Livewire/...`)
- [x] View Blade (`resources/views/livewire/...`)
- [x] Route Laravel
- [ ] Migration / Seeder nếu cần
- [ ] Helper / Trait

---

## 7️⃣. Dữ liệu mẫu (nếu có)
```json
[
  { "id": 1, "title": "Paracetamol", "slug": "paracetamol", "category_id": 2 },
  { "id": 2, "title": "Amoxicillin", "slug": "amoxicillin", "category_id": 3 }
]
```

---

## ✅ Ví dụ điền mẫu hoàn chỉnh

**1️⃣ Model:** Product (id, title, slug, category_id, image, price)

**2️⃣ Mục tiêu:** Quản lý sản phẩm, có lọc theo danh mục, upload ảnh, chọn nhiều danh mục.

**3️⃣ Giao diện:** Dùng AdminLTE, không dùng modal, form dạng tab, có preview ảnh.

**4️⃣ Lọc:** Dropdown danh mục cha + con, lọc động bằng Livewire (wire:model).

**5️⃣ Kết quả:** Component + View + Route.
