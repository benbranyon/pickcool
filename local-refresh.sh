#!/bin/bash
set -x

scp user@ftp.pick.cool:~/next.pick.cool/db.sql.gz .
rm db.sql
gzip -d db.sql.gz
mysql -uroot -pr00t coolfactor < db.sql
./artisan migrate
./artisan cache:clear
./artisan cache:views:clear
rsync -avv user@ftp.pick.cool:~/next.pick.cool/html/i/ html/i
