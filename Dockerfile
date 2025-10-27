FROM composer:2.5 AS composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev

FROM php:8.1-cli

WORKDIR /app

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    libsqlite3-dev && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install pdo pdo_sqlite

COPY . .

COPY --from=composer /app/vendor/ /app/vendor/

RUN cp -n .env.example .env 2>/dev/null || echo ".env already exists" && \
    mkdir -p storage/logs && \
    mkdir -p storage/framework/views && \
    mkdir -p storage/framework/cache && \
    mkdir -p storage/app && \
    touch storage/app/database.sqlite && \
    chmod -R 777 storage && \
    chmod 666 storage/app/database.sqlite

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
