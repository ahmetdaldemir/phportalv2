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
    && rm -rf /var/lib/apt/lists/*

# PHP uzantıları
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        bcmath \
        gd \
        intl \
        soap \
        zip \
        pcntl \
        opcache

# PECL uzantıları
RUN pecl install redis mongodb apcu xdebug \
    && docker-php-ext-enable redis mongodb apcu xdebug

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Kullanıcı oluştur
RUN useradd -G www-data,root -u 1000 -d /home/phportal phportal || true
RUN mkdir -p /home/phportal/.composer && chown -R phportal:phportal /home/phportal

# PHP ini & PHP-FPM config
COPY docker/php/local.ini /usr/local/etc/php/conf.d/local.ini
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Supervisor & Cron
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/cron/laravel-cron /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron && \
    crontab -u phportal /etc/cron.d/laravel-cron || true

# Uygulama dosyaları (public/storage .dockerignore'da)
COPY --chown=phportal:phportal . /var/www

# public/storage symlink'i varsa kaldır (container içinde yeniden oluşturulacak)
RUN rm -rf /var/www/public/storage 2>/dev/null || true

# Yetkiler
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R phportal:www-data /var/www/storage \
    && chown -R phportal:www-data /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Composer bağımlılıkları (development için)
RUN composer install --no-interaction --optimize-autoloader --prefer-dist \
    --ignore-platform-req=ext-pcntl \
    --ignore-platform-req=ext-intl \
    --ignore-platform-req=ext-soap || true

# Entrypoint script oluştur
RUN echo '#!/bin/bash' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "🚀 PHPortal Docker Entrypoint Starting..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'if [ ! -L /var/www/public/storage ]; then' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '    echo "📁 Creating storage symlink..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '    php /var/www/artisan storage:link || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "🧹 Clearing caches..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php /var/www/artisan config:clear || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php /var/www/artisan cache:clear || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php /var/www/artisan view:clear || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'php /var/www/artisan route:clear || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "🔐 Fixing permissions..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chown -R phportal:www-data /var/www/storage /var/www/bootstrap/cache || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'echo "✅ Starting supervisor..."' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
