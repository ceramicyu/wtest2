#!/bin/env bash
cd /usr/share/nginx/html && php vendor/robmorgan/phinx/bin/phinx migrate -e development
php-fpm                          