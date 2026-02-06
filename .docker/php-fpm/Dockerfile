FROM composer:2 AS composer_stage
FROM php:8.4-fpm

# Argumenty dla UID/GID
ARG USER_ID=1000
ARG GROUP_ID=1000

# Instalacja absolutnego minimum zależności systemowych
# Zostawiamy libpq-dev (dla Postgresa), zip/unzip (dla Composera) oraz git
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libpq-dev \
    zip \
    unzip \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalacja rozszerzeń PHP niezbędnych dla Laravel REST API
# pdo_pgsql - baza danych, bcmath - precyzyjne obliczenia (częste w API),
# intl - walidacja i formatowanie, pcntl - obsługa procesów (np. queue worker)
RUN docker-php-ext-install \
    pdo_pgsql \
    bcmath \
    intl \
    pcntl

# Xdebug - pozostawiam, skoro wspomniałeś, że jest niezbędny do kontroli
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Pobranie Composera
COPY --from=composer_stage /usr/bin/composer /usr/local/bin/composer

# Remapowanie użytkownika www-data
RUN groupmod -g ${GROUP_ID} www-data || true && \
    usermod -u ${USER_ID} -g www-data -m -d /home/www-data www-data

# Przygotowanie katalogów i uprawnień
RUN mkdir -p /var/www && \
    chown -R www-data:www-data /var/www /home/www-data

USER www-data

# Katalog roboczy
WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]
