FROM composer:latest AS vendor

WORKDIR /build

# copy full project first (important for autoload + helpers)
COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist


# =====================================================
# NODE BUILD STAGE (🔥 FIX FOR CSS/JS - LARAVEL 13)
# =====================================================
FROM node:20 AS node

WORKDIR /app

# 🔥 COPY EVERYTHING FIRST (fixes missing package.json issue)
COPY . .

# install + build
RUN npm install
RUN npm run build


FROM php:8.3-fpm-alpine

# =====================================================
# System dependencies
# =====================================================
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

# ==========================
# PHP extensions
# ==========================
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

# ==========================
# Config files
# ==========================
COPY docker/php.ini $PHP_INI_DIR/conf.d/99-koresearch.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

WORKDIR /var/www/html

# ==========================
# App source
# ==========================
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /build/vendor vendor

# 🔥 COPY VITE BUILD OUTPUT (IMPORTANT FIX FOR CSS/JS)
COPY --from=node /app/public/build /var/www/html/public/build

# ==========================
# Certificates (TiDB SSL fix)
# ==========================
COPY docker/certs/isrgrootx1.pem /var/www/html/docker/certs/isrgrootx1.pem

# ==========================
# Laravel safety fixes
# ==========================
RUN php artisan optimize:clear || true \
    && rm -rf bootstrap/cache/*.php \
    && mkdir -p storage/framework/views \
       storage/framework/cache \
       storage/framework/sessions \
       bootstrap/cache

# ==========================
# Permissions
# ==========================
RUN chmod +x /entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]