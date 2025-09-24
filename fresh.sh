#!/bin/bash

NAME="php-1"
CONTAINER_NAME=$(docker ps -qf "name=$NAME" | head -1)

if ! docker ps | grep -q $CONTAINER_NAME; then
  echo "Container $CONTAINER_NAME is not running"
  exit 1
fi

exec_cmd() {
  local cmd="$1"
  echo "Executing $cmd"
  docker exec $CONTAINER_NAME bash -c "$cmd"
  local status=$?
  if [ $status -ne 0 ]; then
    echo "Error while executing $cmd"
    exit 1
  fi
}

exec_cmd "composer install"
exec_cmd "php artisan key:generate"
exec_cmd "php artisan migrate"
exec_cmd "php artisan scout:sync-index-settings"
exec_cmd "php artisan scout:run"
exec_cmd "php artisan storage:link"
exec_cmd "chown -R www-data:www-data storage"
exec_cmd "php artisan db:seed --class=SomeSeeder"

echo "Fresh install complete"
