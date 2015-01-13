#!/bin/bash
mysqldump -udb126393 -pl9mrWMj9IM -hexternal-db.s126393.gridserver.com --skip-tz-utc db126393_coolfactor > db.sql
mysql -uroot -pr00t -D coolfactor < db.sql
#mysql -uroot -pr00t -D coolfactor -e "update users set email=concat('user',id,'@benallfree.com');"
./artisan migrate
#mysqldump db126393_coolbeta > db.sql
rsync -avv benallfree.com@205.186.179.182:~/domains/pick.cool/html/i/ html/i

# locally:
# scp mediatemple:~/domains/next.pick.cool/db.sql .
# rsync -avv mediatemple:~/domains/next.pick.cool/html/i/ html/i


