FROM php:8.2
ENV PORT=8000
RUN apt-get update; apt-get install -y wget libzip-dev
RUN docker-php-ext-install zip pdo_mysql
RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/master/web/installer -O - -q | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /app
COPY . /app
RUN composer install
# RUN touch /app/database/database.sqlite
# RUN DB_CONNECTION=sqlite php artisan migrate
# # RUN DB_CONNECTION=sqlite vendor/bin/phpunit
RUN echo "#!/bin/sh\n" \
	"php artisan migrate\n" \
	"php artisan serve --host 0.0.0.0 --port \$PORT" > /app/start.sh
RUN chmod +x /app/start.sh
CMD ["/app/start.sh"]
# FROM composer:2.0 as build
# COPY . /app/
# RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# FROM php:8.1-apache-buster as production

# ENV APP_ENV=local
# ENV APP_DEBUG=false

# RUN docker-php-ext-configure opcache --enable-opcache && \
#     docker-php-ext-install pdo pdo_mysql
    
# COPY opache /usr/local/etc/php/conf.d/opcache.ini

# COPY --from=build /app /var/www/html
# COPY conf /etc/apache2/sites-available/000-default.conf

# COPY .env.example /var/www/html/.env

# RUN php artisan config:cache && \
#     php artisan route:cache && \
#     chmod 777 -R /var/www/html/storage/ && \
#     chown -R www-data:www-data /var/www/ && \
#     a2enmod rewrite

# FROM node:alpine
# WORKDIR /var/www/html/
# COPY ./ ./
# RUN npm install
# CMD ["npm", "run", "dev"]