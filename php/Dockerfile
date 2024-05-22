FROM php:8.3-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/project

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash

## Update PHP memory_limit in both development and production ini files
#RUN sed -i 's/memory_limit = .*/memory_limit = 512M/' /usr/local/etc/php/php.ini-development \
#    && sed -i 's/memory_limit = .*/memory_limit = 512M/' /usr/local/etc/php/php.ini-production
