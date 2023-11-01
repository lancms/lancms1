FROM docker.io/library/php:7.4.24-fpm

RUN apt-get update
RUN apt-get install -y libfreetype6-dev libzip-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd
RUN docker-php-ext-install zip mysqli gettext
