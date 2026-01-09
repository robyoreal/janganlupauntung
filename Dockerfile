FROM php:8.2-fpm

# Install dependencies
RUN apk add --no-cache \
    curl \
    git \
    python3 \
    make \
    g++ \
    bash

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Install npm dependencies
RUN npm ci

# Copy application files
COPY . .

# Expose port
EXPOSE 3000

# Start application
CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
