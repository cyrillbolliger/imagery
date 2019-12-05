#!/bin/bash

DEPLOY_DIR="~/www/imagery.bolliger.tech/"

mkdir deploy
mv -t deploy app artisan bootstrap composer* config database public resources routes

rsync -vrz --delete-after \
  $TRAVIS_BUILD_DIR/deploy $SSH_USER@$SSH_HOST:$DEPLOY_DIR

ssh $SSH_USER@$SSH_HOST "cd $DEPLOY_DIR && $HOME/bin/composer install --optimize-autoloader --no-dev && php artisan config:cache"
