FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql zip

RUN a2enmod rewrite headers

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf

RUN echo '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' >> /etc/apache2/apache2.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json composer.lock /var/www/html/
RUN cd /var/www/html && composer install --no-dev --optimize-autoloader

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

RUN printf '#!/bin/bash\n\
set -e\n\
cd /var/www/html\n\
php artisan config:clear\n\
php artisan cache:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan storage:link\n\
php artisan migrate --force\n\
apache2-foreground\n' > /usr/local/bin/start-app.sh

RUN chmod +x /usr/local/bin/start-app.sh

EXPOSE 80

CMD ["/usr/local/bin/start-app.sh"]