mysqldump use db126393_coolfactor > db.sql
mysql -D use db126393_coolbeta < db.sql
./artisan migrate
cp ../pick.cool/app/storage/data ./app/storage -rf

