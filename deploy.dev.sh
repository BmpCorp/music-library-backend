#!/bin/bash

CYAN_COLOR='\033[0;36m'
RED_COLOR='\033[0;31m'
RESET_COLOR='\033[0m'

# Check for requirements

docker compose version &>/dev/null
if [ $? -ne 0 ]; then
    echo "Please install Docker compose (v2) to proceed."
    exit 1
fi

PORTS_TO_CHECK=(3080 3443 3306 7700 5672 15672 6379 5000)
PORTS_IN_USE=0
for PORT in "${PORTS_TO_CHECK[@]}"; do
    (echo >/dev/tcp/localhost/$PORT) &>/dev/null
    if [ $? -eq 0 ]; then
        PORTS_IN_USE=$((PORTS_IN_USE + 1))
        echo "Port $PORT is in use. Please free it to proceed."
    fi
done

if [ $PORTS_IN_USE -gt 0 ]; then
    exit 0
fi

# Prompt minimal input from user

DEFAULT_PROJECT_NAME=music-library-backend
read -e -p 'Please enter Docker compose project name: ' -i "$DEFAULT_PROJECT_NAME" PROJECT_NAME
PROJECT_NAME="${PROJECT_NAME:-$DEFAULT_PROJECT_NAME}"

DEFAULT_PASSWORD=password
read -e -p 'Please enter password for everything: ' -i "$DEFAULT_PASSWORD" PASSWORD
PASSWORD="${PASSWORD:-$DEFAULT_PASSWORD}"

# Configure environment files

echo -e "${CYAN_COLOR}Copying and configuring environment files...${RESET_COLOR}"
cp .docker/.env.example .docker/.env
cp backend/.env.example backend/.env

sed -i "s/music-library-backend/$PROJECT_NAME/g" .docker/.env

sed -i "s/<generate>/$PASSWORD/g" .docker/.env
sed -i "s/<same_as_docker>/$PASSWORD/g" backend/.env
sed -i "s/<generate>/$PASSWORD/g" backend/.env

# Build and start containers

echo -e "${CYAN_COLOR}Building and starting containers...${RESET_COLOR}"
docker compose -f .docker/docker-compose.dev.yml up -d --build

echo -e "${CYAN_COLOR}Waiting to check if everything is up and running...${RESET_COLOR}"
sleep 10
SERVICES_DOWN=$(docker compose -f .docker/docker-compose.dev.yml ps | grep -E -c "Exit|unhealthy" || true)
if [ $SERVICES_DOWN -gt 0 ]; then
    echo -e "${RED_COLOR}Some containers failed to start!${RESET_COLOR}"
    docker compose -f .docker/docker-compose.dev.yml logs
    exit 1
fi

# Fresh install and seed

echo -e "${CYAN_COLOR}Running fresh install script...${RESET_COLOR}"
bash fresh.sh

echo -e "${CYAN_COLOR}Seeding database...${RESET_COLOR}"
CONTAINER_NAME=$(docker ps -qf "name=php-1" | head -1)
docker exec $CONTAINER_NAME bash -c "php artisan db:mock"

# Redirect user to admin panel

echo -e "${CYAN_COLOR}Deployment finished!${RESET_COLOR}"
echo "Proceed to http://localhost:3080/admin/login to access the admin panel."
echo "Use admin@example.com and ${PASSWORD} to log in."
