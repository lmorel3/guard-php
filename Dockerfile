FROM trafex/alpine-nginx-php7
MAINTAINER Laurent Morel <hello@lmorel3.fr>

# Clean application
RUN rm -rf /var/www/html/*

# Adds SQLite support
RUN apk add --no-cache php7-pdo php7-sqlite3 php7-pdo_sqlite

# Copy application files
WORKDIR /var/www/html
COPY app/ /var/www/html/