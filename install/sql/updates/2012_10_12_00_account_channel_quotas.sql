RENAME TABLE `user_quotas` TO `account_channel_quotas`;
ALTER TABLE `account_channel_quotas` CHANGE `user_id` `account_id` BIGINT(20)  NOT NULL;
ALTER TABLE `account_channel_quotas` CHANGE `quota_usage` `quota` INT(11)  NOT NULL  DEFAULT '0'  COMMENT 'Current no. of options that the user has used up';
ALTER TABLE `account_channel_quotas` ADD `quota_used` INT(11)  NOT NULL  DEFAULT '0'  COMMENT 'Current no. of options that the user has used up'  AFTER `quota`;
