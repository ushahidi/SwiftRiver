-- -----------------------------------------------------
-- Version - 001
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Make Sure We're Using UTF8 Encoding
-- -----------------------------------------------------
ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


-- -----------------------------------------------------
-- Table `user_subscriptions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_subscriptions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `river_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `bucket_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `subscription_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  INDEX `river_id_idx` (`river_id` ASC) ,
  INDEX `bucket_id_idx` (`bucket_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Track subscriptions to rivers and/or buckets' ;


-- -----------------------------------------------------
-- Table `user_followers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_followers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `follower_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `follower_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  INDEX `follower_id_idx` (`follower_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Track followers' ;


-- -----------------------------------------------------
-- Table `tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `tag_type` varchar(255) DEFAULT NULL,
  `tag_source` varchar(100) DEFAULT NULL,
  `tag_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_un_tag_tag_type` (`tag`,`tag_type`),
  KEY `tag_idx` (`tag`),
  KEY `tag_type_idx` (`tag_type`),
  KEY `tag_source_idx` (`tag_source`),
  KEY `tag_date_add_idx` (`tag_date_add`)
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `snapshots`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `snapshots` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `snapshot_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets_links`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `droplets_links` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner of this relationship - \'0\' for System (global)' ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `link_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `droplet_id_idx` (`droplet_id` ASC) ,
  INDEX `link_id_idx` (`link_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `rivers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `rivers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `river_name` VARCHAR(255) NOT NULL ,
  `river_active` TINYINT(4) NOT NULL DEFAULT 1 ,
  `river_public` TINYINT(4) NOT NULL DEFAULT 0 ,
  `river_current` TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Identifies if this is the last River that  was worked on' ,
  `river_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `account_id_idx` (`account_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `media`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `media` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `media_name` VARCHAR(255) NULL DEFAULT NULL ,
  `media_file` VARCHAR(255) NULL DEFAULT NULL ,
  `media_thumb` VARCHAR(255) NULL DEFAULT NULL ,
  `media_mime` VARCHAR(50) NULL DEFAULT NULL ,
  `media_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `media_file_idx` (`media_file` ASC) ,
  INDEX `media_mime_idx` (`media_mime` ASC) ,
  INDEX `media_date_add_idx` (`media_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets_places`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `droplets_places` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner of this relationship - \'0\' for System (global)' ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `place_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `droplet_id_idx` (`droplet_id` ASC) ,
  INDEX `place_id_idx` (`place_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `accounts_droplets_media`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `accounts_droplets_media` (
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `droplets_media_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  INDEX `account_id_idx` (`account_id` ASC) ,
  INDEX `droplets_media_id_idx` (`droplets_media_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `rivers_droplets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `rivers_droplets` (
  `river_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `droplet_id` BIGINT UNSIGNED NOT NULL DEFAULT 0 ,
  INDEX `river_id_idx` (`river_id` ASC) ,
  INDEX `droplet_id_idx` (`droplet_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `discussions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `discussions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `discussion_title` VARCHAR(255) NOT NULL ,
  `discussion_content` TEXT NOT NULL ,
  `discussion_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `discussion_date_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `discussion_sticky` TINYINT(4) NOT NULL DEFAULT 0 ,
  `discussion_deleted` TINYINT(4) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `discussion_date_add_idx` (`discussion_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `places`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `places` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `place_name` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Full place name' ,
  `place_point` POINT NULL DEFAULT NULL COMMENT 'POINT geometry for this place' ,
  `place_source` VARCHAR(100) NULL DEFAULT NULL ,
  `place_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `place_name_idx` (`place_name` ASC) ,
  INDEX `place_point_idx` (`place_point` ASC) ,
  INDEX `place_source_idx` (`place_source` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `user_identities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_identities` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `provider` VARCHAR(255) NOT NULL ,
  `identity` VARCHAR(255) NOT NULL ,
  `identity_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `buckets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `buckets` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `user_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Creator of this bucket' ,
  `bucket_name` VARCHAR(255) NULL DEFAULT NULL ,
  `bucket_description` TEXT NULL DEFAULT NULL ,
  `bucket_publish` TINYINT(4) NOT NULL DEFAULT 0 ,
  `bucket_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `bucket_date_add_idx` (`bucket_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `roles` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(32) NOT NULL ,
  `description` VARCHAR(255) NULL DEFAULT NULL ,
  `permissions` VARCHAR(255) NULL DEFAULT NULL ,
  `user_id` INT(11) NULL DEFAULT NULL ,
  UNIQUE INDEX (`name` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `identities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `identities` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `channel` VARCHAR(100) NOT NULL ,
  `parent_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID of the parent for revision tracking' ,
  `identity_orig_id` VARCHAR(255) NULL DEFAULT NULL ,
  `identity_username` VARCHAR(255) NULL DEFAULT NULL ,
  `identity_name` VARCHAR(255) NULL DEFAULT NULL ,
  `identity_avatar` VARCHAR(255) NULL DEFAULT NULL ,
  `identity_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `identity_date_modified` DATETIME NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `identity_orig_id_idx` (`identity_orig_id` ASC) ,
  INDEX `identity_date_add_idx` (`identity_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Sources are individual People, Organizations or Websites tha' ;


-- -----------------------------------------------------
-- Table `snapshot_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `snapshot_options` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `snapshot_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `key` VARCHAR(255) NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `snapshot_id_idx` (`snapshot_id` ASC) ,
  INDEX `key_idx` (`key` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



-- -----------------------------------------------------
-- Table `links`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `links` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `link` VARCHAR(255) NULL DEFAULT NULL ,
  `link_full` VARCHAR(255) NULL DEFAULT NULL ,
  `link_domain` VARCHAR(255) NULL DEFAULT NULL ,
  `link_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `link_idx` (`link` ASC) ,
  INDEX `link_full_idx` (`link_full` ASC) ,
  INDEX `link_domain_idx` (`link_domain` ASC) ,
  INDEX `link_date_add_idx` (`link_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `plugins`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugins` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `plugin_path` VARCHAR(100) NOT NULL ,
  `plugin_name` VARCHAR(255) NULL DEFAULT NULL ,
  `plugin_description` VARCHAR(255) NULL DEFAULT NULL ,
  `plugin_enabled` TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Global Plugin Enabled/Disable' ,
  `plugin_weight` TINYINT(4) NOT NULL DEFAULT 1 COMMENT 'Lower digit = higher priority' ,
  UNIQUE INDEX (`plugin_path` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;



-- -----------------------------------------------------
-- Table `channel_filters`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `channel_filters` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `channel` VARCHAR(100) NOT NULL ,
  `river_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `filter_name` VARCHAR(255) NULL DEFAULT NULL ,
  `filter_description` VARCHAR(255) NULL DEFAULT NULL ,
  `filter_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `filter_date_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `filter_lastrun` INT(11) NOT NULL DEFAULT 0 ,
  `filter_nextrun` INT(11) NOT NULL DEFAULT 0 ,
  `filter_runs` INT(11) NOT NULL DEFAULT 0 ,
  `filter_schedule` VARCHAR(30) NULL DEFAULT '*:-1:*:*:*' ,
  `filter_enabled` TINYINT(4) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`id`) ,
  INDEX `filter_date_add_idx` (`filter_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Filters generate droplets from channels' ;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `email` VARCHAR(127) NOT NULL ,
  `name` VARCHAR(255) NULL DEFAULT NULL ,
  `username` VARCHAR(255) NOT NULL ,
  `password` VARCHAR(255) NOT NULL ,
  `logins` INT(10) UNSIGNED NOT NULL DEFAULT 0 ,
  `last_login` INT(10) UNSIGNED NULL DEFAULT NULL ,
  UNIQUE INDEX (`email` ASC) ,
  UNIQUE INDEX (`username` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `user_tokens`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_tokens` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL ,
  `user_agent` VARCHAR(40) NOT NULL ,
  `token` VARCHAR(32) NOT NULL ,
  `created` INT(10) UNSIGNED NOT NULL ,
  `expires` INT(10) UNSIGNED NOT NULL ,
  UNIQUE INDEX (`token` ASC) ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  CONSTRAINT `user_tokens_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `roles_users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `roles_users` (
  `user_id` INT(11) UNSIGNED NOT NULL ,
  `role_id` INT(11) UNSIGNED NOT NULL ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`user_id`, `role_id`) ,
  INDEX `account_id_idx` (`account_id` ASC) ,
  INDEX `role_id_idxfk` (`role_id` ASC) ,
  CONSTRAINT `roles_users_ibfk_1`
    FOREIGN KEY (`user_id` )
    REFERENCES `users` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `role_id_idxfk`
    FOREIGN KEY (`role_id` )
    REFERENCES `roles` (`id` )
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `accounts_plugins`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `accounts_plugins` (
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `plugin_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  INDEX `account_id_idx` (`account_id` ASC) ,
  INDEX `plugin_id_idx` (`plugin_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `buckets_droplets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `buckets_droplets` (
  `bucket_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  INDEX `bucket_id_idx` (`bucket_id` ASC) ,
  INDEX `droplet_id_idx` (`droplet_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `droplets` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `parent_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'ID of the parent for revision tracking' ,
  `identity_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'The source to which this item is connected (e.g. @ushahidi, +254123456)' ,
  `channel` VARCHAR(100) NOT NULL ,
  `droplet_hash` VARCHAR(64) NOT NULL ,
  `droplet_orig_id` VARCHAR(255) NOT NULL ,
  `droplet_type` VARCHAR(100) NOT NULL DEFAULT 'original' COMMENT 'original, retweet, comment, revision' ,
  `droplet_title` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Title of the feed item if available' ,
  `droplet_content` TEXT NULL DEFAULT NULL COMMENT 'The content of the feed item (if available)' ,
  `droplet_raw` TEXT NULL DEFAULT NULL ,
  `droplet_locale` VARCHAR(30) NULL DEFAULT NULL COMMENT 'Local of the feed item (e.g. en (English), fr (French))' ,
  `droplet_date_pub` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT 'Original publish date of the feed item' ,
  `droplet_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT 'Date the feed item was added to this database' ,
  `droplet_processed` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `droplet_hash_idx` (`droplet_hash` ASC) ,
  INDEX `droplet_type_idx` (`droplet_type` ASC) ,
  INDEX `droplet_date_pub_idx` (`droplet_date_pub` ASC) ,
  INDEX `droplet_date_add_idx` (`droplet_date_add` ASC) ,
  INDEX `droplet_processed_idx` (`droplet_processed` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Items are generated from feeds and are attached to a specifi' ;


-- -----------------------------------------------------
-- Table `settings`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(100) NOT NULL ,
  `value` VARCHAR(255) NULL DEFAULT NULL ,
  UNIQUE INDEX (`key` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sources`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sources` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `source_name` VARCHAR(255) NULL DEFAULT NULL ,
  `source_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `source_date_modified` DATETIME NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `source_date_add_idx` (`source_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `accounts`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `accounts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner of this account. Transferable.' ,
  `account_path` VARCHAR(100) NULL DEFAULT NULL ,
  `account_private` TINYINT(4) NOT NULL DEFAULT 0 ,
  `account_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `account_date_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `account_active` TINYINT(4) NOT NULL DEFAULT 1 ,
  UNIQUE INDEX (`account_path` ASC) ,
  PRIMARY KEY (`id`) ,
  INDEX `account_path_idx` (`account_path` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `sources_identities`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `sources_identities` (
  `source_id` BIGINT(20) UNSIGNED NOT NULL ,
  `identity_id` BIGINT(20) UNSIGNED NOT NULL ,
  INDEX `source_id_idx` (`source_id` ASC) ,
  INDEX `identity_id_idx` (`identity_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets_media`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `droplets_media` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner of this relationship - \'0\' for System (global)' ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `media_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `droplet_id_idx` (`droplet_id` ASC) ,
  INDEX `media_id_idx` (`media_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `channel_filter_options`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `channel_filter_options` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `channel_filter_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `key` VARCHAR(255) NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  `type` VARCHAR(100) NULL DEFAULT 'text' COMMENT 'Field type. Text, Password, Radio, Checkbox' ,
  PRIMARY KEY (`id`) ,
  INDEX `channel_filter_id_idx` (`channel_filter_id` ASC) ,
  INDEX `key_idx` (`key` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Channel filter options' ;


-- -----------------------------------------------------
-- Table `droplets_tags`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `droplets_tags` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Owner of this relationship - \'0\' for System (global)' ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `tag_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `droplet_id_idx` (`droplet_id` ASC) ,
  INDEX `tag_id_idx` (`tag_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `user_actions`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `user_actions` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `action` VARCHAR(255) NOT NULL ,
  `action_on` VARCHAR(100) NOT NULL COMMENT 'river, bucket, droplet, filter, snapshot, source, identity' ,
  `action_on_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `action_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) ,
  INDEX `action_on_idx` (`action_on` ASC) ,
  INDEX `action_on_id_idx` (`action_on_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Tracks user actions across the system' ;


-- -----------------------------------------------------
-- Create syntax for TABLE 'account_droplet_tags'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_droplet_tags` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UN_ACCOUNT_DROPLET_TAG` (`account_id`,`droplet_id`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Create syntax for TABLE 'account_droplet_links'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_droplet_links` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `link_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UN_ACCOUNT_DROPLET_LINK` (`account_id`,`droplet_id`,`link_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Create syntax for TABLE 'account_droplet_places'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_droplet_places` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `place_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`,`droplet_id`,`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------------------
-- TABLE 'bucket_collaborators'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `bucket_collaborators` (
	`id` bigint(20) NOT NULL AUTO_INCREMENT,
	`user_id` bigint(20) unsigned NOT NULL,
	`bucket_id` bigint(20) unsigned NOT NULL,
	`collaboration_active` smallint(1) NOT NULL DEFAULT 0,
	PRIMARY KEY(`id`),
	UNIQUE KEY `UNQ_BUCKET_COLLAB` (`user_id`, `bucket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Data for table `roles`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `roles` (`id`, `name`, `description`, `permissions`) VALUES (1, 'login', 'Login privileges, granted after account confirmation', NULL);
INSERT INTO `roles` (`id`, `name`, `description`, `permissions`) VALUES (2, 'admin', 'Super Administrator', NULL);
INSERT INTO `roles` (`id`, `name`, `description`, `permissions`) VALUES (3, 'owner', NULL, NULL);
INSERT INTO `roles` (`id`, `name`, `description`, `permissions`) VALUES (4, 'editor', NULL, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `settings`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (1, 'site_name', 'SwiftRiver');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (2, 'site_theme', 'default');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (3, 'site_locale', 'en');

COMMIT;

-- -----------------------------------------------------
-- Data for table `users`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `users` (`id`, `email`, `name`, `username`, `password`, `logins`, `last_login`) VALUES (1, 'myswiftriver@myswiftriver.com', 'Administrator', 'admin', 'c2bac288881c7dd9531c607e73b3af798499917760023656e9847b10b8e75542', 0, NULL);

COMMIT;

-- -----------------------------------------------------
-- Data for table `roles_users`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `roles_users` (`user_id`, `role_id`, `account_id`) VALUES (1, 1, 1);
INSERT INTO `roles_users` (`user_id`, `role_id`, `account_id`) VALUES (1, 2, 1);

COMMIT;

-- -----------------------------------------------------
-- Data for table `accounts`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `accounts` (`user_id`, `account_path`) VALUES (1, 'default');

COMMIT;
