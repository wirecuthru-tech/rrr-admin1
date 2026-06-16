FROM php:8.2-fpm

# Install system deps + PHP extensions + MongoDB
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install deps without running scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy all code
COPY . /var/www

# Finish setup
RUN cp .env.example .env \
    && php artisan key:generate \
    && composer dump-autoload --optimize \
    && php artisan package:discover --ansi

# Set permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000
CMD php artisan serve --host=0.0.0.0 --port=8000
