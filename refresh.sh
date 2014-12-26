!#/bin/bash
source ~/.bash_profile
mysqldump db126393_coolfactor > db.sql
mysql -D db126393_coolbeta < db.sql
mysql -D db126393_coolbeta -e "update users set email=concat('user',id,'@benallfree.com');"
mysqldump db126393_coolbeta > db.sql
./artisan migrate
rsync -avv ../pick.cool/app/storage/data ./app/storage

# locally:
scp mediatemple:~/domains/next.pick.cool/db.sql .
rsync -avv mediatemple:~/domains/next.pick.cool/app/storage/data app/storage

