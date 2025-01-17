# syntax=docker/dockerfile:1
FROM php:8.3-fpm

ENV DEBIAN_FRONTEND=noninteractive

# Get argument value passed in compose.yaml
ARG APP_ENV

# https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions
# https://github.com/docker-library/docs/tree/master/php#php-core-extensions
# https://docs.docker.com/go/dockerfile-aptget-best-practices/
RUN set -eux; \
  apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    p7zip-full \
    libzip-dev \
    libfreetype-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    libavif-dev \
    libldap-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-avif \
  && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install zip mysqli pdo pdo_mysql pdo_pgsql ldap fileinfo exif \
    && docker-php-ext-enable pdo_mysql pdo_pgsql

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add PECL extensions, see https://github.com/docker-library/docs/tree/master/php#pecl-extensions
RUN pecl update-channels && pecl install xdebug && docker-php-ext-enable xdebug
# https://xdebug.org/docs/all_settings
#COPY ./docker/php/xdebug.ini "$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini"


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
COPY ./docker/php/custom.ini "$PHP_INI_DIR/conf.d/custom.ini"


# Switch to a non-privileged user (defined in the base image) that the app will run under
# Set user ID & group ID & home directory & shell of www-data user.
ARG USER_NAME=www-data
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG USER_DIR=/home/www-data

# Some commands may use home dir for caching (like composer, npm or yarn) or for another reason.
RUN mkdir -p ${USER_DIR} \
    && chown -R ${USER_ID}:${GROUP_ID} ${USER_DIR} \
    && usermod --uid ${USER_ID} --home ${USER_DIR} --shell /bin/bash ${USER_NAME} \
    && groupmod --gid ${GROUP_ID} ${USER_NAME}
# https://docs.docker.com/go/dockerfile-user-best-practices/
USER ${USER_NAME}

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
