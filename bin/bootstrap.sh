echo "Avant d'exécuter ce script il faut : "
echo " - Installer Mysql et créer une base de donnée vierge"
echo " - Installer php"
echo " - Installer node"
echo "Voulez vous continuer (y/n) ? "
read cont
if [ "$cont" = "y" ]; then
    echo "******** Installation d'une configuration par défaut ********"
    cp config.dist.yml config.yml
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
    echo "Veuillez saisir votre utilisateur mysql : "
    read user
    echo "Veuillez saisir le nom de la base de données : "
    read db
    cat ddl.sql | mysql -u $user -p -D $db
    echo "******** Fin de préparation de l'environnement ********"
    echo "Pour terminer de configurer votre environnement, editez le fichier config.yml"
    echo "Par la suite, vous pouvez lancer un serveur http avec la commande : grunt dev"
fi