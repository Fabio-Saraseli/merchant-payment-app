FROM composer:2 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist


FROM php:8.3-cli-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql

WORKDIR /app

COPY --from=dependencies /app/vendor ./vendor
COPY public ./public
COPY src ./src
COPY bin ./bin
COPY database ./database

EXPOSE 8080

CMD ["sh", "bin/start.sh"]
