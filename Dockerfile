# 1) Base image
FROM php:8.1-cli

# 2) Install system deps including git for Composer
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      git \
      unzip \
      zip \
 && rm -rf /var/lib/apt/lists/*

# 3) Set working dir
WORKDIR /app

# 4) Copy only composer files & install PHP deps
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php \
 && php composer.phar install --no-dev --optimize-autoloader

# 5) Copy the rest of your app
COPY . .

# 6) Expose a default port (Railway will override $PORT)
EXPOSE 8080

# 7) Launch PHP built-in server on the dynamic port
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]
