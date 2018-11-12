#ARG PHP_VERSION=7.2
FROM php:7.2-fpm

# PHP Extensions
RUN apt-get update \
  && docker-php-ext-install pdo_mysql
