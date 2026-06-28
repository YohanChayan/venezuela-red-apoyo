FROM php:8.5-fpm

# Easiest, most reliable way to add PHP extensions.
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Extensions needed by Laravel + this app (MySQL & SQLite drivers, image, intl, etc.).
RUN install-php-extensions \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    bcmath \
    gd \
    zip \
    exif \
    pcntl \
    intl

# Composer (copied from the official image).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

# Node.js 22 — used by the `node` service to run Vite. The Wayfinder Vite plugin
# shells out to `php artisan wayfinder:generate`, so the Vite container needs
# both Node AND PHP; sharing this image gives it both.
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates curl gnupg git unzip \
    && curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
