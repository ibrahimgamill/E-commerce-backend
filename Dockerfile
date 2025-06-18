FROM php:8.3-apache

# Install system dependencies FIRST
RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
        git \
        unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Enable Apache rewrite module
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy composer files first, install dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of your app code
COPY . .
RUN ls -la /var/www/html/public


# Set Apache DocumentRoot to /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Set permissions (optional but good practice)
RUN chown -R www-data:www-data /var/www/html

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost/health.txt || exit 1


EXPOSE 80

CMD ["apache2-foreground"]
