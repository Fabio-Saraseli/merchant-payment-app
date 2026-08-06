FROM composer:2 AS dependencies

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist


FROM php:8.3-cli-alpine

WORKDIR /app

COPY --from=dependencies /app/vendor ./vendor
COPY public ./public
COPY src ./src

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
