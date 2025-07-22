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
    libssl-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# ✅ Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Copy existing application files
COPY . /app

# ✅ Install Laravel dependencies and run setup
RUN composer install && php artisan config:cache && php artisan migrate --force

# Set permissions if needed
RUN chown -R www-data:www-data /app

# Expose port 9000 (if needed)
EXPOSE 9000

# Start the application
CMD ["php-fpm"]
