# =============================================================================
# Stage 1: Build dependencies (composer, bun, vite)
# =============================================================================
FROM php:8.3-fpm-alpine AS builder

# Install system dependencies for building
RUN apk add --no-cache \
    git \
    curl \
    linux-headers \
    $PHPIZE_DEPS \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite-dev \
    postgresql-dev \
    mysql-client \
    nodejs \
    npm \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

# Install PHP extensions needed for build
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    bcmath \
    opcache \
    gd

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Install Bun
RUN apk add --no-cache bash \
    && curl -fsSL https://bun.sh/install | bash
ENV PATH="/root/.bun/bin:${PATH}"

# Set working directory
WORKDIR /var/www/html

# Copy dependency files first (for cache)
COPY composer.json composer.lock ./
COPY package.json bun.lock ./

# Install PHP dependencies (production only)
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader \
    && composer clear-cache

# Copy application source needed for build (resources, public, vite.config)
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY tailwind.config.js ./

# Install Node dependencies and build assets
RUN bun install --frozen-lockfile && bun run build

# Copy remaining application code
COPY . .

# Run post-install scripts
RUN php artisan package:discover --ansi

# =============================================================================
# Stage 2: Runtime (nginx + php-fpm)
# =============================================================================
FROM php:8.3-fpm-alpine AS runtime

# Install runtime dependencies only
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    oniguruma \
    libxml2 \
    sqlite-libs \
    postgresql-libs \
    bash

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    bcmath \
    opcache \
    gd

# Configure OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Create nginx user and group
RUN addgroup -g 1000 -S www-data \
    && adduser -u 1000 -D -S -G www-data www-data

# Create necessary directories
RUN mkdir -p /var/www/html \
    && mkdir -p /run/nginx \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/www/html /run/nginx /var/lib/nginx /var/log/supervisor

WORKDIR /var/www/html

# Copy application from builder
COPY --from=builder --chown=www-data:www-data /var/www/html .

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Make entrypoint executable
RUN chmod +x /entrypoint.sh

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/up || exit 1

# Entrypoint
ENTRYPOINT ["/entrypoint.sh"]

# Default command (supervisord manages processes)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
