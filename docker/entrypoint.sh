#!/bin/bash

set -e
cd /var/www/app

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
touch /proc/self/fd/2 || true
chown www-data:www-data /proc/self/fd/2 || true

if [ ! -d "vendor" ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
