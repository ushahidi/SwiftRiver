RENAME TABLE `comments` TO `bucket_comments`;
RENAME TABLE `comment_scores` TO `bucket_comment_scores`;
ALTER TABLE `bucket_comment_scores` CHANGE `comment_id` `bucket_comment_id` BIGINT(20)  NOT NULL;
ALTER TABLE `bucket_comment_scores` CHANGE `score` `score` TINYINT  NOT NULL;
ALTER TABLE `bucket_comment_scores` ADD `score_date_add` TIMESTAMP  NULL  DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `bucket_comment_scores` ADD `score_date_modified` TIMESTAMP  NULL  DEFAULT '0000-00-00 00:00:00';

