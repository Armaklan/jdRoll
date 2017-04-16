--
-- Base de données: `armaklanjdrollgd`
--
delimiter $$

DROP FUNCTION IF EXISTS `VERSION_TABLE_EXISTS`;$$
CREATE FUNCTION `VERSION_TABLE_EXISTS`() RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE _exists  TINYINT(1) DEFAULT 0;

    SELECT COUNT(*) INTO _exists
    FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'version';

    RETURN _exists;

END$$

DROP FUNCTION IF EXISTS `VERSION_EXISTS`;$$
CREATE FUNCTION `VERSION_EXISTS`(VersionNumber INT(11)) RETURNS tinyint(1)
    READS SQL DATA
    DETERMINISTIC
BEGIN
    DECLARE _exists  TINYINT(1) DEFAULT 0;

    SELECT COUNT(*) INTO _exists
    FROM version
    WHERE id = VersionNumber;

    RETURN _exists;

END$$


DROP PROCEDURE IF EXISTS `coris_update`;$$
CREATE PROCEDURE coris_update()
BEGIN

    IF VERSION_TABLE_EXISTS() = 0 THEN

        CREATE TABLE IF NOT EXISTS version (
          id int(11) NOT NULL,
          install_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- --------------------------------------------------------
        --
        -- Structure de la table `absences`
        --

        CREATE TABLE IF NOT EXISTS `absences` (
          `user_id` int(11) NOT NULL,
          `begin_date` date DEFAULT NULL,
          `end_date` date DEFAULT NULL,
          `id` int(11) NOT NULL AUTO_INCREMENT,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

        -- --------------------------------------------------------

        --
        -- Structure de la table `alert`
        --

        CREATE TABLE IF NOT EXISTS `alert` (
          `campagne_id` int(11) NOT NULL,
          `joueur_id` bigint(20) NOT NULL,
          PRIMARY KEY (`campagne_id`,`joueur_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- --------------------------------------------------------

        --
        -- Structure de la table `campagne`
        --

        CREATE TABLE IF NOT EXISTS `campagne` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `mj_id` int(10) unsigned NOT NULL,
          `nb_joueurs` int(10) unsigned NOT NULL,
          `nb_joueurs_actuel` int(10) unsigned NOT NULL DEFAULT '0',
          `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `banniere` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `systeme` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `univers` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `description` longtext COLLATE utf8_unicode_ci NOT NULL,
          `statut` int(10) unsigned NOT NULL DEFAULT '0',
          `is_recrutement_open` int(1) NOT NULL DEFAULT '1',
          `rythme` int(1) DEFAULT '2',
          `rp` int(1) DEFAULT '1',
          PRIMARY KEY (`id`),
          KEY `IDX_539B5D166A1B4FA4` (`mj_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=40 ;

        ALTER TABLE campagne
        ADD COLUMN
        is_admin_open int(1) default 1;
        -- --------------------------------------------------------

        --
        -- Structure de la table `campagne_config`
        --

        CREATE TABLE IF NOT EXISTS `campagne_config` (
          `campagne_id` int(11) NOT NULL,
          `banniere` varchar(500) DEFAULT NULL,
          `hr` varchar(500) DEFAULT NULL,
          `odd_line_color` varchar(10) DEFAULT NULL,
          `even_line_color` varchar(10) DEFAULT NULL,
          `sidebar_color` varchar(10) DEFAULT NULL,
          `link_color` varchar(10) DEFAULT NULL,
          `template` longtext NOT NULL,
          `sidebar_text` text NOT NULL,
          `link_sidebar_color` varchar(8) NOT NULL,
          PRIMARY KEY (`campagne_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;


        ALTER TABLE  `campagne_config`
        ADD  `text_color` VARCHAR( 10 ) NULL,
        ADD  `default_perso_id` bigint(20) NULL,
        ADD  `template_html` text,
        ADD  `template_img` text,
        ADD  `template_fields` text;

        ALTER TABLE  `campagne_config`
        ADD  `dialogue_color` VARCHAR( 10 ) NULL,
        ADD  `pensee_color` VARCHAR( 10 ) NULL,
        ADD  `rp1_color` VARCHAR( 10 ) NULL,
        ADD  `rp2_color` VARCHAR( 10 ) NULL;


        ALTER TABLE  `campagne_config`
        ADD  `quote_color` VARCHAR( 10 ) NULL;

        ALTER TABLE  `campagne_config`
        ADD  `width` VARCHAR( 8 ) NULL DEFAULT '600px';

        -- --------------------------------------------------------

        --
        -- Structure de la table `campagne_favoris`
        --

        CREATE TABLE IF NOT EXISTS `campagne_favoris` (
          `campagne_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          PRIMARY KEY (`campagne_id`,`user_id`),
          KEY `IDX_FAVORIS_USER` (`user_id`),
          KEY `IDX_FAVORIS_CAMP` (`campagne_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

        -- --------------------------------------------------------

        --
        -- Structure de la table `campagne_participant`
        --

        CREATE TABLE IF NOT EXISTS `campagne_participant` (
          `campagne_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          `statut` int(11) DEFAULT '0',
          PRIMARY KEY (`campagne_id`,`user_id`),
          KEY `IDX_ABB90F7DA76ED395` (`user_id`),
          KEY `IDX_ABB90F7D16227374` (`campagne_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

        -- --------------------------------------------------------

        --
        -- Structure de la table `can_read`
        --

        CREATE TABLE IF NOT EXISTS `can_read` (
          `user_id` int(11) NOT NULL,
          `topic_id` int(11) NOT NULL,
          PRIMARY KEY (`user_id`,`topic_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        -- --------------------------------------------------------

        --
        -- Structure de la table `chat`
        --

        CREATE TABLE IF NOT EXISTS `chat` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(200) DEFAULT NULL,
          `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `message` text,
          PRIMARY KEY (`id`),
          KEY `Date` (`time`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20647 ;

        ALTER TABLE chat ADD COLUMN `to_username` varchar(200) DEFAULT '';

        -- --------------------------------------------------------

        --
        -- Structure de la table `chat_actions`
        --

        CREATE TABLE IF NOT EXISTS `chat_actions` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `actionType` int(11) NOT NULL,
          `messageId` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=203 ;

        -- --------------------------------------------------------

        --
        -- Structure de la table `dicer`
        --

        CREATE TABLE IF NOT EXISTS `dicer` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned NOT NULL,
          `campagne_id` int(10) unsigned NOT NULL,
          `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `result` varchar(500) DEFAULT NULL,
          `description` varchar(900) DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=785 ;

        -- --------------------------------------------------------

        --
        -- Structure de la table `last_action`
        --

        CREATE TABLE IF NOT EXISTS `last_action` (
          `user_id` int(11) NOT NULL,
          `time` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`user_id`),
          KEY `IDX_TIME` (`time`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        -- --------------------------------------------------------

        --
        -- Structure de la table `messages`
        --

        CREATE TABLE IF NOT EXISTS `messages` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `from_id` int(11) NOT NULL,
          `from_username` varchar(200) NOT NULL,
          `title` varchar(200) NOT NULL,
          `content` text NOT NULL,
          `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `statut` int(11) DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `idx_from` (`from_id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=839 ;

        -- --------------------------------------------------------

        --
        -- Structure de la table `messages_to`
        --

        CREATE TABLE IF NOT EXISTS `messages_to` (
          `id_message` int(11) NOT NULL,
          `to_id` int(11) NOT NULL,
          `to_username` varchar(200) NOT NULL,
          `statut` int(11) DEFAULT '0',
          PRIMARY KEY (`id_message`,`to_id`),
          KEY `idx_message` (`id_message`),
          KEY `idx_to` (`to_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1;

        -- --------------------------------------------------------

        --
        -- Structure de la table `note`
        --

        CREATE TABLE IF NOT EXISTS `note` (
          `campagne_id` bigint(20) NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `content` longtext NOT NULL,
          `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`campagne_id`,`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

        -- --------------------------------------------------------

        --
        -- Structure de la table `personnages`
        --

        CREATE TABLE IF NOT EXISTS `personnages` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(10) unsigned DEFAULT NULL,
          `campagne_id` int(10) unsigned NOT NULL,
          `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `concept` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `avatar` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `publicDescription` longtext COLLATE utf8_unicode_ci NOT NULL,
          `privateDescription` longtext COLLATE utf8_unicode_ci NOT NULL,
          `technical` longtext COLLATE utf8_unicode_ci NOT NULL,
          `statut` int(11) NOT NULL DEFAULT '0',
          `cat_id` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `IDX_286738A6A76ED395` (`user_id`),
          KEY `IDX_286738A616227374` (`campagne_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=325 ;


        ALTER TABLE  `personnages`
        ADD  `perso_fields` text;

        -- --------------------------------------------------------

        --
        -- Structure de la table `pnj_category`
        --

        CREATE TABLE IF NOT EXISTS `pnj_category` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `campagne_id` int(11) NOT NULL,
          `name` varchar(200) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

        -- --------------------------------------------------------

        ALTER TABLE pnj_category
        ADD COLUMN
        default_collapse int(1) unsigned NOT NULL DEFAULT 0;

        --
        -- Structure de la table `posts`
        --

        CREATE TABLE IF NOT EXISTS `posts` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `topic_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned DEFAULT NULL,
          `perso_id` int(10) unsigned DEFAULT NULL,
          `content` longtext COLLATE utf8_unicode_ci NOT NULL,
          `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `IDX_885DBAFA1F55203D` (`topic_id`),
          KEY `IDX_885DBAFAA76ED395` (`user_id`),
          KEY `IDX_885DBAFA1221E019` (`perso_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6525 ;

        ALTER TABLE posts
        ADD COLUMN
        editor int(1) DEFAULT 0;


        -- --------------------------------------------------------

        --
        -- Structure de la table `draft`
        --

        CREATE TABLE IF NOT EXISTS `draft` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `topic_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned DEFAULT NULL,
          `perso_id` int(10) unsigned DEFAULT NULL,
          `content` longtext COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`),
          KEY `IDX_DRAFT_TOPIC` (`topic_id`),
          KEY `IDX_DRAFT_USER` (`user_id`),
          KEY `IDX_DRAFT_PERSO` (`perso_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6525 ;


        --
        -- Structure de la table `read_post`
        --

        CREATE TABLE IF NOT EXISTS `read_post` (
          `topic_id` int(10) unsigned NOT NULL,
          `user_id` int(10) unsigned NOT NULL,
          `post_id` int(10) unsigned NOT NULL,
          KEY `IDX_DF7EB0B41F55203D` (`topic_id`),
          KEY `IDX_DF7EB0B4A76ED395` (`user_id`),
          KEY `IDX_DF7EB0B44B89032C` (`post_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

        -- --------------------------------------------------------

        --
        -- Structure de la table `sections`
        --

        CREATE TABLE IF NOT EXISTS `sections` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `campagne_id` int(10) unsigned DEFAULT NULL,
          `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `ordre` int(10) unsigned NOT NULL,
          `default_collapse` int(10) unsigned NOT NULL,
          `banniere` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `IDX_2B96439816227374` (`campagne_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=159 ;

        -- --------------------------------------------------------

        --
        -- Structure de la table `session`
        --

        CREATE TABLE IF NOT EXISTS `session` (
          `session_id` varchar(255) NOT NULL,
          `session_value` text NOT NULL,
          `session_time` int(11) NOT NULL,
          PRIMARY KEY (`session_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        -- --------------------------------------------------------

        --
        -- Structure de la table `topics`
        --

        CREATE TABLE IF NOT EXISTS `topics` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `section_id` int(10) unsigned NOT NULL,
          `last_post_id` int(10) unsigned DEFAULT NULL,
          `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `stickable` int(10) unsigned NOT NULL,
          `is_private` int(10) NOT NULL DEFAULT '0',
          `ordre` int(10) unsigned NOT NULL,
          `is_closed` int(11) DEFAULT '0',
          PRIMARY KEY (`id`),
          KEY `IDX_91F64639D823E37A` (`section_id`),
          KEY `IDX_91F646392D053F64` (`last_post_id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=573 ;

        -- --------------------------------------------------------

        --
        -- Structure de la table `user`
        --

        CREATE TABLE IF NOT EXISTS `user` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `username` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
          `password` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
          `mail` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
          `avatar` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
          `description` longtext COLLATE utf8_unicode_ci NOT NULL,
          `profil` int(11) DEFAULT '0',
          `subscribe_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `birthDate` date DEFAULT NULL,
          `reinitDate` datetime DEFAULT NULL,
          `reinitAlea` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
          `titre` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
          PRIMARY KEY (`id`),
          UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
          UNIQUE KEY `UNIQ_8D93D6495126AC48` (`mail`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=57 ;

        --
        -- Structure de la table `feedback`
        --

        CREATE TABLE IF NOT EXISTS `feedback` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(500) NOT NULL,
          `content` text NOT NULL,
          `vote` int(11) DEFAULT '0',
          `user_id` bigint(20) NOT NULL,
          `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          `closed` int(1) DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE IF NOT EXISTS `feedback_vote` (
          `id` int(10) unsigned NOT NULL,
          `user_id` bigint(20) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        ALTER TABLE  `feedback_vote` ADD PRIMARY KEY (  `id` ,  `user_id` ) ;


        CREATE TABLE IF NOT EXISTS `feedback_comment` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `content` text NOT NULL,
          `user_id` bigint(20) NOT NULL,
          `feedback_id` int(10) NOT NULL,
          `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


        --
        -- Structure de la table `notification`
        --

        CREATE TABLE IF NOT EXISTS `notif` (
          `user_id` bigint(20) NOT NULL,
          `title` varchar(500) NOT NULL,
          `content` text NOT NULL,
          `id` int(11) NOT NULL AUTO_INCREMENT,
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

        ALTER TABLE  `notif` ADD `url` VARCHAR( 500 ) NULL;

        ALTER TABLE  `notif`
        ADD  `type` VARCHAR( 10 ) NULL,
        ADD  `target_id` bigint(20) NULL;

        ALTER TABLE  `notif`
        ADD  `nb` int(10) NULL DEFAULT 1;

        ALTER TABLE  `notif`
        ADD  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;


        --
        -- Structure de la table `annonce`
        --

        CREATE TABLE IF NOT EXISTS `annonce` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(500) NOT NULL,
          `content` text NOT NULL,
          `create_date` timestamp NOT NULL,
          `end_date` timestamp NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        --
        -- Contraintes pour les tables exportées
        --

        --
        -- Contraintes pour la table `campagne`
        --
        ALTER TABLE `campagne`
          ADD CONSTRAINT `FK_539B5D166A1B4FA4` FOREIGN KEY (`mj_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `campagne_favoris`
        --
        ALTER TABLE `campagne_favoris`
          ADD CONSTRAINT `FK_FAVORIS_CAMP` FOREIGN KEY (`campagne_id`) REFERENCES `campagne` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_FAVORIS_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `campagne_participant`
        --
        ALTER TABLE `campagne_participant`
          ADD CONSTRAINT `FK_ABB90F7D16227374` FOREIGN KEY (`campagne_id`) REFERENCES `campagne` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_ABB90F7DA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `personnages`
        --
        ALTER TABLE `personnages`
          ADD CONSTRAINT `FK_286738A616227374` FOREIGN KEY (`campagne_id`) REFERENCES `campagne` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_286738A6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `posts`
        --
        ALTER TABLE `posts`
          ADD CONSTRAINT `FK_885DBAFA1221E019` FOREIGN KEY (`perso_id`) REFERENCES `personnages` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_885DBAFA1F55203D` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_885DBAFAA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `draft`
        --
        ALTER TABLE `draft`
          ADD CONSTRAINT `FK_DRAFT_PERSO` FOREIGN KEY (`perso_id`) REFERENCES `personnages` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_DRAFT_TOPIC` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`) ON DELETE CASCADE,
          ADD CONSTRAINT `FK_DRAFT_USER` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `read_post`
        --
        ALTER TABLE `read_post`
          ADD CONSTRAINT `FK_DF7EB0B41F55203D` FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`),
          ADD CONSTRAINT `FK_DF7EB0B4A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

        --
        -- Contraintes pour la table `sections`
        --
        ALTER TABLE `sections`
          ADD CONSTRAINT `FK_2B96439816227374` FOREIGN KEY (`campagne_id`) REFERENCES `campagne` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `topics`
        --
        ALTER TABLE `topics`
          ADD CONSTRAINT `FK_91F646392D053F64` FOREIGN KEY (`last_post_id`) REFERENCES `posts` (`id`),
          ADD CONSTRAINT `FK_91F64639D823E37A` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

        --
        -- Contraintes pour la table `notif`
        --
        ALTER TABLE `notif`
          ADD CONSTRAINT `FK_NOTIF_01` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;


        --
        -- Mise à jour de la version en base de donnée
        --
        INSERT INTO version (ID) VALUES (1);

    END IF;
END$$

CALL coris_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(2) = 0 THEN
       -- Your update or alter
      ALTER TABLE campagne
      ADD `is_multi_character` int(1) NOT NULL DEFAULT '0';

      INSERT INTO version (ID) VALUES (2);
    END IF;
END$$

CALL jdroll_update();


DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(3) = 0 THEN

        CREATE TABLE IF NOT EXISTS theme (
          id int(11) NOT NULL AUTO_INCREMENT,
          title varchar(50) NOT NULL,
          odd_line_color varchar(10) DEFAULT NULL,
          even_line_color varchar(10) DEFAULT NULL,
          sidebar_color varchar(10) DEFAULT NULL,
          link_color varchar(10) DEFAULT NULL,
          link_sidebar_color varchar(8) NOT NULL,
          text_color VARCHAR( 10 ) NULL,
          dialogue_color VARCHAR( 10 ) NULL,
          pensee_color VARCHAR( 10 ) NULL,
          rp1_color VARCHAR( 10 ) NULL,
          rp2_color VARCHAR( 10 ) NULL,
          quote_color VARCHAR( 10 ) NULL,
          PRIMARY KEY (id)
        );

        /* To insert theme

INSERT INTO theme
(title, odd_line_color, even_line_color, sidebar_color, link_color, link_sidebar_color, text_color, dialogue_color, pensee_color, rp1_color, rp2_color, quote_color)
SELECT
'Rouge Sang',
odd_line_color, even_line_color, sidebar_color, link_color, link_sidebar_color, text_color, dialogue_color, pensee_color, rp1_color, rp2_color, quote_color
 FROM campagne_config WHERE campagne_id = 3

        */

      INSERT INTO version (ID) VALUES (3);
    END IF;
END$$

CALL jdroll_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(4) = 0 THEN

      ALTER TABLE user
      ADD `notif_mp` int(1) NOT NULL DEFAULT '1',
      ADD `notif_inscription` int(1) NOT NULL DEFAULT '1',
      ADD `notif_perso` int(1) NOT NULL DEFAULT '1',
      ADD `notif_message` int(1) NOT NULL DEFAULT '1',
      ADD `mail_mp` int(1) NOT NULL DEFAULT '0',
      ADD `mail_inscription` int(1) NOT NULL DEFAULT '0',
      ADD `mail_perso` int(1) NOT NULL DEFAULT '0',
      ADD `mail_message` int(1) NOT NULL DEFAULT '1';

      INSERT INTO version (ID) VALUES (4);
    END IF;
END$$

CALL jdroll_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(5) = 0 THEN

      ALTER TABLE `personnages` ADD `widgets` MEDIUMTEXT NOT NULL;
      ALTER TABLE `campagne_config` ADD `widgets` MEDIUMTEXT NOT NULL;

      INSERT INTO version (ID) VALUES (5);
    END IF;
END$$

CALL jdroll_update();



DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(6) = 0 THEN

        CREATE TABLE `carte` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `campagne_id` INT NULL,
          `name` VARCHAR(45) NULL,
          `description` TEXT NULL,
          `image` TEXT NULL,
          `published` tinyint(1) DEFAULT '1',
          `config` LONGTEXT NULL,
          PRIMARY KEY (`id`),
          INDEX `campagne` (`campagne_id` ASC)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

      INSERT INTO version (ID) VALUES (6);
    END IF;
END$$

CALL jdroll_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(7) = 0 THEN

      ALTER TABLE  `campagne_config`
      ADD  `default_dice` VARCHAR(50) NULL;

      INSERT INTO version (ID) VALUES (7);
    END IF;
END$$

CALL jdroll_update();


DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(8) = 0 THEN

      ALTER TABLE note DROP PRIMARY KEY, ADD INDEX  `IDX_NOTES` (  `campagne_id` ,  `user_id` );
      ALTER TABLE note ADD id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;

      INSERT INTO version (ID) VALUES (8);
    END IF;
END$$

CALL jdroll_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    IF VERSION_EXISTS(9) = 0 THEN

ALTER TABLE user
      ADD `notif_dice` int(1) NOT NULL DEFAULT '1',
      ADD `mail_dice` int(1) NOT NULL DEFAULT '0';

      INSERT INTO version (ID) VALUES (9);
    END IF;
END$$

CALL jdroll_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
IF VERSION_EXISTS(10) = 0 THEN

ALTER TABLE `session` ADD `sess_lifetime` INT( 11 ) NOT NULL ;
INSERT INTO version (ID) VALUES (10);

END IF;
END$$

CALL jdroll_update();

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
IF VERSION_EXISTS(10) = 0 THEN

ALTER TABLE absences
ADD commentaire VARCHAR(200);

END IF;
END$$

CALL jdroll_update();


/*
Bloc exemple à dupliquer dans le cas d'un nouveau Bloc exemple

DROP PROCEDURE IF EXISTS `jdroll_update`;$$
CREATE PROCEDURE jdroll_update()
BEGIN
    -- FIXIT - Change XXXXX by version
    IF VERSION_EXISTS(XXXXX) = 0 THEN
       -- Your update or alter

      INSERT INTO version (ID) VALUES (XXXX);
    END IF;
END$$

CALL jdroll_update();

*/
