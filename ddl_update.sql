delimiter $$

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