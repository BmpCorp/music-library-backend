#!/bin/bash
set -e

crontab /etc/cron.d/crontab
cron

exec php-fpm
