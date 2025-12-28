# 📌 CHỨC NĂNG: NHÂN VIÊN ĐỀ XUẤT CÔNG VIỆC

Tài liệu này mở rộng từ flowchart, bao gồm:
- UI Flow (React Native)
- API Flow (Backend)
- Data Model
- Prompt AI dùng tiếp cho việc code

---

## 1️⃣ UI FLOW – THEO MÀN HÌNH (REACT NATIVE)

### 1.1 Nhân viên

**Screen: ProposalListScreen**
- Danh sách đề xuất của nhân viên
- Trạng thái: Pending / Approved / Rejected
- Nút: `+ Tạo đề xuất mới`

**Screen: CreateProposalScreen**
- Input:
  - Tiêu đề
  - Mô tả chi tiết
  - Thời gian dự kiến
  - Độ ưu tiên
  - File đính kèm
- Button:
  - Gửi đề xuất

**Screen: ProposalDetailScreen (Employee)**
- Xem chi tiết đề xuất
- Xem phản hồi của cấp trên
- Trạng thái xử lý

---

### 1.2 Cấp trên

**Screen: ApprovalListScreen**
- Danh sách đề xuất từ nhân viên
- Bộ lọc: Pending / Approved / Rejected

**Screen: ApprovalDetailScreen**
- Xem chi tiết đề xuất
- Nút:
  - Phê duyệt
  - Từ chối (nhập lý do)

---

## 2️⃣ API FLOW – BACKEND

### 2.1 Tạo đề xuất
```
POST /api/proposals
```
```json
{
  "title": "Đề xuất triển khai tính năng A",
  "description": "Chi tiết công việc...",
  "expected_time": "2025-12-20",
  "priority": "HIGH"
}
```

---

### 2.2 Lấy danh sách đề xuất
```
GET /api/proposals?role=employee
GET /api/proposals?role=manager
```

---

### 2.3 Phê duyệt / Từ chối
```
PUT /api/proposals/{id}/approve
PUT /api/proposals/{id}/reject
```
```json
{
  "comment": "Phù hợp, triển khai ngay"
}
```

---

## 3️⃣ DATA MODEL (GỢI Ý)

### Proposal
```ts
{
  id: string
  title: string
  description: string
  expected_time: string
  priority: 'LOW' | 'MEDIUM' | 'HIGH'
  status: 'PENDING' | 'APPROVED' | 'REJECTED'
  created_by: userId
  approved_by?: userId
  manager_comment?: string
  created_at: datetime
}
```

---

## 4️⃣ TRẠNG THÁI & LUỒNG XỬ LÝ

| Trạng thái | Mô tả |
|---------|------|
| PENDING | Chờ cấp trên xử lý |
| APPROVED | Đã phê duyệt |
| REJECTED | Bị từ chối |

---

## 5️⃣ PROMPT AI – DÙNG ĐỂ CODE TIẾP

### Prompt Backend
```
Bạn là backend developer.
Hãy xây dựng API cho chức năng nhân viên đề xuất công việc.
Yêu cầu:
- CRUD proposal
- Phân quyền nhân viên / cấp trên
- Trạng thái pending / approved / rejected
- Có comment phản hồi
Tech: NodeJS / Laravel / NestJS
```

---

### Prompt Frontend (React Native)
```
Bạn là React Native developer.
Hãy xây dựng UI cho chức năng nhân viên đề xuất công việc.
Bao gồm:
- Proposal List
- Create Proposal
- Proposal Detail
- Approval Screen cho cấp trên
State management: Redux hoặc Zustand
```

---

## 6️⃣ HƯỚNG MỞ RỘNG (OPTIONAL)
- Bình luận nhiều vòng
- Đính kèm file
- Push notification
- Giao việc sau phê duyệt
- Thống kê hiệu suất đề xuất

---

📌 Tài liệu này dùng trực tiếp cho:
- Giao việc dev
- Prompt AI tiếp tục code
- Viết README / SRS

