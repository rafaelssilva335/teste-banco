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

COPY . .

COPY --from=composer /app/vendor/ /app/vendor/

RUN cp -n .env.example .env 2>/dev/null || echo ".env already exists" && \
    mkdir -p storage/logs && \
    mkdir -p storage/framework/views && \
    mkdir -p storage/framework/cache && \
    chmod -R 777 storage

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
