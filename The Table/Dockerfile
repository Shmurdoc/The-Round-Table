# Multi-stage Dockerfile for Laravel (PHP-FPM) + built frontend assets

# Node stage: build frontend assets with Vite
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json* ./
COPY vite.config.js .
COPY resources ./resources
RUN npm ci --silent --no-audit && npm run build || true

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Use composer cache for faster builds
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress

FROM php:8.3-fpm
LABEL maintainer="The Round Table <dev@theroundtable.example>"

# Install system deps and PHP extensions
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
    libzip-dev zip unzip git libpng-dev libonig-dev libxml2-dev zlib1g-dev ca-certificates procps curl \
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

# Copy source and set permissions
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=node_builder /app/public /var/www/html/public

RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    mkdir -p /var/www/html/storage && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Create a dedicated non-root user for running runtime processes
RUN useradd -m -u 1000 appuser || true
USER appuser

EXPOSE 9000
CMD ["php-fpm"]
