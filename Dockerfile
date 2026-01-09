FROM php:8.2-fpm

# Install nginx and other dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    curl \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

# Copy application code
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html

# Create startup script
RUN mkdir -p /usr/local/bin && \
    echo '#!/bin/bash\n\
set -e\n\
\n\
# Start PHP-FPM in background\n\
php-fpm &\n\
PHP_PID=$!\n\
\n\
# Start nginx in foreground\n\
nginx -g "daemon off;"\n\
\n\
# Wait for both processes\n\
wait $PHP_PID\n\
' > /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

# Expose port 80 for HTTP
EXPOSE 80

# Run startup script
CMD ["/usr/local/bin/start.sh"]
