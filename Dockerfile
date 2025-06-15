# Use official PHP CLI image
FROM php:8.1-cli

# Set working directory
WORKDIR /app

# Copy all code
COPY . .

# Install dependencies if you have composer.json
RUN curl -sS https://getcomposer.org/installer | php \
 && php composer.phar install --no-dev --optimize-autoloader

# Expose the port your app will listen on (Railway will override $PORT)
EXPOSE 8080

# Start PHP’s built-in server on the dynamic $PORT (fallback to 8080 locally)
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]
