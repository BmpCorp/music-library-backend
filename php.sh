#!/bin/bash
if [ -n "$1" ]
then
NAME=$1
else
NAME='php-1'
fi
CONTAINER=$(docker ps -qf "name=$NAME" | head -1)
docker exec -it ${CONTAINER} bash
