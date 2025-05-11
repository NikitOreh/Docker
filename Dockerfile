FROM php:8.2-apache

RUN apt-get update && \
    docker-php-ext-install pdo pdo_mysql mysqli

# Включаем mod_rewrite
RUN a2enmod rewrite

# Указываем ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Меняем DocumentRoot на /var/www/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/public|g' /etc/apache2/sites-available/000-default.conf

# Копируем всё внутрь /var/www
COPY . /var/www

# Даем права Apache на директорию
RUN chown -R www-data:www-data /var/www

# Разрешаем .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

EXPOSE 80
CMD ["apache2-foreground"]
    