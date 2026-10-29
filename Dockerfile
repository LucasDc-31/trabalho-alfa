FROM php:8.3-apache

# Extensão para conectar no MySQL via PDO
RUN docker-php-ext-install pdo_mysql \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
