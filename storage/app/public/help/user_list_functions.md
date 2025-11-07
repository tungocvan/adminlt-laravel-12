# UserList Component - Function Documentation

## Mô tả chung
Component Livewire `UserList` quản lý người dùng trong hệ thống với các chức năng CRUD, quản lý role, approve email và xuất dữ liệu. Sử dụng AdminLTE 3.1 + Bootstrap 4.6.1 + Livewire 3.1.

---

## 1️⃣ Computed Properties

### `getUsersProperty()`
- **Chức năng**: Lấy danh sách người dùng từ DB, áp dụng filter, phân trang và sắp xếp.
- **Return**: `LengthAwarePaginator`

### `getRolesProperty()`
- **Chức năng**: Trả về mảng `[id => name]` của tất cả role.
- **Sử dụng**: Dropdown chọn role trong modal.

---

## 2️⃣ Selection

### `toggleSelectAll()`
- **Chức năng**: Chọn hoặc bỏ chọn tất cả user hiển thị trên table.

### `updatedSelectedUsers()`
- **Chức năng**: Lưu selection hiện tại vào session.

### `updatedPerPage()`
- **Chức năng**: Khi thay đổi số bản ghi hiển thị, reset trang về trang 1.

---

## 3️⃣ Modal Open / Close

### `openModal()`
- **Chức năng**: Mở modal tạo / edit user, reset form.

### `closeModal()`
- **Chức năng**: Đóng modal user và reset form.

### `openModalRole()`
- **Chức năng**: Mở modal cập nhật role cho các user được chọn.
- **Hành động**: Nếu chỉ 1 user, load role hiện tại.

### `closeModalRole()`
- **Chức năng**: Đóng modal role và reset `selectedRoleId`.

---

## 4️⃣ Form Handling

### `resetForm()`
- **Chức năng**: Reset tất cả biến form về mặc định.

### `save()`
- **Chức năng**: Tạo mới user.
- **Validation**: `$rulesCreate`
- **Hành động**: Gọi `TnvUserHelper::register()`, đóng modal, show message, refresh list.

### `edit($userId)`
- **Chức năng**: Mở modal edit user, load dữ liệu user, populate form.

### `update()`
- **Chức năng**: Cập nhật user.
- **Validation**: `$rulesUpdate` + unique email
- **Hành động**: Gọi `TnvUserHelper::updateUser()`, sync role nếu có, đóng modal, show success, refresh list.

---

## 5️⃣ Delete

### `delete($userId)`
- **Chức năng**: Xóa 1 user.
- **Hành động**: Không xóa admin (`is_admin == -1`), xóa và refresh list.

### `deleteSelected()`
- **Chức năng**: Xóa tất cả user được chọn.
- **Hành động**: Không xóa admin, reset selection, show success, refresh list.

---

## 6️⃣ Role Management

### `updateUserRole()`
- **Chức năng**: Cập nhật role cho các user đã chọn.
- **Validation**: `selectedRoleId` tồn tại.
- **Hành động**: Sync role qua Spatie Permission, đóng modal, reset selection, show success.

---

## 7️⃣ Approve / Email Verification

### `approve($id)`
- **Chức năng**: Duyệt email của user.
- **Hành động**: Nếu `email_verified_at` null → cập nhật `now()`, show success, refresh list.

---

## 8️⃣ Export

### `exportSelected()`
- **Chức năng**: Xuất Excel cho user đã chọn.
- **Hành động**: Gọi `UsersExport`, trả về file download với timestamp.

### `exportToPDF()`
- **Chức năng**: Xuất PDF cho user đã chọn.
- **Hành động**: Load view `exports.users-pdf`, trả về stream download PDF.

---

## 9️⃣ Sorting

### `sortBy($field)`
- **Chức năng**: Sắp xếp table theo `$field`.
- **Hành động**: Toggle asc/desc nếu đang sort theo field, hoặc set field mới + asc, reset pagination.

---

## 10️⃣ Render

### `render()`
- **Chức năng**: Trả view Livewire `livewire.users.user-list` với `$users` và `$roles`.

---

## 11️⃣ Ghi chú
- Modal: không đóng khi click ngoài (`backdrop: static`) và không đóng khi ESC (`keyboard: false`).
- Tất cả thao tác CRUD + Role + Export đều dùng session flash messages và Livewire dispatch events.
- `TnvUserHelper` chịu trách nhiệm tạo và update user (bao gồm hash password, gán role).

---

> File này có thể dùng như tài liệu tham khảo cho việc bảo trì, mở rộng hoặc code review component `UserList`.