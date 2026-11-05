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

# Function to safely update key in .env (safe for Docker bind-mounts)
set_env_val() {
    key="$1"
    val="$2"
    if [ -f /var/www/html/.env ]; then
        if grep -q "^[# ]*${key}" /var/www/html/.env; then
            sed "s|^[# ]*${key}[ ]*=.*|${key} = ${val}|" /var/www/html/.env > /tmp/.env.tmp && cat /tmp/.env.tmp > /var/www/html/.env && rm -f /tmp/.env.tmp
        else
            echo "${key} = ${val}" >> /var/www/html/.env
        fi
    fi
}

# Substitute DB environment variables into .env if passed from container
if [ -n "$DB_HOST" ]; then
    set_env_val "database.default.hostname" "$DB_HOST"
fi
if [ -n "$DB_DATABASE" ]; then
    set_env_val "database.default.database" "$DB_DATABASE"
fi
if [ -n "$DB_USER" ]; then
    set_env_val "database.default.username" "$DB_USER"
fi
if [ -n "$DB_PASS" ]; then
    set_env_val "database.default.password" "$DB_PASS"
fi
if [ -n "$DB_PORT" ]; then
    set_env_val "database.default.port" "$DB_PORT"
fi
if [ -n "$APP_BASE_URL" ]; then
    set_env_val "app.baseURL" "'$APP_BASE_URL'"
fi
if [ -n "$ADMIN_SERVER_URL" ]; then
    set_env_val "ADMIN_SERVER_URL" "'$ADMIN_SERVER_URL'"
fi
if [ -n "$PHISH_SERVER_URL" ]; then
    set_env_val "PHISH_SERVER_URL" "'$PHISH_SERVER_URL'"
fi

exec "$@"

