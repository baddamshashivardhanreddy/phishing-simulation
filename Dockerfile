# ==============================================================================
# PhishGuard - Production Docker Container (Nginx + PHP 8.2-FPM + Alpine)
# ==============================================================================
FROM php:8.2-fpm-alpine

# Install system dependencies & build tools
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    bash

# Configure and install required PHP extensions for CodeIgniter 4
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        intl \
        mbstring \
        zip \
        opcache \
        gd

# Configure PHP-FPM to run as user nginx
RUN sed -i 's/user = www-data/user = nginx/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = www-data/group = nginx/g' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/listen = 127.0.0.1:9000/listen = 127.0.0.1:9000/g' /usr/local/etc/php-fpm.d/www.conf

# Production PHP settings
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'upload_max_filesize=25M'; \
        echo 'post_max_size=25M'; \
        echo 'memory_limit=256M'; \
        echo 'date.timezone=UTC'; \
    } > /usr/local/etc/php/conf.d/production.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html

# Copy Nginx & Supervisor configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

# Ensure script is executable and set correct permissions
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /var/log/supervisor /var/run/nginx /var/log/nginx \
    && chown -R nginx:nginx /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/writable

EXPOSE 80 443

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

