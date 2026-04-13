# Multi-stage build for Noble Nest Academy
# Stage 1: Build dependencies
FROM composer:2.8 AS build
WORKDIR /build
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist

# Stage 2: Runtime (PHP 8.3 with extensions)
FROM php:8.3-fpm-alpine

LABEL maintainer="Noble Nest Academy <team@noblenest.local>"
LABEL description="Noble Nest Academy LMS Platform"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    redis \
    git \
    curl \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    openssl-dev \
    && rm -rf /var/cache/apk/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pcntl \
    intl \
    zip \
    xml \
    mbstring \
    ctype \
    json \
    tokenizer \
    bcmath

# Install OPcache with JIT
RUN docker-php-ext-install opcache
COPY .docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install Redis extension
RUN apk add --no-cache --virtual .redis-deps \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .redis-deps

# Copy PHP configuration
COPY .docker/php.ini /usr/local/etc/php/conf.d/php.ini

# Create app user
RUN addgroup -g 1000 -S app && adduser -u 1000 -S app -G app

# Set working directory
WORKDIR /app

# Copy application from build stage
COPY --from=build /build/vendor vendor/

# Copy application source
COPY --chown=app:app . .

# Create necessary directories
RUN mkdir -p \
    storage/logs \
    storage/framework/views \
    storage/framework/cache \
    storage/app/public \
    bootstrap/cache \
    && chown -R app:app storage bootstrap \
    && chmod -R 775 storage bootstrap

# Install application dependencies
RUN composer install --no-dev --classmap-authoritative

# Generate optimized autoloader
RUN php artisan optimize

# Copy Nginx and Supervisor configuration
COPY .docker/nginx.conf /etc/nginx/nginx.conf
COPY .docker/app.conf /etc/nginx/conf.d/app.conf
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create Supervisor log directory
RUN mkdir -p /var/log/supervisor && touch /var/log/supervisor/supervisord.log

# Health check
HEALTHCHECK --interval=10s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose ports
EXPOSE 80 443

# Run Supervisor (manages PHP-FPM, Nginx, Horizon)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
