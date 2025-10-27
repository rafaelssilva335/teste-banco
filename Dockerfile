FROM php:8.1-cli

WORKDIR /app

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    zip \
    unzip \
    git && \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader

COPY . .

RUN composer dump-autoload --no-dev --optimize

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
