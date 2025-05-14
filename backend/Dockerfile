# Dockerfile
FROM php:8.2-fpm
# Set working directory
WORKDIR /var/www/html
# Install system dependencies
RUN apt-get update && apt-get install -y \
git \
unzip \
libpq-dev \
libzip-dev \
libicu-dev \
libonig-dev \
libxml2-dev \
default-mysql-client \
&& docker-php-ext-install pdo pdo_pgsql pdo_mysql intl zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# Copy application files
COPY . .
# Set file permissions
RUN chown -R www-data:www-data /var/www/html
# Expose port
EXPOSE 9000
# Start PHP-FPM server
CMD ["php-fpm"]