FROM php:8.2-fpm

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    libpq-dev libcurl4-openssl-dev libssl-dev \
    gnupg ca-certificates

RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# ✅ Install Node.js + npm (v16)
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -s -- && \
    apt-get install -y nodejs

COPY . /app

RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

RUN npm install && npm run build

RUN chown -R www-data:www-data /app && chmod -R 775 /app/storage /app/bootstrap/cache

RUN php artisan key:generate && php artisan config:cache && php artisan route:cache && php artisan migrate --force

EXPOSE 9000
CMD ["php-fpm"]
