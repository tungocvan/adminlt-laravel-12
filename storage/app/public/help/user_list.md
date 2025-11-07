# Livewire Component: UserList

## 1️⃣ Công nghệ sử dụng
- Laravel 12
- Livewire 3.1
- AdminLTE 3.1 + Bootstrap 4.6.1
- jQuery
- Spatie Permission
- Maatwebsite Excel
- Barryvdh DomPDF
- Carbon
- Custom Helper: TnvUserHelper

## 2️⃣ Traits
- WithPagination
- WithFileUploads

## 3️⃣ Modal / Blade
### user-form.blade.php
- Modal thêm mới / chỉnh sửa user
- Fields: Name, Email, Username, Password, Birthdate, Google ID, Admin switch, Role select
- Validation / binding: wire:model.defer, wire:submit.prevent
- Modal options: v-centered, scrollable, backdrop static, keyboard false

### user-form-role.blade.php
- Modal cập nhật role
- Chỉ hiển thị khi selectedUsers > 0
- Role select + validation
- Modal options: v-centered, scrollable, static-backdrop, wire:ignore.self

## 4️⃣ Tính năng
### Quản lý danh sách user
- Phân trang
- Tìm kiếm
- Sắp xếp
- Table responsive

### Quản lý selection
- Chọn từng user
- Chọn tất cả
- Lưu selection

### Modal user
- Thêm mới / chỉnh sửa
- Reset form
- Không đóng khi click ngoài / ESC

### Form user
- Create / Update user
- Gán role khi tạo / update
- Cập nhật birthdate, google_id, is_admin
- Password optional khi edit

### Quản lý role
- Cập nhật role cho 1 hoặc nhiều user
- Validate role tồn tại
- Sync role với Spatie Permission

### Xóa user
- Xóa từng user
- Xóa user đã chọn
- Không xóa admin

### Approve / xác thực email
- Duyệt email (email_verified_at)
- Hiển thị badge "Đã duyệt" hoặc nút "Duyệt"

### Xuất dữ liệu
- Excel & PDF
- Timestamp filename
- Chỉ export user đã chọn

### Script / Event Handling
- dispatch events + document.addEventListener
- Click nút đóng modal từ DOM
- Không cho click ngoài modal đóng

### UI / AdminLTE
- Card header + action buttons
- Alerts thành công / lỗi
- Table hover, sortable
- Footer modal: Submit / Close