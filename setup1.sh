#!/bin/bash

cp .env.example .env && \
docker run --rm --interactive --tty \
  --volume $PWD:/app \
  --volume ${COMPOSER_HOME:-$HOME/.composer}:/tmp \
  composer install && \
sudo chown -R $(whoami):$(id -gn) .
