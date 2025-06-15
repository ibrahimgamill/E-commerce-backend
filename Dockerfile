# Use official PHP CLI image
FROM php:8.1-cli

# Install git for Composer source installs
RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip zip \
 && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy only composer files first to cache deps
COPY composer.json composer.lock ./

# Download and install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php \
 && php composer.phar install --no-dev --optimize-autoloader

# Copy the rest of your application
COPY . .

# Expose a default port (Railway will override $PORT for you)
EXPOSE 8080

# Use a shell so that $PORT is expanded at runtime, with a default
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]
