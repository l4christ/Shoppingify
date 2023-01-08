FROM php:8.1-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    curl \
    mariadb-client \
    nginx

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

# Install xdebug
RUN pecl install xdebug

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
