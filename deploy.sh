#!/bin/bash

set -e

DEPLOY_DIR="~/www/imagery.bolliger.tech/"

mkdir -p $TRAVIS_BUILD_DIR/deploy
mv -t $TRAVIS_BUILD_DIR/deploy app artisan bootstrap composer* config database public resources routes

rsync -vrz --delete-after  \
    --exclude='.htaccess' \
    --exclude='.env' \
    --exclude='storage' \
    --exclude='vendor' \
    --exclude='.docker' \
  $TRAVIS_BUILD_DIR/deploy/ $SSH_USER@$SSH_HOST:$DEPLOY_DIR

ssh $SSH_USER@$SSH_HOST "cd $DEPLOY_DIR && ~/bin/composer install --optimize-autoloader --no-dev && php artisan cache:clear && php artisan config:cache"
