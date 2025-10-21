# 🧩 TEMPLATE MÔ TẢ COMPONENT LARAVEL + LIVEWIRE

> 💡 Chỉ cần copy mẫu này, điền thông tin vào các chỗ `[ ... ]` rồi gửi cho ChatGPT để mình viết component, view, route hoặc logic hoàn chỉnh cho bạn.

---

## 1️⃣. Thông tin Model

- **Model tên:** `[Tên Model, ví dụ: Category]`
- **Bảng trong DB:** `[Tên bảng nếu khác, ví dụ: wp_categories]`
- **Các trường chính:**
  ```
  id, name, slug, description, parent_id, image, created_at, updated_at
  ```
- **Quan hệ (nếu có):**
  - `[Tên quan hệ]()` → `[Loại quan hệ]` (`hasMany`, `belongsTo`, `belongsToMany`, v.v.)
  - Ví dụ:
    - `children()` → `hasMany(Category::class, 'parent_id')`
    - `parent()` → `belongsTo(Category::class, 'parent_id')`

---

## 2️⃣. Mục tiêu component

> Mô tả component cần làm gì, ví dụ:
- Quản lý danh mục (thêm, sửa, xóa, tìm kiếm)
- Import/export Excel
- Upload ảnh có preview
- Hiển thị dạng cây hoặc danh sách
- Phân trang, lọc, sắp xếp

---

## 3️⃣. Giao diện mong muốn

> Mô tả bố cục / style bạn dùng:

- Giao diện: `[AdminLTE / TailwindCSS / Bootstrap / Custom]`
- Có bảng danh sách: `[Có/Không]`
- Có form thêm/sửa: `[Có/Không]`
- Form hiển thị dạng: `[Tab panel / accordion / ẩn hiện khi nhấn nút]`
- Không dùng modal: `[Đúng/Sai]`
- Có upload ảnh: `[Có/Không]`
- Có preview ảnh: `[Có/Không]`
- Chọn danh mục cha dạng cây checkbox: `[Có/Không]`
- Khi bấm Hủy → quay lại danh sách: `[Đúng/Sai]`

---

## 4️⃣. Luồng xử lý & hành vi đặc biệt

> Những hành vi cần có trong component:
- Khi chọn checkbox cha → tự động chọn tất cả con
- Khi lưu → kiểm tra trùng `slug`
- Khi sửa → giữ nguyên ảnh cũ nếu không thay
- Khi hủy → reset form và ẩn form
- Khi lưu → thông báo `toastr` hoặc `session()->flash`
- Khi xóa → xác nhận bằng `confirm()` hoặc `sweetalert`

---

## 5️⃣. Kết quả bạn muốn mình xuất ra

> Chọn phần bạn muốn mình tạo:
- [x] File component Livewire (`app/Livewire/...`)
- [x] View Blade (`resources/views/livewire/...`)
- [x] Helper / trait hỗ trợ (nếu cần)
- [x] Route Laravel
- [ ] Migration (nếu cần tạo bảng)
- [ ] Seeder mẫu dữ liệu

---

## 6️⃣. (Tùy chọn) – Dữ liệu mẫu

> Nếu có thể, bạn gửi mẫu dữ liệu hoặc vài record:
```json
[
  { "id": 1, "name": "Nhóm thuốc", "slug": "nhom-thuoc" },
  { "id": 2, "name": "Kháng sinh", "slug": "khang-sinh", "parent_id": 1 }
]
```

---

## ✅ Ví dụ điền mẫu hoàn chỉnh

**1️⃣ Model:**
Category (id, name, slug, description, parent_id, image)

**2️⃣ Mục tiêu:**
Tạo component `CategoryManager` để quản lý danh mục (thêm, sửa, xóa, upload ảnh, chọn danh mục cha dạng cây).

**3️⃣ Giao diện:**
Dùng AdminLTE, không dùng modal, form hiển thị dạng tab, có preview ảnh, khi hủy quay về danh sách.

**4️⃣ Luồng xử lý:**
Kiểm tra trùng slug, khi chọn cha → chọn tất cả con, khi lưu → hiện thông báo “Lưu thành công”.

**5️⃣ Kết quả cần:**
Component + Blade view + Route.
