FROM php:8.3-fpm

# Installer dépendances système utiles pour Composer et Symfony
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Installer les dépendances Symfony
RUN composer install --no-interaction --optimize-autoloader 