#!/usr/bin/env bash

FILE=./.env.local
if test -f "$FILE"; then
composer  install
npm install

php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:schema:create
#php bin/console doctrine:migrations:sync-metadata-storage
#php bin/console doctrine:migrations:version --add --all --no-interaction

php bin/console app:organisation:add Demokratis

php bin/console app:user:add super@test.com super 1 --superadmin

#php bin/console app:consultations:pull
#php bin/console app:consultations:documents

php bin/console doctrine:fixtures:load --append

else
    echo "init.sh can only be run locally"
fi
