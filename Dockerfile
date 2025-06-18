FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY public/ /var/www/html/
COPY src/ /var/www/html/src/
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy vendor folder (if you already have it locally - optional but not preferred)
# COPY vendor/ /var/www/html/vendor/

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Set proper permissions (if needed)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
