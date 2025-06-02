FROM php:8.2-apache

# Установка необходимых системных библиотек и PHP-расширений
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install gd zip fileinfo pdo pdo_mysql

# Установка Composer (статически с официального сайта)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Копирование исходников
COPY . /var/www/html

# Права (если нужно)
RUN chown -R www-data:www-data /var/www/html