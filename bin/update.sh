#!/bin/sh

echo "******** Mise à jour de Git ********"
git pull
echo "******** Mise à jour de l'outillage ********"
npm install
echo "******** Mise à jour des dépendances Web ********"
bower install --allow-root
echo "******** Mise à jour des dépendances Php ********"
php composer.phar install
echo "******** Regénération de l'index autoload ********"
php composer.phar dumpautoload -o
echo "**************************************************"
echo "Votre environnement est à jour et peut être utilisé"
#TODO - Mise à jour BDD sql
