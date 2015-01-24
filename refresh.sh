#!/bin/bash
mysqldump -uroot -pr00t --skip-tz-utc coolfactor > db.sql
mysql -uroot -pr00t drop coolbeta
mysql -uroot -pr00t create coolbeta
mysql -uroot -pr00t -D coolbeta < db.sql
mysql -uroot -pr00t -D coolbeta  -e "update users set email=concat('user',id,'@benallfree.com');"
./artisan migrate
mysqldump -uroot -pr00t coolbeta > db.sql
rsync -avv ../pick.cool/html/i/ html/i

# locally:
# scp user@ftp.pick.cool:~/next.pick.cool/db.sql .
# rsync -avv user@ftp.pick.cool:~/next.pick.cool/html/i/ html/i


