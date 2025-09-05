FROM node:22.14.0-alpine AS node_base
FROM php:8.4-fpm-alpine

ARG APP_ENV

RUN apk add --no-cache --virtual build-dependencies icu-dev libpq-dev libzip-dev zip libldap openldap-dev libtool intltool dcron supervisor $PHPIZE_DEPS \
    && apk add --update linux-headers rabbitmq-c rabbitmq-c-dev \
    && docker-php-ext-install -j$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) mysqli opcache pdo pdo_pgsql pgsql pdo_mysql zip fileinfo exif ldap intl bcmath \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# add redis ext
RUN pecl update-channels \
    && pecl install -o -f redis \
    && docker-php-ext-enable redis

# add gd ext
RUN apk add --no-cache \
    libjpeg-turbo \
    libpng \
    freetype \
    libwebp \
    libxpm \
    libjpeg-turbo-dev \
    libpng-dev \
    freetype-dev \
    libwebp-dev \
    libxpm-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-xpm \
    && docker-php-ext-install gd \
    && apk del libjpeg-turbo-dev libpng-dev freetype-dev libwebp-dev libxpm-dev

# fix ldap requires certificate
RUN echo 'TLS_REQCERT never' >> /etc/openldap/ldap.conf

# cron log file
RUN touch /var/log/cron.log && chmod 0777 /var/log/cron.log

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# symfony cli
RUN apk add --no-cache bash \
    && curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | sh \
    && apk add symfony-cli

COPY --from=node_base /usr/local/bin/node /usr/local/bin/
COPY --from=node_base /usr/local/include/node /usr/local/include/node
COPY --from=node_base /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node_base /usr/local/share/doc/node /usr/local/share/doc/node

RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN npm install -g npm@10.7.0

RUN if [ ${APP_ENV} = "dev" ]; then \
       apk add --no-cache --update \
            bash \
            bash-completion \
            htop \
            tree \
            acl \
            vim \
        ; \
      pecl update-channels && pecl install xdebug && docker-php-ext-enable xdebug ; \
    fi

ARG USER_NAME=www-data

# copy app files
COPY --chown=${USER_NAME}:${USER_NAME} . /var/www/seo-box/

# supervisor
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# crontab
COPY --chown=${USER_NAME}:${USER_NAME} docker/php/cronjobs/www-data.cron /var/spool/cron/crontabs/${USER_NAME}.cron

## set permissions
RUN chmod go-rwx /var/spool/cron/crontabs/${USER_NAME}.cron && \
    chmod u=rwx,g=wx,o=t /var/spool/cron/crontabs && \
    addgroup ${USER_NAME} wheel && \
    chown ${USER_NAME}:wheel /var/spool/cron/crontabs && \
    chown root:${USER_NAME} /var/spool/cron/crontabs/${USER_NAME}.cron


# php configure
COPY docker/php/custom.ini /usr/local/etc/php/custom.ini
COPY docker/php/www.conf /usr/local/etc/php/conf.d/
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/

# cleanup
RUN rm -rf /var/cache/apk/* /tmp/*

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
