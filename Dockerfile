# Use official PHP image as base
FROM php:8.2-fpm

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Node.js for Vite asset building
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Create necessary storage directories
RUN mkdir -p storage/logs \
    && mkdir -p storage/app \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && chmod -R 775 storage bootstrap/cache

# Install PHP dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Install Node dependencies
RUN npm install

# Set environment file if not exists
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Generate application key (will be done at runtime if needed)
# Skip Vite asset build in Docker - it will happen on first run
# RUN npm run build

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
