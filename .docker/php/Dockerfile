FROM php:8.1-fpm-buster

RUN apt update \
    && apt install -y \
    gnupg \
    g++ \
    procps \
    openssl \
    git \
    curl \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxslt1-dev \
    acl \
    && docker-php-ext-install  \
    intl  \
    opcache  \
    pdo  \
    pdo_mysql \
    zip \
    mbstring \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && echo 'alias sf="php bin/console"' >> ~/.bashrc
# /var/www/symfony_docker pdo pdo_mysql zip xsl gd intl opcache exif mbstring
WORKDIR /var/www/app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
