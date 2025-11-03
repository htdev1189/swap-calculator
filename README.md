# 💱 Swap Calculator – Laravel 10 (Service & Repository + REST API)

## 🧩 Mô tả dự án
**Swap Calculator** là ứng dụng web được xây dựng bằng **Laravel 10 + Blade + MySQL**,  
giúp người dùng tính **phí qua đêm (Swap Fee)** trong giao dịch Forex.  

Ứng dụng được thiết kế theo **Service – Repository**, có **API**

---

## 🚀 Tính năng chính
✅ Form nhập liệu tính phí Swap  
✅ Tính toán theo công thức:

    Total Swap = Lot Size × Swap Rate × Holding Days

✅ Kiểm tra đầu vào & hiển thị kết quả trực quan  
✅ Lưu lịch sử tính toán vào database  
✅ Hiển thị 10 kết quả gần nhất  
✅ Hiển thị thông tin lịch sử bằng Datatable, có chức năng phân trang cũng như xóa  
✅ API cho phép thêm và lấy dữ liệu qua JSON  
✅ Thống kê dữ liệu  
✅ Import thông tin các cặp mệnh giá từ file csv  

---

## 🧠 Kiến trúc & Kỹ thuật sử dụng
| Thành phần | Công nghệ |
|-------------|------------|
| **Backend** | Laravel 10 (PHP 8.1.31) |
| **Frontend** | Blade + Bootstrap 5 |
| **Database** | MySQL (version 9.1.0)|
| **Pattern** | Service + Repository |
| **API** | Laravel API Routes (`routes/api.php`) |
| **Validation** | Laravel Validator |
| **ORM** | Eloquent |
| **Migration** | Có sẵn file tạo bảng `swap_calculations` |

---

## 📂 Cấu trúc thư mục chính
```css
app/
├── Http/
│ └── Controllers/
│   ├── SwapController.php # Controller cho Web (Blade)
│   └── SwapApiController.php # Controller cho API JSON
├── Models/
│ └── SwapCalculation.php
│ └── SwapPair.php
├── Repositories/
│ └── SwapRepository.php # trung gian xử lý data giữa model và service
│ └── SwapImportRepository.php # 
├── Services/
│ └── SwapService.php # Xử lý logic
│ └── SwapImportService.php

resources/
└── views/
│ └── Backend/
│   ├── swap
│       ├── history.balde.php # hiển thị lịch sử
│       ├── index.balde.php # thêm
│       ├── statistics.balde.php # thống kê 
│       ├── import.balde.php # import file và render data
│   ├── master.blade.php # master layout


routes/
├── web.php # Route cho web
└── api.php # Route cho RESTful API
```


---

## ⚙️ Cài đặt & Chạy project

### 1️⃣ Clone project
```bash
git clone https://github.com/htdev1189/swap-calculator.git
cd swap-calculator

# cài đặt các gói cần thiết
composer install
```
2️⃣ Cài đặt phụ thuộc
```bash
cp .env.example .env
```
3️⃣ Tạo key
```bash
php artisan key:generate
```
Cập nhật thông tin database trong file .env:
```ini
DB_DATABASE=forex
DB_USERNAME=root
DB_PASSWORD=
```
4️⃣ Tạo database và migrate
```bash
php artisan migrate
```
5️⃣ Khởi động server
```bash
php artisan serve
```

Truy cập trình duyệt tại
👉 http://127.0.0.1:8000/admin

---

🧮 Hướng dẫn sử dụng
```bash
 # Mở trình duyệt → truy cập http://127.0.0.1:8000/admin/swap
 # Nhập các thông số sau đó nhấn Calculate
    - Cặp tiền: EURUSD, XAUUSD, GBPJPY, ...
    - Lot size > 0
    - Swap long / short
    - Số ngày giữ lệnh
    - Type (Long hoặc Short)
 # Kết quả được hiển thị ngay dưới form và lưu vào database
 # Bảng “Lịch sử tính gần nhất” hiển thị 10 bản ghi mới nhất
 # File swap_pairs.csv ở ngoài thư mục root là file mẫu import
```
---
🧰 Bảng dữ liệu

Bảng: `swap_calculations`
| Cột        | Kiểu dữ liệu | Ghi chú          |
| ---------- | ------------ | ---------------- |
| id         | bigint       | Primary key      |
| pair       | string       | Cặp tiền tệ      |
| lot_size   | float        | Số lot           |
| type       | string       | Long/Short       |
| swap_rate  | float        | Giá trị swap     |
| days       | integer      | Số ngày giữ lệnh |
| total_swap | float        | Tổng phí qua đêm |
| created_at | datetime     | Thời gian tính   |
| updated_at | datetime     | Thời gian tính   |


🔧 Route
| Method | URL                | Name                   | Controller                 | Chức năng                     |
| ------ | ------------------------- | ---------------------- | -------------------------- | ----------------------------- |
| GET    | `/admin`                  | `admin.home`           | `SwapController@index`     | Home page      |
| Get    | `/admin/swap`             | `admin.swap`           | `SwapController@swap`      | hiển thị form tạo swap |
| POST   | `/admin/calculate`        | `admin.swap.calculate` | `SwapController@calculate` | Xử lý tính toán & lưu kết quả |
| DELETE | `/admin/swap/delete/{id}` | `admin.swap.destroy`   | `SwapController@destroy`   | Xử lý xóa |
| GET    | `/admin/statistics`       | `admin.swap.history.statistics`   | `SwapController@statistics`   | Thống kê  |
| GET    | `/admin/swap/import`       | `admin.swap.history.import`   | `SwapImportController@index`   | Hiển thị các Pair được import lên  |
| GET    | `/admin/swap/pairs`       | `admin.swap.pairs.data`   | `SwapImportController@getData`   | Chuẩn bị data cho Datatable  |
| POST    | `/admin/swap/import`       | `admin.swap.pairs.import`   | `SwapImportController@import`   | Import nội dung file csv vào DB  |
| GET    | `/admin/swap-pair/{pair}`       | `admin.swap.pairs.get`   | `SwapImportController@getPair`   | Thực hiện lấy thông tin theo pair  |

API
| Method | URL                   | Controller                        | Chức năng             |
| ------ | --------------------- | --------------------------------- | --------------------- |
| POST   | `/api/swap/calculate` | `SwapApiController@api_calculate` | Thêm mới Swap         |
| GET    | `/api/swap/history`   | `SwapApiController@api_history`   | Trả danh sách History |
---

🌐 RESTful API

1️⃣ POST /api/swap/calculate

Body JSON:
```json
{
  "pair": "EURUSD",
  "lot_size": 1.5,
  "swap_long": "2",
  "swap_short": "4",
  "holding_days": 3,
  "position_type": "Long"
}
```
Response:
```json
{
    "success": true,
    "data": {
        "data": {
            "pair": "EURUSD",
            "lot_size": 1.5,
            "position_type": "Long",
            "swap_rate": "2",
            "holding_days": 3,
            "totalSwap": 9
        },
        "message": "Swap dương, có thể giữ lệnh lâu"
    }
}
```
2️⃣ GET /api/swap/history

Response:

```json
{
    "success": true,
    "data": [
        {
            "id": 37,
            "pair": "EURUSD",
            "lot_size": 1.5,
            "type": "Long",
            "swap_rate": 2,
            "days": 3,
            "total_swap": 9,
            "created_at": "2025-10-25 06:05:17",
            "updated_at": "2025-10-25T06:05:17.000000Z"
        }
    ]
}
```
---

🎁 Bonus mở rộng:
### ➕ Import danh sách cặp tiền & swap mặc định từ CSV

Ứng dụng hỗ trợ import danh sách các cặp tiền tệ và giá trị swap mặc định từ file CSV.

**Cấu trúc CSV:**
```csv
pair,swap_long,swap_short
USDEUR,2.3,3.5
EURUSD,5.5,3.4
```
---
Chức năng:
- Trang: [http://127.0.0.1:8000/admin/swap/import](http://127.0.0.1:8000/admin/swap/import)
- Cho phép người dùng upload file .csv
- Hệ thống đọc dữ liệu và lưu vào bảng swap_pairs
- Nếu cặp tiền đã tồn tại → tự động cập nhật giá trị swap mới

Cấu trúc bảng swap_pairs:

| Cột        | Kiểu dữ liệu | Ghi chú              |
| ---------- | ------------ | -------------------- |
| id         | bigint       | Primary key          |
| pair       | string       | Cặp tiền tệ (unique) |
| swap_long  | float        | Giá trị swap Long    |
| swap_short | float        | Giá trị swap Short   |
| created_at | datetime     | Thời gian tạo        |
| updated_at | datetime     | Thời gian cập nhật   |
---

## 🎁 Cập nhật tính năng login vào hệ thống 
### 1️⃣ Chạy Seeder
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=RolePermissionSeeder
```



## 👨‍💻 Người thực hiện


Hoàng Anh Tuấn 📧 [htuan1189@gmail.com](mailto:htuan1189@gmail.com)

📅 Bài test kỹ thuật Laravel – Swap Calculator