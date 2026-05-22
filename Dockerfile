FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create required Laravel storage folders + set permissions
RUN mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && chmod -R 775 storage \
    && chmod -R 775 bootstrap/cache

# Generate storage symlink
RUN php artisan storage:link || true

# Expose port
EXPOSE 8000

# Start Laravel
CMD php artisan serve --host 0.0.0.0 --port 8000
