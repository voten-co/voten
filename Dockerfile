FROM node:10-buster AS assets

WORKDIR /build

COPY package.json package-lock.json webpack.mix.js ./
COPY resources ./resources
COPY public ./public

RUN npm ci --no-audit --no-fund \
    && npm run production

FROM php:7.4-apache-bullseye

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        ffmpeg \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j1 bcmath exif gd mbstring pcntl pdo_mysql zip \
    && a2enmod expires headers rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache-global.conf /etc/apache2/conf-enabled/servername.conf

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-autoloader \
        --no-interaction \
        --no-progress \
        --no-scripts \
        --prefer-dist

COPY . .
COPY --from=assets /build/public ./public

RUN mkdir -p public/js \
    && mv public/public/js/* public/js/ \
    && rm -rf public/public \
    && sed -i 's#/public/js#/js#g' public/mix-manifest.json \
    && composer dump-autoload --no-dev --optimize \
    && mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage \
    && chown -R www-data:www-data storage bootstrap/cache

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -fsS http://127.0.0.1/ >/dev/null || exit 1
