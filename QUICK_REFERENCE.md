# 🎯 Quick Reference - Hệ Thống Giá Linh Hoạt

## 📍 Các trang quản lý

| Tính năng | URL | Mô tả |
|----------|-----|-------|
| 📋 Danh sách sân | `/admin/time-slots` | Xem tất cả sân và khung giờ |
| ✏️ Quản lý khung giờ & giá | `/admin/time-slots/{id}` | Thêm/chỉnh/xóa khung giờ, đặt giá |
| ⚙️ Cấu hình giá | `/admin/time-slots/pricing` | Thiết lập giá cơ bản, phụ phí |
| 📊 Xuất Excel | `/admin/time-slots/{id}/export` | Download giá hiện tại |

---

## 🔄 Quy trình thiết lập

### Bước 1️⃣ - Cấu hình giá cơ bản (Một lần)
```
Đi đến: /admin/time-slots/pricing → Tab "Giá cơ bản loại sân"
↓
Nhập giá cho từng loại sân (VD: Sân 9 người = 400.000đ)
↓
Bấm "Lưu"
```

### Bước 2️⃣ - Thêm phụ phí (Nếu cần)
```
Vẫn ở: /admin/time-slots/pricing → Tab "Phụ phí khung giờ"
↓
Chọn khung giờ → Thêm phụ phí (VD: Giờ tối +50.000đ)
↓
Bấm "➕ Thêm phụ phí"
```

### Bước 3️⃣ - Quản lý khung giờ
```
Đi đến: /admin/time-slots/{id}
↓
Thêm khung giờ (8:00-9:30) → Bấm "Thêm/Cập nhật"
↓
Xem bảng giá tự động tính toán
↓
Ghi đè giá riêng nếu cần (nhập số → Bấm "💾")
```

---

## 💰 Công thức tính giá

```
┌─ Giá cơ bản loại sân (Từ cấu hình)
├─ + Phụ phí khung giờ (Nếu có)
├─ + Phụ phí khác (Nếu có)
└─ = Giá mặc định

NẾU có ghi đè riêng → Dùng giá ghi đè
NẾU không → Dùng giá mặc định
```

**Ví dụ:**
```
Sân 9 người: 400.000đ
Khung giờ 18:00 có phụ phí: +100.000đ
= Giá mặc định: 500.000đ

Nhưng sân này khung giờ này ghi đè: 480.000đ
→ Khách hàng sẽ trả: 480.000đ
```

---

## 🎨 UI Features

### Bảng giá (Show view)
- ✅ Hiển thị khung giờ dạng badge màu xanh
- ✅ Hiển thị loại sân ở header cột
- ✅ Phân biệt giá **mặc định** vs **ghi đè** (chữ màu vàng)
- ✅ Bấm "💾" để lưu, "↺" để xóa override
- ✅ Nút "Xuất Excel" để tải file CSV

### Trang cấu hình (Pricing view)
- ✅ Tab 1: Thiết lập giá cơ bản theo loại sân
- ✅ Tab 2: Quản lý phụ phí khung giờ
- ✅ Form thêm phụ phí (tên, số tiền, loại cố định/%)
- ✅ Danh sách phụ phí hiện tại
- ✅ Nút xóa phụ phí nhanh

---

## 🔧 Để chỉnh sửa giá trong tương lai

### Thay đổi giá cơ bản
```
/admin/time-slots/pricing
→ Tab "Giá cơ bản loại sân"
→ Chỉnh sửa số → Bấm "Lưu"
```

### Thêm phụ phí mới
```
/admin/time-slots/pricing
→ Tab "Phụ phí khung giờ"
→ Chọn khung giờ → Thêm mới → Bấm "➕"
```

### Ghi đè giá sân cụ thể
```
/admin/time-slots/{id}
→ Chỉnh ô giá → Bấm "💾" Lưu
→ Xóa override: Bấm "↺"
```

---

## ⚡ Thay đổi tức thì?

❌ **KHÔNG** - Giá chỉ thay đổi cho **đơn đặt mới**  
✅ **Đơn đặt cũ** vẫn giữ giá cũ  
✅ Thay đổi có hiệu lực ngay lập tức cho **booking mới**

---

## 📱 Mobile thân thiện

- ✅ Responsive design
- ✅ Form compact
- ✅ Touch-friendly buttons

---

## 🆘 Gặp vấn đề?

| Vấn đề | Giải pháp |
|--------|----------|
| Giá không thay đổi | Kiểm tra đã lưu giá cơ bản chưa? |
| Phụ phí không hiển thị | Kiểm tra khung giờ có status = true? |
| Lỗi 404 | Chạy: `php artisan migrate` |
| Cache cũ | Chạy: `php artisan cache:clear` |

---

**Tài liệu chi tiết:** Xem file `HUONG_DAN_GIA_LINH_HOAT.md`
