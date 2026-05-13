FROM php:8.3-fpm

# Zainstaluj systemowe zależności wymagane przez rozszerzenia PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zlib1g-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Skompiluj i włącz rozszerzenia PHP
RUN docker-php-ext-install pdo mbstring xml curl zip

# Skopiuj binarny Composer z oficjalnego obrazu
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Kopiuj tylko pliki Composer — Docker cache nie inwaliduje się przy zmianie kodu aplikacji
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-scripts

# Kopiuj resztę aplikacji po instalacji zależności
COPY . .

# Przekaż własność plików do www-data — użytkownika PHP-FPM
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# PHP-FPM nasłuchuje na tym porcie — Nginx przekazuje tu requesty przez FastCGI
EXPOSE 9000

# Entrypoint startuje PHP-FPM jako www-data
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
USER www-data
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]