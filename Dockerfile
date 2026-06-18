FROM php:8.2-fpm

# Ye line me libssl-dev add kar diya
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libssl-dev pkg-config \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Baaki sab same
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts
COPY . /var/www
RUN composer dump-autoload --optimize && \
    php artisan package:discover --ansi && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
EXPOSE 10000
CMD ["sh", "-c", "php artisan config:clear && php artisan serve --host=0.0.0.0 --port=$PORT"]
