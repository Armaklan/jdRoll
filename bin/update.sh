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

echo "******** Initialisation de la base de données ********"
if [ -z "$user" ]; then
    echo "Veuillez saisir votre utilisateur mysql : "
    read user
fi
if [ -z "$pass" ]; then
    echo "Veuillez saisir le mot de passe de la base de données : "
    read pass
fi
if [ -z "$db" ]; then
    echo "Veuillez saisir le nom de la base de données : "
    read db
fi
echo "Création du schéma"
cat ddl.sql | mysql -u $user -p$pass -D $db

echo "**************************************************"
echo "Votre environnement est à jour et peut être utilisé"