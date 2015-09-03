#!/bin/bash
set -x

rsync -avv user@ftp.pick.cool:~/next.pick.cool/html/i/ html/i
scp user@ftp.pick.cool:~/next.pick.cool/db-live.sql.gz .
rm db-live.sql
gzip -d db-live.sql.gz
git pull
./composer install
./artisan dump-autoload

mysql -uroot -pr00t coolfactor < db-live.sql
./artisan migrate
./artisan cache:clear
./artisan cache:views:clear

mysql -uroot -pr00t coolfactor -e "update users set is_visible=1;"
