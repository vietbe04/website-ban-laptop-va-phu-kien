# Dockerfile for PHP + Apache (chuẩn, không gây lỗi MPM)
FROM php:8.3-apache

# Bật mod_rewrite (chỉ bật module này, không bật MPM khác)
RUN a2enmod rewrite

# Copy toàn bộ mã nguồn vào thư mục web root của Apache
COPY . /var/www/html/

# Đặt thư mục làm việc mặc định
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
