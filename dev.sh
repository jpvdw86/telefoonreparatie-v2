#!/bin/bash
function exportVariables() {
  [ -f database.localvar ] && source database.localvar

  if [[ -n "$1" ]]; then
    echo "DATABASE_URL=mysql://root:access@mysql:3306/$1" > database.localvar
    source database.localvar
  fi

  if [[ -z "$DATABASE_URL" ]]; then
    echo "DATABASE_URL=mysql://root:access@mysql:3306/database" > database.localvar
    source database.localvar
  fi

  export DATABASE_URL=$DATABASE_URL
}

function up(){
  exportVariables $1
  docker compose -f compose.yaml -f compose-dev.yaml up -d
}

function test(){
  exportVariables $1
  docker compose -f compose.yaml -f compose-test.yaml up -d
}

function down_test(){
  exportVariables
  docker compose -f compose.yaml -f compose-test.yaml down
}

function restart(){
  down
  exportVariables $1
  docker compose -f compose.yaml -f compose-dev.yaml up -d
}

function down(){
  exportVariables
  docker compose -f compose.yaml -f compose-dev.yaml down
}

function styling() {
  run_php_cs
  run_twig_cs
  run_prettier
  run_phpstan
  run_php_md
}

function rebuild(){
  exportVariables
  docker compose -f compose.yaml -f compose-dev.yaml up --force-recreate --build $1
}

function install_application() {
  echo "Run Composer and Yarn install";
  exportVariables
  docker compose -f compose.yaml -f compose-dev.yaml run --rm -e XDEBUG_MODE=off app sh -c "composer install && yarn install"
}

function run_migrate() {
  echo "Run migrate";
  docker exec -it "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:migrations:migrate --no-interaction
}

function run_phpstan() {
  echo "Run phpstan";
  docker exec -it "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/vendor/bin/phpstan
}

function run_phpstan_baseline() {
  echo "Generate new phpstan baseline";
  docker exec -it "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/vendor/bin/phpstan -b
}

function run_twig_cs() {
  echo "Run Twig Cs";
  docker exec -it "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" /var/www/html/vendor/bin/twig-cs-fixer lint --config=.twig-cs-fixer.dist.php --fix /var/www/html/templates/
}

function run_reset_unit_tests_environment() {
  echo "Reset unit tests environment";
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:database:drop --env=test --force
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:database:create --env=test
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:migrations:migrate --no-interaction --env=test
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:schema:validate --env=test
  docker exec -it "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:fixtures:load --no-interaction --env=test
}

function run_unit_tests() {
  echo "Run unit_tests";
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/vendor/bin/phpunit
}

function run_unit_coverage() {
  echo "Run unit coverage report";
  docker exec -it -e XDEBUG_MODE=coverage "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/vendor/bin/phpunit --coverage-html /var/www/html/coverage
}

function run_php_cs() {
  echo "Run php_cs";
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --using-cache=no
}

function run_php_md() {
  echo "Run php_md";
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/vendor/bin/phpmd /var/www/html/src ansi /var/www/html/phpmd.xml --suffixes php
}

function run_doctrine_schema_validate() {
  echo "Run doctrine schema_validation";
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" php -d memory_limit=-1 /var/www/html/bin/console doctrine:schema:validate
}

function run_prettier() {
  echo "Run prettier";
  docker exec -it -e XDEBUG_MODE=off "$(docker ps | grep -E -i '(app)' | awk '{print $1}')" yarn format
}

function prune(){
    docker system prune -a -f
    echo "Done"
}

if [ -z "$1" ]; then
  GREEN='\033[0;32m'
  NC='\033[0m'

  actions=(
    "Application Up:up"
    "Application Down:down"
    "Application Restart:restart"
    "Styling:styling"
    "CS Fixer:run_php_cs"
    "Prettier:run_prettier"
    "Reset Unit test environment:run_reset_unit_tests_environment"
    "Unit Tests:run_unit_tests"
    "Unit Coverage:run_unit_coverage"
    "Doctrine schema validation:run_doctrine_schema_validate"
    "PHP MD:run_php_md"
    "PHPStan:run_phpstan"
    "PHPStan generate new baseline:run_phpstan_baseline"
    "Doctrine Migrate:run_migrate"
    "Install dependencies:install_application"
    "Docker prune all:prune"
  )

  echo "Select action"
  for i in "${!actions[@]}"; do
    action_name="${actions[i]%%:*}"
    echo -e "  [${GREEN}$((i + 1))${NC}] $action_name"
  done

  read -p "Insert number: " choice

  if [[ $choice -ge 1 && $choice -le ${#actions[@]} ]]; then
    command="${actions[$((choice - 1))]#*:}"
    eval "$command"
  else
    echo "Unrecognized selection: $choice"
  fi
  exit
fi

if [ "up" = $1 ]; then
    up $2
fi
