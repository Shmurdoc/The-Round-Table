# Multi-stage Dockerfile for Laravel (PHP-FPM) + built frontend assets

# Node stage: build frontend assets with Vite
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package.json package-lock.json* ./
COPY vite.config.js .
COPY resources ./resources
RUN npm ci --silent --no-audit || true
RUN npm run build || true

# Composer stage: install PHP dependencies
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-progress

# Final stage: PHP-FPM runtime
FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libpng-dev libonig-dev libxml2-dev zlib1g-dev && \
    docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl gd && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy application source (Dockerfile is inside project directory so copy relative)
COPY . /var/www/html

# Copy vendor and built frontend assets from previous stages
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=node_builder /app/public /var/www/html/public

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["php-fpm"]
