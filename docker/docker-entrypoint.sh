#!/bin/bash
set -e

echo "Starting Laravel application..."

# Wait for database to be ready if using MySQL/PostgreSQL
if [ "$DB_CONNECTION" = "mysql" ] || [ "$DB_CONNECTION" = "pgsql" ]; then
    echo "Waiting for database connection..."
    
    max_attempts=30
    attempt=0
    
    until php artisan db:show > /dev/null 2>&1 || [ $attempt -eq $max_attempts ]; do
        attempt=$((attempt + 1))
        echo "Database not ready yet (attempt $attempt/$max_attempts)..."
        sleep 2
    done
    
    if [ $attempt -eq $max_attempts ]; then
        echo "Failed to connect to database after $max_attempts attempts"
        exit 1
    fi
    
    echo "Database connection established!"
fi

# Run Laravel optimizations
echo "Running Laravel optimizations..."

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction

# Clear and optimize
echo "Optimizing application..."
php artisan optimize

# Link storage (if not already linked)
if [ ! -L "/var/www/html/public/storage" ]; then
    php artisan storage:link
fi

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

echo "Laravel application started successfully!"

# Execute the main command
exec "$@"
