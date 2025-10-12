FROM php:8.4-fpm

WORKDIR /var/www

# Sistem bağımlılıkları
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    vim \
    nano \
    supervisor \
    cron \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libxpm-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    libssl-dev \
    libgmp-dev \
    libbz2-dev \
    libreadline-dev \
    libsqlite3-dev \
    libxslt-dev \
    libffi-dev \
    pkg-config \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# PHP uzantıları
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        bcmath \
        gd \
        intl \
        soap \
        zip

# PECL uzantıları
RUN pecl install redis mongodb apcu xdebug \
    && docker-php-ext-enable redis mongodb apcu xdebug

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Kullanıcı oluştur
RUN useradd -G www-data,root -u 1000 -d /home/phportal phportal
RUN mkdir -p /home/phportal/.composer && chown -R phportal:phportal /home/phportal

# PHP ini
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini

# Supervisor & Cron
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/cron/laravel-cron /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron

# Uygulama dosyaları
COPY --chown=phportal:phportal . /var/www

# Yetkiler
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R phportal:phportal /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Composer bağımlılıkları
RUN composer install --no-interaction --no-dev --optimize-autoloader --prefer-dist --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-intl --ignore-platform-req=ext-soap

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
