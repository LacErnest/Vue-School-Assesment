#!/bin/sh

/usr/local/bin/wait-for-it db:3306 -t 60

php artisan migrate --force

php artisan db:seed --force

php-fpm