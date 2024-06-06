# syntax=docker/dockerfile:1
FROM php:8.3-fpm

# Copy app files from the app directory.
COPY ./ /var/www/html

# https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions
# https://github.com/docker-library/docs/tree/master/php#php-core-extensions
# https://docs.docker.com/go/dockerfile-aptget-best-practices/
RUN apt-get update && apt-get install -y \
  curl \
  wget \
  libzip-dev
#  libfreetype-dev \
#  libjpeg62-turbo-dev \
#  libpng-dev \
#  && docker-php-ext-configure gd --with-freetype --with-jpeg \
#  && docker-php-ext-install -j$(nproc) gd


RUN docker-php-ext-install zip

# Add PECL extensions, see
# https://github.com/docker-library/docs/tree/master/php#pecl-extensions
# This example adds the 'redis' and 'xdebug' extensions.
RUN pecl install xdebug  && docker-php-ext-enable xdebug

# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
#USER www-data

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

CMD ["php-fpm"]