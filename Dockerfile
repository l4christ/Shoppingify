FROM composer:2.0 as build
COPY . /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

FROM php:8.1-apache-buster as production

ENV APP_ENV=production
ENV APP_DEBUG=true

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



RUN apt-get update && apt-get install nano

# ENV APP_ENV=local
# ENV APP_DEBUG=false

# ENV DB_CONNECTION=pgsql
# ENV DB_HOST=somber-hulk-5426.7tc.cockroachlabs.cloud
# ENV DB_PORT=26257
# ENV DB_DATABASE=defaultdb
# ENV DB_USERNAME=ciara
# ENV DB_PASSWORD='Q_6qr4vLwiNsNEQwv237hA'

# RUN curl https://binaries.cockroachdb.com/ccloud/ccloud_linux-amd64_0.3.6.tar.gz | tar -xz && cp -i ccloud /usr/local/bin/
