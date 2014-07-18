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

apt-get install -y python-software-properties python g++ make
add-apt-repository -y ppa:chris-lea/node.js
add-apt-repository -y ppa:ondrej/php5-oldstable

apt-get update

apt-get install -y curl apache2 php5 libapache2-mod-php5 php5-curl php5-gd php5-mcrypt php5-mysql php-apc php5-cli nodejs mysql-server mysql-client phpmyadmin

rm -rf /var/www
ln -fs /vagrant /var/www

export user=root
export pass=vagrant
export db=jdRoll
cd /vagrant && bin/bootstrap.sh -q