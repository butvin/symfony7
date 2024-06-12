FROM httpd:2.4-alpine

RUN apk update; \
    apk upgrade;

COPY vhost.conf ${HTTPD_PREFIX}/conf/app.conf
RUN echo "Include conf/app.conf" >> ${HTTPD_PREFIX}/conf/httpd.conf