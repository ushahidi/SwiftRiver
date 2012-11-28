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
  `subscription_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
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
  `follower_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `user_id` (`user_id`,`follower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Track followers';


-- -----------------------------------------------------
-- Table `tags`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(20) unsigned NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `tag_canonical` varchar(50) NOT NULL,
  `tag_type` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tag_un_hash` (`hash`),
  KEY `tag_idx` (`tag_canonical`)
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
CREATE TABLE IF NOT EXISTS `rivers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `river_name` varchar(255) NOT NULL DEFAULT '',
  `river_name_url` varchar(255) NOT NULL DEFAULT '',
  `river_active` tinyint(4) NOT NULL DEFAULT '1',
  `river_public` tinyint(4) NOT NULL DEFAULT '0',
  `river_current` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Identifies if this is the last River that  was worked on',
  `default_layout` varchar(10) DEFAULT 'list',
  `max_drop_id` bigint(20) NOT NULL DEFAULT '0',
  `drop_count` int(11) NOT NULL DEFAULT '0',
  `drop_quota` int(11) DEFAULT '10000',
  `river_full` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the river has expired',
  `river_date_add` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `river_date_expiry` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date when the river shall expire',
  `river_expired` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether the river has expired',
  `expiry_notification_sent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flags whether the river has been marked for expiry',
  `extension_count` int(11) NOT NULL DEFAULT '0' COMMENT 'The no. of times the expiry date has been extended',
  `public_token` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_river_name_url` (`account_id`,`river_name_url`),
  KEY `river_name_url` (`river_name_url`),
  KEY `account_id_idx` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `media`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint(20) unsigned NOT NULL,
  `hash` char(32) NOT NULL,
  `url` text NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'image',
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_un_hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `media_thumbnails`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `media_thumbnails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` bigint(20) NOT NULL,
  `size` int(4) NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_un_thumbnail` (`media_id`,`size`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
CREATE TABLE IF NOT EXISTS `rivers_droplets` (
  `id` bigint(20) unsigned NOT NULL,
  `river_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `droplet_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `droplet_date_pub` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `rivers_droplet_un_river_droplet` (`river_id`,`droplet_id`),
  KEY `river_id_idx` (`river_id`),
  KEY `droplet_id_idx` (`droplet_id`),
  KEY `droplet_date_pub` (`droplet_date_pub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `bucket_comments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bucket_comments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `bucket_id` INT(11) unsigned NOT NULL DEFAULT 0,  
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `parent_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 ,
  `comment_content` TEXT NOT NULL ,
  `comment_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `comment_date_modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `comment_sticky` TINYINT(4) NOT NULL DEFAULT 0 ,
  `comment_deleted` TINYINT(4) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  INDEX `bucket_id_idx` (`bucket_id` ASC) ),
  INDEX `comment_date_add_idx` (`comment_date_add` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `places`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `places` (
  `id` bigint(20) unsigned NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `place_name` varchar(50) NOT NULL DEFAULT '' COMMENT 'Full place name',
  `place_name_canonical` varchar(50) NOT NULL COMMENT 'Full place name',
  `longitude` float DEFAULT NULL,
  `latitude` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `places_un_hash` (`hash`),
  KEY `places_idx` (`place_name_canonical`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
  `identity_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `user_id_idx` (`user_id` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `buckets`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `buckets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Creator of this bucket',
  `bucket_name` varchar(255) NOT NULL DEFAULT '',
  `bucket_name_url` varchar(255) NOT NULL DEFAULT '',
  `bucket_description` text,
  `bucket_publish` tinyint(4) NOT NULL DEFAULT '0',
  `default_layout` varchar(10) DEFAULT 'drops',
  `bucket_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `public_token` varchar(32),
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
  `id` bigint(20) unsigned NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `channel` varchar(20) NOT NULL DEFAULT '',
  `identity_orig_id` varchar(255) DEFAULT NULL,
  `identity_username` varchar(255) DEFAULT NULL,
  `identity_name` varchar(255) DEFAULT NULL,
  `identity_avatar` varchar(255) DEFAULT NULL,
  `identity_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `identity_date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identity_hash` (`hash`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Sources are individual People, Organizations or Websites tha';


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
  `id` bigint(20) unsigned NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `url` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `links_un_hash` (`hash`)
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
  `plugin_installed` TINYINT(4) NOT NULL DEFAULT 0 ,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

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
  `filter_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `filter_date_modified` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `filter_last_run` TIMESTAMP NOT NULL,
  `filter_last_successful_run` TIMESTAMP NOT NULL,
  `filter_runs` int(11) NOT NULL DEFAULT '0',
  `filter_enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `filter_date_add_idx` (`filter_date_add`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Filters generate droplets from channels';


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
-- Table `user_tokens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_tokens` (
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
  `id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'ID of the parent for revision tracking',
  `identity_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'The source to which this item is connected (e.g. @ushahidi, +254123456)',
  `channel` varchar(20) NOT NULL DEFAULT '',
  `droplet_hash` char(32) NOT NULL DEFAULT '',
  `droplet_orig_id` varchar(255) NOT NULL,
  `droplet_type` varchar(10) NOT NULL DEFAULT 'original' COMMENT 'original, retweet, comment, revision',
  `droplet_title` varchar(255) DEFAULT NULL COMMENT 'Title of the feed item if available',
  `droplet_content` text COMMENT 'The content of the feed item (if available)',
  `droplet_locale` varchar(10) DEFAULT NULL COMMENT 'Local of the feed item (e.g. en (English), fr (French))',
  `droplet_image` bigint(20) DEFAULT NULL,
  `droplet_date_pub` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Original publish date of the feed item',
  `droplet_date_add` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date the feed item was added to this database',
  `processing_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Bitwise flags indicating the status of drop post processing',
  `original_url` bigint(20) DEFAULT NULL,
  `comment_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `droplet_hash_idx` (`droplet_hash`),
  KEY `droplet_date_pub_idx` (`droplet_date_pub`),
  KEY `droplet_date_add_idx` (`droplet_date_add`),
  KEY `droplet_processed_idx` (`processing_status`),
  KEY `parent_id` (`parent_id`),
  KEY `droplet_image` (`droplet_image`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Items are generated from feeds and are attached to a specifi';

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
  `source_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `source_date_modified` TIMESTAMP NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`id`) ,
  INDEX `source_date_add_idx` (`source_date_add` ASC) )
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
  `action_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirmed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `action_on_idx` (`action_to_id`),
  KEY `action_on_id_idx` (`action_on_id`),
  KEY (`action_on`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tracks user actions across the system';



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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- ----------------------------------------
-- TABLE 'river_collaborators'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `river_collaborators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `river_id` bigint(20) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `collaborator_active` tinyint(1) DEFAULT NULL,
  `read_only` tinyint(1)  DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `river_id` (`river_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'bucket_collaborators'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `bucket_collaborators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(11) unsigned NOT NULL DEFAULT '0',
  `bucket_id` bigint(11) unsigned NOT NULL DEFAULT '0',
  `collaborator_active` tinyint(1) DEFAULT NULL,
  `read_only` tinyint(1)  DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`bucket_id`)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'trends'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `river_tag_trends` (
  `id` bigint(20) NOT NULL,
  `hash` char(32) NOT NULL DEFAULT '',
  `river_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `date_pub` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `tag_type` varchar(20) NOT NULL DEFAULT '',
  `count` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `river_id` (`river_id`),
  KEY `tag_type` (`tag_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'bucket_comment_scores'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `bucket_comment_scores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bucket_comment_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `score` tinyint(4) NOT NULL,
  `score_date_add` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `score_date_modified` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `comment_id` (`bucket_comment_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'droplet_comments'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `droplet_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `droplet_id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `comment_text` varchar(1024) NOT NULL DEFAULT '',
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `droplet_id` (`droplet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ----------------------------------------
-- TABLE 'sequence'
-- ----------------------------------------
CREATE TABLE IF NOT EXISTS `seq` (
  `name` varchar(30) NOT NULL DEFAULT '',
  `id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER //
CREATE FUNCTION `NEXTVAL`(v_seq_name varchar(30), v_increment BIGINT) 
RETURNS INT 
NOT DETERMINISTIC 
MODIFIES SQL DATA
SQL SECURITY DEFINER 
COMMENT 'Generate a range of ids from a sequence' 
BEGIN    
    UPDATE `seq` SET `id` = last_insert_id(`id` + v_increment) WHERE `name` = v_seq_name;
    RETURN LAST_INSERT_ID() - v_increment;    
END;
//
DELIMITER ;

START TRANSACTION;

-- -----------------------------------------------------
-- Data for table `roles`
-- -----------------------------------------------------
INSERT INTO `seq` (`name`, `id`) VALUES 
('droplets', 1),
('tags', 1),
('places', 1),
('links', 1),
('identities', 1),
('media', 1),
('river_tag_trends', 1),
('rivers_droplets', 1);

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
(12, 'comments_email_domain', 'example.com');

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

COMMIT;

-- -----------------------------------------------------
-- Table `channel_quotas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `channel_quotas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `channel` varchar(100) NOT NULL DEFAULT '' COMMENT 'Channel on which to apply the quota',
  `channel_option` varchar(100) NOT NULL,
  `quota` int(11) NOT NULL DEFAULT 0 COMMENT 'No. of allowable options for the specified channel',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_channel_option` (`channel`,`channel_option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `account_channel_quotas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `account_channel_quotas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL,
  `channel` varchar(100) NOT NULL DEFAULT '',
  `channel_option` varchar(100) NOT NULL DEFAULT '',
  `quota` int(11) NOT NULL DEFAULT '0' COMMENT 'Limit for this type of optin',
  `quota_used` int(11) NOT NULL DEFAULT '0' COMMENT 'Current no. of options that the user has used up',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_channel_option` (`account_id`, `channel`,`channel_option`),
  KEY `idx_user_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
