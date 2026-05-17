FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git \
    unzip \
    curl \
    icu-dev \
    libpq-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install \
        intl \
        pdo_pgsql \
        zip \
        opcache \
    && pecl install apcu \
    && docker-php-ext-enable apcu

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data var

EXPOSE 9000
CMD ["php-fpm"]
