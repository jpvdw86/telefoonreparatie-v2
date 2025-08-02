#!/bin/sh
printf "\n ################ start migrations ################ \n"
php bin/console doctrine:migrations:migrate --no-interaction
printf "\n ################ end migrations ################ \n\n"

printf "\n ################ clear the cache ################ \n"
php bin/console assets:install public || exit 1
php bin/console cache:warmup || exit 1
printf "\n ################ end clear the cache ################ \n\n"

printf "\n ################ start crond ################ \n"
set -e
crond -f
printf "\n ################ end crond ################ \n\n\n"