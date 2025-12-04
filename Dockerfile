FROM php:8.2-fpm-alpine

RUN apk update && apk add \
    git \
    curl \
    mysql-client \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql opcache zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN mkdir -p bootstrap/cache

RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache
    
EXPOSE 80