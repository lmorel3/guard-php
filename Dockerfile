FROM phpearth/php:7.1-nginx

MAINTAINER Laurent Morel <hello@lmorel3.fr>

RUN apk add -U \
    php7.1-pdo \
    php7.1-pdo_sqlite \
    sqlite

#RUN rm -rf /var/cache/apk/* \
#    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer --version=${COMPOSER_VERSION}

RUN sed -i -e 's|root /var/www/html;|root /var/www/html/public;|g' \
	/etc/nginx/conf.d/default.conf
		
RUN sed -i -e "s/;listen.mode = 0660/listen.mode = 0666/g" \
	/etc/php/7.1/php-fpm.d/www.conf

RUN rm -rf /var/www/html/*


VOLUME /var/log/guard /config

COPY ./docker /docker
COPY ./app/config/config.yaml /config/
COPY ./app /var/www/html

WORKDIR /var/www/html
RUN php bin/composer.phar install

ENTRYPOINT ["sh", "/docker/entrypoint.sh"]
