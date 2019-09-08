#
# PHP Dependencies
#
#
FROM composer:1.7 as vendor

WORKDIR /app
COPY  ./src ./src
COPY  app.php app.php
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# Application
#
FROM php:7.3

RUN apt-get update && apt-get upgrade -y && apt-get install docker.io -y && apt-get clean
COPY --from=vendor /app /app
WORKDIR /app

CMD ["php", "app.php", "swarmon"]

