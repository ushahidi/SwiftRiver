ALTER TABLE `identities` ADD `identity_date_add` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `identity_avatar`;
ALTER TABLE `identities` ADD `identity_date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `identity_date_add`;
