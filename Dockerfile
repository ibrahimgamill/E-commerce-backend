FROM php:8.1-cli

RUN apt-get update \
 && apt-get install -y --no-install-recommends git unzip zip \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php \
 && php composer.phar install --no-dev --optimize-autoloader

COPY . .

# Railway will map its port to this container port
EXPOSE 8080

# Bind PHP’s dev server to 0.0.0.0:8080 unconditionally
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
