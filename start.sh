#!/bin/bash

# Start PHP-FPM and Nginx together
echo "Starting PHP-FPM..."
php-fpm &

echo "Starting Nginx..."
nginx -g "daemon off;" &

# Wait for all background processes
wait
