#!/bin/sh

chown -R www-data:www-data /data/www
chown -R 777 /data/www

apache2ctl -D FOREGROUND