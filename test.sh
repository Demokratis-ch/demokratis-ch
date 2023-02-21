#!/usr/bin/env bash

FILE=./.env.local
if test -f "$FILE"; then

php vendor/bin/php-cs-fixer fix
php bin/phpunit
php vendor/bin/psalm --no-cache
# php vendor/bin/twigcs templates

else
    echo "test.sh can only be run locally"
fi
