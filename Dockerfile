# Image PHP de base
FROM php:8.2-apache

# Activer le module Apache pour PHP
RUN docker-php-ext-install pdo pdo_mysql

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip

RUN apt-get update && apt-get install -y vim

# Désactiver le vhost par défaut
RUN a2dissite 000-default.conf

RUN a2enmod rewrite

# Créer un fichier de configuration pour le vhost
COPY ./my-vhost.conf /etc/apache2/sites-available/my-vhost.conf

# Activer le vhost
RUN a2ensite my-vhost.conf

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Installer les dépendances PHP
RUN composer install

# Installer les dépendances Node
# RUN npm install

# Exposer le port
EXPOSE 80

# Démarrer Apache en mode foreground
CMD ["apachectl", "-D", "FOREGROUND"]
