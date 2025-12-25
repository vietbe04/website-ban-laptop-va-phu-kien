# Dockerfile for PHP + Apache on Railway
FROM php:8.2-apache

# Enable Apache mod_rewrite (for pretty URLs)
RUN a2enmod rewrite

# Copy all project files to Apache's web root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Expose port 80 (Railway will use this port)
EXPOSE 80
