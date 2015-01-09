!#/bin/bash
source ~/.bash_profile
mysqldump db126393_coolfactor > db.sql
mysql -D db126393_coolbeta < db.sql
mysql -D db126393_coolbeta -e "update users set email=concat('user',id,'@benallfree.com');"
./artisan migrate
mysqldump db126393_coolbeta > db.sql
rsync -avv ../pick.cool/html/i/ html/i

# locally:
scp mediatemple:~/domains/next.pick.cool/db.sql .
rsync -avv mediatemple:~/domains/next.pick.cool/html/i/ html/i


