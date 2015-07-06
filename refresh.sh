#!/bin/bash
set -x
git pull
LIVE_DB_USER=www
LIVE_DB_PASSWORD=yeqWaDdlDbAu1VKyxfaS
LIVE_DB_NAME=www
LIVE_DB_HOST=45.33.22.110

DEV_DB_USER=next
DEV_DB_PASSWORD=at73DkCGhsDYYhuOfm8s
DEV_DB_NAME=next
DEV_DB_HOST=45.33.22.110

mysqldump -u$LIVE_DB_USER -p$LIVE_DB_PASSWORD -h$LIVE_DB_HOST --skip-tz-utc --add-drop-table $LIVE_DB_NAME > db.sql
mysql -u$DEV_DB_USER -p$DEV_DB_PASSWORD -h$DEV_DB_HOST $DEV_DB_NAME < db.sql
mysql -u$DEV_DB_USER -p$DEV_DB_PASSWORD -h$DEV_DB_HOST $DEV_DB_NAME -e "update users set email=concat('user',id,'@benallfree.com');"
./artisan migrate
mysqldump -u$DEV_DB_USER -p$DEV_DB_PASSWORD -h$DEV_DB_HOST --skip-tz-utc --add-drop-table $DEV_DB_NAME > db.sql
rm db.sql.gz
gzip -9 db.sql
rsync -avv ../pick.cool/html/i/ html/i
./artisan cache:clear
./artisan cache:views:clear

# locally:
# scp user@ftp.pick.cool:~/next.pick.cool/db.sql .
# rsync -avv user@ftp.pick.cool:~/next.pick.cool/html/i/ html/i


