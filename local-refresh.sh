#!/bin/bash
scp user@ftp.pick.cool:~/next.pick.cool/db.sql .
rsync -avv user@ftp.pick.cool:~/next.pick.cool/html/i/ html/i
