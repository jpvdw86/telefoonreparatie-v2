# syntax=docker/dockerfile:1
FROM r.yourgeek.nl/alpine-php:3.20-php83 AS base

WORKDIR /var/www/html

COPY composer.json composer.lock yarn.lock package.json symfony.lock ./

RUN composer validate --strict
RUN composer install --no-scripts
RUN yarn install --frozen-lockfile

COPY .docker/config/php/20-opcache.ini /etc/php83/conf.d/20-opcache.ini
COPY .docker/config/php/20-php_config.ini /etc/php83/conf.d/20-php_config.ini
COPY .docker/cron/crontab.txt /var/spool/cron/crontabs/root

ARG RELEASE_VERSION=unknown
ARG DATABASE_URL=mysql://app:app@database.yourgeek.nl:3306/

ENV APP_SECRET=72c2d4642a6e5f284b9ddad6524865e8
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV RELEASE_VERSION=${RELEASE_VERSION}
ENV DATABASE_URL=${DATABASE_URL}

COPY . /var/www/html

RUN git config --global --add safe.directory /var/www/html && \
	composer dump-autoload --classmap-authoritative && rm -rf /usr/bin/composer

RUN chmod +rwx /var/www/html/.docker/cron/cron.sh && \
    chmod +rwx /var/www/html/.docker/cron/health-check.sh && \
    chmod +rwx /var/www/html/.docker/caddy/entrypoint.sh && \
    chmod +rwx /var/www/html/.docker/caddy/entrypoint-unittest.sh


## APPLICATION
FROM base AS application
RUN yarn build
RUN rm -rf node_modules