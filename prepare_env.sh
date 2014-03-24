npm install
bower install &
cd api
curl -s http://getcomposer.org/installer | php
php composer.phar install
php composer.phar dumpautoload -o
