FROM php:8.2-fpm

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libcurl4-openssl-dev \
    libssl-dev \
    gnupg \
    ca-certificates

# ✅ Install Node.js & npm (v16 LTS) — Cách đúng
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - && \
    apt-get install -y nodejs

# ✅ Kiểm tra lại npm đã có
RUN node -v && npm -v

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# ✅ Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Copy source code
COPY . /app

# ✅ Laravel install dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# ✅ Build frontend (Vite / React)
RUN npm install && npm run build

# ✅ Set permissions (Laravel storage/logs/cache)
RUN chown -R www-data:www-data /app \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# ✅ Laravel setup
RUN php artisan key:generate && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan migrate --force

EXPOSE 9000

CMD ["php-fpm"]
