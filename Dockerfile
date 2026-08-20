# Use PHP 8.2 FPM base image
FROM php:8.2-fpm

# Set the working directory
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Install Composer globally
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files to the container
COPY . .

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set correct permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 8000 for PHP FPM
EXPOSE 8000

# Start PHP-FPM as the container's entry point
CMD ["php-fpm"]
