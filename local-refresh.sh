#!/bin/bash
set -x

scp user@ftp.pick.cool:~/next.pick.cool/db-live.sql.gz .
rm db-live.sql
gzip -d db-live.sql.gz
mysql -uroot -pr00t coolfactor < db-live.sql
# ./artisan migrate
# ./artisan cache:clear
# ./artisan cache:views:clear
# rsync -avv user@ftp.pick.cool:~/next.pick.cool/html/i/ html/i
