# Order API Documentation

## Base URL
```
/api/orders
```

---

## 1. Create Order
**POST** `/api/orders`

### Request Body
```
{
  "user_id": 1,
  "customer_id": 10,
  "order_detail": [
    { "product_id": 5, "quantity": 2 },
    { "product_id": 12, "quantity": 1 }
  ]
}
```

### Response
```
{
  "success": true,
  "order_id": 123,
  "message": "Order created successfully"
}
```

---

## 2. Update Order
**PUT** `/api/orders/{orderId}`

### Request Body
```
{
  "customer_id": 200,
  "status": "pending",
  "order_note": "Cập nhật đơn",
  "order_detail": [
    { "product_id": 12, "quantity": 3 },
    { "product_id": 5, "quantity": 1 }
  ]
}
```

### Response
```
{
  "success": true,
  "message": "Order updated successfully"
}
```

---

## 3. Delete Order
**DELETE** `/api/orders/{orderId}`

### Response
```
{
  "success": true,
  "message": "Order deleted successfully"
}
```

---

## 4. Get Order Detail
**GET** `/api/orders/{orderId}`

### Response
```
{
  "id": 14,
  "user_id": 1,
  "customer_id": 20,
  "status": "pending",
  "order_detail": [
    {
      "product_id": 5,
      "quantity": 2,
      "so_lo": "A123",
      "han_dung": "2025-12-31",
      "don_gia": 15000,
      "total": 30000
    }
  ],
  "total": 30000
}
```

---

## Suggested Extra APIs

### ✅ List Orders
**GET** `/api/orders`
- Pagination
- Filter status
- Filter by date range

### ✅ Restore Deleted Order (nếu dùng soft delete)
**POST** `/api/orders/{id}/restore`

### ✅ Export Order to PDF
**GET** `/api/orders/{id}/export`

### ✅ Check Stock
**GET** `/api/stock/{medicine_id}`

---

Nếu muốn tôi triển khai đầy đủ mã nguồn cho API Controller + Routes + Service, hãy nói tôi biết!

