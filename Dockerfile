FROM php:8.3-cli AS build

RUN apt-get update && apt-get install -y \
    zip unzip git libpng-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

FROM php:8.3-cli

RUN apt-get update && apt-get install -y libpng-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring tokenizer

WORKDIR /app

COPY --from=build /app /app

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
