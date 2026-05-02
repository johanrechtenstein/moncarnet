# 1. Utilisation de l'image PHP officielle avec Apache
FROM php:8.4-apache

# 2. Installation des dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql

# 3. Activation du module rewrite d'Apache
RUN a2enmod rewrite

# 4. Configuration du DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/conf-available/docker-php.conf!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 5. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Répertoire de travail
WORKDIR /var/www/html

# 7. Copie composer en premier pour le cache
COPY composer.json composer.lock /var/www/html/

# 8. Installation des dépendances
RUN composer install --no-dev --no-scripts --optimize-autoloader

# 9. Copie du projet
COPY . /var/www/html

# 10. Autoload et scripts
RUN composer dump-autoload --optimize --no-dev && \
    php artisan package:discover --ansi

# 11. Permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Port
EXPOSE 80

# 13. Lancement : cache config au runtime PUIS Apache
CMD php artisan config:cache || true && \
    php artisan route:cache || true && \
    php artisan migrate --force || true && \
    apache2-foreground