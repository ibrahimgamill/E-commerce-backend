FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y libzip-dev unzip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
