# ============================================================
# Stage: development
# PHP 8.3 + Apache, with SQLite support, Xdebug
# ============================================================
FROM php:8.3-apache AS development

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    unzip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_sqlite \
        mbstring \
        zip \
        gd \
        xml \
        opcache

# Install Xdebug for dev
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Apache virtual host config: DocumentRoot → /var/www/html/public
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

# PHP dev ini
COPY docker/php/php-dev.ini /usr/local/etc/php/conf.d/php-dev.ini

# Xdebug config
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Set working directory
WORKDIR /var/www/html

# Expose port 80

# ============================================================
# Stage: production (optional, bisa di-extend nanti)
# ============================================================
FROM development AS production

# Copy source code
COPY . /var/www/html

# Fix permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html