FROM php:8.1-cli

WORKDIR /app

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    curl \
    git \
    unzip \
    libzip-dev && \
    docker-php-ext-install zip && \
    rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader

COPY . .

RUN composer dump-autoload --no-scripts --no-dev --optimize

RUN cp -n .env.example .env 2>/dev/null || echo ".env already exists" && \
    chmod -R 777 storage

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
