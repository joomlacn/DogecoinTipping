CREATE TABLE IF NOT EXISTS `#__dogecointipping_address`(
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`address` varchar(255) NOT NULL DEFAULT '',
	`label` varchar(255) NOT NULL DEFAULT '',
	`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`inline_amount` decimal(15,8) NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS `#__dogecointipping_reward` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`from_user_id` int(10) unsigned NOT NULL,
	`to_user_id` int(10) unsigned NOT NULL,
	`amount` decimal(15,8) NOT NULL,
	`desc` text default '',
	`payment_result` text default '',
	`query_string` varchar(255) default '',
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`address_amount` decimal(15, 8) NOT NULL DEFAULT 0 COMMENT '',
	`inline_amount` decimal(15, 8) NOT NULL DEFAULT 0,
	`article_id` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`),
	KEY `idx_from_user_id` (`from_user_id`),
	KEY `idx_to_user_id` (`to_user_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;

CREATE TABLE IF NOT EXISTS `#__dogecointipping_withdraw` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`from_address` varchar(255) NOT NULL DEFAULT '',
	`to_address` varchar(255) NOT NULL DEFAULT '',
	`amount` decimal(15, 8) NOT NULL,
	`desc` text default '',
	`address_amount` decimal(15, 8) NOT NULL DEFAULT 0 COMMENT '',
	`inline_amount` decimal(15, 8) NOT NULL DEFAULT 0,
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	KEY `idx_user_id` (`user_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;