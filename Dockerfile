FROM php:8.3-fpm-alpine AS builder

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

# gd needs freetype/jpeg support for QR/barcode image work
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    bcmath \
    opcache \
    gd

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# bun.sh installer script requires bash (absent in alpine)
RUN apk add --no-cache bash \
    && curl -fsSL https://bun.sh/install | bash
ENV PATH="/root/.bun/bin:${PATH}"

WORKDIR /var/www/html

# Lockfiles first so dependency layers stay cached across code changes
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

# Vite needs these sources present before bun run build
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./
COPY tailwind.config.js ./

RUN bun install --frozen-lockfile && bun run build

COPY . .

RUN php artisan package:discover --ansi

FROM php:8.3-fpm-alpine AS runtime

# Runtime shared libs matching the extensions compiled in the builder;
# the compiled .so files are copied over below, nothing is built here.
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
    bash \
    curl

COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/docker-php-ext-*.ini /usr/local/etc/php/conf.d/

# Production-tuned OPcache defaults (validate_timestamps off: immutable image)
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# www-data already ships with php:*-alpine (uid/gid 82); only dirs are needed
RUN mkdir -p /var/www/html \
    && mkdir -p /run/nginx \
    && mkdir -p /var/log/supervisor \
    && mkdir -p /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/www/html /run/nginx /var/lib/nginx /var/log/supervisor

WORKDIR /var/www/html

COPY --from=builder --chown=www-data:www-data /var/www/html .
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8000/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]

# supervisord runs nginx, php-fpm, the queue worker, and the scheduler
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
