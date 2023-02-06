#!/usr/bin/env bash

#Drop and recreate database
php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:schema:create
# Pretend that the migrations have run
php bin/console doctrine:migrations:sync-metadata-storage
php bin/console doctrine:migrations:version --add --all --no-interaction

php bin/console app:organisation:add Demokratis

php bin/console app:user:add test@test.com test 1
php bin/console app:user:add toast@test.com toast 1
php bin/console app:user:add admin@test.com admin 1 --admin
php bin/console app:user:add super@test.com super 1 --superadmin

php bin/console app:tag:add Energie
php bin/console app:tag:add Bildung
php bin/console app:tag:add Forschung
php bin/console app:tag:add Informatik
php bin/console app:tag:add Landwirtschaft
php bin/console app:tag:add Wirtschaft
php bin/console app:tag:add Finanzen
php bin/console app:tag:add Gesundheit
php bin/console app:tag:add Kultur
php bin/console app:tag:add Verteidigung
php bin/console app:tag:add Sicherheit
php bin/console app:tag:add Recht
php bin/console app:tag:add Verkehr
php bin/console app:tag:add Umwelt
php bin/console app:tag:add Kommunikation
php bin/console app:tag:add Migration
php bin/console app:tag:add Sport
php bin/console app:tag:add Versicherung
php bin/console app:tag:add Raumordnung
php bin/console app:tag:add Diplomatie

php bin/console app:consultations:pull
php bin/console app:consultations:documents
