FROM php:8.4-cli-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    curl \
    unzip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
