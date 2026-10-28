# ==============================================================================
# Tahap 1: Frontend Asset Builder (Node.js 22 + Vite + Tailwind CSS v4)
# ==============================================================================
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package*.json ./
RUN npm ci || npm install

COPY . .
RUN npm run build

# ==============================================================================
# Tahap 2: Composer Dependency Builder
# ==============================================================================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer*.json ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-autoloader \
    --no-scripts

COPY . .
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ==============================================================================
# Tahap 3: Production Runner (PHP 8.3-FPM + Nginx + Supervisor)
# ==============================================================================
FROM php:8.3-fpm-alpine AS runner

WORKDIR /var/www/html

# Install dependensi sistem dan runtime library
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpq \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    icu-libs \
    bash \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        postgresql-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        bcmath \
        gd \
        intl \
        opcache \
        pcntl \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/*

# Salin konfigurasi PHP, Nginx, dan Supervisor
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Salin source code aplikasi
COPY . /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=frontend /app/public/build /var/www/html/public/build

# Salin dan siapkan script entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN tr -d '\r' < /usr/local/bin/docker-entrypoint.sh > /usr/local/bin/docker-entrypoint.sh.tmp \
    && mv /usr/local/bin/docker-entrypoint.sh.tmp /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# Set hak akses direktori storage dan cache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Port default Coolify sesuai permintaan: 4176
ENV PORT=4176
EXPOSE 4176

ENTRYPOINT ["docker-entrypoint.sh"]
