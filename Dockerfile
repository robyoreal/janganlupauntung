FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    php8.2-fpm \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . .

EXPOSE 9000

CMD ["php-fpm"]
