#!/bin/sh
set -e
/usr/sbin/php-fpm83 --allow-to-run-as-root

printf "\n ################ start installing dependencies ################ \n\n"
php bin/console assets:install public || exit 1
php bin/console cache:warmup || exit 1

printf "\n ################ Start Caddy ################ \n"
caddy run --config /var/www/html/.docker/caddy/Caddyfile
printf "\n ################ Stop Caddy ################ \n"