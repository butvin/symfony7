# syntax=docker/dockerfile:1
FROM php:8.3-fpm

ENV DEBIAN_FRONTEND=noninteractive

# arg passed from file compose.yaml
ARG APP_ENV

# https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions
# https://github.com/docker-library/docs/tree/master/php#php-core-extensions
# https://docs.docker.com/go/dockerfile-aptget-best-practices/
RUN set -eux; \
  apt-get update && apt-get install -y --no-install-recommends \
    curl \
    wget \
    unzip \
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


#RUN apt-get update && apt-get install -y libicu-dev \
#  && docker-php-ext-configure intl \
#  && docker-php-ext-install intl \
#  && docker-php-ext-enable intl \


RUN docker-php-ext-install zip mysqli pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-enable pdo_mysql pdo_pgsql

# Add PECL extensions, see
# https://github.com/docker-library/docs/tree/master/php#pecl-extensions
# This example adds the 'redis' and 'xdebug'.
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
        apt-get update && apt-get install -y --no-install-recommends \
            bash-completion \
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


# Use the default production or development configuration for PHP runtime arguments, see
# https://github.com/docker-library/docs/tree/master/php#configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy app files from the app directory, excluding .dockerignore files.
COPY --chown=1000:1000 . /var/www/html/
COPY ./docker/php/xdebug.ini "$PHP_INI_DIR/docker-php-ext-xdebug.ini"
COPY ./docker/php/upload.ini "$PHP_INI_DIR/custom.ini"

# Set user ID & group ID & home directory & shell of www-data user.
ARG UNAME=www-data
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER_DIR=/home/www-data
# Some commands may use home dir for caching (like composer, npm or yarn) or for another reason.
RUN mkdir ${USER_DIR} && chown -R ${USER_ID}:${GROUP_ID} ${USER_DIR} \
    && usermod --uid ${USER_ID} --home ${USER_DIR} --shell /bin/bash ${UNAME} \
    && groupmod --gid ${GROUP_ID} ${UNAME}

#RUN set -ex; \
#    mkdir -p /home/www-data; \
#    chown -R 1000:1000 /home/www-data; \
#    usermod --uid 1000 --home /home/www-data --shell /bin/bash www-data; \
#    groupmod --gid 1000  www-data

# Switch to a non-privileged user (defined in the base image) that the app will run under.
# https://docs.docker.com/go/dockerfile-user-best-practices/
USER www-data

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]