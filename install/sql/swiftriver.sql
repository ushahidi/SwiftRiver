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
CREATE TABLE IF NOT EXISTS `user_subscriptions` (
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
CREATE TABLE IF NOT EXISTS `user_followers` (
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `follower_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `follower_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  UNIQUE KEY `user_id` (`user_id`,`follower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Track followers';


-- -----------------------------------------------------
-- Table `tags`
-- -----------------------------------------------------
CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `tag_type` varchar(255) DEFAULT NULL,
  `tag_source` varchar(100) DEFAULT NULL,
  `tag_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `tag_hash` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_un_tag_hash` (`tag_hash`),
  UNIQUE KEY `tags_un_tag_tag_type` (`tag`,`tag_type`),
  KEY `tag_idx` (`tag`),
  KEY `tag_type_idx` (`tag_type`),
  KEY `tag_source_idx` (`tag_source`),
  KEY `tag_date_add_idx` (`tag_date_add`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `snapshots`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `snapshots` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `snapshot_name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets_links`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `droplets_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `link_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `droplets_links_id` (`droplet_id`,`link_id`),
  KEY `droplet_id_idx` (`droplet_id`),
  KEY `link_id_idx` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `rivers`
-- -----------------------------------------------------
CREATE TABLE `rivers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `river_name` varchar(25) NOT NULL DEFAULT '',
  `river_name_url` varchar(30) NOT NULL DEFAULT '',
  `river_active` tinyint(4) NOT NULL DEFAULT '1',
  `river_public` tinyint(4) NOT NULL DEFAULT '0',
  `river_current` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Identifies if this is the last River that  was worked on',
  `default_layout` varchar(10) DEFAULT 'drops',
  `river_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_river_name_url` (`account_id`,`river_name_url`),
  KEY `river_name_url` (`river_name_url`),
  KEY `account_id_idx` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `media` VARCHAR(255) NOT NULL DEFAULT '',
  `media_hash` VARCHAR(32) NOT NULL DEFAULT '',
  `media_type` VARCHAR(50) NOT NULL DEFAULT 'image',
  `media_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  INDEX `media_date_add_idx` (`media_date_add` ASC),
  INDEX `media_hash_idx` (`media_hash` ASC),
  INDEX `media_type_idx` (`media_type` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets_places`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `droplets_places` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `place_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `droplets_places_id` (`droplet_id`,`place_id`),
  KEY `droplet_id_idx` (`droplet_id`),
  KEY `place_id_idx` (`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `account_droplet_media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS  `account_droplet_media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `media_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`,`droplet_id`,`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `rivers_droplets`
-- -----------------------------------------------------
CREATE TABLE `rivers_droplets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `river_id` int(11) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rivers_droplet_un_river_droplet` (`river_id`,`droplet_id`),
  KEY `river_id_idx` (`river_id`),
  KEY `droplet_id_idx` (`droplet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bucket_id` INT(11) unsigned NOT NULL DEFAULT 0,  
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `comment_content` TEXT NOT NULL ,
  `comment_date_add` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `comment_date_modified` DATETIME NOT NULL DEFAULT '1000-01-01 00:00:00' ,
  `comment_sticky` TINYINT(4) NOT NULL DEFAULT 0 ,
  `comment_deleted` TINYINT(4) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `comment_date_add_idx` (`comment_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `places`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `places` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `place_name` varchar(255) DEFAULT NULL COMMENT 'Full place name',
  `place_point` point DEFAULT NULL COMMENT 'POINT geometry for this place',
  `place_source` varchar(100) DEFAULT NULL,
  `place_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `places_un_name_point` (`place_name`,`place_point`(25)),
  KEY `place_name_idx` (`place_name`),
  KEY `place_point_idx` (`place_point`(25)),
  KEY `place_source_idx` (`place_source`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `account_droplet_links`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS  `account_droplet_links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `link_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`,`droplet_id`,`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `user_identities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_identities` (
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
CREATE TABLE `buckets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Creator of this bucket',
  `bucket_name` varchar(25) NOT NULL DEFAULT '',
  `bucket_name_url` varchar(30) NOT NULL DEFAULT '',
  `bucket_description` text,
  `bucket_publish` tinyint(4) NOT NULL DEFAULT '0',
  `default_layout` varchar(10) DEFAULT 'drops',
  `bucket_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_bucket_name` (`account_id`,`bucket_name`),
  UNIQUE KEY `un_bucket_name_url` (`account_id`,`bucket_name_url`),
  KEY `bucket_date_add_idx` (`bucket_date_add`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
-- Table `identities`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `identities` (
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
CREATE TABLE IF NOT EXISTS `snapshot_options` (
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
-- Table `account_droplet_places`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_droplet_places` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `place_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`,`droplet_id`,`place_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `links`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(2048) NOT NULL DEFAULT '',
  `url_hash` varchar(32) NOT NULL DEFAULT '',
  `domain` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_hash` (`url_hash`),
  KEY `domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  UNIQUE INDEX (`plugin_path` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `account_droplet_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_droplet_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`,`droplet_id`,`tag_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `channel_filters`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `channel_filters` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(100) NOT NULL,
  `river_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `filter_name` varchar(255) DEFAULT NULL,
  `filter_description` varchar(255) DEFAULT NULL,
  `filter_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `filter_date_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `filter_last_run` datetime NOT NULL,
  `filter_last_successful_run` datetime NOT NULL,
  `filter_runs` int(11) NOT NULL DEFAULT '0',
  `filter_enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filter_date_add_idx` (`filter_date_add`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Filters generate droplets from channels';


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `riverid` varchar(255) NOT NULL,
  `email` varchar(127) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `un_api_key` (`api_key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `user_tokens`
-- -----------------------------------------------------
CREATE TABLE `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(64) NOT NULL,
  `token` varchar(64) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id_idx` (`user_id`)
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
-- Table `accounts_plugins`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `accounts_plugins` (
  `account_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `plugin_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  INDEX `account_id_idx` (`account_id` ASC) ,
  INDEX `plugin_id_idx` (`plugin_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `buckets_droplets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `buckets_droplets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bucket_id` int(11) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `droplet_date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date when the droplet was added to the bucket',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bucket_droplet_un_bucket_droplet` (`bucket_id`,`droplet_id`),
  KEY `bucket_id_idx` (`bucket_id`),
  KEY `droplet_id_idx` (`droplet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `droplets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `droplets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'ID of the parent for revision tracking',
  `identity_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'The source to which this item is connected (e.g. @ushahidi, +254123456)',
  `channel` varchar(100) NOT NULL,
  `channel_filter_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'The feed that generated this item (e.g. BBCNews RSS feed, #Haiti)',
  `droplet_hash` varchar(64) NOT NULL,
  `droplet_orig_id` varchar(255) NOT NULL,
  `droplet_type` varchar(100) NOT NULL DEFAULT 'original' COMMENT 'original, retweet, comment, revision',
  `droplet_title` varchar(255) DEFAULT NULL COMMENT 'Title of the feed item if available',
  `droplet_content` text COMMENT 'The content of the feed item (if available)',
  `droplet_raw` text,
  `droplet_locale` varchar(30) DEFAULT NULL COMMENT 'Local of the feed item (e.g. en (English), fr (French))',
  `droplet_date_pub` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT 'Original publish date of the feed item',
  `droplet_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT 'Date the feed item was added to this database',
  `droplet_processed` tinyint(1) NOT NULL DEFAULT '0',
  `semantics_complete` tinyint(1) DEFAULT '0',
  `links_complete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_identity_id_channel_orig_id` (`identity_id`, `channel`, `droplet_orig_id`),
  UNIQUE KEY `droplet_hash_idx` (`droplet_hash`),
  KEY `droplet_type_idx` (`droplet_type`),
  KEY `droplet_date_pub_idx` (`droplet_date_pub`),
  KEY `droplet_date_add_idx` (`droplet_date_add`),
  KEY `droplet_processed_idx` (`droplet_processed`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4867 DEFAULT CHARSET=utf8 COMMENT='Items are generated from feeds and are attached to a specifi';

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
-- Table `sources`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sources` (
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
CREATE TABLE IF NOT EXISTS `accounts` (
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
CREATE TABLE IF NOT EXISTS `sources_identities` (
  `source_id` BIGINT(20) UNSIGNED NOT NULL ,
  `identity_id` BIGINT(20) UNSIGNED NOT NULL ,
  INDEX `source_id_idx` (`source_id` ASC) ,
  INDEX `identity_id_idx` (`identity_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `droplets_media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `droplets_media` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `droplet_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  `media_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE KEY `droplets_media_id` (`droplet_id`, `media_id`),
  INDEX `droplet_id_idx` (`droplet_id` ASC) ,
  INDEX `media_id_idx` (`media_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `channel_filter_options`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `channel_filter_options` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `channel_filter_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `key` VARCHAR(255) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `channel_filter_id_idx` (`channel_filter_id` ASC) ,
  INDEX `key_idx` (`key` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Channel filter options' ;


-- -----------------------------------------------------
-- Table `droplets_tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `droplets_tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `droplets_tags_id` (`droplet_id`,`tag_id`),
  KEY `droplet_id_idx` (`droplet_id`),
  KEY `tag_id_idx` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `user_actions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_actions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `action` varchar(255) NOT NULL,
  `action_on` varchar(100) NOT NULL DEFAULT '',
  `action_on_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'river, bucket, droplet, filter, snapshot, source, identity',
  `action_to_id` bigint(20) DEFAULT NULL,
  `action_date_add` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `confirmed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `action_on_idx` (`action_to_id`),
  KEY `action_on_id_idx` (`action_on_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='Tracks user actions across the system';



-- ----------------------------------------
-- TABLE 'account_collaborators'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `account_collaborators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL COMMENT 'The user_id of the collaborator',
  `collaborator_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;



-- ----------------------------------------
-- TABLE 'river_collaborators'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `river_collaborators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `river_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `collaborator_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `river_id` (`river_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'bucket_collaborators'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `bucket_collaborators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(11) unsigned NOT NULL DEFAULT '0',
  `bucket_id` bigint(11) unsigned NOT NULL DEFAULT '0',
  `collaborator_active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`bucket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;


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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'auth_tokens'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL DEFAULT '',
  `type` varchar(20) DEFAULT NULL,
  `data` text,
  `created_date` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_tokens_un_token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- VIEW 'activity_stream'
-- ----------------------------------------
CREATE OR REPLACE VIEW `activity_stream` AS
SELECT ua.id, action_date_add, ua.user_id, u1.name user_name, u1.email user_email, action, action_on, 
  action_on_id, ac.account_path action_on_name, u2.name action_to_name, u2.id action_to_id, confirmed
FROM user_actions ua 
LEFT JOIN users u1 ON (ua.user_id = u1.id)
JOIN accounts ac ON (ua.action_on_id = ac.id)
LEFT OUTER JOIN users u2 ON (ua.action_to_id = u2.id)
WHERE action_on = 'account'
UNION ALL
SELECT ua.id, action_date_add, ua.user_id, u1.name user_name, u1.email user_email, action, action_on, 
  action_on_id, r.river_name action_on_name, u2.name action_to_name, u2.id action_to_id, confirmed
FROM user_actions ua
LEFT JOIN users u1 ON (ua.user_id = u1.id)
JOIN rivers r ON (ua.action_on_id = r.id)
LEFT OUTER JOIN users u2 ON (ua.action_to_id = u2.id)
WHERE action_on = 'river'
UNION ALL
SELECT ua.id, action_date_add, ua.user_id, u1.name user_name, u1.email user_email, action, action_on, 
  action_on_id, b.bucket_name action_on_name, u2.name action_to_name, u2.id action_to_id, confirmed
FROM user_actions ua 
LEFT JOIN users u1 ON (ua.user_id = u1.id)
JOIN buckets b ON (ua.action_on_id = b.id)
LEFT OUTER JOIN users u2 ON (ua.action_to_id = u2.id)
WHERE action_on = 'bucket';


-- ----------------------------------------
-- TABLE 'bucket_subscriptions'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `bucket_subscriptions` (
  `bucket_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  UNIQUE KEY `bucket_id` (`bucket_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'river_subscriptions'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `river_subscriptions` (
  `river_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  UNIQUE KEY `river_id` (`river_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'droplet_scores'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `droplet_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `droplet_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `droplet_id` (`droplet_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'comment_scores'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `comment_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_id` (`comment_id`,`user_id`)
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
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (4, 'public_registration_enabled', '0');
INSERT INTO `settings` (`id`, `key`, `value`) VALUES (5, 'anonymous_access_enabled', '0');

COMMIT;

-- -----------------------------------------------------
-- Data for table `users`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `users` (`id`, `email`, `name`, `username`, `password`, `logins`, `last_login`, `api_key`) VALUES (1, 'myswiftriver@myswiftriver.com', 'Administrator', 'admin', 'c2bac288881c7dd9531c607e73b3af798499917760023656e9847b10b8e75542', 0, NULL, md5(rand()));
INSERT INTO `users` (`id`, `email`, `name`, `username`, `password`, `logins`, `last_login`, `api_key`) VALUES (2, 'public@myswiftriver.com', 'public', 'public', '', 0, NULL, '');

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
INSERT INTO `accounts` (`user_id`, `account_path`) VALUES (2, 'public');

COMMIT;