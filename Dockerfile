# Build stage
FROM composer:2.5 as build

WORKDIR /app

COPY . .

RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts

FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

ADD https://github.com/vishnubob/wait-for-it/raw/master/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

COPY --from=build /app /var/www

WORKDIR /var/www

RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

COPY --chown=www:www . /var/www

USER www

EXPOSE 9000
CMD ["php-fpm"]