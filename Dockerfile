# Use the official PHP + Apache image
FROM php:8.3-apache

# Install system deps & PHP extensions in one go
RUN apt-get update && \
    apt-get install -y libzip-dev git unzip && \
    docker-php-ext-install pdo pdo_mysql zip && \
    # Suppress the ServerName warning
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    a2enmod rewrite headers && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# enable rewrite + .htaccess
RUN a2enmod rewrite \
 && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copy composer manifest & install
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer \
      | php -- --install-dir=/usr/local/bin --filename=composer && \
    composer install --no-dev --optimize-autoloader

# Copy your app code (including public/, src/, vendor/, etc.)
COPY . .

# Ensure health.txt is served from the public folder
COPY health.txt /var/www/html/public/health.txt

# At runtime, Apache needs to listen on $PORT not hard‑coded 80.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

CMD ["sh","-c", "\
  sed -i \"s|Listen 80|Listen ${PORT:-80}|g\" /etc/apache2/ports.conf && \
  sed -i \"s|<VirtualHost \\*:80>|<VirtualHost *:${PORT:-80}>|g\" /etc/apache2/sites-available/000-default.conf && \
  sed -i \"s|DocumentRoot /var/www/html|DocumentRoot ${APACHE_DOCUMENT_ROOT}|g\" /etc/apache2/sites-available/000-default.conf && \
  apache2-foreground\
"]

# Healthcheck uses localhost inside the container
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:${PORT:-80}/health.txt || exit 1

# Let Docker/Render know what port we expect
EXPOSE 80
