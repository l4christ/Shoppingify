FROM composer:2.0 as build
COPY . /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

FROM php:8.1-apache-buster as production

ENV APP_ENV=local
ENV APP_DEBUG=false

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql
COPY opache /usr/local/etc/php/conf.d/opcache.ini

COPY --from=build /app /var/www/html
COPY conf /etc/apache2/sites-available/000-default.conf

COPY .env.example /var/www/html/.env

RUN php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite

FROM node:alpine
WORKDIR /var/www/html/
COPY ./ ./
RUN npm install
CMD ["npm", "run", "dev"]

# WORKDIR /var/www/html/
# COPY ["package.json", "package-lock.json*", "./var/www/html/"]
# RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
#     && apt-get install -y nodejs \
#     && npm install --global npm@8 \
#     && node --version \
#     && npm -v



