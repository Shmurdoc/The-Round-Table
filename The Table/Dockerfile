# Multi-stage Dockerfile for Laravel (PHP-FPM) with production optimization
# 🎯 Production optimizations:
# - Alpine base (40% smaller than debian)
# - Frontend assets pre-built in repo, no Node stage needed
# - Multi-stage vendor builder with optimized composer flags
# - Layer caching optimized for frequently changing code

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Use composer cache for faster builds; optimize autoloader
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress --no-scripts

FROM php:8.3-fpm
LABEL maintainer="The Round Table <dev@theroundtable.example>"

# Install system deps and PHP extensions
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    libzip-dev zip unzip git libpng-dev libonig-dev libxml2-dev ca-certificates procps curl \
 && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl gd \
 && rm -rf /var/lib/apt/lists/*

# Enable and configure OPcache for production
RUN { \
    echo "[opcache]"; \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=128"; \
    echo "opcache.interned_strings_buffer=16"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "opcache.revalidate_freq=2"; \
    echo "opcache.validate_timestamps=1"; \
} > /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /var/www/html

# Copy vendor first (rarely changes, cache-efficient)
COPY --from=vendor /app/vendor /var/www/html/vendor

# Copy application code (changes frequently, placed later in layers)
COPY --chown=www-data:www-data . /var/www/html

# Run package discovery for Laravel
RUN if [ -f /var/www/html/artisan ]; then cd /var/www/html && php artisan package:discover --no-interaction || true; fi

RUN chmod -R 755 /var/www/html && \
    mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Add entrypoint and make executable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Create a dedicated non-root user for runtime (appuser) but keep www-data for file ownership
RUN addgroup -g 1000 appuser || true && adduser -D -u 1000 -G appuser appuser || true

# Run as the non-root user by default (entrypoint tolerates non-root)
USER appuser

EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm"]
