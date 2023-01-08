FROM php:8.1-fpm


# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
#    mysql-client \
    locales \
    git \
    unzip \
    zip \
    curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring  exif pcntl

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Remove the default NGINX configuration file
RUN rm -v /etc/nginx/nginx.conf

# Copy the custom NGINX configuration file
COPY nginx.conf /etc/nginx/

# Create the log directory and set the ownership to www-data
RUN mkdir -p /var/log/nginx && chown -R www-data:www-data /var/log/nginx

# Set the working directory
WORKDIR /var/www/html

# Copy the application code
COPY . /var/www/html

# Copy the environment variables
COPY .env.example /var/www/html/.env

# Set the ownership of the code to the www-data user
RUN chown -R www-data:www-data /var/www/html

# Run composer install
RUN composer install --no-dev

# Run database migrations and seeders
RUN php artisan migrate --seed

# Set the default command
CMD ["php-fpm"]
