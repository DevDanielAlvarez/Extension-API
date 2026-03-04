# Define image to use (PHP 8.4) with FPM
FROM php:8.4-fpm

# install OS(Debian) dependeces
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    libzip-dev \
    libicu-dev

# Install php extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip intl

# Import composer from image to our container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Defines where the code will run
WORKDIR /var/www

# Create user to use the container
RUN useradd -G www-data,root -u 1000 -d /home/daniel daniel
RUN mkdir -p /home/daniel/.composer && \
    chown -R daniel:daniel /home/daniel

# Change to Daniel's user
USER daniel
