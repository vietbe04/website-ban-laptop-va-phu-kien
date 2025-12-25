# Dockerfile for PHP + Apache (fix lỗi More than one MPM loaded)
FROM php:8.3-apache

# Tắt các MPM không cần thiết và bật đúng mpm_prefork
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

# Copy toàn bộ mã nguồn vào thư mục web root của Apache
COPY . /var/www/html/

# Đặt thư mục làm việc mặc định
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
