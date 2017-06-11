#!/usr/bin/env python2
#encoding: UTF-8

# To change this license header, choose License Headers in Project Properties.
# To change this template file, choose Tools | Templates
# and open the template in the editor.
import os

files = ""
filesSplit = ""
daFile = ""

files = os.popen("find /var/www/sistema/ -ctime -5").read()

filesSplit = [s.strip() for s in files.splitlines()]

for xx in filesSplit:
    if(os.path.isfile(xx)):
        resultado = os.popen("/usr/bin/php /var/www/html/dev/huerin/cron/updateTaskFile.php "+"'"+xx+"'").read()
        print resultado