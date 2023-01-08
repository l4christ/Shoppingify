FROM composer:2.0 as build
COPY . /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

FROM php:8.1-apache-buster as production

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql
COPY opache /usr/local/etc/php/conf.d/opcache.ini

#Update APT repository & Install gnupg
RUN apt-get update && apt-get install -y gnupg

#Add an account for running MySQL
RUN groupadd -r mysql && useradd -r -g mysql mysql

#Add the MySQL APT repository & Install necessary programs
RUN apt-get update \
    && echo "deb http://repo.mysql.com/apt/ubuntu/ bionic mysql-5.7" > \
      /etc/apt/sources.list.d/mysql.list \
    && apt-key adv --keyserver pgp.mit.edu --recv-keys 5072E1F5 \
    && apt-get update \
    && apt-get install -y --no-install-recommends perl pwgen

#Install MySQL
RUN apt-get update \
    && echo mysql-community-server mysql-community-server/root-pass password ''; \
    && apt-get install -y mysql-server \
    && mkdir -p /var/lib/mysql /var/run/mysqld \
    && chown -R mysql:mysql /var/lib/mysql /var/run/mysqld \
    && chmod 777 /var/run/mysqld

#Solve the problem that ubuntu cannot log in from another container
RUN sed -i 's/bind-address/#bind-address/' /etc/mysql/mysql.conf.d/mysqld.cnf

#Mount Data Volume
VOLUME /var/lib/mysql

#Expose the default port
EXPOSE 3306

#Start MySQL
CMD ["mysqld","--user","mysql"]

RUN mysql -u root -e "CREATE DATABASE laravel"
RUN mysql -u root -e "GRANT ALL PRIVILEGES ON laravel.* TO 'laravel'@'%' IDENTIFIED BY 'secret'"

COPY --from=build /app /var/www/html
COPY conf /etc/apache2/sites-available/000-default.conf

COPY .env.example /var/www/html/.env

RUN php artisan config:cache && \
    php artisan route:cache && \
    chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite