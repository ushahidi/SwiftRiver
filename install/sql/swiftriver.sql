-- -----------------------------------------------------
-- Version - 001
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Make Sure We're Using UTF8 Encoding
-- -----------------------------------------------------
ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- -----------------------------------------------------
-- Table `roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
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
-- Table `plugins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugins` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `plugin_path` VARCHAR(100) NOT NULL ,
  `plugin_name` VARCHAR(255) NULL DEFAULT NULL ,
  `plugin_description` VARCHAR(255) NULL DEFAULT NULL ,
  `plugin_enabled` TINYINT(4) NOT NULL DEFAULT 0 COMMENT 'Global Plugin Enabled/Disable' ,
  `plugin_weight` TINYINT(4) NOT NULL DEFAULT 1 COMMENT 'Lower digit = higher priority' ,
  `plugin_installed` TINYINT(4) NOT NULL DEFAULT 0 ,
  UNIQUE INDEX (`plugin_path` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `riverid` char(128) NOT NULL DEFAULT '',
  `email` varchar(127) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `invites` smallint(6) NOT NULL DEFAULT '10',
  `last_login` int(10) unsigned DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `un_api_key` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `roles_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `account_id_idx` (`account_id`),
  KEY `role_id_idxfk` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `settings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `key` VARCHAR(100) NOT NULL ,
  `value` LONGTEXT NULL DEFAULT NULL ,
  UNIQUE INDEX (`key` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `accounts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Owner of this account. Transferable.',
  `account_path` varchar(100) DEFAULT NULL,
  `account_private` tinyint(4) NOT NULL DEFAULT '0',
  `account_date_add` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account_date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account_active` tinyint(4) NOT NULL DEFAULT '1',
  `river_quota_remaining` INT  NULL  DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_path` (`account_path`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------------------
-- TABLE 'twitter_crawls'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `twitter_crawls` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `river_id` bigint(11) DEFAULT NULL,
  `request_hash` text,
  `refresh_url` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `twitter_crawls_un_riverid` (`river_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'auth_tokens'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(20) DEFAULT NULL,
  `data` text,
  `created_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
  `expire_date` TIMESTAMP DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_tokens_un_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

START TRANSACTION;

-- -----------------------------------------------------
-- Data for table `roles`
-- -----------------------------------------------------
INSERT INTO `roles` (`id`, `name`, `description`, `permissions`) VALUES 
(1, 'login', 'Login privileges, granted after account confirmation', NULL),
(2, 'admin', 'Super Administrator', NULL);


-- -----------------------------------------------------
-- Data for table `settings`
-- -----------------------------------------------------
INSERT INTO `settings` (`id`, `key`, `value`) VALUES 
(1, 'site_name', 'SwiftRiver'),
(2, 'site_theme', 'default'),
(3, 'site_locale', 'en'),
(4, 'public_registration_enabled', '0'),
(5, 'anonymous_access_enabled', '0'),
(6, 'default_river_lifetime', '14'),
(7, 'river_expiry_notice_period', '3'),
(8, 'general_invites_enabled', '0'),
(9, 'default_river_quota', '1'),
(10, 'default_river_drop_quota', '10000'),
(11, 'site_url', 'http://www.example.com'),
(12, 'email_domain', 'example.com'),
(13, 'comments_email_domain', 'example.com');

-- -----------------------------------------------------
-- Data for table `users`
-- -----------------------------------------------------
INSERT INTO `users` (`id`, `email`, `name`, `username`, `password`, `logins`, `last_login`, `api_key`) VALUES 
(1, 'myswiftriver@myswiftriver.com', 'Administrator', 'admin', 'c2bac288881c7dd9531c607e73b3af798499917760023656e9847b10b8e75542', 0, NULL, md5(rand())),
(2, 'public@myswiftriver.com', 'public', 'public', '', 0, NULL, '');

-- -----------------------------------------------------
-- Data for table `roles_users`
-- -----------------------------------------------------
INSERT INTO `roles_users` (`user_id`, `role_id`, `account_id`) VALUES 
(1, 1, 1),
(1, 2, 1);

-- -----------------------------------------------------
-- Data for table `accounts`
-- -----------------------------------------------------
INSERT INTO `accounts` (`user_id`, `account_path`) VALUES 
(1, 'default'),
(2, 'public');

-- -----------------------------------------------------
-- Data for table `plugins`
-- -----------------------------------------------------
INSERT INTO `plugins` (`id`, `plugin_path`, `plugin_name`, `plugin_description`, `plugin_enabled`, `plugin_weight`, `plugin_installed`)
VALUES
	(1,'rss','RSS','Adds an RSS/Atom channel to SwiftRiver to parse RSS and Atom Feeds.',1,1,0),
	(2,'twitter','Twitter','Adds a Twitter channel to SwiftRiver.',1,1,0);

COMMIT;