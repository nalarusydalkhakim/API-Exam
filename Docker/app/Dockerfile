FROM php:8.2-fpm

RUN apt-get update \
  && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip \
  zlib1g-dev \
  libpq-dev \
  libzip-dev \
  libmagickwand-dev

RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql zip bcmath gd

RUN pecl install imagick \
    && docker-php-ext-enable imagick
    
RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

EXPOSE 9000