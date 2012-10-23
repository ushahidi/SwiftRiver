INSERT INTO `settings` (`id`, `key`, `value`) VALUES 
(11, 'site_url', 'http://www.example.com');
UPDATE `settings` SET `key` = 'default_river_lifetime' WHERE `key` = 'river_active_duration';