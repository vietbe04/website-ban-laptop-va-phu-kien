# Dockerfile for PHP + Apache (fix lỗi More than one MPM loaded)
FROM php:8.3-apache

# Ensure no conflicting MPM load files remain, then enable a single MPM (prefork)
# Defensive: remove any mpm_* .load/.conf from both mods-enabled and mods-available to avoid duplicates
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf || true \
 && rm -f /etc/apache2/mods-available/mpm_*.load /etc/apache2/mods-available/mpm_*.conf || true
# Create a minimal mpm_prefork.load to force a single MPM and enable it
RUN printf 'LoadModule mpm_prefork_module /usr/lib/apache2/modules/mod_mpm_prefork.so\n' > /etc/apache2/mods-available/mpm_prefork.load \
 && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
# Ensure other MPMs are not enabled
RUN a2dismod mpm_event mpm_worker || true || true
# Enable rewrite
RUN a2enmod rewrite || true

# Output enabled modules for debugging (check build logs)
RUN ls -la /etc/apache2/mods-enabled || true && apachectl -M || true

# Add startup script to ensure a single MPM is enabled at runtime and then start Apache
COPY docker-start.sh /usr/local/bin/docker-start.sh
RUN chmod +x /usr/local/bin/docker-start.sh

ENTRYPOINT ["/usr/local/bin/docker-start.sh"]
CMD ["apache2-foreground"]

# Copy toàn bộ mã nguồn vào thư mục web root của Apache
COPY . /var/www/html/

# Đặt thư mục làm việc mặc định
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80
