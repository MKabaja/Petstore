FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    zlib1g-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo mbstring xml curl zip

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Przyjmij UID z hosta — domyślnie 1000
ARG WWWUSER=1000
ARG WWWGROUP=1000

# Utwórz usera z tym samym UID co na WSL
RUN groupadd -g $WWWGROUP appuser \
    && useradd -ms /bin/bash -u $WWWUSER -g appuser appuser

WORKDIR /var/www

EXPOSE 9000

USER appuser

CMD ["php-fpm"]