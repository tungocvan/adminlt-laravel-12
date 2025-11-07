# Hệ thống CLI Quản lý Đơn Hàng (Order Management CLI)

Tài liệu này tổng hợp toàn bộ **các lệnh CLI**, **chức năng**, cũng như **đề xuất mở rộng** cho hệ thống quản lý đơn hàng bằng Laravel Artisan Command.

---

## ✅ 1. Danh sách các Lệnh CLI

### **1.1. Tạo đơn hàng**
```
php artisan order:create --data='{...}'
```
**Chức năng:**
- Tạo mới đơn hàng
- Trừ tồn kho theo sản phẩm
- Tự động lấy số lô & hạn dùng theo tồn kho gần hết hạn

---

### **1.2. Cập nhật đơn hàng**
```
php artisan order:update {order_id} --data='{...}'
```
**Chức năng:**
- Cập nhật thông tin đơn hàng
- Hoàn trả tồn kho cũ
- Trừ tồn kho mới theo dữ liệu sửa

---

### **1.3. Xoá đơn hàng**
```
php artisan order:delete {order_id}
```
**Chức năng:**
- Xoá đơn
- Hoàn trả tồn kho

---

## ✅ 2. Mẫu dữ liệu sử dụng cho \`--data\`

### **Tạo đơn hoặc cập nhật đơn (mẫu đầy đủ)**
```json
{
  "customer_id": 123,
  "status": "pending",
  "order_note": "Ghi chú đơn hàng",
  "order_detail": [
    { "product_id": 12, "quantity": 3 },
    { "product_id": 5, "quantity": 1 }
  ]
}
```

---

## ✅ 3. Chức năng hệ thống CLI hiện có

### **3.1. Kiểm soát tồn kho tự động**
- Khi tạo đơn: trừ kho → chọn số lô chuẩn → ghi số lô + hạn dùng
- Khi cập nhật: hoàn kho cũ → trừ kho mới
- Khi xoá: hoàn kho

### **3.2. Tự động xử lý số lô & hạn dùng**
- Luôn lấy batch gần hết hạn nhất (FEFO)

### **3.3. An toàn dữ liệu**
- Tất cả thao tác create/update/delete đều chạy trong Transaction

---

## ✅ 4. Gợi ý thêm các lệnh CLI có thể bổ sung

### **4.1. Xem danh sách đơn hàng**
```
php artisan order:list
```
- Liệt kê đơn hàng dạng bảng
- Hỗ trợ filter (trạng thái, ngày tạo, user...)

---

### **4.2. Xem chi tiết đơn hàng**
```
php artisan order:show {id}
```
- Hiển thị đầy đủ thông tin đơn + chi tiết + tồn kho liên quan

---

### **4.3. Xoá nhiều đơn**
```
php artisan order:delete-multi "10,12,15"
```
- Hỗ trợ xóa hàng loạt
- Có confirm trước khi thực hiện

---

### **4.4. Rollback đơn hàng**
```
php artisan order:rollback {id}
```
- Khôi phục các đơn đã xoá
- Trừ lại tồn kho tương ứng

---

### **4.5. Đồng bộ trạng thái tồn kho**
```
php artisan stock:sync-status
```
- Tự động cập nhật trạng thái: `in_stock`, `low_stock`, `out_stock`

---

### **4.6. Kiểm tra tồn kho theo hạn dùng**
```
php artisan stock:check-expired
```
- Thống kê thuốc sắp hết hạn / hết hạn

---

## ✅ 5. Gợi ý cấu trúc thư mục để quản lý CLI tốt hơn

```
app/
 ├─ Console/
 │   ├─ Commands/
 │   │   ├─ Order/
 │   │   │   ├─ CreateOrderCommand.php
 │   │   │   ├─ UpdateOrderCommand.php
 │   │   │   ├─ DeleteOrderCommand.php
 │   │   │   ├─ ListOrderCommand.php
 │   │   │   ├─ ShowOrderCommand.php
 │   │   │   └─ ...
 │   └─ Kernel.php
 ├─ Services/
 │   └─ OrderService.php
```

---

## ✅ 6. Gợi ý thêm chức năng nâng cao trong tương lai
- **Xuất hoá đơn PDF qua CLI**
- **Gửi email xác nhận đơn hàng qua CLI**
- **Import đơn hàng từ file CSV**
- **Đồng bộ đơn hàng lên API khác (Shopee/Lazada)**

---

## ✅ 7. Ghi chú sử dụng
- Luôn dùng \`--data\` với JSON chuẩn
- Không cần escape khi dùng PowerShell, nhưng cần escape khi dùng Linux Bash nếu có dấu nháy
- Nên dùng Postman để test dữ liệu trước khi chạy bằng CLI

---

## ✅ 8. Phiên bản tài liệu
- **Version:** 1.0
- **Cập nhật gần nhất:** 2025-11-07
- **Người tạo:** ChatGPT hỗ trợ theo yêu cầu của Từ Ngọc Vân

---

Nếu bạn muốn tôi viết thêm file README.md riêng cho GitHub hoặc tạo thêm vài command nữa, cứ nói nhé!

