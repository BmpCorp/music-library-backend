#!/bin/bash
set -e

crontab /etc/cron.d/crontab
cron

service supervisor start
php-fpm
tail -f /dev/null
