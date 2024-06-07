# syntax=docker/dockerfile:1
FROM php:8.3-fpm

ARG APP_ENV=$APP_ENV

ARG

## Copy app files from the app directory.
COPY ../../ /var/www/html

# https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions
# https://github.com/docker-library/docs/tree/master/php#php-core-extensions
# https://docs.docker.com/go/dockerfile-aptget-best-practices/
RUN set -ex; \
  apt-get update && apt-get install -y \
    curl \
    wget \
    locales \
    libzip-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd


# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


#RUN apt-get update && apt-get install -y \
#    libicu-dev \
#  && docker-php-ext-configure intl \
#  && docker-php-ext-install intl



RUN docker-php-ext-install zip mysqli pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-enable pdo_mysql pdo_pgsql

# Add PECL extensions, see
# https://github.com/docker-library/docs/tree/master/php#pecl-extensions
# This example adds the 'redis' and 'xdebug' .
RUN pecl update-channels && pecl install xdebug  && docker-php-ext-enable xdebug


# Compile 'rdkafka' extensions from source code.
#ENV LIBRDKAFKA_VERSION v0.11.0
#ENV BUILD_DEPS \
#    build-essential \
#    git \
#    libsasl2-dev \
#    libssl-dev \
#    python-is-python3 \
#    zlib1g-dev
#
#RUN apt-get update \
#    && apt-get install -y --no-install-recommends ${BUILD_DEPS} \
#    && cd /tmp \
#    && git clone \
#        --branch ${LIBRDKAFKA_VERSION} \
#        --depth 1 \
#        https://github.com/edenhill/librdkafka.git \
#    && cd librdkafka \
#    && ./configure \
#    && make \
#    && make install \
#    && pecl install rdkafka \
#    && docker-php-ext-enable rdkafka \
#    && rm -rf /tmp/librdkafka \
#    && apt-get purge \
#        -y --auto-remove \
#        -o APT::AutoRemove::RecommendsImportant=false \
#        ${BUILD_DEPS}


RUN if [ ${APP_ENV} = "dev" ]; then \
        apt-get update && apt-get install -y \
            bash-completion \
            iputils-ping \
            net-tools \
            fish \
            htop \
            tree \
            acl \
            vim \
        ; \
        curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash ; \
            apt-get update && apt-get install --fix-broken -y symfony-cli \
        ; \
    fi


# Use the default production configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# See https://docs.docker.com/go/dockerfile-user-best-practices/
#USER www-data

WORKDIR /var/www/html

CMD ["php-fpm"]