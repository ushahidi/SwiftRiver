# Sweeper Database Installer
# http://www.ushahidi.com
#
# version: 001
# ------------------------------------------------------------


# Make Sure We're Using UTF8 Encoding
# ------------------------------------------------------------
ALTER DATABASE DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;



# Dump of table feeds
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `feeds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,  
  `service` varchar(100) NOT NULL,
  `service_option` varchar(100) default NULL,
  `feed_name` varchar(255) DEFAULT NULL,
  `feed_description` varchar(255) DEFAULT NULL,
  `feed_type` varchar(100) NOT NULL,
  `feed_url` varchar(255) DEFAULT NULL,
  `feed_lastrun` int(11) DEFAULT '0',
  `feed_nextrun` int(11) DEFAULT '0',
  `feed_runs` int(11) DEFAULT '0',
  `feed_schedule` varchar(30) DEFAULT '*:-1:*:*:*',
  `feed_enabled` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `service` (`service`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Feeds generate items.';



# Dump of table feeds_options
# ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feed_options` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `feed_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table items
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL, 
  `parent_id` bigint(20) DEFAULT '0' COMMENT 'ID of the parent for revision tracking',
  `source_id` bigint(20) NOT NULL COMMENT 'The source to which this item is connected (e.g. @ushahidi, +254123456)',
  `feed_id` int(11) DEFAULT '0' COMMENT 'The feed that generated this item (e.g. BBCNews RSS feed, #Haiti)',
  `user_id` int(11) DEFAULT '0' COMMENT 'Unique user ID of the last user to modify this item',
  `item_type` int(11) DEFAULT '1' COMMENT 'What type of item is this?\n1 - Original\n2 - Reply\n3 - Retweet\n4 - Comment\n5 - Revision',
  `item_title` varchar(255) DEFAULT NULL COMMENT 'Title of the feed item if available',
  `item_description` text COMMENT 'The content of the feed item (if available)',
  `item_author` varchar(150) DEFAULT NULL COMMENT 'The full name of the author of this item',
  `item_locale` varchar(30) DEFAULT NULL COMMENT 'Local of the feed item (e.g. en (English), fr (French))',
  `item_date_pub` datetime DEFAULT '0000-00-00 00:00:00' COMMENT 'Original publish date of the feed item',
  `item_date_add` datetime DEFAULT '0000-00-00 00:00:00' COMMENT 'Date the feed item was added to this database',
  `item_state` tinyint(4) DEFAULT '1' COMMENT 'Status of the feed item\n0 - inactive\n1 - new\n2 - accurate etc etc',
  `item_is_translation` tinyint(4) DEFAULT '0' COMMENT 'If tagged as a translation, it references the translated item via the parent_id',
  `item_locked` tinyint(4) DEFAULT '0' COMMENT 'If item is locked, another user is editing it',
  `item_veracity` tinyint(4) DEFAULT '0' COMMENT 'Veracity score of this item -- generated via a combination of variables',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Items are generated from feeds and are attached to a specifi';



# Dump of table items_locations
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `items_locations` (
  `item_id` bigint(20) NOT NULL,
  `location_id` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table items_stories
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `items_stories` (
  `story_id` int(11) NOT NULL,
  `item_id` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table items_tags
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `items_tags` (
  `item_id` bigint(20) NOT NULL,
  `tag_id` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table links
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint(20) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `full_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `link_idx` (`link`),
  KEY `full_link_idx` (`full_link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table locations
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) DEFAULT NULL COMMENT 'Full location name',
  `location_lonlat` point DEFAULT NULL COMMENT 'POINT geometry for this location',
  `location_polygon` polygon DEFAULT NULL COMMENT 'POLYGON geometry for this location',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugins
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `plugins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_path` varchar(100) NOT NULL,
  `plugin_name` varchar(255) DEFAULT NULL,
  `plugin_description` varchar(255) DEFAULT NULL,
  `plugin_enabled` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_plugin_path` (`plugin_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table roles
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;

INSERT INTO `roles` (`id`, `name`, `description`, `permissions`)
VALUES
	(1,'login','Login privileges, granted after account confirmation',NULL),
	(2,'admin','',NULL),
	(3,'editor','',NULL);

/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table roles_users
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`),
  CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `roles_users` WRITE;
/*!40000 ALTER TABLE `roles_users` DISABLE KEYS */;

INSERT INTO `roles_users` (`user_id`, `role_id`)
VALUES
	(1,1),
	(1,2);

/*!40000 ALTER TABLE `roles_users` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table settings
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;

INSERT INTO `settings` (`id`, `key`, `value`)
VALUES
	(1,'site_name','Sweeper'),
	(2,'site_theme','default'),
	(3,'site_locale','en');

/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;



# Dump of table sources
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `sources` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) DEFAULT '0' COMMENT 'ID of the parent for revision tracking',
  `user_id` int(11) DEFAULT '0' COMMENT 'Unique user ID of the last user to modify this source',
  `orig_id` varchar(255) DEFAULT NULL,
  `source_username` varchar(255) DEFAULT NULL,
  `source_name` varchar(255) DEFAULT NULL,
  `source_description` text,
  `source_link` varchar(255) DEFAULT NULL,
  `source_full_link` varchar(255) DEFAULT NULL,
  `source_followers` int(10) DEFAULT '0',
  `source_book` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Sources are individual People, Organizations or Websites tha';



# Dump of table stories
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `stories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `story_title` varchar(255) DEFAULT NULL,
  `story_description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Stories belong to a specific project and contain multiple items ';



# Dump of table tags
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table user_tokens
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(127) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `email`, `username`, `name`, `password`, `logins`, `last_login`)
VALUES
	(1,'mysweeper@mysweeper.com','sweeperadmin','Administrator','c2bac288881c7dd9531c607e73b3af798499917760023656e9847b10b8e75542',0,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table projects
# ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `identity` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


# Dump of table projects
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `project_title` varchar(255) DEFAULT NULL,
  `project_description` text,
  `project_enabled` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Projects can contain multiple stories within them';
