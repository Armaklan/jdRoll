#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive 
DBPASSWD=vagrant
DBNAME=jdRoll

apt-get update

sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $DBPASSWD"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DBPASSWD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/dbconfig-install boolean true"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/app-password-confirm password $DBPASSWD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/admin-pass password $DBPASSWD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/mysql/app-pass password $DBPASSWD"
sudo debconf-set-selections <<< "phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2"

apt-get install -y apache2 php5 libapache2-mod-php5 php5-curl php5-gd php5-mcrypt php5-mysql php-apc php5-cli nodejs mysql-server mysql-client phpmyadmin

echo -e "\n--- Setting up our MySQL user and db ---\n"
mysql -u root -p$DBPASSWD -e "CREATE DATABASE $DBNAME"

rm -rf /var/www
ln -fs /vagrant /var/www

cd /vagrant
echo "******** Installation d'une configuration par défaut ********"
cp config.dist.yml config.yml
sed "s/devdev/vagrant/g" config.yml > config.yml
echo "******** Installation des dependances Php ********"
curl -s http://getcomposer.org/installer | php
php composer.phar install
php composer.phar dumpautoload -o
echo "******** Installation des outils ********"
sudo npm install -g bower grunt grunt-cli
npm install
echo "******** Installation des dependances Web ********"
bower install
echo "******** Initialisation de la base de données ********"
cat ddl.sql | mysql -u root -pvagrant -D jdRoll
echo "******** Fin de préparation de l'environnement ********"