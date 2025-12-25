# Dockerfile for PHP + Apache on Render
FROM php:8.2-apache

# Copy all project files to Apache's web root
COPY . /var/www/html/

# Enable Apache mod_rewrite if needed (uncomment if your app uses it)
# RUN a2enmod rewrite

# Expose port 80
EXPOSE 80

# Set working directory (optional, default is /var/www/html)
WORKDIR /var/www/html
