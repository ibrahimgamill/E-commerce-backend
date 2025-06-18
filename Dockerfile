           # Use official PHP image with Apache and required extensions
           FROM php:8.3-apache

           # Install system dependencies for Composer and MySQL support
           RUN apt-get update && apt-get install -y \
               libzip-dev zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
               && docker-php-ext-install pdo_mysql

           # Enable Apache mod_rewrite (needed for most modern PHP apps)
           RUN a2enmod rewrite

           # Copy source code into the container
           COPY . /var/www/html/

           # Set working directory
           WORKDIR /var/www/html

           # Set up permissions
           RUN chown -R www-data:www-data /var/www/html \
               && chmod -R 755 /var/www/html

           # Expose port 80
           EXPOSE 80

           # Start Apache server
           CMD ["apache2-foreground"]
