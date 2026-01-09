FROM php:8.2-fpm

WORKDIR /var/www/html

# Install system dependencies + Node.js
RUN apt-get update && apt-get install -y \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql mbstring exif pcntl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy and build
COPY . .
RUN mkdir -p storage/logs storage/app storage/framework/{cache,sessions,views}
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
