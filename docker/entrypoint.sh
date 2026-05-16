#!/bin/sh
set -e

# If PORT is set by Render/Railway, replace nginx listen port
if [ -n "$PORT" ] && [ "$PORT" != "8080" ]; then
    sed -i "s/listen 8080;/listen $PORT;/" /etc/nginx/sites-enabled/default
fi

# Ensure storage directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/debugbar
mkdir -p /var/www/html/storage/app/public

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
