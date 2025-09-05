# syntax=docker/dockerfile:1
FROM php:8.4-fpm-alpine

ENV DEBIAN_FRONTEND=noninteractive

ARG APP_ENV

RUN apk add --update --no-cache curl openssl supervisor dcron tzdata

# TZ
RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo "${TZ}" > /etc/timezone
RUN "date"

# https://github.com/docker-library/docs/tree/master/php#how-to-install-more-php-extensions
# https://github.com/docker-library/docs/tree/master/php#php-core-extensions
# https://docs.docker.com/go/dockerfile-aptget-best-practices/
RUN apk add --no-cache --virtual build-dependencies icu-dev libpq-dev libzip-dev zip libldap openldap-dev libtool intltool $PHPIZE_DEPS \
    && apk add --update linux-headers rabbitmq-c rabbitmq-c-dev \
    && docker-php-ext-install -j$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) zip mysqli opcache pdo pdo_pgsql pgsql pdo_mysql fileinfo exif ldap intl \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# Fix ldap requires certificate
RUN echo 'TLS_REQCERT never' >> /etc/openldap/ldap.conf

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Symfony CLI
#RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh && apk add symfony-cli

# Xdebug
RUN if [ ${APP_ENV} = "dev" ]; then \
       apk add --no-cache --update \
            bash \
            bash-completion \
            fish \
            htop \
            tree \
            acl \
            vim \
        ; \
      pecl update-channels && pecl install xdebug && docker-php-ext-enable xdebug && \
      curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh && apk add symfony-cli ; \
    fi


ARG USER_NAME=www-data
RUN mkdir -p /var/www/html && chown -R ${USER_NAME}:${USER_NAME} /var/www/html

# codebase
COPY --chown=${USER_NAME}:${USER_NAME} . /var/www/html

# php-fpm
COPY docker/php/www.conf /etc/php8/php-fpm.d/www.conf

# supervisord
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# dcron (via www-data)
RUN touch /var/log/cron.log && chmod 0777 /var/log/cron.log
COPY docker/php/cronjobs /var/spool/cron/crontabs/${USER_NAME}

# cleanup
RUN rm -rf /var/cache/apk/* /tmp/*

# https://docs.docker.com/go/dockerfile-user-best-practices/
#USER ${USER_NAME}

WORKDIR /var/www/html

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
