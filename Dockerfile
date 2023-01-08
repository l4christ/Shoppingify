
# FROM node:12.18.1
# ENV NODE_ENV=production
# COPY ["package.json", "package-lock.json*", "./app/"]
# RUN npm install 
# RUN npm run dev

# Install Node.js

FROM composer:2.0 as build
# COPY . /app/
COPY . /var/www/html/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

FROM php:8.1-apache-buster as production
WORKDIR /var/www/html/
ENV APP_ENV=local
ENV APP_DEBUG=true

RUN pwd

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql
COPY opache /usr/local/etc/php/conf.d/opcache.ini

#Update APT repository & Install gnupg

# RUN mysql -u root -e "CREATE DATABASE laravel"
# RUN mysql -u root -e "GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%' IDENTIFIED BY 'secret'"

COPY . /var/www/html/
ENV NODE_ENV=production
COPY ["package.json", "package-lock.json*", "./var/www/html/"]
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install --global npm@8 \
    && npm run build  \
    && node --version \
    && npm -v

# COPY --from=build /app /var/www/html
COPY conf /etc/apache2/sites-available/000-default.conf

COPY .env.example /var/www/html/.env

RUN php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite