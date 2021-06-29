FROM php:8.0-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libzip-dev \
    nano \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && docker-php-ext-install \
           bcmath \
           gd \
           zip \
           opcache

COPY ./conf.d/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./conf.d/apcu.ini /usr/local/etc/php/conf.d/apcu.ini

WORKDIR /var/www

RUN chown www-data:www-data /var/www
RUN chmod -R 777 /var/log

# only for dev environment
RUN usermod -u 1000 www-data