FROM node:20

WORKDIR /app

# Install PHP and necessary extensions
RUN apt-get update && apt-get install -y \
    php8.2 \
    php8.2-cli \
    php8.2-fpm \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-curl \
    php8.2-zip \
    php8.2-pdo \
    php8.2-pdo-mysql \
    php8.2-json \
    && rm -rf /var/lib/apt/lists/*

# Create storage directories
RUN mkdir -p storage/logs storage/app storage/framework/cache storage/framework/sessions storage/framework/views \
    && chmod -R 775 storage bootstrap/cache

# Copy package files
COPY package*.json ./
COPY composer.json composer.lock ./

# Install Node dependencies
RUN npm ci

# Build Vite assets
RUN npm run build

# Install PHP dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# Copy application files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 8000 9000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
