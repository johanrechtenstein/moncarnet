# 1. Utilisation de l'image PHP officielle avec Apache
FROM php:8.4-apache

# 2. Installation des dépendances système nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# 3. Activation du module rewrite d'Apache (Crucial pour les routes Laravel)
RUN a2enmod rewrite

# 4. Configuration du DocumentRoot d'Apache vers le dossier /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/conf-available/docker-php.conf!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 5. Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Définition du répertoire de travail
WORKDIR /var/www/html

# 7. ÉTAPE PRO : Copie des fichiers composer en premier pour le cache
COPY composer.json composer.lock /var/www/html/

# 8. Installation des dépendances SANS les scripts (évite l'erreur "artisan not found")
RUN composer install --no-dev --no-scripts --optimize-autoloader

# 9. Copie de TOUT le reste du projet dans le conteneur
COPY . /var/www/html

# 10. Maintenant que les fichiers sont là, on génère l'autoload et les scripts
RUN composer dump-autoload --optimize --no-dev && \
    php artisan package:discover --ansi

# 11. Réglage des permissions pour Laravel (Storage et Cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Exposition du port 80
EXPOSE 80

# 13. Lancement d'Apache en premier plan
CMD php artisan migrate --force && apache2-foreground