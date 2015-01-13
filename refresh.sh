!#/bin/bash
mysqldump -udb126393 -pl9mrWMj9IM -hexternal-db.s126393.gridserver.com db126393_coolfactor > db.sql
mysql -uroot -pr00t -D coolbeta < db.sql
mysql -uroot -pr00t -D coolbeta -e "update users set email=concat('user',id,'@benallfree.com');"
./artisan migrate
mysqldump db126393_coolbeta > db.sql
rsync -avv benallfree.com@205.186.179.182:~/domains/pick.cool/html/i/ html/i

# locally:
scp mediatemple:~/domains/next.pick.cool/db.sql .
rsync -avv mediatemple:~/domains/next.pick.cool/html/i/ html/i


