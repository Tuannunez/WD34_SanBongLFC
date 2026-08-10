# Hướng dẫn Cấu hình Giá Linh Hoạt cho Hệ thống Đặt Sân Bóng

## 📋 Tổng quan cải tiến

Bạn đã cập nhật hệ thống để hỗ trợ quản lý giá **linh hoạt** cho các khung giờ và sân. Thay vì dùng công thức cứng, giờ bạn có thể:

✅ Thiết lập giá cơ bản cho từng **loại sân**  
✅ Thêm **phụ phí** cho khung giờ đặc biệt (giờ tối, giờ cao điểm, v.v.)  
✅ **Ghi đè giá** cho các sân/khung giờ cụ thể  
✅ **Xuất/nhập** giá bằng CSV/Excel  
✅ Quản lý giá từ giao diện **thân thiện** và **trực quan**

---

## 🚀 Tính năng mới

### 1. **Giao diện quản lý khung giờ cải tiến**
   - **Địa chỉ**: `/admin/time-slots/{stadium}`
   - Bảng giá dạng grid dễ dàng chỉnh sửa
   - Phân biệt giá **mặc định** và **ghi đè**
   - Thêm khung giờ nhanh chóng

### 2. **Trang cấu hình giá linh hoạt**
   - **Địa chỉ**: `/admin/time-slots/pricing`
   - **Tab 1 - Giá cơ bản loại sân**:
     - Thiết lập giá mặc định cho sân 7 người, 9 người, 11 người, v.v.
     - Tùy chỉnh hoàn toàn không cần code
   
   - **Tab 2 - Phụ phí khung giờ**:
     - Thêm phụ phí cho giờ tối (VD: +50.000đ)
     - Thêm phụ phí cho giờ cao điểm (VD: +20%)
     - Quản lý linh hoạt tất cả các khoản phụ phí

### 3. **Xuất dữ liệu Excel**
   - **Nút**: Xuất Excel ở trang quản lý khung giờ
   - Format: Khung giờ + Tất cả sân con
   - Dễ dàng tái sử dụng cho các sân khác

---

## 📊 Cấu trúc dữ liệu mới

### Bảng `field_type_base_prices`
```sql
id
field_type_id      -- Loại sân (7-a-side, 9-a-side, 11-a-side, v.v.)
base_price         -- Giá cơ bản (VND)
description        -- Mô tả (tùy chọn)
```

### Bảng `time_slot_surcharges`
```sql
id
time_slot_id       -- Khung giờ
name               -- Tên phụ phí (VD: "Phụ phí giờ tối")
surcharge_amount   -- Số tiền/phần trăm
type               -- 'fixed' (VND) hoặc 'percentage' (%)
```

### Mở rộng bảng `time_slots`
```sql
duration_minutes       -- Thời lượng (phút)
name                   -- Tên khung giờ (tùy chọn)
is_peak_hour           -- Là giờ cao điểm?
is_evening             -- Là giờ tối?
peak_hour_surcharge    -- Phụ phí (nếu là giờ cao điểm)
```

---

## 🔄 Cách tính giá

```
Giá cuối cùng = Giá cơ bản loại sân + Phụ phí khung giờ + Ghi đè riêng (nếu có)
```

**Ví dụ:**
- Sân 9 người có giá cơ bản: 400.000đ
- Khung giờ 18:00-19:30 có phụ phí giờ tối: +100.000đ
- Kết quả: 400.000 + 100.000 = 500.000đ
- Nếu ghi đè riêng cho sân này khung giờ này: 480.000đ → Dùng giá ghi đè

---

## 📝 Hướng dẫn sử dụng

### **Bước 1: Thiết lập giá cơ bản cho loại sân**

1. Đi tới `/admin/time-slots/pricing`
2. Tab "💰 Giá cơ bản loại sân"
3. Nhập giá cho từng loại sân (VD: Sân 9 người = 400.000đ)
4. Bấm "💾 Lưu"

### **Bước 2: Thêm phụ phí cho khung giờ (tùy chọn)**

1. Vẫn ở `/admin/time-slots/pricing`
2. Chuyển sang tab "🕐 Phụ phí khung giờ"
3. Chọn khung giờ muốn thêm phụ phí
4. Nhập:
   - Tên phụ phí (VD: "Phụ phí giờ tối")
   - Số tiền hoặc % tăng
   - Loại (VND cố định hoặc % phần trăm)
5. Bấm "➕ Thêm phụ phí"

### **Bước 3: Quản lý giá từng sân**

1. Đi tới `/admin/time-slots/{stadium}` (chọn sân)
2. Xem bảng giá hiện tại
3. Chỉnh sửa từng ô giá (nếu cần ghi đè)
4. Bấm "💾" để lưu giá riêng cho sân đó
5. Bấm "↺" để xóa ghi đè và quay về giá mặc định

### **Bước 4: Xuất dữ liệu**

1. Ở trang quản lý khung giờ sân
2. Bấm nút "Xuất Excel"
3. File CSV sẽ tự động tải xuống

---

## 🔧 API Endpoints

Các endpoint mới cho nhà phát triển:

```
POST   /admin/time-slots/pricing/field-type/{fieldTypeId}
       Lưu giá cơ bản loại sân

POST   /admin/time-slots/pricing/add-surcharge/{timeSlotId}
       Thêm phụ phí khung giờ

POST   /admin/time-slots/pricing/delete-surcharge/{surchargeId}
       Xóa phụ phí

POST   /admin/time-slots/{stadium}/bulk-update-prices
       Cập nhật nhiều giá cùng lúc

PUT    /admin/time-slots/{timeSlot}/info
       Cập nhật thông tin khung giờ

GET    /admin/time-slots/{stadium}/export
       Xuất giá dưới dạng CSV
```

---

## 💡 Mẹo & Lưu ý

### ✨ Lợi ích
- **Linh hoạt**: Thay đổi giá bất cứ lúc nào không cần code
- **Chính xác**: Hỗ trợ % tăng giá theo giờ cao điểm
- **Dễ quản lý**: Giao diện rõ ràng, phân biệt mặc định vs ghi đè
- **Dễ mở rộng**: Thêm loại sân, thêm phụ phí mà không cần thay đổi code

### ⚠️ Lưu ý
- Giá cơ bản **không ảnh hưởng** đến các đơn đặt đã tồn tại
- Nếu muốn dùng giá cũ (không có giá cơ bản loại sân), hệ thống sẽ tự động fallback
- Phụ phí được cộng **tuần tự**: nếu có nhiều phụ phí, chúng sẽ cộng gộp lại
- Ghi đè riêng cho sân **có ưu tiên cao nhất** so với các tính toán khác

---

## 🆘 Xử lý sự cố

### Giá không thay đổi?
1. Kiểm tra đã lưu giá cơ bản loại sân chưa?
2. Xóa cache: `php artisan cache:clear`
3. Kiểm tra xem sân có loại sân không? (field_type_id)

### Phụ phí không xuất hiện?
1. Kiểm tra khung giờ có status = true không?
2. Kiểm tra số tiền phụ phí > 0?
3. Chờ trang load lại

### Lỗi 404 khi truy cập?
1. Chắc chắn có chạy migration: `php artisan migrate`
2. Kiểm tra routes được thêm vào `/routes/web.php`

---

## 📚 Các file được chỉnh sửa

```
✏️  Models/
    - TimeSlot.php (thêm quan hệ, attribute)
    - FieldTypeBasePrice.php (mới)
    - TimeSlotSurcharge.php (mới)

📝 Controllers/
    - Admin/TimeSlotsController.php (thêm methods mới)

🎨 Views/
    - admin/time-slots/show.blade.php (UI mới, cải tiến)
    - admin/time-slots/pricing.blade.php (mới, cấu hình giá)

🛣️  Routes/
    - routes/web.php (thêm 7 routes mới)

💾 Migrations/
    - 2026_08_10_000001_create_flexible_pricing_tables.php (mới)
```

---

## 🎯 Các bước tiếp theo

1. ✅ Cài đặt dữ liệu (chạy migration)
2. ✅ Thiết lập giá cơ bản cho từng loại sân
3. ✅ Thêm phụ phí (nếu cần)
4. ✅ Kiểm tra bảng giá tính toán đúng
5. ✅ Thông báo cho khách hàng về giá mới

---

**Chúc bạn sử dụng tốt! 🎉**

Nếu có câu hỏi, vui lòng liên hệ!
