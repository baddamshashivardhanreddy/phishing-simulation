#!/bin/sh
set -e

# Ensure writable directories exist
mkdir -p /var/www/html/writable/cache \
         /var/www/html/writable/logs \
         /var/www/html/writable/session \
         /var/www/html/writable/uploads \
         /var/www/html/writable/debugbar

# Set ownership and permissions for Nginx and PHP-FPM
chown -R nginx:nginx /var/www/html/writable
chmod -R 775 /var/www/html/writable

# Ensure .env exists if not mounted
if [ ! -f /var/www/html/.env ] && [ -f /var/www/html/.env.example ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Substitute DB environment variables into .env if passed from container
if [ -n "$DB_HOST" ]; then
    sed -i "s|^database.default.hostname = .*|database.default.hostname = $DB_HOST|" /var/www/html/.env
fi
if [ -n "$DB_DATABASE" ]; then
    sed -i "s|^database.default.database = .*|database.default.database = $DB_DATABASE|" /var/www/html/.env
fi
if [ -n "$DB_USER" ]; then
    sed -i "s|^database.default.username = .*|database.default.username = $DB_USER|" /var/www/html/.env
fi
if [ -n "$DB_PASS" ]; then
    sed -i "s|^database.default.password = .*|database.default.password = $DB_PASS|" /var/www/html/.env
fi
if [ -n "$APP_BASE_URL" ]; then
    sed -i "s|^app.baseURL = .*|app.baseURL = '$APP_BASE_URL'|" /var/www/html/.env
fi
if [ -n "$ADMIN_SERVER_URL" ]; then
    sed -i "s|^ADMIN_SERVER_URL = .*|ADMIN_SERVER_URL = '$ADMIN_SERVER_URL'|" /var/www/html/.env
fi
if [ -n "$PHISH_SERVER_URL" ]; then
    sed -i "s|^PHISH_SERVER_URL = .*|PHISH_SERVER_URL = '$PHISH_SERVER_URL'|" /var/www/html/.env
fi

exec "$@"

