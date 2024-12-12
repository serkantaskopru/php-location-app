FROM php:8.2-fpm

# Install necessary packages
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libxpm-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install all PHP dependencies
#RUN composer install

CMD ["php-fpm"]

EXPOSE 9000