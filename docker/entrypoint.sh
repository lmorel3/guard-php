#!/bin/sh

if [ ! -f /config/database.db ]; then
    echo "Database not found: creating"
    cat /docker/init.sql | sqlite3 /config/database.db
fi

if [ ! -z "$PUID" ]; then
  if [ -z "$PGID" ]; then
    PGID=${PUID}
  fi
  deluser www-data
  addgroup -g ${PGID} www-data
  adduser -D -S -h /var/cache/nginx -s /sbin/nologin -G www-data -u ${PUID} www-data
  chown -Rf  ${PUID}.www-data /config
  chown -Rf  ${PUID}.www-data /var/www
else
  chown -Rf www-data.www-data /var/www/html
fi

chmod 770 -R /config

/sbin/runit-wrapper
