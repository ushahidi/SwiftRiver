ALTER TABLE `users` ADD COLUMN `invites` smallint(6) NOT NULL DEFAULT '10' AFTER `logins`;

