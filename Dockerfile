FROM php:8.2-apache AS build

WORKDIR /var/www/html
EXPOSE 80

CMD apache2-foreground
