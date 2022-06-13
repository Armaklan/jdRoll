FROM php:7-fpm
WORKDIR /code

RUN apt-get update && apt-get install -y \
    git \
    unzip

RUN set -ex && \
    docker-php-ext-install mysqli pdo_mysql

RUN set -ex && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    rm -rf /tmp/*

RUN curl -s http://getcomposer.org/installer | php -- --install-dir /usr/local/bin --filename composer 
RUN composer --version

EXPOSE 80