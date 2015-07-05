#!/bin/bash
set -x

git pull origin master
./artisan cache:clear
./artisan cache:views:clear
