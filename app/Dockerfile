FROM php:8.4-apache
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zlib1g-dev \
    libpng-dev \
    unzip \
    && docker-php-ext-install zip gd fileinfo pdo pdo_mysql
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/tmp
CMD ["apache2-foreground"]