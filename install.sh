#!/usr/bin/env bash

set -euo pipefail

## generate .env file
if [ ! -f .env ]; then
    SECRET=$(openssl rand 128 | openssl sha256 | sed 's/(stdin)= //')
    sed "s/APP_HASH_SECRET=.*/APP_HASH_SECRET=${SECRET}/" .env.example > .env
fi

# get containers ready
docker-compose pull
docker-compose build app

# install dependencies
docker-compose run app composer install
docker-compose run -uroot node npm install -g cross-env
docker-compose run -uroot node chown -R node:node /home/node/app
docker-compose run node yarn install --frozen-lockfile --production=false

# start up containers
docker-compose up -d

# set application key
docker-compose exec -T app php artisan key:generate

# get params to create test database
TEST_MYSQL_ROOT_PASSWORD=$(grep MYSQL_ROOT_PASSWORD .env.docker | cut -d '=' -f2 | sed -e 's/[[:space:]]*$//')
TEST_MYSQL_USER=$(grep DB_USERNAME .env.testing | cut -d '=' -f2 | sed -e 's/[[:space:]]*$//')
TEST_MYSQL_PASSWORD=$(grep DB_PASSWORD .env.testing | cut -d '=' -f2 | sed -e 's/[[:space:]]*$//')
TEST_MYSQL_DATABASE=$(grep DB_DATABASE .env.testing | cut -d '=' -f2 | sed -e 's/[[:space:]]*$//')

# wait until MySQL is really available
maxcounter=60
counter=0
while ! docker exec -t imagery_mysql mysql -uroot -p"${TEST_MYSQL_ROOT_PASSWORD}" -e quit > /dev/null 2>&1; do
    sleep 1
    counter=$(( counter + 1))
    if [ $counter -gt $maxcounter ]; then
        echo >&2 "FAILED: We have been waiting for MySQL too long, but we couldn't reach it."
        exit 1
    fi
    echo "Waiting for MySQL to get ready... ${counter}s"
done
echo "Yay, MySQL is up and ready."

# create test database
if docker exec -t imagery_mysql mysql -uroot -p"${TEST_MYSQL_ROOT_PASSWORD}" imagery_test -e quit > /dev/null 2>&1; then
    echo "Test database exists."
else
    echo "Creating test database."
    docker exec -t imagery_mysql mysql -uroot -p"${TEST_MYSQL_ROOT_PASSWORD}" -e"CREATE DATABASE ${TEST_MYSQL_DATABASE};"
    docker exec -t imagery_mysql mysql -uroot -p"${TEST_MYSQL_ROOT_PASSWORD}" -e"CREATE USER '${TEST_MYSQL_USER}'@'%' IDENTIFIED BY '${TEST_MYSQL_PASSWORD}';"
    docker exec -t imagery_mysql mysql -uroot -p"${TEST_MYSQL_ROOT_PASSWORD}" -e"GRANT ALL PRIVILEGES ON ${TEST_MYSQL_DATABASE}.* TO '${TEST_MYSQL_USER}'@'%';"
fi

# setup database and seed with demo data
docker-compose exec -T app php artisan migrate:fresh
docker-compose exec -T app php artisan db:seed --class=DemoSeeder

# generate mix manifest
docker-compose run node yarn production

# fully restart all containers (else there is a problem with the application key)
docker-compose down
docker-compose up -d

# set colors if script is executed by a tty
GREEN=
YELLOW=
NC=
if [ -t 1 ]; then
    GREEN="$(tput setf 2)"
    YELLOW="$(tput setf 3)"
    NC="$(tput sgr0)"
fi

# just some user info
echo -e "\n"
echo -e "${GREEN}Yupii, installation successful!${NC}\n"
echo -e "${YELLOW}NOTE:${NC} You'll need to add the proprietary fonts, to get this working properly."
echo -e "Ask ${YELLOW}admin at gruene dot ch${NC} for more information.\n"
echo -e "Now visit ${GREEN}http://localhost:8000${NC}"
echo -e "Login with the email ${GREEN}superadmin@user.login${NC} and ${GREEN}password${NC} as password"
