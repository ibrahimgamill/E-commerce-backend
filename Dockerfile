# Use official PHP-Apache image
FROM php:8.3-apache

# Enable required Apache modules
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory to /var/www/html (default Apache root)
WORKDIR /var/www/html

# Copy composer files and install dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install zip
RUN apt-get update \
    && apt-get install -y git unzip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of your app code into the container
COPY . .

# Set Apache DocumentRoot to /var/www/html/public
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# If you have .htaccess in public, make sure permissions are set (optional)
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 (Apache default)
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
