# Dockerfile for PHP + Apache (fix lỗi More than one MPM loaded)
FROM php:8.3-apache

# Ensure no conflicting MPM load files remain, then enable a single MPM (prefork)
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf || true
RUN a2dismod mpm_event mpm_worker || true || true
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

# Output enabled modules for debugging (check build logs)
RUN ls -la /etc/apache2/mods-enabled && apachectl -M || true

# Copy toàn bộ mã nguồn vào thư mục web root của Apache
COPY . /var/www/html/

# Đặt thư mục làm việc mặc định
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
