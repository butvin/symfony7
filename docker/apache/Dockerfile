FROM httpd:2.4-alpine

RUN apk update; \
    apk upgrade;

COPY vhost.conf ${HTTPD_PREFIX}/conf/app.conf

RUN echo "Include conf/app.conf" >> ${HTTPD_PREFIX}/conf/httpd.conf
#RUN echo "Include /usr/local/apache2/conf/app.conf" >> /usr/local/apache2/conf/httpd.conf