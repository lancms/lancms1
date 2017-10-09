FROM php:7.0-fpm

RUN apt-get update
RUN apt-get install -y libmcrypt-dev \
    && docker-php-ext-install mcrypt
RUN apt-get install -y libfreetype6-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd
RUN docker-php-ext-install zip mysqli gettext
