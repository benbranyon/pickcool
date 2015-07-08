#!/bin/bash
set -x

git pull origin master
./composer install
./artisan dump-autoload
./artisan cache:clear
./artisan cache:views:clear
