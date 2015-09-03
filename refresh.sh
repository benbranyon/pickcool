#!/bin/bash
set -x

rsync -avv ../pick.cool/html/i/ html/i

git pull
composer install
./artisan dump-autoload
./artisan cache:clear
./artisan cache:views:clear

LIVE_DB_USER=www
LIVE_DB_PASSWORD=yeqWaDdlDbAu1VKyxfaS
LIVE_DB_NAME=www
LIVE_DB_HOST=45.33.22.110

DEV_DB_USER=next
DEV_DB_PASSWORD=at73DkCGhsDYYhuOfm8s
DEV_DB_NAME=next
DEV_DB_HOST=45.33.22.110

mysqldump -u$LIVE_DB_USER -p$LIVE_DB_PASSWORD -h$LIVE_DB_HOST --skip-tz-utc --add-drop-table $LIVE_DB_NAME > db-live.sql
mysql -u$DEV_DB_USER -p$DEV_DB_PASSWORD -h$DEV_DB_HOST $DEV_DB_NAME < db-live.sql
mysql -u$DEV_DB_USER -p$DEV_DB_PASSWORD -h$DEV_DB_HOST $DEV_DB_NAME -e "update users set email=concat('user',id,'@benallfree.com');"

rm db-live.sql.gz
gzip -9 db-live.sql

./artisan migrate

mysql -u$DEV_DB_USER -p$DEV_DB_PASSWORD -h$DEV_DB_HOST $DEV_DB_NAME -e "update users set is_visible=1;"
