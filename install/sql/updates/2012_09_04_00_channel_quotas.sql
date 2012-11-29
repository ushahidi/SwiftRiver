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
-- Table `user_quotas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_quotas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `channel` varchar(100) NOT NULL DEFAULT '',
  `channel_option` varchar(100) NOT NULL DEFAULT '',
  `quota_usage` int(11) NOT NULL DEFAULT 0 COMMENT 'Current no. of options that the user has used up',
  PRIMARY KEY (`id`),
  UNIQUE KEY `un_channel_option` (`channel`,`channel_option`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;