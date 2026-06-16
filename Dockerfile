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

# Finish setup - NO .env and NO key:generate here
RUN composer dump-autoload --optimize && \
    php artisan package:discover --ansi && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Use Render's PORT env var
EXPOSE 10000
CMD ["sh", "-c", "php artisan config:cache && php artisan serve --host=0.0.0.0 --port=$PORT"]
