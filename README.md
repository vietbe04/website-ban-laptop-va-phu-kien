# Website Bán Laptop và Phụ Kiện

## Giới thiệu
Đây là dự án website bán laptop và phụ kiện, xây dựng bằng PHP thuần, sử dụng mô hình MVC. Website hỗ trợ các chức năng quản lý sản phẩm, đơn hàng, người dùng, khuyến mãi, chat hỗ trợ, và tích hợp thanh toán VNPay.

## Cấu trúc thư mục
- `app/`: Chứa các file cấu hình, lớp điều khiển và kết nối cơ sở dữ liệu.
- `controllers/`: Các controller xử lý logic cho từng chức năng (sản phẩm, đơn hàng, người dùng, v.v.).
- `models/`: Các model tương tác với cơ sở dữ liệu.
- `public/`: Tài nguyên tĩnh như CSS, JS, hình ảnh.
- `views/`: Giao diện người dùng (frontend và backend).
- `vnpay_php/`: Tích hợp thanh toán VNPay.
- `index.php`: Điểm vào của ứng dụng.
- `composer.json`: Quản lý thư viện PHP.
- `website.sql`: File cấu trúc và dữ liệu mẫu cho cơ sở dữ liệu.

## Yêu cầu hệ thống
- PHP >= 7.4
- MySQL/MariaDB
- Composer

## Cài đặt
1. Clone dự án về máy:
   ```bash
   git clone <repo-url>
   ```
2. Cài đặt các thư viện PHP:
   ```bash
   composer install
   ```
3. Tạo database và import file `website.sql`.
4. Cấu hình kết nối database trong `app/config.php`.
5. Chạy ứng dụng trên localhost hoặc deploy lên server.

## Tính năng chính
- Quản lý sản phẩm, loại sản phẩm, biến thể
- Quản lý đơn hàng, chi tiết đơn hàng
- Quản lý người dùng, đăng nhập/đăng ký
- Quản lý khuyến mãi, mã giảm giá
- Đánh giá, bình luận sản phẩm
- Chat hỗ trợ khách hàng
- Tích hợp thanh toán VNPay
- Quản lý kho, nhà cung cấp



## Tài khoản demo
- **Admin:**
   - Email: nttv9604@gmail.com
   - Mật khẩu: 123456
- **Nhân viên (staff):**
   - Email: quocviet161104@gmail.com
   - Mật khẩu: 123456
- **User:**
   - Email: quocviet16114@gmail.com
   - Mật khẩu: 123456

## Liên hệ
- Tác giả: [Dương Quốc Việt]
- Email: [quocviet161104@gmail.com]
