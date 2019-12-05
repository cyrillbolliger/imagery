#!/bin/bash

DEPLOY_DIR="~/www/imagery.bolliger.tech"

rsync -vrz --delete-after \
  --exclude='.env' \
  --exclude='deploy_rsa.enc' \
  --exclude='node_modules' \
  --exclude='storage' \
  --exclude='tests' \
  --exclude='vendor' \
  $TRAVIS_BUILD_DIR/. $SSH_USER@$SSH_HOST:$DEPLOY_DIR

ssh $SSH_USER@$SSH_HOST "cd $DEPLOY_DIR && ~/bin/composer install --optimize-autoloader --no-dev && php artisan config:cache"
