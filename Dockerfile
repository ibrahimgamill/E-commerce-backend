# Use the official PHP + Apache image
FROM php:8.3-apache

# 1) Install OS deps (no recommends) and clean up immediately
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libzip-dev \
      git \
      unzip \
 && rm -rf /var/lib/apt/lists/*

# 2) Install and configure PHP extensions
RUN docker-php-ext-configure zip \
 && docker-php-ext-install zip pdo pdo_mysql

# 3) Enable Apache modules & suppress ServerName warning
RUN a2enmod rewrite headers \
 && echo "ServerName localhost" >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# 4) Install Composer and your PHP dependencies
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer \
      | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --optimize-autoloader

# 5) Copy the rest of your app
COPY . .

# 6) Expose health check file
COPY health.txt /var/www/html/public/health.txt

# 7) Patch Apache to use $PORT
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
CMD ["sh","-c", "\
    sed -i \"s|Listen 80|Listen ${PORT:-80}|\" /etc/apache2/ports.conf && \
    sed -i \"s|<VirtualHost \\*:80>|<VirtualHost *:${PORT:-80}>|\" /etc/apache2/sites-available/000-default.conf && \
    sed -i \"s|DocumentRoot /var/www/html|DocumentRoot ${APACHE_DOCUMENT_ROOT}|\" /etc/apache2/sites-available/000-default.conf && \
    apache2-foreground\
"]

HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:${PORT:-80}/health.txt || exit 1

EXPOSE 80
