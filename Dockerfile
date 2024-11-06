FROM php:8.2-apache AS build

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip

WORKDIR /var/www/html
EXPOSE 80

CMD composer install; apache2-foreground
