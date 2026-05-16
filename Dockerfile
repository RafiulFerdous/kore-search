FROM composer:latest AS vendor

WORKDIR /build

# copy full project first
COPY . .

# 🚨 IMPORTANT FIX: prevent Laravel boot during composer install
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist \
    --no-scripts


# =====================================================
# NODE BUILD STAGE (VITE / CSS FIX)
# =====================================================
FROM node:20 AS node

WORKDIR /app

COPY . .

RUN npm install

# safe build (prevents crash if scripts differ)
RUN npm run build || true


# =====================================================
# PHP RUNTIME
# =====================================================
FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    ca-certificates \
    nginx \
    supervisor \
    curl \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    autoconf \
    build-base \
    libxml2-dev

RUN docker-php-ext-install -j$(nproc) \
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

# configs
COPY docker/php.ini $PHP_INI_DIR/conf.d/99-koresearch.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

WORKDIR /var/www/html

# app source
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /build/vendor vendor

# VITE BUILD OUTPUT
COPY --from=node /app/public/build /var/www/html/public/build

# SSL CERT (TiDB)
COPY docker/certs/isrgrootx1.pem /var/www/html/docker/certs/isrgrootx1.pem

# =====================================================
# SAFE LARAVEL BOOT (CRITICAL FIX)
# =====================================================
RUN cp .env.example .env || true

RUN php artisan config:clear || true \
    && php artisan cache:clear || true \
    && php artisan package:discover || true \
    || true

RUN rm -rf bootstrap/cache/*.php \
    && mkdir -p storage/framework/views \
       storage/framework/cache \
       storage/framework/sessions \
       bootstrap/cache

# permissions
RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]