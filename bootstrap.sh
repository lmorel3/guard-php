#!/bin/bash

# Install PHP dependencies
if [ "$APP_ENV" != "dev" ]; then \
    cd /var/www/html \
    php composer.phar install \
    rm /var/www/html/composer* \
    ; \
fi