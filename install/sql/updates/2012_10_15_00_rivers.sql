ALTER TABLE `rivers` DROP `expiry_extension_token`;
ALTER TABLE `rivers` CHANGE `expiry_candidate` `expiry_notification_sent` TINYINT(1)  NOT NULL  DEFAULT '0';
ALTER TABLE `rivers` ADD `river_full` TINYINT(1)  NOT NULL  DEFAULT '0';


