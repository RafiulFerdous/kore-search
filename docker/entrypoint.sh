#!/bin/sh
set -e

# If PORT is set by Render/Railway, replace nginx listen port
NGINX_CONF="/etc/nginx/http.d/default.conf"
if [ -n "$PORT" ] && [ "$PORT" != "8080" ]; then
    if [ -f "$NGINX_CONF" ]; then
        sed -i "s/listen 8080;/listen $PORT;/" "$NGINX_CONF"
    fi
fi

# Ensure storage directories exist and are writable by www-data
for dir in \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/storage/debugbar \
    /var/www/html/storage/app/public; do
    mkdir -p "$dir"
    chown www-data:www-data "$dir"
    chmod 775 "$dir"
done

# Create storage symlink if missing
if [ ! -L /var/www/html/public/storage ]; then
    php /var/www/html/artisan storage:link --quiet 2>/dev/null || true
fi

# Cache boost for production
if [ "${APP_ENV:-production}" = "production" ] || [ "${APP_ENV:-production}" = "staging" ]; then
    php /var/www/html/artisan config:cache --quiet 2>/dev/null || true
    php /var/www/html/artisan route:cache --quiet 2>/dev/null || true
    php /var/www/html/artisan view:cache --quiet 2>/dev/null || true
fi

# Run migrations if RUN_MIGRATIONS is set
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    php /var/www/html/artisan migrate --force --quiet 2>/dev/null || true
fi

exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
