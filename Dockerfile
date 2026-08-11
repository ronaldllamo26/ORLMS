FROM php:8.2-apache

# Install PostgreSQL dev libraries and PDO extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files to the container
COPY . /var/www/html/

# Copy config.php.example to config.php (since config.php is gitignored)
RUN cp /var/www/html/config/config.php.example /var/www/html/config/config.php

# Set working directory
WORKDIR /var/www/html/

# Expose standard port
EXPOSE 80
