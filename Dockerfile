FROM php:8.1-fpm-alpine

# Install dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    freetype \
    libpng \
    libjpeg-turbo \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    && docker-php-ext-install \
    pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && rm -f /var/cache/apk/*

# Configure Nginx
COPY config/nginx.conf /etc/nginx/nginx.conf

# Copy code of the Laravel application into the image
COPY . /var/www/html

# Grant permission for storage directory
RUN chown -R www-data:www-data /var/www/html/storage && \
    chmod -R 775 /var/www/html/storage && \
    ln -s /var/www/html/storage/app/public /var/www/html/public/storage

# Run Composer
RUN composer install --no-scripts --no-autoloader

# Generate autoloader and run artisan optimize
RUN composer dump-autoload --optimize && \
    php artisan optimize

# Run Supervisor
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
