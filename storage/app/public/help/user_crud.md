# User CRUD Implementation Plan (Laravel 12 + Livewire 3 + AdminLTE)

## PHẦN 1 — CRUD CORE FUNCTION
### 1.1. List Users (hiển thị danh sách)
**Mục tiêu:** Hiển thị danh sách user với tính năng:
- Tìm kiếm theo tên, email, username.
- Sắp xếp theo cột.
- Phân trang (perPage tuỳ chọn).
- Checkbox chọn nhiều user.
- Tích hợp AdminLTE + Livewire loading states.

**Chi tiết triển khai:**
- Livewire Component: `UserList`
- Blade: `resources/views/livewire/users/user-list.blade.php`
- Trường hiển thị: id, name, email, username, role, created_at.
- Event: emit `editUser(id)` và `deleteUser(id)`.

### 1.2. Create User (Add)
**Mục tiêu:** Thêm user mới với các trường:
- name, email, username, password, role, is_admin, birthdate, referral_code, google_id, device_token, email_verified_at.

**Chi tiết triển khai:**
- Livewire Component: `UserForm`
- Validation chuẩn Laravel.
- Nếu không nhập password → random tự động.
- Nếu check auto_verify → set `email_verified_at = now()`.

### 1.3. Update User (Edit)
**Mục tiêu:**
- Cho phép chỉnh sửa toàn bộ thông tin trừ username.
- Nếu password trống → giữ nguyên.
- Nếu role thay đổi → sync lại roles (Spatie).
- Không cho chỉnh sửa admin root.

### 1.4. Delete User
**Mục tiêu:**
- Xóa 1 user hoặc nhiều user.
- Không cho xóa admin.
- Khi xóa → detach roles và permissions.

**Chi tiết triển khai:**
- Xác nhận trước khi xóa (modal confirm).
- Emit event refresh danh sách sau khi xóa.

---

## PHẦN 2 — ROLE & PERMISSION
### 2.1. Assign role (một user)
### 2.2. Bulk update roles (nhiều user)
### 2.3. Kiểm soát logic bảo vệ admin

---

## PHẦN 3 — IMPORT / EXPORT
### 3.1. Import Excel/CSV
- Validate file.
- Hiển thị lỗi từng dòng.
- Prevent duplicate email/username.
- Optimize performance.

### 3.2. Export Excel
- Chọn nhiều user → export.
- Format đẹp, dễ in.

### 3.3. Export PDF
- Template chuẩn UTF-8.
- In danh sách user.

### 3.4. Print preview
- Tạo view in trực tiếp trên trình duyệt.

---

## PHẦN 4 — UI/UX TỐI ƯU
### 4.1. Modal chuẩn AdminLTE.
### 4.2. Toastr notify.
### 4.3. Datepicker birthdate.
### 4.4. Avatar upload.
### 4.5. Loading states, skeleton loading.

---

## PHẦN 5 — OPTIMIZATION / CLEAN CODE
### 5.1. Component hóa Livewire.
### 5.2. Model chuẩn hóa fillable & casts.
### 5.3. Helper: `UserService` để tách logic tạo/sửa/xóa.

---

## KẾ HOẠCH TRIỂN KHAI
1️⃣ **STEP 1:** CRUD CORE FUNCTION  → *Thực hiện đầu tiên*.

2️⃣ **STEP 2:** Roles & Permissions.

3️⃣ **STEP 3:** Import & Export.

4️⃣ **STEP 4:** UI/UX nâng cao.

5️⃣ **STEP 5:** Tối ưu & hoàn thiện.