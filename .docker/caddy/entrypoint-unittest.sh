#!/bin/sh
set -e

printf "\n ################ Create Database ################ \n"
php -d memory_limit=-1 bin/console doctrine:database:create

printf "\n ################ Migrate ################ \n"
php -d memory_limit=-1 bin/console doctrine:migrations:migrate --no-interaction

printf "\n ################ Schema validate ################ \n"
php -d memory_limit=-1 bin/console doctrine:schema:validate -v

#printf "\n ################ Elastic populate ################ \n"
#php -d memory_limit=-1 bin/console fos:elastica:populate

printf "\n ################ Load Fixtures ################ \n"
php -d memory_limit=-1 bin/console doctrine:fixtures:load --no-interaction

printf "\n ################ Run Unit test ################ \n\n"
php -d memory_limit=-1 bin/phpunit