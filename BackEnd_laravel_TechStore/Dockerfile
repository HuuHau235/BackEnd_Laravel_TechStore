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

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath gd

# ✅ Install Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# ✅ Install Node.js + npm (v16 LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs

# Copy application files
COPY . /app

# ✅ Install Laravel & frontend (if needed)
RUN composer install --ignore-platform-reqs

# 🔹 Only run if you use Vite or frontend in Laravel
RUN npm install && npm run build

# Permissions (optional)
RUN chown -R www-data:www-data /app

EXPOSE 9000

CMD ["php-fpm"]
