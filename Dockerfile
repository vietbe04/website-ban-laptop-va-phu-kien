# Dockerfile for PHP + Apache on Render
FROM php:8.2-apache

# Enable Apache mod_rewrite (hỗ trợ route đẹp cho Laravel, CodeIgniter, v.v.)
RUN a2enmod rewrite

# Copy toàn bộ mã nguồn vào thư mục web root của Apache
COPY . /var/www/html/

# Thiết lập quyền cho các file/folder nếu cần (tuỳ dự án, có thể bỏ qua nếu không cần)
# RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (Render sẽ tự publish port này)
EXPOSE 80

# Đặt thư mục làm việc mặc định
WORKDIR /var/www/html
