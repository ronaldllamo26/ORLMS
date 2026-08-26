FROM php:8.2-apache

# Install system dependencies, MySQL client libraries, and PDO extensions
RUN apt-get update && apt-get install -y \
    default-libmysqlclient-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files to the container
COPY . /var/www/html/

# Copy config.php.example to config.php (since config.php is gitignored)
RUN cp /var/www/html/config/config.php.example /var/www/html/config/config.php

# Set working directory
WORKDIR /var/www/html/

# Install composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose standard port
EXPOSE 80
