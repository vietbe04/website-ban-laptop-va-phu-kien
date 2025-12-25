<?php 
// Cấu hình kết nối MySQL Railway
// DB_HOST = shinkansen.proxy.rlwy.net
// DB_USER = root
// DB_PASS = srHfFNvTybZwsCdqgOvCutAtNxzDtbtxf
// DB_NAME = railway
// DB_PORT = 33952
define("APP_URL", getenv('APP_URL') ?: "http://localhost/DQV");
// Thiết lập múi giờ Việt Nam cho toàn bộ ứng dụng
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Đã chuyển quản lý ngưỡng giảm giá sang cơ sở dữ liệu (bảng order_thresholds).
// Không còn định nghĩa mặc định tại config; nếu không có dữ liệu trong DB thì sẽ không áp dụng giảm giá ngưỡng.
// Secret dùng để ký cookie "remember me" (thay đổi giá trị này trên môi trường production và giữ bí mật)
define('REMEMBER_ME_SECRET', 'CHANGE_THIS_TO_A_RANDOM_SECRET_2025');
