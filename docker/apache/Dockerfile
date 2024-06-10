FROM httpd:2.4-alpine

RUN apk update; \
    apk upgrade;

# Copying config 'vhost.conf' file to apache directory
COPY vhost.conf /usr/local/apache2/conf/app.conf

RUN echo "Include /usr/local/apache2/conf/app.conf" >> /usr/local/apache2/conf/httpd.conf