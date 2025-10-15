# Railway PHP 8.2 Dockerfile with SQLite support (No Volume)
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_sqlite mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy application code
COPY . .

# Create data directory for SQLite (no volume needed)
RUN mkdir -p /app/data && chmod 755 /app/data

# Set permissions
RUN chown -R www-data:www-data /app/data

# Expose port (Railway uses $PORT environment variable)
EXPOSE 8080

# Start PHP built-in server with proper port and error reporting
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /app -d display_errors=1 -d log_errors=1"]