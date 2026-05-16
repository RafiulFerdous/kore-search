FROM composer:latest AS vendor

WORKDIR /build

COPY composer.json composer.lock ./
COPY app/helpers.php app/helpers.php
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    autoconf \
    build-base \
    $([ "$(uname -m)" = "x86_64" ] && echo "libxml2-dev") \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        bcmath \
        xml \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del autoconf build-base libpng-dev libzip-dev oniguruma-dev \
    && rm -rf /var/cache/apk/* /tmp/*

COPY docker/php.ini $PHP_INI_DIR/conf.d/99-koresearch.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /build/vendor vendor

RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && rm -f /var/www/html/bootstrap/cache/packages.php \
           /var/www/html/bootstrap/cache/services.php

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
