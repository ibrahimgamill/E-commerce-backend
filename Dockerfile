FROM php:8.3-apache

# Install tools
RUN apt-get update && apt-get install -y git unzip

# Enable Apache rewrite
RUN a2enmod rewrite

WORKDIR /var/www/html

# Copy everything
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# Set Apache to serve from /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
