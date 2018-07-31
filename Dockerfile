FROM trafex/alpine-nginx-php7
MAINTAINER Laurent Morel <hello@lmorel3.fr>

VOLUME ["/var/log/guard", "/config"]

# Clean application
RUN rm -rf /var/www/*

# Nginx conf
RUN sed -i 's|root /var/www/html;|root /var/www/public;|g' /etc/nginx/nginx.conf

# Adds SQLite support
RUN apk add --no-cache php7-pdo php7-sqlite3 php7-pdo_sqlite

# Copy application files
WORKDIR /var/www/
COPY app/ /var/www/

# Install PHP dependencies
RUN if [ "$APP_ENV" != "dev" ]; then \
        cd /var/www \
        php bin/composer.phar install \
        ; \
    fi
