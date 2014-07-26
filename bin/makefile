WWW_DIR=/usr/share/nginx/html
prepare:
        @echo
        @echo "Preparation du build"
        @echo
        $(eval RELEASE_DIR:=$(WWW_DIR)/release/$(shell date +'%Y-%m-%d_%H-%M'))

clone:
        @echo
        @echo "Clone des sources dans $(RELEASE_DIR)"
        @echo
        git clone https://github.com/Armaklan/jdRoll $(RELEASE_DIR)

link:
        @echo
        @echo "Link files and config"
        @echo
        @ln $(WWW_DIR)/config/config.yml $(RELEASE_DIR)/config.yml
        @ln -s $(WWW_DIR)/files $(RELEASE_DIR)/files

getcomposer:
        @echo
        @echo "Recuperation de composer"
        @echo
        @cd $(RELEASE_DIR) && curl -s http://getcomposer.org/installer | php

update:
        @echo
        @echo "Update dependencies and database"
        @echo
        @cd $(RELEASE_DIR) && bin/update.sh

deploy:
        @echo
        @echo "Deploy new version"
        @echo
        @rm $(WWW_DIR)/jdRoll -f
        @ln -s $(RELEASE_DIR) $(WWW_DIR)/jdRoll

restart:
        @echo
        @echo "Restart server"
        @echo
        @service php5-fpm restart
        @service nginx restart

install: prepare clone link getcomposer update deploy restart