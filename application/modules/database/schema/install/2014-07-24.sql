SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

SET NAMES utf8;

CREATE DATABASE IF NOT EXISTS `RS` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `RS`;

DROP TABLE IF EXISTS `collection`;
CREATE TABLE IF NOT EXISTS `collection` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `public` int(11) DEFAULT '0',
  `theme` varchar(100) DEFAULT NULL,
  `theme2` varchar(100) DEFAULT NULL,
  `theme3` varchar(100) DEFAULT NULL,
  `allow_changes` int(11) DEFAULT '0',
  `cant_delete` int(11) DEFAULT '0',
  `keywords` text,
  `savedsearch` int(11) DEFAULT NULL,
  `home_page_publish` int(11) DEFAULT NULL,
  `home_page_text` text,
  `home_page_image` int(11) DEFAULT NULL,
  PRIMARY KEY (`ref`),
  KEY `theme` (`theme`),
  KEY `public` (`public`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `collection` (`ref`, `name`, `user`, `created`, `public`, `theme`, `theme2`, `theme3`, `allow_changes`, `cant_delete`, `keywords`, `savedsearch`, `home_page_publish`, `home_page_text`, `home_page_image`) VALUES
(1, 'My Collection', 1, '2008-01-01 10:00:00', 0, NULL, '0', '1', NULL, 1, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `collection_keyword`;
CREATE TABLE IF NOT EXISTS `collection_keyword` (
  `collection` int(11) DEFAULT NULL,
  `keyword` int(11) DEFAULT NULL,
  KEY `collection` (`collection`),
  KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `collection_log`;
CREATE TABLE IF NOT EXISTS `collection_log` (
  `date` datetime DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `collection` int(11) DEFAULT NULL,
  `type` char(1) DEFAULT NULL,
  `resource` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `collection_resource`;
CREATE TABLE IF NOT EXISTS `collection_resource` (
  `collection` int(11) DEFAULT NULL,
  `resource` int(11) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text,
  `rating` int(11) DEFAULT NULL,
  `use_as_theme_thumbnail` int(11) DEFAULT NULL,
  `purchase_size` varchar(10) DEFAULT NULL,
  `purchase_complete` int(11) DEFAULT '0',
  `purchase_price` decimal(10,2) DEFAULT '0.00',
  `sortorder` int(11) DEFAULT NULL,
  KEY `collection` (`collection`),
  KEY `resource_collection` (`collection`,`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `collection_savedsearch`;
CREATE TABLE IF NOT EXISTS `collection_savedsearch` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `collection` int(11) DEFAULT NULL,
  `search` text,
  `restypes` text,
  `starsearch` int(11) DEFAULT NULL,
  `archive` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `result_limit` int(11) DEFAULT NULL,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `ref_parent` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hide` int(1) DEFAULT '0',
  `collection_ref` int(11) DEFAULT NULL,
  `resource_ref` int(11) DEFAULT NULL,
  `user_ref` int(11) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website_url` text,
  `body` text,
  PRIMARY KEY (`ref`),
  KEY `ref_parent` (`ref_parent`),
  KEY `collection_ref` (`collection_ref`),
  KEY `resource_ref` (`resource_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `daily_stat`;
CREATE TABLE IF NOT EXISTS `daily_stat` (
  `year` int(11) DEFAULT NULL,
  `month` int(11) DEFAULT NULL,
  `day` int(11) DEFAULT NULL,
  `usergroup` int(11) DEFAULT '0',
  `activity_type` varchar(50) DEFAULT NULL,
  `object_ref` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT '0',
  KEY `stat_day` (`year`,`month`,`day`),
  KEY `stat_month` (`year`,`month`),
  KEY `stat_usergroup` (`usergroup`),
  KEY `stat_day_activity` (`year`,`month`,`day`,`activity_type`),
  KEY `stat_day_activity_ref` (`year`,`month`,`day`,`activity_type`,`object_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `daily_stat` (`year`, `month`, `day`, `usergroup`, `activity_type`, `object_ref`, `count`) VALUES
(2014, 7, 4, 3, 'User session', 1, 1);

DROP TABLE IF EXISTS `dynamic_tree_node`;
CREATE TABLE IF NOT EXISTS `dynamic_tree_node` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `resource_type_field` int(11) DEFAULT '0',
  `parent` int(11) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ref`),
  KEY `parent` (`parent`),
  KEY `resource_type_field` (`resource_type_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `external_access_keys`;
CREATE TABLE IF NOT EXISTS `external_access_keys` (
  `resource` int(11) DEFAULT NULL,
  `access_key` char(10) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `collection` int(11) DEFAULT NULL,
  `request_feedback` int(11) DEFAULT '0',
  `email` varchar(100) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `lastused` datetime DEFAULT NULL,
  `access` int(11) DEFAULT '-1',
  `expires` datetime DEFAULT NULL,
  KEY `resource` (`resource`),
  KEY `resource_key` (`resource`,`access_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ip_lockout`;
CREATE TABLE IF NOT EXISTS `ip_lockout` (
  `ip` varchar(40) NOT NULL DEFAULT '',
  `tries` int(11) DEFAULT '0',
  `last_try` datetime DEFAULT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `keyword`;
CREATE TABLE IF NOT EXISTS `keyword` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(100) DEFAULT NULL,
  `soundex` varchar(50) DEFAULT NULL,
  `hit_count` int(11) DEFAULT '0',
  PRIMARY KEY (`ref`),
  KEY `keyword` (`keyword`),
  KEY `keyword_hit_count` (`hit_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `keyword_related`;
CREATE TABLE IF NOT EXISTS `keyword_related` (
  `keyword` int(11) DEFAULT NULL,
  `related` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `plugins`;
CREATE TABLE IF NOT EXISTS `plugins` (
  `name` varchar(20) NOT NULL DEFAULT '',
  `descrip` text,
  `author` varchar(100) DEFAULT NULL,
  `update_url` varchar(100) DEFAULT NULL,
  `info_url` varchar(100) DEFAULT NULL,
  `inst_version` float DEFAULT NULL,
  `config` longblob,
  `config_json` text,
  `config_url` varchar(100) DEFAULT NULL,
  `enabled_groups` varchar(200) DEFAULT NULL,
  `priority` int(11) DEFAULT '999',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `preview_size`;
CREATE TABLE IF NOT EXISTS `preview_size` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `id` char(3) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `padtosize` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `internal` int(11) DEFAULT '0',
  `allow_preview` int(11) DEFAULT '0',
  `allow_restricted` int(11) DEFAULT '0',
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `preview_size` (`ref`, `id`, `width`, `height`, `padtosize`, `name`, `internal`, `allow_preview`, `allow_restricted`) VALUES
(1, 'thm', 150, 150, 0, 'Thumbnail', 1, 0, 0),
(2, 'pre', 450, 450, 0, 'Preview', 1, 0, 1),
(3, 'scr', 1100, 800, 0, 'Screen', 0, 1, 0),
(4, 'lpr', 2000, 2000, 0, 'Low resolution print', 0, 0, 0),
(5, 'hpr', 999999, 999999, 0, 'High resolution print', 0, 0, 0),
(6, 'col', 75, 75, 0, 'Collection', 1, 0, 0);

DROP TABLE IF EXISTS `report`;
CREATE TABLE IF NOT EXISTS `report` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `query` text,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `report` (`ref`, `name`, `query`) VALUES
(1, 'Keywords used in resource edits', 'select k.keyword ''Keyword'',sum(count) ''Entered Count'' from keyword k,daily_stat d where k.ref=d.object_ref and d.activity_type=''Keyword added to resource''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\n\ngroup by k.ref order by ''Entered Count'' desc limit 100;\n'),
(2, 'Keywords used in searches', 'select k.keyword ''Keyword'',sum(count) Searches from keyword k,daily_stat d where k.ref=d.object_ref and d.activity_type=''Keyword usage''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\n\ngroup by k.ref order by Searches desc\n'),
(3, 'Resource download summary', 'select r.ref ''Resource ID'',r.view_title_field ''Title'',count(*) Downloads \n\nfrom\n\nresource_log rl\njoin resource r on rl.resource=r.ref\nwhere\nrl.type=''d''\nand rl.date>=date(''[from-y]-[from-m]-[from-d]'') and rl.date<=adddate(date(''[to-y]-[to-m]-[to-d]''),1)\ngroup by r.ref order by ''Downloads'' desc'),
(4, 'Resource views', 'select r.ref ''Resource ID'',r.view_title_field ''Title'',sum(count) Views from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=''Resource view''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\n\ngroup by r.ref order by Views desc;\n'),
(5, 'Resources sent via e-mail', 'select r.ref ''Resource ID'',r.view_title_field ''Title'',sum(count) Sent from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=''E-mailed resource''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\n\ngroup by r.ref order by Sent desc;\n'),
(6, 'Resources added to collection', 'select r.ref ''Resource ID'',r.view_title_field ''Title'',sum(count) Added from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=''Add resource to collection''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\n\ngroup by r.ref order by Added desc;\n'),
(7, 'Resources created', 'select\nrl.date ''Date / Time'',\nconcat(u.username,'' ('',u.fullname,'' )'') ''Created By User'',\ng.name ''User Group'',\nr.ref ''Resource ID'',\nr.view_title_field ''Resource Title''\n\nfrom\nresource_log rl\njoin resource r on r.ref=rl.resource\nleft outer join user u on rl.user=u.ref\nleft outer join usergroup g on u.usergroup=g.ref\nwhere\nrl.type=''c''\nand\nrl.date>=date(''[from-y]-[from-m]-[from-d]'') and rl.date<=adddate(date(''[to-y]-[to-m]-[to-d]''),1)\norder by rl.date'),
(8, 'Resources with zero downloads', 'select ref ''Resource ID'',view_title_field ''Title'' from resource where ref not in \n\n(\n#Previous query to fetch resource downloads\nselect r.ref from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=''Resource download''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\n\ngroup by r.ref\n)'),
(9, 'Resources with zero views', 'select ref ''Resource ID'',view_title_field ''Title'' from resource where ref not in \n\n(\n#Previous query to fetch resource views\nselect r.ref from resource r,daily_stat d where r.ref=d.object_ref and d.activity_type=''Resource view''\n\n# --- date ranges\n# Make sure date is greater than FROM date\nand \n(\nd.year>[from-y]\nor \n(d.year=[from-y] and d.month>[from-m])\nor\n(d.year=[from-y] and d.month=[from-m] and d.day>=[from-d])\n)\n# Make sure date is less than TO date\nand\n(\nd.year<[to-y]\nor \n(d.year=[to-y] and d.month<[to-m])\nor\n(d.year=[to-y] and d.month=[to-m] and d.day<=[to-d])\n)\n\ngroup by r.ref\n)'),
(10, 'Resource downloads by group', 'select\ng.name ''Group Name'',\ncount(rl.resource) ''Resource Downloads''\n\nfrom\nresource_log rl\nleft outer join user u on rl.user=u.ref\nleft outer join usergroup g on u.usergroup=g.ref\nwhere\nrl.type=''d''\nand rl.date>=date(''[from-y]-[from-m]-[from-d]'') and rl.date<=adddate(date(''[to-y]-[to-m]-[to-d]''),1)\ngroup by g.ref order by ''Resource Downloads'' desc'),
(11, 'Resource download detail', 'select\nrl.date ''Date / Time'',\nconcat(u.username,'' ('',u.fullname,'' )'') ''Downloaded By User'',\ng.name ''User Group'',\nr.ref ''Resource ID'',\nr.title ''Resource Title'',\nrt.name ''Resource Type''\n\nfrom\nresource_log rl\njoin resource r on r.ref=rl.resource\nleft outer join user u on rl.user=u.ref\nleft outer join usergroup g on u.usergroup=g.ref\nleft outer join resource_type rt on r.resource_type=rt.ref\nwhere\nrl.type=''d''\nand\nrl.date>=date(''[from-y]-[from-m]-[from-d]'') and rl.date<=adddate(date(''[to-y]-[to-m]-[to-d]''),1)\norder by rl.date'),
(12, 'User details including group allocation', 'select \nu.username ''Username'',\nu.email ''E-mail address'',\nu.fullname ''Full Name'',\nu.created ''Created'',\nu.last_active ''Last Seen'',\ng.name ''Group name''\n\nfrom user u join usergroup g on u.usergroup=g.ref\n\norder by username;'),
(13, 'Expired Resources', 'select distinct resource.ref ''Resource ID'',resource.field8 ''Resource Title'',resource_data.value ''Expires'' from resource join resource_data on resource.ref=resource_data.resource join resource_type_field on resource_data.resource_type_field=resource_type_field.ref where resource_type_field.type=6 and value>=date(''[from-y]-[from-m]-[from-d]'') and value<=adddate(date(''[to-y]-[to-m]-[to-d]''),1) and length(value)>0 and resource.ref>0 order by resource.ref;'),
(14, 'Resources created - with thumbnails', 'select\nr.ref ''thumbnail'',\nrl.date ''Date / Time'',\nconcat(u.username,'' ('',u.fullname,'' )'') ''Created By User'',\ng.name ''User Group'',\nr.ref ''Resource ID'',\nr.view_title_field ''Resource Title''\n\nfrom\nresource_log rl\njoin resource r on r.ref=rl.resource\nleft outer join user u on rl.user=u.ref\nleft outer join usergroup g on u.usergroup=g.ref\nwhere\nrl.type=''c''\nand\nrl.date>=date(''[from-y]-[from-m]-[from-d]'') and rl.date<=adddate(date(''[to-y]-[to-m]-[to-d]''),1)\norder by rl.date;');

DROP TABLE IF EXISTS `report_periodic_emails`;
CREATE TABLE IF NOT EXISTS `report_periodic_emails` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `send_all_users` int(11) DEFAULT NULL,
  `report` int(11) DEFAULT NULL,
  `period` int(11) DEFAULT NULL,
  `email_days` int(11) DEFAULT NULL,
  `last_sent` datetime DEFAULT NULL,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `request`;
CREATE TABLE IF NOT EXISTS `request` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `collection` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `request_mode` int(11) DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `comments` text,
  `expires` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `reason` text,
  `reasonapproved` text,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `research_request`;
CREATE TABLE IF NOT EXISTS `research_request` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `description` text,
  `deadline` datetime DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `finaluse` text,
  `resource_types` varchar(50) DEFAULT NULL,
  `noresources` int(11) DEFAULT NULL,
  `shape` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `collection` int(11) DEFAULT NULL,
  PRIMARY KEY (`ref`),
  KEY `research_collections` (`collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource`;
CREATE TABLE IF NOT EXISTS `resource` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `resource_type` int(11) DEFAULT NULL,
  `has_image` int(11) DEFAULT '0',
  `is_transcoding` int(11) DEFAULT '0',
  `hit_count` int(11) DEFAULT '0',
  `new_hit_count` int(11) DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `user_rating` int(11) DEFAULT NULL,
  `user_rating_count` int(11) DEFAULT NULL,
  `user_rating_total` int(11) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  `preview_extension` varchar(10) DEFAULT NULL,
  `image_red` int(11) DEFAULT NULL,
  `image_green` int(11) DEFAULT NULL,
  `image_blue` int(11) DEFAULT NULL,
  `thumb_width` int(11) DEFAULT NULL,
  `thumb_height` int(11) DEFAULT NULL,
  `archive` int(11) DEFAULT '0',
  `access` int(11) DEFAULT '0',
  `colour_key` varchar(5) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_modified` datetime DEFAULT NULL,
  `file_checksum` varchar(32) DEFAULT NULL,
  `request_count` int(11) DEFAULT '0',
  `expiry_notification_sent` int(11) DEFAULT '0',
  `preview_tweaks` varchar(50) DEFAULT NULL,
  `geo_lat` double DEFAULT NULL,
  `geo_long` double DEFAULT NULL,
  `mapzoom` int(11) DEFAULT NULL,
  `disk_usage` bigint(11) DEFAULT NULL,
  `disk_usage_last_updated` datetime DEFAULT NULL,
  `file_size` bigint(11) DEFAULT NULL,
  `preview_attempts` int(11) DEFAULT NULL,
  `field12` varchar(200) DEFAULT NULL,
  `field8` varchar(200) DEFAULT NULL,
  `field3` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`ref`),
  KEY `hit_count` (`hit_count`),
  KEY `resource_archive` (`archive`),
  KEY `resource_access` (`access`),
  KEY `resource_type` (`resource_type`),
  KEY `resource_creation_date` (`creation_date`),
  KEY `rating` (`rating`),
  KEY `colour_key` (`colour_key`),
  KEY `has_image` (`has_image`),
  KEY `file_checksum` (`file_checksum`),
  KEY `geo_lat` (`geo_lat`),
  KEY `geo_long` (`geo_long`),
  KEY `disk_usage` (`disk_usage`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `resource` (`ref`, `title`, `resource_type`, `has_image`, `is_transcoding`, `hit_count`, `new_hit_count`, `creation_date`, `rating`, `user_rating`, `user_rating_count`, `user_rating_total`, `country`, `file_extension`, `preview_extension`, `image_red`, `image_green`, `image_blue`, `thumb_width`, `thumb_height`, `archive`, `access`, `colour_key`, `created_by`, `file_path`, `file_modified`, `file_checksum`, `request_count`, `expiry_notification_sent`, `preview_tweaks`, `geo_lat`, `geo_long`, `mapzoom`, `disk_usage`, `disk_usage_last_updated`, `file_size`, `preview_attempts`, `field12`, `field8`, `field3`) VALUES
(-1, NULL, 1, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 1, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `resource_alt_files`;
CREATE TABLE IF NOT EXISTS `resource_alt_files` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `resource` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `file_name` varchar(200) DEFAULT NULL,
  `file_extension` varchar(10) DEFAULT NULL,
  `file_size` bigint(11) DEFAULT '0',
  `creation_date` datetime DEFAULT NULL,
  `unoconv` int(11) DEFAULT NULL,
  `alt_type` varchar(100) DEFAULT NULL,
  `page_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource_custom_access`;
CREATE TABLE IF NOT EXISTS `resource_custom_access` (
  `resource` int(11) DEFAULT NULL,
  `usergroup` int(11) DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `access` int(11) DEFAULT NULL,
  `user_expires` date DEFAULT NULL,
  KEY `resource` (`resource`),
  KEY `usergroup` (`usergroup`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource_data`;
CREATE TABLE IF NOT EXISTS `resource_data` (
  `resource` int(11) DEFAULT NULL,
  `resource_type_field` int(11) DEFAULT NULL,
  `value` mediumtext,
  KEY `resource` (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource_dimensions`;
CREATE TABLE IF NOT EXISTS `resource_dimensions` (
  `resource` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT '0',
  `height` int(11) DEFAULT '0',
  `file_size` int(11) DEFAULT '0',
  `resolution` int(11) DEFAULT '0',
  `unit` varchar(11) DEFAULT '0',
  `page_count` int(11) DEFAULT NULL,
  KEY `resource` (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource_keyword`;
CREATE TABLE IF NOT EXISTS `resource_keyword` (
  `resource` int(11) DEFAULT NULL,
  `keyword` int(11) DEFAULT NULL,
  `hit_count` int(11) DEFAULT '0',
  `position` int(11) DEFAULT '0',
  `resource_type_field` int(11) DEFAULT '0',
  `new_hit_count` int(11) DEFAULT '0',
  KEY `resource_keyword` (`resource`,`keyword`),
  KEY `resource` (`resource`),
  KEY `keyword` (`keyword`),
  KEY `resource_type_field` (`resource_type_field`),
  KEY `rk_all` (`resource`,`keyword`,`resource_type_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource_log`;
CREATE TABLE IF NOT EXISTS `resource_log` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `user` int(11) DEFAULT NULL,
  `resource` int(11) DEFAULT NULL,
  `type` char(1) DEFAULT NULL,
  `resource_type_field` int(11) DEFAULT NULL,
  `notes` text,
  `diff` text,
  `usageoption` int(11) DEFAULT NULL,
  `purchase_size` varchar(10) DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT '0.00',
  `access_key` char(50) DEFAULT NULL,
  `previous_value` text,
  PRIMARY KEY (`ref`),
  KEY `resource` (`resource`),
  KEY `type` (`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `resource_log` (`ref`, `date`, `user`, `resource`, `type`, `resource_type_field`, `notes`, `diff`, `usageoption`, `purchase_size`, `purchase_price`, `access_key`, `previous_value`) VALUES
(1, '2014-06-01 10:15:14', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(2, '2014-06-28 15:15:01', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(3, '2014-06-28 16:26:43', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(4, '2014-06-28 16:27:08', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(5, '2014-07-02 17:22:47', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(6, '2014-07-02 17:48:09', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(7, '2014-07-02 17:48:52', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(8, '2014-07-02 17:58:53', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(9, '2014-07-02 17:59:55', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL),
(10, '2014-07-02 18:40:38', 1, 0, 'l', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, NULL);

DROP TABLE IF EXISTS `resource_related`;
CREATE TABLE IF NOT EXISTS `resource_related` (
  `resource` int(11) DEFAULT NULL,
  `related` int(11) DEFAULT NULL,
  KEY `resource_related` (`resource`),
  KEY `related` (`related`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `resource_type`;
CREATE TABLE IF NOT EXISTS `resource_type` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `allowed_extensions` text,
  `order_by` int(11) DEFAULT NULL,
  `config_options` text,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `resource_type` (`ref`, `name`, `allowed_extensions`, `order_by`, `config_options`) VALUES
(1, 'Photo', NULL, NULL, NULL),
(2, 'Document', NULL, NULL, NULL),
(3, 'Video', NULL, NULL, NULL),
(4, 'Audio', NULL, NULL, NULL);

DROP TABLE IF EXISTS `resource_type_field`;
CREATE TABLE IF NOT EXISTS `resource_type_field` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(400) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `options` text,
  `order_by` int(11) DEFAULT '0',
  `keywords_index` int(11) DEFAULT '0',
  `partial_index` int(11) DEFAULT '0',
  `resource_type` int(11) DEFAULT '0',
  `resource_column` varchar(50) DEFAULT NULL,
  `display_field` int(11) DEFAULT '1',
  `use_for_similar` int(11) DEFAULT '1',
  `iptc_equiv` varchar(20) DEFAULT NULL,
  `display_template` text,
  `tab_name` varchar(50) DEFAULT NULL,
  `required` int(11) DEFAULT '0',
  `smart_theme_name` varchar(200) DEFAULT NULL,
  `exiftool_field` varchar(200) DEFAULT NULL,
  `advanced_search` int(11) DEFAULT '1',
  `simple_search` int(11) DEFAULT '0',
  `help_text` text,
  `display_as_dropdown` int(11) DEFAULT '0',
  `external_user_access` int(11) DEFAULT '1',
  `autocomplete_macro` text,
  `hide_when_uploading` int(11) DEFAULT '0',
  `hide_when_restricted` int(11) DEFAULT '0',
  `value_filter` text,
  `exiftool_filter` text,
  `omit_when_copying` int(11) DEFAULT '0',
  `tooltip_text` text,
  `regexp_filter` varchar(400) DEFAULT NULL,
  `sync_field` int(11) DEFAULT NULL,
  `display_condition` varchar(400) DEFAULT NULL,
  `onchange_macro` text,
  PRIMARY KEY (`ref`),
  KEY `resource_type` (`resource_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `resource_type_field` (`ref`, `name`, `title`, `type`, `options`, `order_by`, `keywords_index`, `partial_index`, `resource_type`, `resource_column`, `display_field`, `use_for_similar`, `iptc_equiv`, `display_template`, `tab_name`, `required`, `smart_theme_name`, `exiftool_field`, `advanced_search`, `simple_search`, `help_text`, `display_as_dropdown`, `external_user_access`, `autocomplete_macro`, `hide_when_uploading`, `hide_when_restricted`, `value_filter`, `exiftool_filter`, `omit_when_copying`, `tooltip_text`, `regexp_filter`, `sync_field`, `display_condition`, `onchange_macro`) VALUES
(1, 'keywords', 'Keywords', 1, NULL, 30, 1, 0, 0, NULL, 1, 1, '2#025', NULL, NULL, 0, NULL, 'Keywords,Subject', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'country', 'Country', 9, 'Afghanistan, Aland Islands, Albania, Algeria, American Samoa, Andorra, Angola, Anguilla, Antarctica, Antigua And Barbuda, Argentina, Armenia, Aruba, Australia, Austria, Azerbaijan, Bahamas, Bahrain, Bangladesh, Barbados, Belarus, Belgium, Belize, Benin, Bermuda, Bhutan, Bolivia, Bosnia And Herzegovina, Botswana, Bouvet Island, Brazil, British Indian Ocean Territory, Brunei Darussalam, Bulgaria, Burkina Faso, Burundi, Cambodia, Cameroon, Canada, Cape Verde, Cayman Islands, Central African Republic, Chad, Chile, China, Christmas Island, Cocos (Keeling) Islands, Colombia, Comoros, Congo, Congo - The Democratic Republic Of The, Cook Islands, Costa Rica, CÃ´te D''ivoire, Croatia, Cuba, Cyprus, Czech Republic, Denmark, Djibouti, Dominica, Dominican Republic, Ecuador, Egypt, El Salvador, Equatorial Guinea, Eritrea, Estonia, Ethiopia, Falkland Islands (Malvinas), Faroe Islands, Fiji, Finland, France, French Guiana, French Polynesia, French Southern Territories, Gabon, Gambia, Georgia, Germany, Ghana, Gibraltar, Greece, Greenland, Grenada, Guadeloupe, Guam, Guatemala, Guernsey, Guinea, Guinea-Bissau, Guyana, Haiti, Heard Island And Mcdonald Islands, Holy See (Vatican City State), Honduras, Hong Kong, Hungary, Iceland, India, Indonesia, Iran - Islamic Republic Of, Iraq, Ireland, Isle Of Man, Israel, Italy, Jamaica, Japan, Jersey, Jordan, Kazakhstan, Kenya, Kiribati, Korea - Democratic People''s Republic Of, Korea - Republic Of, Kuwait, Kyrgyzstan, Lao People''s Democratic Republic, Latvia, Lebanon, Lesotho, Liberia, Libyan Arab Jamahiriya, Liechtenstein, Lithuania, Luxembourg, Macao, Macedonia - The Former Yugoslav Republic Of, Madagascar, Malawi, Malaysia, Maldives, Mali, Malta, Marshall Islands, Martinique, Mauritania, Mauritius, Mayotte, Mexico, Micronesia - Federated States Of, Moldova - Republic Of, Monaco, Mongolia, Montenegro, Montserrat, Morocco, Mozambique, Myanmar, Namibia, Nauru, Nepal, Netherlands, Netherlands Antilles, New Caledonia, New Zealand, Nicaragua, Niger, Nigeria, Niue, Norfolk Island, Northern Mariana Islands, Norway, Oman, Pakistan, Palau, Palestinian Territory - Occupied, Panama, Papua New Guinea, Paraguay, Peru, Philippines, Pitcairn, Poland, Portugal, Puerto Rico, Qatar, RÃ©union, Romania, Russian Federation, Rwanda, Saint BarthÃ©lemy, Saint Helena, Saint Kitts And Nevis, Saint Lucia, Saint Martin, Saint Pierre And Miquelon, Saint Vincent And The Grenadines, Samoa, San Marino, Sao Tome And Principe, Saudi Arabia, Senegal, Serbia, Seychelles, Sierra Leone, Singapore, Slovakia, Slovenia, Solomon Islands, Somalia, South Africa, South Georgia And The South Sandwich Islands, Spain, Sri Lanka, Sudan, Suriname, Svalbard And Jan Mayen, Swaziland, Sweden, Switzerland, Syrian Arab Republic, Taiwan - Province Of China, Tajikistan, Tanzania - United Republic Of, Thailand, Timor-Leste, Togo, Tokelau, Tonga, Trinidad And Tobago, Tunisia, Turkey, Turkmenistan, Turks And Caicos Islands, Tuvalu, Uganda, Ukraine, United Arab Emirates, United Kingdom, United States, United States Minor Outlying Islands, Uruguay, Uzbekistan, Vanuatu, Venezuela - Bolivarian Republic Of, Viet Nam, Virgin Islands - British, Virgin Islands - U.S., Wallis And Futuna, Western Sahara, Yemen, Zambia, Zimbabwe', 60, 1, 0, 0, 'country', 1, 1, '2#101', NULL, NULL, 0, NULL, 'category,country', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'title', 'Title', 0, NULL, 10, 1, 0, 0, 'title', 0, 1, '2#005', NULL, NULL, 1, NULL, 'Title', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'extract', 'Document extract', 1, NULL, 7, 0, 0, 2, NULL, 1, 0, NULL, '<div class="RecordStory">\n\n  <h1>[title]</h1>\n\n  <p>[value]</p>\n\n</div>', NULL, 0, NULL, NULL, 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'credit', 'Credit', 0, NULL, 90, 1, 0, 0, NULL, 1, 1, '2#080', NULL, NULL, 0, NULL, 'Source,Creator,Credit,By-line', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'date', 'Date', 4, NULL, 80, 1, 0, 0, 'creation_date', 1, 1, '2#055', NULL, NULL, 0, NULL, 'DateTimeOriginal', 0, 0, NULL, 0, 1, NULL, 0, 0, 'if ($value!=''''){$value=nicedate($value,false);}', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'caption', 'Caption', 1, NULL, 40, 1, 0, 0, NULL, 1, 0, '2#120', '<div class="item"><h3>[title]</h3><p>[value]</p></div>\n\n<div class="clearerleft"> </div>', NULL, 0, NULL, 'Caption-Abstract,Description,ImageDescription', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, NULL, 'Notes', 1, NULL, 110, 0, 0, 0, NULL, 1, 0, '2#103', '<div class="RecordStory">\n\n  <h1>[title]</h1>\n\n  <p>[value]</p>\n\n</div>', NULL, 0, NULL, 'JobID', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'person', 'Named person(s)', 0, NULL, 70, 1, 0, 0, NULL, 1, 1, NULL, NULL, NULL, 0, NULL, 'People', 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'originalfilename', 'Original filename', 0, NULL, 20, 1, 0, 0, 'file_path', 0, 1, NULL, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, 0, 1, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'camera', 'Camera make / model', 0, NULL, 1600, 0, 0, 1, NULL, 1, 0, NULL, NULL, NULL, 0, NULL, 'Model', 1, 0, NULL, 0, 1, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'source', 'Source', 3, ',Digital Camera, Scanned Negative, Scanned Photo', 1601, 0, 0, 1, NULL, 1, 1, NULL, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'text', 'Extracted text', 5, NULL, 50, 1, 0, 0, NULL, 0, 0, NULL, '<div class="item"><h3>[title]</h3><p>[value]</p></div><div class="clearerleft"> </div>', NULL, 0, NULL, NULL, 1, 0, NULL, 0, 1, NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

DROP TABLE IF EXISTS `site_text`;
CREATE TABLE IF NOT EXISTS `site_text` (
  `page` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `text` text,
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `language` varchar(10) DEFAULT NULL,
  `ignore_me` int(11) DEFAULT NULL,
  `specific_to_group` int(11) DEFAULT NULL,
  `custom` int(11) DEFAULT NULL,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sysvars`;
CREATE TABLE IF NOT EXISTS `sysvars` (
  `name` varchar(50) DEFAULT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `usergroup` int(11) DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `logged_in` int(11) DEFAULT NULL,
  `last_browser` text,
  `last_ip` varchar(100) DEFAULT NULL,
  `current_collection` int(11) DEFAULT NULL,
  `accepted_terms` int(11) DEFAULT '0',
  `account_expires` datetime DEFAULT NULL,
  `comments` text,
  `session` varchar(50) DEFAULT NULL,
  `ip_restrict` text,
  `password_last_change` datetime DEFAULT NULL,
  `login_tries` int(11) DEFAULT '0',
  `login_last_try` datetime DEFAULT NULL,
  `approved` int(11) DEFAULT '1',
  `lang` varchar(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ref`),
  KEY `session` (`session`),
  UNIQUE (`username`),
  UNIQUE (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `user` (`ref`, `username`, `password`, `fullname`, `email`, `usergroup`, `last_active`, `logged_in`, `last_browser`, `last_ip`, `current_collection`, `accepted_terms`, `account_expires`, `comments`, `session`, `ip_restrict`, `password_last_change`, `login_tries`, `login_last_try`, `approved`, `lang`, `created`) VALUES
(1, 'admin', 'b936db334b981b2ec826dd3a5ef21b0b', 'Admin User', 'john@rsintheclouds.com', 3, '2014-07-04 21:02:26', 1, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36', '127.0.0.1', 1, 1, NULL, NULL, '8ec18e5ed9ec2a7f77a70298f730dbd6', NULL, '2014-06-28 15:15:56', 0, '2014-06-28 16:26:23', 1, 'en', '2014-05-28 18:32:01');

DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE IF NOT EXISTS `usergroup` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `permissions` text,
  `fixed_theme` varchar(50) DEFAULT NULL,
  `parent` varchar(50) DEFAULT NULL,
  `search_filter` text,
  `edit_filter` text,
  `ip_restrict` text,
  `resource_defaults` text,
  `config_options` text,
  `welcome_message` text,
  `request_mode` int(11) DEFAULT '0',
  `allow_registration_selection` int(11) DEFAULT '0',
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `usergroup` (`ref`, `name`, `permissions`, `fixed_theme`, `parent`, `search_filter`, `edit_filter`, `ip_restrict`, `resource_defaults`, `config_options`, `welcome_message`, `request_mode`, `allow_registration_selection`) VALUES
(1, 'Administrators', 's,g,c,e,t,h,r,u,i,e-2,e-1,e0,e1,e3,v,o,m,q,n,f*,j*,k,R,Ra,Rb', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(2, 'General Users', 's,e-1,e-2,g,d,q,n,f*,j*', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 1, 1),
(3, 'Super Admin', 's,g,c,e,a,t,h,u,r,i,e-2,e-1,e0,e1,e2,e3,o,m,g,v,q,n,f*,j*,k,R,Ra', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(4, 'Archivists', 's,g,c,e,t,h,r,u,i,e1,e2,e3,v,q,n,f*,j*', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(5, 'Restricted User - Requests Emailed', 's,f*,j*,q', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0),
(6, 'Restricted User - Requests Managed', 's,f*,j*,q', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 1, 0),
(7, 'Restricted User - Payment Immediate', 's,f*,j*,q', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 2, 0),
(8, 'Restricted User - Payment Invoice', 's,f*,j*,q', NULL, '0', NULL, NULL, NULL, NULL, NULL, NULL, 3, 0);

DROP TABLE IF EXISTS `user_collection`;
CREATE TABLE IF NOT EXISTS `user_collection` (
  `user` int(11) DEFAULT NULL,
  `collection` int(11) DEFAULT NULL,
  `request_feedback` int(11) DEFAULT '0',
  KEY `collection` (`collection`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user_collection` (`user`, `collection`, `request_feedback`) VALUES
(1, 1, 0);

DROP TABLE IF EXISTS `user_rating`;
CREATE TABLE IF NOT EXISTS `user_rating` (
  `user` int(11) DEFAULT '0',
  `rating` int(11) DEFAULT '0',
  `ref` int(11) DEFAULT '0',
  KEY `ref` (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_userlist`;
CREATE TABLE IF NOT EXISTS `user_userlist` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) DEFAULT NULL,
  `userlist_name` varchar(50) DEFAULT NULL,
  `userlist_string` text,
  PRIMARY KEY (`ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `site_text` (`page`, `name`, `text`, `ref`, `language`, `ignore_me`, `specific_to_group`, `custom`) VALUES
('collection_public', 'introtext', 'Public collections are created by other users.', 1, 'en', NULL, NULL, NULL),
('all', 'searchpanel', 'Search using descriptions, keywords and resource numbers', 2, 'en', NULL, NULL, NULL),
('home', 'themes', 'The very best resources, hand picked and grouped.', 3, 'en', NULL, NULL, NULL),
('home', 'mycollections', 'Organise, collaborate & share your resources. Use these tools to help you work more effectively.', 4, 'en', NULL, NULL, NULL),
('home', 'help', 'Help and advice to get the most out of ResourceSpace.', 5, 'en', NULL, NULL, NULL),
('home', 'welcometitle', 'Welcome to ResourceSpace', 6, 'en', NULL, NULL, NULL),
('home', 'welcometext', 'Your introductory text here.', 7, 'en', NULL, NULL, NULL),
('themes', 'introtext', 'Themes are groups of resources that have been selected by the administrators to provide an example of the resources available in the system.', 8, 'en', NULL, NULL, NULL),
('edit', 'multiple', 'Please select which fields you wish to overwrite. Fields you do not select will be left untouched.', 9, 'en', NULL, NULL, NULL),
('team_archive', 'introtext', 'To edit individual archive resources, simply search for the resource, and click edit in the â€˜Resource Toolâ€™ panel on the resource screen. All resources that are ready to be archived are listed Resources Pending list. From this list it is possible to add further information and transfer the resource record into the archive. ', 10, 'en', NULL, NULL, NULL),
('research_request', 'introtext', 'Our professional researchers are here to assist you in finding the very best resources for your projects. Complete this form as thoroughly as possible so weâ€™re able to meet your criteria accurately. <br /><br />A member of the research team will be assigned to your request. Weâ€™ll keep in contact via email throughout the process, and once weâ€™ve completed the research youâ€™ll receive an email with a link to all the resources that we recommend.  ', 11, 'en', NULL, NULL, NULL),
('collection_manage', 'introtext', 'Organise and manage your work by grouping resources together. Create â€˜Collectionsâ€™ to suit your way of working. You may want to group resources under projects that you are working on independently, share resources amongst a project team or simply keep your favourite resources together in one place. All the collections in your list appear in the â€˜My Collectionsâ€™ panel at the bottom of the screen.', 12, 'en', NULL, NULL, NULL),
('collection_manage', 'findpublic', 'Public collections are groups of resources made widely available by users of the system. Enter a collection ID, or all or part of a collection name or username to find public collections. Add them to your list of collections to access the resources.', 13, 'en', NULL, NULL, NULL),
('team_home', 'introtext', 'Welcome to the team centre. Use the links below to administer resources, respond to resource requests, manage themes and alter system settings.', 14, 'en', NULL, NULL, NULL),
('help', 'introtext', '<p>Get the most out of ResourceSpace. These guides will help you use the system and the resources more effectively. </p>\n<p>Use "Themes" to browse resources by theme or use the simple search box to search for specific resources.</p>\n<p><a href="http://www.montala.net/downloads/resourcespace-GettingStarted.pdf">Download the user guide (PDF file)</a></p>\n<p><a target="_blank" href="http://wiki.resourcespace.org/index.php/Main_Page">Online Documentation (Wiki)</a></p>', 15, 'en', NULL, NULL, NULL),
('terms and conditions', 'terms and conditions', 'Your terms and conditions go here.', 16, 'en', NULL, NULL, NULL),
('contribute', 'introtext', 'You can contribute your own resources. When you initially create a resource it is in the "Pending Submission" status. When you have uploaded your file and edited the fields, set the status field to "Pending Review". It will then be reviewed by the resources team.', 17, 'en', NULL, NULL, NULL),
('done', 'user_password', 'An e-mail containing your username and password has been sent.', 18, 'en', NULL, NULL, NULL),
('user_password', 'introtext', 'Enter your e-mail address and your username and password will be sent to you.', 19, 'en', NULL, NULL, NULL),
('edit', 'batch', NULL, 20, 'en', NULL, NULL, NULL),
('team_copy', 'introtext', 'Enter the ID of the resource you would like to copy. Only the resource data will be copied - any uploaded file will not be copied.', 21, 'en', NULL, NULL, NULL),
('delete', 'introtext', 'Please enter your password to confirm that you would like to delete this resource.', 22, 'en', NULL, NULL, NULL),
('team_report', 'introtext', 'Please choose a report and a date range. The report can be opened in Microsoft Excel or similar spreadsheet application.', 23, 'en', NULL, NULL, NULL),
('terms', 'introtext', 'Before you proceed you must accept the terms and conditions.\n\n', 24, 'en', NULL, NULL, NULL),
('download_progress', 'introtext', 'Your download will start shortly. When your download completes, use the links below to continue.', 25, 'en', NULL, NULL, NULL),
('view', 'storyextract', 'Story extract:', 26, 'en', NULL, NULL, NULL),
('contact', 'contact', 'Your contact details here.', 27, 'en', NULL, NULL, NULL),
('search_advanced', 'introtext', '<strong>Search Tip</strong><br />Any section that you leave blank, or unticked will include ALL those terms in the search. For example, if you leave all the country boxes empty, the search will return results from all those countries. If you select only â€˜Africaâ€™ then the results will ONLY contain resources from â€˜Africaâ€™. ', 28, 'en', NULL, NULL, NULL),
('all', 'researchrequest', 'Let our resources team find the resources you need.', 29, 'en', NULL, NULL, NULL),
('done', 'research_request', 'A member of the research team will be assigned to your request. Weâ€™ll keep in contact via email throughout the process, and once weâ€™ve completed the research youâ€™ll receive an email with a link to all the resources that we recommend.', 30, 'en', NULL, NULL, NULL),
('done', 'collection_email', 'An email containing a link to the collection has been sent to the users you specified. The collection has been added to their ''Collections'' list.', 31, 'en', NULL, NULL, NULL),
('done', 'resource_email', 'An email containing a link to the resource has been sent to the users you specified.', 32, 'en', NULL, NULL, NULL),
('themes', 'manage', 'Organise and edit the themes available online. Themes are specially promoted collections. <br /><br /> <strong>1 To create a new entry under a Theme -  build a collection</strong><br /> Choose <strong>My Collections</strong> from the main top menu and set up a brand new <strong>public</strong> collection. Remember to include a theme name during the setup. Use an existing theme name to group the collection under a current theme (make sure you type it exactly the same), or choose a new title to create a brand new theme. Never allow users to add/remove resources from themed collections. <br /> <br /><strong>2 To edit the content of an existing entry under a theme </strong><br /> Choose <strong>edit collection</strong>. The items in that collection will appear in the <strong>My Collections</strong> panel at the bottom of the screen. Use the standard tools to edit, remove or add resources. <br /> <br /><strong>3 To alter a theme name or move a collection to appear under a different theme</strong><br /> Choose <strong>edit properties</strong> and edit theme category or collection name. Use an existing theme name to group the collection under an current theme (make sure you type it exactly the same), or choose a new title to create a brand new theme. <br /> <br /><strong>4 To remove a collection from a theme </strong><br /> Choose <strong>edit properties</strong> and delete the words in the theme category box. ', 33, 'en', NULL, NULL, NULL),
('terms', 'terms', 'Your terms and conditions go here.', 34, 'en', NULL, NULL, NULL),
('done', 'resource_request', 'Your request has been submitted and we will be in contact shortly.', 35, 'en', NULL, NULL, NULL),
('user_request', 'introtext', 'Please complete the form below to request a user account.', 36, 'en', NULL, NULL, NULL),
('themes', 'findpublic', 'Public collections are collections of resources that have been shared by other users.', 37, 'en', NULL, NULL, NULL),
('done', 'user_request', 'Your request for a user account has been sent. Your login details will be sent to you shortly.', 38, 'en', NULL, NULL, NULL),
('about', 'about', 'Your about text goes here.', 39, 'en', NULL, NULL, NULL),
('team_content', 'introtext', NULL, 40, 'en', NULL, NULL, NULL),
('done', 'deleted', 'The resource has been deleted.', 41, 'en', NULL, NULL, NULL),
('upload', 'introtext', NULL, 42, 'en', NULL, NULL, NULL),
('home', 'restrictedtitle', '<h1>Welcome to ResourceSpace</h1>', 43, 'en', NULL, NULL, NULL),
('home', 'restrictedtext', 'Please click on the link that you were e-mailed to access the resources selected for you.', 44, 'en', NULL, NULL, NULL),
('resource_email', 'introtext', 'Quickly share this resource with other users by email. A link is automatically sent out. You can also include any message as part of the email.', 45, 'en', NULL, NULL, NULL),
('team_resource', 'introtext', 'Add individual resources or batch upload resources. To edit individual resources, simply search for the resource, and click edit in the â€˜Resource Toolâ€™ panel on the resource screen.', 46, 'en', NULL, NULL, NULL),
('team_user', 'introtext', 'Use this section to add, remove and modify users.', 47, 'en', NULL, NULL, NULL),
('team_research', 'introtext', 'Organise and manage â€˜Research Requestsâ€™. <br /><br />Choose â€˜edit researchâ€™ to review the request details and assign the research to a team member. It is possible to base a research request on a previous collection by entering the collection ID in the â€˜editâ€™ screen. <br /><br />Once the research request is assigned, choose â€˜edit collectionâ€™ to add the research request to â€˜My collectionâ€™ panel. Using the standard tools, it is then possible to add resources to the research. <br /><br />Once the research is complete, choose â€˜edit researchâ€™,  change the status to complete and an email is automatically  sent to the user who requested the research. The email contains a link to the research and it is also automatically added to their â€˜My Collectionâ€™ panel.', 48, 'en', NULL, NULL, NULL),
('collection_edit', 'introtext', 'Organise and manage your work by grouping resources together. Create â€˜Collectionsâ€™ to suit your way of working.\n\n<br />\n\nAll the collections in your list appear in the â€˜My Collectionsâ€™ panel at the bottom of the screen\n\n<br /><br />\n\n<strong>Private Access</strong> allows only you and and selected users to see the collection. Ideal for grouping resources under projects that you are working on independently and share resources amongst a project team.\n\n<br /><br />\n\n<strong>Public Access</strong> allows all users of the system to search and see the collection. Useful if you wish to share collections of resources that you think others would benefit from using.\n\n<br /><br />\n\nYou can choose whether you allow other users (public or users you have added to your private collection) to add and remove resources or simply view them for reference.', 49, 'en', NULL, NULL, NULL),
('team_stats', 'introtext', 'Charts are generated on demand based on live data. Tick the box to print all charts for your selected year.', 50, 'en', NULL, NULL, NULL),
('resource_request', 'introtext', 'Your request is almost complete. Please include the reason for your request so we can respond efficiently.', 51, 'en', NULL, NULL, NULL),
('team_batch', 'introtext', NULL, 52, 'en', NULL, NULL, NULL),
('team_batch_upload', 'introtext', NULL, 53, 'en', NULL, NULL, NULL),
('team_batch_select', 'introtext', NULL, 54, 'en', NULL, NULL, NULL),
('download_click', 'introtext', 'To download the resource file, right click the link below and choose "Save As...". You will then be asked where you would like to save the file. To open the file in your browser simply click the link.', 55, 'en', NULL, NULL, NULL),
('collection_manage', 'newcollection', 'To create a new collection, enter a short name.', 56, 'en', NULL, NULL, NULL),
('collection_email', 'introtext', 'Please complete the form below. The recipients will receive an email containing links to the collections rather than file attachments so they can choose and download the appropriate resources.', 57, 'en', NULL, NULL, NULL),
('all', 'footer', 'Powered by <a target="_blank" href="http://www.resourcespace.org/">ResourceSpace</a>: Open Source Digital Asset Management', 58, 'en', NULL, NULL, NULL),
('change_language', 'introtext', 'Please select your language below.', 59, 'en', NULL, NULL, NULL),
('all', 'searchpanel', 'Search using descriptions, keywords and resource numbers', 60, 'es', NULL, NULL, NULL),
('all', 'footer', 'Accionado por el <a href="http://www.resourcespace.org/">ResourceSpace</a>', 61, 'es', NULL, NULL, NULL),
('all', 'researchrequest', 'Let our resources team find the resources you need.', 62, 'es', NULL, NULL, NULL),
('delete', 'introtext', NULL, 68, 'es', NULL, NULL, NULL),
('contribute', 'introtext', 'You can contribute your own resources. When you initially create a resource it is in the "Pending Submission" status. When you have uploaded your file and edited the fields, set the status field to "Pending Review". It will then be reviewed by the resources team.', 69, 'es', NULL, NULL, NULL),
('done', 'deleted', 'The resource has been deleted.', 70, 'es', NULL, NULL, NULL),
('user_preferences', 'introtext', 'Enter a new password below to change your password.', 71, 'en', NULL, NULL, NULL),
('collection_manage', 'findpublic', 'Public collections are groups of resources made widely available by users of the system. Enter a collection ID, or all or part of a collection name or username to find public collections. Add them to your list of collections to access the resources.', 72, 'es', NULL, NULL, NULL),
('themes', 'findpublic', 'Powered by <a href="http://www.resourcespace.org/">ResourceSpace</a>', 73, 'es', NULL, NULL, NULL),
('login', 'welcomelogin', 'Welcome to ResourceSpace, please log in...', 74, 'en', NULL, NULL, NULL),
('all', 'footer', 'Desarrollado por <a href="http://www.resourcespace.org/">ResourceSpace</a>', 75, 'es', NULL, NULL, NULL),
('all', 'researchrequest', 'PÃ­denos fotografÃ­as, vÃ­deos o testimonios.', 76, 'es', NULL, NULL, NULL),
('contact', 'contact', 'Introduce aquÃ­ tus datos de contacto.', 77, 'es', NULL, NULL, NULL),
('collection_manage', 'newcollection', 'Para crear una nueva colecciÃ³n, introduce un nombre.', 78, 'es', NULL, NULL, NULL),
('delete', 'introtext', 'Por favor, introduce tu contraseÃ±a para confirmar que quieres borrar este contenido.', 79, 'es', NULL, NULL, NULL),
('done', 'deleted', 'El contenido ha sido borrado.', 80, 'es', NULL, NULL, NULL),
('done', 'research_request', 'Un miembro del equipo del SERGI se asignarÃ¡ a tu peticiÃ³n. Te mantendremos informado durante el proceso. Una vez tengamos los resultados de tu peticiÃ³n te enviaremos un correo electrÃ³nico con un enlace a los contenidos seleccionados.', 81, 'es', NULL, NULL, NULL),
('all', 'searchpanel', 'Busca por descripciones, palabras clave y cÃ³digos de contenido.', 82, 'es', NULL, NULL, NULL),
('about', 'about', 'ImÃ¡genes y Palabras pretende ser un lugar...', 83, 'es', NULL, NULL, NULL),
('collection_edit', 'introtext', 'Organiza tu trabajo agrupando recursos. Crea tantas colecciones como necesites.\n\n<br />\n\nTodas la colecciones aparecerÃ¡n en tu panel "Mis colecciones" en la parte inferior de la pantalla.\n\n<br /><br />\n\n<strong>Acceso privado</strong> sÃ³lo permite a ti y a los usuarios que selecciones a visualizar la colecciÃ³n. <br /><br />\n\n<strong>Accesos pÃºblico</strong> permite a todos los usuarios de la aplicaciÃ³n a visualizar la colecciÃ³n.\n\n<br /><br />\n\nTambiÃ©n puedes elegir quÃ© usuarios podrÃ¡n modificar la colecciÃ³n o simplemente podrÃ¡n visualizarla.', 84, 'es', NULL, NULL, NULL),
('collection_email', 'introtext', 'Completa el formulario adjunto para enviar por mail esta colecciÃ³n. RecibirÃ¡s un link con todo el material y podrÃ¡s elegir lo que mÃ¡s se adapte a tus necesidades.', 85, 'es', NULL, NULL, NULL),
('collection_manage', 'findpublic', 'Las colecciones pÃºblicas son grupos de material disponible para los usuarios del sistema. Para encontrarlas debes introducir un identificador. Ã‰ste puede ser el nombre completo o parcial de la colecciÃ³n que te interese o bien el ID de usuario de la misma. AÃ±Ã¡delo a tu lista de colecciones para acceder al material.\n\n\n\n', 86, 'es', NULL, NULL, NULL),
('home', 'restrictedtitle', '<h1>Bienvenido al Panel de Control</h1>', 87, 'es', NULL, NULL, NULL),
('home', 'themes', 'AquÃ­ encontrarÃ¡s los materiales recogidos recientemente.', 88, 'es', NULL, NULL, NULL),
('home', 'welcometext', NULL, 92, 'es', NULL, NULL, NULL),
('help', 'introtext', 'ObtÃ©n el mÃ¡ximo rendimiento del Espacio de Material. Estas guÃ­as te ayudarÃ¡n a usar el sistema con mayor efectividad.', 93, 'es', NULL, NULL, NULL),
('home', 'welcometitle', 'Bienvenida/o a ImÃ¡genes y Palabras, una herramienta que te permitirÃ¡ buscar fotografÃ­as, vÃ­deos y testimonios entre sus mÃ¡s de xxx materiales.', 94, 'es', NULL, NULL, NULL),
('team_batch', 'introtext', NULL, 95, 'es', NULL, NULL, NULL),
('team_home', 'introtext', 'Bienvenido al Panel de Control. Utiliza los enlaces siguientes para gestionar materiales, responder a peticiones de materiales, gestionar temas o modificar configuraciones del sistema.', 96, 'es', NULL, NULL, NULL),
('user_password', 'introtext', 'Introduce tu direcciÃ³n de e-mail y te enviaremos tu nombre de usuario y contraseÃ±a.', 97, 'es', NULL, NULL, NULL),
('team_user', 'introtext', 'Utiliza esta secciÃ³n para aÃ±adir, eliminar o modificar usuarios.', 98, 'es', NULL, NULL, NULL),
('collection_public', 'introtext', 'Encuentra una colecciÃ³n pÃºblica', 99, 'es', NULL, NULL, NULL),
('home', 'mycollections', 'Esta herramienta te permite seleccionar, organizar y compartir tu material. ', 100, 'es', NULL, NULL, NULL),
('collection_manage', 'introtext', 'Organiza y controla tu trabajo agrupando material. Crea "Colecciones" que se ajusten a tus necesidades de trabajo. PodrÃ­as querer agrupar colecciones en proyectos con los que trabajas independientemente, compartirlas con un equipo o simplemente guardar tu material favorito en un sitio concreto. Todas estas opciones aparecerÃ¡n en la pestaÃ±a "Mis colecciones" en un botÃ³n de tu pantalla.', 101, 'es', NULL, NULL, NULL),
('done', 'collection_email', 'Los usuarios que quieras pueden recibir un correo con un enlace a la colecciÃ³n que les envÃ­es. Esta recopilaciÃ³n se aÃ±ade a su "Lista de Colecciones"', 102, 'es', NULL, NULL, NULL),
('done', 'resource_request', 'Tu peticiÃ³n ha sido enviada y en breve nos pondremos en contacto contigo', 103, 'es', NULL, NULL, NULL),
('done', 'user_password', 'Te hemos envÃ­ado un correo electrÃ³nico con tu ID de usuario y tu contraseÃ±a', 104, 'es', NULL, NULL, NULL),
('done', 'user_request', 'Tu solicitud para obtener una cuenta de usuario ha sido enviada. Muy pronto te enviaremos los detalles.', 105, 'es', NULL, NULL, NULL),
('download_click', 'introtext', 'Para bajar el archivo adjunto pincha en el enlace de abajo y elige "Guardar como". Te preguntarÃ¡n dÃ³nde quieres guardar el archivo. Para abrir el archivo en tu navegador sÃ³lo tienes que pinchar el enlace.', 106, 'es', NULL, NULL, NULL),
('download_progress', 'introtext', 'La descarga comenzarÃ¡ en pocos segundos', 107, 'es', NULL, NULL, NULL),
('edit', 'batch', NULL, 108, 'es', NULL, NULL, NULL),
('edit', 'multiple', 'Selecciona los campos que desees editar de la lista de abajo. Los que selecciones se sobreescribirÃ¡n sobre el material que estÃ©s editando. Cualquier campo no selecciÃ³nado serÃ¡ ignorado.', 109, 'es', NULL, NULL, NULL),
('home', 'help', NULL, 110, 'es', NULL, NULL, NULL),
('home', 'restrictedtext', 'Por favor, pincha en el enlace que te envÃ­amos a tu correo para acceder a los materiales seleccionados para ti', 111, 'es', NULL, NULL, NULL),
('login', 'welcomelogin', 'Bienvenido a Palabras y FotografÃ­as', 112, 'es', NULL, NULL, NULL),
('research_request', 'introtext', 'Podemos ayudarte a encontrar el material que mejor se ajuste a tus necesidades. Completa el cuestionario tan minuciosamente como sea posible para que evaluemos los criterios que mÃ¡s te interesan. Estaremos en contacto por correo electrÃ³nico sobre la evoluciÃ³n del proceso y una vez hayamos completado la busqueda recibirÃ¡s un mail con un enlace al material que te recomendamos', 113, 'es', NULL, NULL, NULL),
('resource_email', 'introtext', 'Comparte este material rÃ¡pidamente vÃ­a mail. Se enviarÃ¡ automÃ¡ticamente un enlace. AdemÃ¡s puedes incluir texto informativo como parte del correo electrÃ³nico.', 114, 'es', NULL, NULL, NULL),
('resource_request', 'introtext', 'El material que has solicitado no esta disponible en Internet. La informaciÃ³n que has requerido se incluirÃ¡ automÃ¡ticamente en el correo electrÃ³nico, si quieres puedes aÃ±adir cualquier comentario adicional.', 115, 'es', NULL, NULL, NULL),
('search_advanced', 'introtext', 'BÃºsqueda por Campos. Cualquier secciÃ³n que dejes en blanco o no selecciones, incluirÃ¡ todos esos tÃ©rminos en la bÃºsqueda. Por ejemplo, si dejas todas las opciones de un paÃ­s vacÃ­as, la bÃºsqueda te darÃ¡ resultados de todos esos paÃ­ses. Si seleccionas sÃ³lo Ãfrica entonces los resultados sÃ³lo contendrÃ¡n material de Ãfrica.\n\n', 116, 'es', NULL, NULL, NULL),
('team_archive', 'introtext', 'Para editar materiales de archivo individualmente, simplemente busca por el material y pincha en "Herramienta de Material" en la misma pantalla. Todos los materiales listos para ser archivados serÃ¡n listados en Material Pendiente de Lista. Desde esta lista es posible aÃ±adir informaciÃ³n posteriormente y transferir el material grabado al archivo\n\n', 117, 'es', NULL, NULL, NULL),
('view', 'storyextract', 'Estracto de historia:', 118, 'es', NULL, NULL, NULL),
('user_request', 'introtext', 'Por favor completa el formulario adjunto para solicitar una cuenta de usuario.', 119, 'es', NULL, NULL, NULL),
('team_research', 'introtext', 'Organiza y controla â€œCriterios de BÃºsquedaâ€. Elige â€œEditar bÃºsquedaâ€ para examinar los detalles de la bÃºsqueda y asignarle la investigaciÃ³n a un miembro del equipo. Es posible basar una bÃºsqueda en una colecciÃ³n previa introduciendo el nombre de la misma en â€œEditâ€. Una vez sean asignados los criterios de bÃºsqueda, selecciona â€œEditar ColecciÃ³nâ€ para aÃ±adir los criterios al panel â€œMi colecciÃ³nâ€. Usando las herramientas estÃ¡ndar es posible aÃ±adir material a la bÃºsqueda. Una vez se ha completado, selecciona â€œEditar bÃºsquedaâ€ y cambia el estado para recibir automÃ¡ticamente un correo electrÃ³nico con la informaciÃ³n solicitada. El mail contendrÃ¡ un enlace a la bÃºsqueda y ademÃ¡s se aÃ±adirÃ¡ automÃ¡ticamente al panel â€œMi colecciÃ³nâ€.', 120, 'es', NULL, NULL, NULL),
('team_batch_select', 'introtext', NULL, 121, 'es', NULL, NULL, NULL),
('team_batch_upload', 'introtext', NULL, 122, 'es', NULL, NULL, NULL),
('team_resource', 'introtext', 'AÃ±ade material individual o sube varios materiales simultÃ¡neamente. Para editar materiales individuales sÃ³lo tienes que buscar el material, y pinchar en editar en "Herramienta de Material" de la pantalla de material.', 123, 'es', NULL, NULL, NULL),
('team_stats', 'introtext', 'Los grÃ¡ficos son generados a bajo demanda. Marca la casilla para imprimir todos los grÃ¡ficos del aÃ±os seleccionado.', 124, 'es', NULL, NULL, NULL),
('terms', 'introtext', 'Antes de iniciar el proceso de descarga debes aceptar los siguientes tÃ©rminos y condiciones de uso.\n\n', 125, 'es', NULL, NULL, NULL),
('themes', 'findpublic', 'Encuentra una colecciÃ³n pÃºblica introduciendo un tÃ©rmino de bÃºsqueda', 126, 'es', NULL, NULL, NULL),
('upload', 'introtext', NULL, 127, 'es', NULL, NULL, NULL),
('themes', 'manage', 'Organiza y edita los temas disponibles en la red. Los temas aparecen organizados en colecciones. Para crear una nueva entrada e incluirla en un tema, debes pinchar en "Construye una colecciÃ³n" y elegir "Mis Colecciones" del menÃº principal. Recuerda incluir el nombre de un tema durante la configuraciÃ³n y crear una nueva colecciÃ³n. Puedes usar un nombre ya existente para agrupar la nueva colecciÃ³n en un apartado ya establecido (es importante asegurarse de que el nombre es exactamente el mismo) o elegir un nuevo tÃ­tulo para crear una nueva categorÃ­a de temas. Nunca permitas a los usuarios que aÃ±adan o cambien materiales de las colecciones ya establecidas. Para editar el contenido de una entrada existente bajo un tema, elige â€œEditar una ColecciÃ³nâ€. Los iconos de esta colecciÃ³n aparecerÃ¡n en el botÃ³n de la pantalla â€œMis Coleccionesâ€. Emplea las herramientas estÃ¡ndar para editar, mover o aÃ±adir material. Para cambiar el  nombre de un tema o mover una colecciÃ³n y que Ã©sta aparezca ubicada en un tema diferente. Elige: â€œEditar Propiedadesâ€ y edita la categorÃ­a del tema o el nombre de la colecciÃ³n. Selecciona un nombre existente para el tema y asÃ­ agruparlo en de las categorÃ­as existentes (asegÃºrate de que lo escribes exactamente igual), o elige un nuevo tÃ­tulo para crear un nuevo tipo de tema. Para borrar una selecciÃ³n del tema. Elige: â€œEditar Propiedadesâ€ y borra las palabras de la caja de categorÃ­as del tema.', 128, 'es', NULL, NULL, NULL),
('themes', 'introtext', 'Los temas estÃ¡n formados por grupos con nuestros mejores materiales', 129, 'es', NULL, NULL, NULL),
('team_copy', 'introtext', 'Introduce el identificador del material que te gustarÃ­a copiar. SÃ³lo se copiarÃ¡ el material seÃ±alado -no se copiarÃ¡ ningÃºn archivo adjunto.', 130, 'es', NULL, NULL, NULL),
('change_language', 'introtext', 'Por favor, selecciona el idioma en el que deseas trabajar.', 131, 'es', NULL, NULL, NULL),
('team_content', 'introtext', NULL, 133, 'es', NULL, NULL, NULL),
('team_report', 'introtext', 'Por favor, elige un informe y un rango de fecha. El informe podrÃ¡ abrirse en un documento de Excel o un documento de caracterÃ­sticas similares.', 134, 'es', NULL, NULL, NULL),
('change_password', 'introtext', 'Por favor introduzca una nueva contraseÃ±a.', 135, 'es', NULL, NULL, NULL),
('upload_swf', 'introtext', NULL, 136, 'en', NULL, NULL, NULL),
('tag', 'introtext', 'Help to improve search results by tagging resources. Say what you see, separated by spaces or commas... for example: dog, house, ball, birthday cake. Enter the full name of anyone visible in the photo and the location the photo was taken if known.', 137, 'en', NULL, NULL, NULL),
('about', 'about', 'â˜…ã‚µã‚¤ãƒˆã®èª¬æ˜Žã‚’è¨˜è¿°ã—ã¦ãã ã•ã„â˜…', 138, 'jp', NULL, NULL, NULL),
('download_click', 'introtext', 'ãƒ•ã‚¡ã‚¤ãƒ«ã‚’ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰ã™ã‚‹ã«ã¯ã€ãƒªãƒ³ã‚¯ä¸Šã§å³ã‚¯ãƒªãƒƒã‚¯ã—"åå‰ã‚’ä»˜ã‘ã¦ãƒªãƒ³ã‚¯å…ˆã‚’ä¿å­˜"ã‚’é¸æŠžã—ã¾ã™ã€‚ ãƒ•ã‚¡ã‚¤ãƒ«ã®ä¿å­˜å…ˆã‚’èžã‹ã‚Œã¾ã™ã€‚ãƒ•ã‚¡ã‚¤ãƒ«ã‚’é–‹ãã«ã¯ãƒ–ãƒ©ã‚¦ã‚¶ã®ãƒ•ã‚¡ã‚¤ãƒ«ã‚’é–‹ãã®ãƒªãƒ³ã‚¯ã‚’ã‚¯ãƒªãƒƒã‚¯ã—ã¾ã™ã€‚', 139, 'jp', NULL, NULL, NULL),
('home', 'restrictedtitle', '<h1>â˜…ã‚µã‚¤ãƒˆã®åç§°ã‚’è¨˜è¿°ã—ã¦ãã ã•ã„â˜…</h1>', 140, 'jp', NULL, NULL, NULL),
('home', 'welcometitle', 'â˜…ã‚¦ã‚§ãƒ«ã‚«ãƒ ãƒ¡ãƒƒã‚»ãƒ¼ã‚¸ã®ã‚¿ã‚¤ãƒˆãƒ«ã‚’è¨˜è¿°ã—ã¦ãã ã•ã„â˜…', 141, 'jp', NULL, NULL, NULL),
('home', 'welcometext', 'â˜…ã‚¦ã‚§ãƒ«ã‚«ãƒ ãƒ¡ãƒƒã‚»ãƒ¼ã‚¸ã‚’è¨˜è¿°ã—ã¦ãã ã•ã„â˜…', 142, 'jp', NULL, NULL, NULL),
('all', 'researchrequest', 'ã‚ãªãŸãŒæ±‚ã‚ã¦ã„ã‚‹å†™çœŸç”»åƒã‚’èª¿æŸ»ä¾é ¼ã—ã¦ã¿ã¦ãã ã•ã„ã€‚', 143, 'jp', NULL, NULL, NULL),
('all', 'searchpanel', 'èª¬æ˜Žæ–‡ã€ã‚­ãƒ¼ãƒ¯ãƒ¼ãƒ‰ã€ãƒªã‚½ãƒ¼ã‚¹ãƒ»ãƒŠãƒ³ãƒãƒ¼ã‚’ä½¿ã£ã¦æ¤œç´¢ã—ã¦ãã ã•ã„ã€‚', 144, 'jp', NULL, NULL, NULL),
('change_language', 'introtext', 'ä¸‹è¨˜ã‹ã‚‰è¨€èªžã‚’é¸æŠžã—ã¦ãã ã•ã„ã€‚', 145, 'jp', NULL, NULL, NULL),
('change_password', 'introtext', 'ã‚ãªãŸã®ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰ã‚’å¤‰æ›´ã™ã‚‹ãŸã‚ã«ä»¥ä¸‹ã«æ–°ã—ã„ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰ã‚’å…¥åŠ›ã—ã¦ãã ã•ã„ã€‚', 146, 'jp', NULL, NULL, NULL),
('collection_edit', 'introtext', 'ãƒªã‚½ãƒ¼ã‚¹ã‚’åˆ†é¡žã™ã‚‹ã“ã¨ã«ã‚ˆã£ã¦ã€ä½œå“ã‚’çµ„ç¹”åŒ–ã—ã€ç®¡ç†ã—ã¦ãã ã•ã„ã€‚ã‚ãªãŸã®æµå„€ã§ä½œå“ã«é©ã—ãŸã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’ä½œæˆã—ã¦ãã ã•ã„ã€‚<br />ç”»é¢ã®ä¸‹éƒ¨ã®ãƒžã‚¤ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ãƒ»ãƒ‘ãƒãƒ«ã«ä¸€è¦§ãŒè¡¨ç¤ºã•ã‚Œã¾ã™ã€‚<br /><br />ã‚ãªãŸã¨é¸ã°ã‚ŒãŸäººã ã‘ãŒå‚ç…§ã§ãã‚‹<strong>ãƒ—ãƒ©ã‚¤ãƒ™ãƒ¼ãƒˆãƒ»ã‚¢ã‚¯ã‚»ã‚¹</strong>ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã§ã™ã€‚ã‚ãªãŸãŒå˜ç‹¬ã‚ã‚‹ã„ã¯ãƒ—ãƒ­ã‚¸ã‚§ã‚¯ãƒˆãƒãƒ¼ãƒ é–“ã§ãƒªã‚½ãƒ¼ã‚¹ã‚’å…±æœ‰ã—ã¦ä½œæ¥­ã™ã‚‹ä¸Šã§ãƒªã‚½ãƒ¼ã‚¹ã®ã‚°ãƒ«ãƒ¼ãƒ—åŒ–ã«æœ€é©ã§ã™ã€‚<br /><br /><strong>å…¬é–‹ã‚¢ã‚¯ã‚»ã‚¹</strong>ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã¯å…¨ãƒ¦ãƒ¼ã‚¶ãŒæ¤œç´¢ã¨å‚ç…§ãŒã§ãã¾ã™ã€‚ã‚ãªãŸãŒãƒªã‚½ãƒ¼ã‚¹ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’å…±æœ‰ã™ã‚‹ã¨ã€ä»–ã®äººã¯åˆ©ç›Šã‚’å¾—ã‚‹ã§ã—ã‚‡ã†ã€‚<br /><br />ã‚ãªãŸã¯ã€ä»–ã®ãƒ¦ãƒ¼ã‚¶(ã‚ãªãŸã®ãƒ—ãƒ©ã‚¤ãƒ™ãƒ¼ãƒˆãƒ»ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’å…¬é–‹ã‚‚ã—ãã¯è¿½åŠ ã—ãŸãƒ¦ãƒ¼ã‚¶)ã«ãƒªã‚½ãƒ¼ã‚¹ã‚’è¿½åŠ ã€å‰Šé™¤å¯èƒ½ã¨ã™ã‚‹ã‹ã€ã¾ãŸã¯å‚ç…§ã®ã¿ã¨ã™ã‚‹ã‹ã‚’é¸æŠžã§ãã¾ã™ã€‚', 147, 'jp', NULL, NULL, NULL),
('contact', 'contact', 'â˜…ã“ã¡ã‚‰ã«ã¯ã‚ãªãŸã®é€£çµ¡å…ˆã‚’è¨˜å…¥ã—ã¦ãã ã•ã„â˜…', 148, 'jp', NULL, NULL, NULL),
('search_advanced', 'introtext', '<strong>æ¤œç´¢ã®æŠ€</strong><br />æ¤œç´¢ã§ç©ºç™½ã®ã¾ã¾ã«ã™ã‚‹ã‹ã€ã¾ãŸã¯ãƒã‚§ãƒƒã‚¯ã‚’å…¥ã‚Œãªã‹ã£ãŸã‚»ã‚¯ã‚·ãƒ§ãƒ³ã¯ã™ã¹ã¦ã®ç”¨èªžã‚’å«ã‚“ã ã‚‚ã®ã¨ã—ã¦æ¤œç´¢ã—ã¾ã™ã€‚ ä¾‹ãˆã°ã€å›½ã‚’ç©ºç™½ã«ã™ã‚‹ã¨ã€æ¤œç´¢ã¯ã™ã¹ã¦ã®å›½ã‹ã‚‰çµæžœã‚’è¿”ã™ã§ã—ã‚‡ã†ã€‚ ã‚ãªãŸãŒã‚¢ãƒ•ãƒªã‚«ã ã‘ã‚’é¸æŠžã™ã‚‹ã¨ã€çµæžœã¯ã‚¢ãƒ•ãƒªã‚«ã‹ã‚‰ã®ãƒªã‚½ãƒ¼ã‚¹ã ã‘ã‚’å«ã‚€ã§ã—ã‚‡ã†ã€‚', 149, 'jp', NULL, NULL, NULL),
('collection_email', 'introtext', 'ä»¥ä¸‹ã®ãƒ•ã‚©ãƒ¼ãƒ ã«è¨˜å…¥ã—ã¦ã€ã“ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’ãƒ¡ãƒ¼ãƒ«ã—ã¦ãã ã•ã„ã€‚ ãƒ¦ãƒ¼ã‚¶ãŒé©åˆ‡ãªãƒªã‚½ãƒ¼ã‚¹ã‚’é¸ã‚“ã§ã€ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰ã§ãã‚‹ã‚ˆã†ã«ã€ãƒ•ã‚¡ã‚¤ãƒ«æ·»ä»˜ã‚ˆã‚Šã‚€ã—ã‚ãƒªã‚½ãƒ¼ã‚¹ã¸ã®ãƒªãƒ³ã‚¯ã‚’å—ã‘å–ã‚‹ã§ã—ã‚‡ã†ã€‚', 150, 'jp', NULL, NULL, NULL),
('collection_manage', 'findpublic', 'å…¬é–‹ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã¯ã‚·ã‚¹ãƒ†ãƒ ã®ãƒ¦ãƒ¼ã‚¶ã«ã‚ˆã£ã¦åºƒãåˆ©ç”¨å¯èƒ½ã«ã•ã‚ŒãŸãƒªã‚½ãƒ¼ã‚¹ã®ã‚°ãƒ«ãƒ¼ãƒ—ã§ã™ã€‚ ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³IDã‹ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³åã¾ãŸã¯ãƒ¦ãƒ¼ã‚¶åã®ã™ã¹ã¦ã¾ãŸã¯ä¸€éƒ¨ã‚’å…¥åŠ›ã—ã¦ã€å…¬é–‹ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’è¦‹ã¤ã‘ã¦ãã ã•ã„ã€‚ ã‚ãªãŸã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã®ãƒªã‚¹ãƒˆã«ãã‚Œã‚‰ã‚’è¿½åŠ ã—ã¦ã€ãƒªã‚½ãƒ¼ã‚¹ã«ã‚¢ã‚¯ã‚»ã‚¹ã—ã¦ãã ã•ã„ã€‚', 151, 'jp', NULL, NULL, NULL),
('collection_manage', 'introtext', 'ãƒªã‚½ãƒ¼ã‚¹ã‚’åˆ†é¡žã™ã‚‹ã“ã¨ã«ã‚ˆã£ã¦ã€ä½œå“ã‚’çµ„ç¹”åŒ–ã—ã¦ã€ç®¡ç†ã—ã¦ãã ã•ã„ã€‚ ã‚ãªãŸã®ã‚„ã‚Šæ–¹ã«åˆã£ãŸã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’ä½œæˆã—ã¦ãã ã•ã„ã€‚ ã‚ãªãŸã¯ã€ã‚ãªãŸãŒå˜ç‹¬ã§å–ã‚Šçµ„ã‚“ã§ã„ã‚‹ãƒ—ãƒ­ã‚¸ã‚§ã‚¯ãƒˆã®ä¸‹ã§ãƒªã‚½ãƒ¼ã‚¹ã‚’åˆ†é¡žã—ãŸã‚Šã€ãƒ—ãƒ­ã‚¸ã‚§ã‚¯ãƒˆãƒ»ãƒãƒ¼ãƒ ã§ãƒªã‚½ãƒ¼ã‚¹ã‚’å…±æœ‰ã—ãŸã‚Šã€ã¾ãŸã¯1ã¤ã®å ´æ‰€ã§å˜ã«ãŠæ°—ã«å…¥ã‚Šã®ãƒªã‚½ãƒ¼ã‚¹ã‚’åŽé›†ã™ã‚‹ã“ã¨ãŒã§ãã¾ã™ã€‚ ã‚ãªãŸã®ã™ã¹ã¦ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ãŒã‚¹ã‚¯ãƒªãƒ¼ãƒ³ä¸‹éƒ¨ã®ãƒžã‚¤ãƒ»ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ãƒ‘ãƒãƒ«ã«è¡¨ç¤ºã•ã‚Œã¾ã™ã€‚', 152, 'jp', NULL, NULL, NULL),
('home', 'help', 'æœ¬ã‚µã‚¤ãƒˆã‚’æœ€å¤§é™ã«æ´»ç”¨ã™ã‚‹ãŸã‚ã®ãƒ˜ãƒ«ãƒ—ã¨åŠ©è¨€ã€‚', 153, 'jp', NULL, NULL, NULL),
('home', 'mycollections', 'ãƒªã‚½ãƒ¼ã‚¹ã®çµ„ç¹”åŒ–ã€å…±åŒåŒ–ã€å…±æœ‰åŒ–ã—ã¦ãã ã•ã„ã€‚ ã‚ˆã‚ŠåŠ¹æžœçš„ã«åƒã‘ã‚‹ã‚ˆã†ã«ã“ã‚Œã‚‰ã®ãƒ„ãƒ¼ãƒ«ã‚’æ´»ç”¨ã—ã¦ãã ã•ã„ã€‚', 154, 'jp', NULL, NULL, NULL),
('home', 'restrictedtext', 'ã‚ãªãŸã®ãŸã‚ã«é¸æŠžã•ã‚ŒãŸãƒªã‚½ãƒ¼ã‚¹ã«ã‚¢ã‚¯ã‚»ã‚¹ã™ã‚‹ãŸã‚ã«ãƒ¡ãƒ¼ãƒ«ã•ã‚ŒãŸãƒªãƒ³ã‚¯ã‚’ã‚¯ãƒªãƒƒã‚¯ã—ã¦ãã ã•ã„ã€‚', 155, 'jp', NULL, NULL, NULL),
('login', 'welcomelogin', 'â˜…ã‚¦ã‚§ãƒ«ã‚«ãƒ ãƒ¡ãƒƒã‚»ãƒ¼ã‚¸ã‚’è¨˜å…¥ã—ã¦ãã ã•ã„â˜…', 156, 'jp', NULL, NULL, NULL),
('themes', 'findpublic', 'å…¬é–‹ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã¯ä»–ã®ãƒ¦ãƒ¼ã‚¶ã«ã‚ˆã£ã¦å…±æœ‰ã•ã‚ŒãŸãƒªã‚½ãƒ¼ã‚¹ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã§ã™ã€‚', 157, 'jp', NULL, NULL, NULL),
('user_password', 'introtext', 'Eãƒ¡ãƒ¼ãƒ«ã‚¢ãƒ‰ãƒ¬ã‚¹ã‚’å…¥åŠ›ã—ã¦ãã ã•ã„ã€‚ãã†ã™ã‚Œã°ã€ã‚ãªãŸã®ãƒ¦ãƒ¼ã‚¶åã¨ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰ãŒã‚ãªãŸã«é€ä¿¡ã•ã‚Œã‚‹ã§ã—ã‚‡ã†ã€‚', 158, 'jp', NULL, NULL, NULL),
('user_request', 'introtext', 'ä»¥ä¸‹ã®ãƒ•ã‚©ãƒ¼ãƒ ã«è¨˜å…¥ã—ã¦ã€ãƒ¦ãƒ¼ã‚¶ã‚¢ã‚«ã‚¦ãƒ³ãƒˆã‚’è¦æ±‚ã—ã¦ãã ã•ã„ã€‚', 159, 'jp', NULL, NULL, NULL),
('view', 'storyextract', 'ã‚¹ãƒˆãƒ¼ãƒªæŠ½å‡º:', 160, 'jp', NULL, NULL, NULL),
('collection_manage', 'newcollection', 'æ–°è¦ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’ä½œæˆã™ã‚‹ãŸã‚ã«çŸ­åç§°ã‚’å…¥åŠ›ã—ã¦ãã ã•ã„ã€‚', 161, 'jp', NULL, NULL, NULL),
('collection_public', 'introtext', 'ä»–ã®ãƒ¦ãƒ¼ã‚¶ã«ã‚ˆã£ã¦ä½œæˆã•ã‚ŒãŸå…¬é–‹ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã€‚', 162, 'jp', NULL, NULL, NULL),
('contribute', 'introtext', 'ã‚ãªãŸã¯ã‚ãªãŸè‡ªèº«ã®ãƒªã‚½ãƒ¼ã‚¹ã‚’æŠ•ç¨¿ã§ãã¾ã™ã€‚ ã‚ãªãŸãŒãƒªã‚½ãƒ¼ã‚¹ã‚’ä½œæˆã—ãŸç›´å¾Œã¯ç™»éŒ²å¾…ã¡çŠ¶æ…‹ã«ã‚ã‚Šã¾ã™ã€‚ ã‚ãªãŸã®ãƒ•ã‚¡ã‚¤ãƒ«ã‚’ã‚¢ãƒƒãƒ—ãƒ­ãƒ¼ãƒ‰ã—ã¦ã€ãƒ•ã‚£ãƒ¼ãƒ«ãƒ‰ã‚’ç·¨é›†å¾Œã«ã¯ãƒ¬ãƒ“ãƒ¥ãƒ¼å¾…ã¡çŠ¶æ…‹ã«è¨­å®šã•ã‚Œã¾ã™ã€‚ æ¬¡ã«ãƒªã‚½ãƒ¼ã‚¹ãƒãƒ¼ãƒ ã«ã‚ˆã£ã¦ãƒ¬ãƒ“ãƒ¥ãƒ¼ã•ã‚Œã‚‹ã§ã—ã‚‡ã†ã€‚', 163, 'jp', NULL, NULL, NULL),
('delete', 'introtext', 'ã“ã®ãƒªã‚½ãƒ¼ã‚¹ã‚’å‰Šé™¤ã™ã‚‹ã«ã¯ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰ã‚’å…¥åŠ›ã—ã¦ã€ç¢ºèªã—ã¦ãã ã•ã„ã€‚', 164, 'jp', NULL, NULL, NULL),
('done', 'collection_email', 'ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã¸ã®ãƒªãƒ³ã‚¯ã‚’å«ã‚€ãƒ¡ãƒ¼ãƒ«ã‚’ã‚ãªãŸãŒæŒ‡å®šã—ãŸãƒ¦ãƒ¼ã‚¶ã«é€ã‚Šã¾ã—ãŸã€‚ ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã¯''ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³''ãƒªã‚¹ãƒˆã«åŠ ãˆã‚‰ã‚Œã¾ã™ã€‚', 165, 'jp', NULL, NULL, NULL),
('done', 'deleted', 'ãƒªã‚½ãƒ¼ã‚¹ã¯å‰Šé™¤ã•ã‚Œã¾ã—ãŸã€‚', 166, 'jp', NULL, NULL, NULL),
('done', 'research_request', 'èª¿æŸ»ãƒãƒ¼ãƒ ã®ãƒ¡ãƒ³ãƒãƒ¼ãŒã‚ãªãŸã®è¦æ±‚ã«ã‚¢ã‚µã‚¤ãƒ³ã•ã‚Œã‚‹ã§ã—ã‚‡ã†ã€‚ ãƒ¡ãƒ¼ãƒ«ã§ç§ãŸã¡ã¯é€”ä¸­çµŒéŽã‚’ãŠçŸ¥ã‚‰ã›ã—ã¾ã™ã€‚ãã—ã¦ã€ç§ãŸã¡ãŒèª¿æŸ»ã‚’çµ‚äº†ã™ã‚‹ã¨ã€ã‚ãªãŸã¯ç§ãŸã¡ãŒæŽ¨è–¦ã™ã‚‹ã™ã¹ã¦ã®ãƒªã‚½ãƒ¼ã‚¹ã¸ã®ãƒªãƒ³ã‚¯ã‚’ãƒ¡ãƒ¼ãƒ«ã§å—ã‘å–ã‚Šã¾ã™ã€‚', 167, 'jp', NULL, NULL, NULL),
('done', 'resource_email', 'ãƒªã‚½ãƒ¼ã‚¹ã¸ã®ãƒªãƒ³ã‚¯ã‚’å«ã‚€ãƒ¡ãƒ¼ãƒ«ã‚’ã‚ãªãŸãŒæŒ‡å®šã—ãŸãƒ¦ãƒ¼ã‚¶ã«é€ã‚Šã¾ã—ãŸã€‚', 168, 'jp', NULL, NULL, NULL),
('done', 'resource_request', 'ã‚ãªãŸã®è¦æ±‚ã¯å—ä»˜ã—ã¾ã—ãŸã®ã§ã€ç§ãŸã¡ã¯ã¾ã‚‚ãªãã€é€£çµ¡ã™ã‚‹ã§ã—ã‚‡ã†ã€‚', 169, 'jp', NULL, NULL, NULL),
('done', 'user_password', 'ã‚ãªãŸã®ãƒ¦ãƒ¼ã‚¶åã¨ãƒ‘ã‚¹ãƒ¯ãƒ¼ãƒ‰ã‚’å«ã‚€ãƒ¡ãƒ¼ãƒ«ã‚’é€ã‚Šã¾ã—ãŸã€‚', 170, 'jp', NULL, NULL, NULL),
('done', 'user_request', 'ãƒ¦ãƒ¼ã‚¶ã‚¢ã‚«ã‚¦ãƒ³ãƒˆã‚’æ±‚ã‚ã‚‹ã‚ãªãŸã®è¦æ±‚ã‚’é€ã‚Šã¾ã—ãŸã€‚ ã¾ã‚‚ãªãã€ã‚ãªãŸã®ãƒ­ã‚°ã‚¤ãƒ³è©³ç´°ãŒé€ã‚‰ã‚Œã‚‹ã§ã—ã‚‡ã†ã€‚', 171, 'jp', NULL, NULL, NULL),
('download_progress', 'introtext', 'ã‚ãªãŸã®ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰ã¯ã¾ã‚‚ãªãã€å§‹ã¾ã‚‹ã§ã—ã‚‡ã†ã€‚ ãƒ€ã‚¦ãƒ³ãƒ­ãƒ¼ãƒ‰å®Œäº†å¾Œã€ç¶™ç¶šã™ã‚‹ã«ã¯ä»¥ä¸‹ã®ãƒªãƒ³ã‚¯ã‚’ä½¿ç”¨ã—ã¦ãã ã•ã„ã€‚', 172, 'jp', NULL, NULL, NULL),
('edit', 'batch', NULL, 173, 'jp', NULL, NULL, NULL),
('edit', 'multiple', 'ã©ã®ãƒ•ã‚£ãƒ¼ãƒ«ãƒ‰ã‚’ä¸Šæ›¸ãã—ãŸã„ã‹ã‚’é¸æŠžã—ã¦ãã ã•ã„ã€‚ ã‚ãªãŸãŒé¸æŠžã—ãªã„ãƒ•ã‚£ãƒ¼ãƒ«ãƒ‰ã¯ãã®ã¾ã¾ã®çŠ¶æ…‹ã§ãŠã‹ã‚Œã‚‹ã§ã—ã‚‡ã†ã€‚', 174, 'jp', NULL, NULL, NULL),
('help', 'introtext', '<p>æœ¬ã‚µã‚¤ãƒˆã‚’æœ€å¤§é™ã«æ´»ç”¨ã—ã¦ãã ã•ã„ã€‚ ã“ã‚Œã‚‰ã®ã‚¬ã‚¤ãƒ‰ã¯ã€ã‚ãªãŸãŒã€ã‚ˆã‚ŠåŠ¹æžœçš„ã«ã‚·ã‚¹ãƒ†ãƒ ã¨ãƒªã‚½ãƒ¼ã‚¹ã‚’ä½¿ç”¨ã™ã‚‹ã®ã‚’åŠ©ã‘ã‚‹ã§ã—ã‚‡ã†ã€‚ </p>    <p>ãƒ†ãƒ¼ãƒžã‚’ä½¿ç”¨ã—ã¦ã€ãƒ†ãƒ¼ãƒžã§ãƒªã‚½ãƒ¼ã‚¹ã‚’ãƒ–ãƒ©ã‚¦ã‚ºã™ã‚‹ã‹ã€ã¾ãŸã¯å˜ç´”æ¤œç´¢ã‚’ä½¿ç”¨ã—ã¦ã€ç‰¹å®šã®ãƒªã‚½ãƒ¼ã‚¹ã‚’æ¤œç´¢ã—ã¦ãã ã•ã„ã€‚</p>   <p><a href=http://www.montala.net/downloads/ResourceSpace-GettingStarted.pdf>Download the user guide (PDF file)</a></p>   <p><a target=_blank href=http://wiki.resourcespace.org/index.php/Main_Page>Online Documentation (Wiki)</a></p>', 175, 'jp', NULL, NULL, NULL),
('home', 'themes', 'ç²¾é¸ã•ã‚Œåˆ†é¡žã•ã‚ŒãŸæœ€è‰¯ã®ãƒªã‚½ãƒ¼ã‚¹', 176, 'jp', NULL, NULL, NULL),
('research_request', 'introtext', 'ã‚ãªãŸã®ãƒ—ãƒ­ã‚¸ã‚§ã‚¯ãƒˆã«ã€æœ€ã‚‚è‰¯ã„ãƒªã‚½ãƒ¼ã‚¹ã‚’è¦‹ã¤ã‘ã‚‹ã®æ‰‹åŠ©ã‘ã™ã‚‹ãŸã‚ã«ã€ç§ãŸã¡ã®ãƒ—ãƒ­ã®èª¿æŸ»è€…ãŒã“ã“ã«ã„ã¾ã™ã€‚ ç§ãŸã¡ãŒæ­£ç¢ºã«è©•ä¾¡åŸºæº–ã‚’æº€ãŸã™ã“ã¨ãŒã§ãã‚‹ã‚ˆã†ã«ã€ã“ã®ãƒ•ã‚©ãƒ¼ãƒ ã«ã§ãã‚‹ã ã‘å…¨ã¦ã«è¨˜å…¥ã—ã¦ãã ã•ã„ã€‚ <br /><br />èª¿æŸ»ãƒãƒ¼ãƒ ã®ãƒ¡ãƒ³ãƒãƒ¼ãŒã‚ãªãŸã®è¦æ±‚ã«ã‚¢ã‚µã‚¤ãƒ³ã•ã‚Œã‚‹ã§ã—ã‚‡ã†ã€‚ é€”ä¸­çµŒéŽã‚’ãƒ¡ãƒ¼ãƒ«ã§å ±å‘Šã—ã¾ã™ã€ãã—ã¦ã€ç§ãŸã¡ãŒèª¿æŸ»ã‚’å®Œäº†ã—ãŸå¾Œã€ç§ãŸã¡ãŒæŽ¨è–¦ã™ã‚‹ã™ã¹ã¦ã®ãƒªã‚½ãƒ¼ã‚¹ã¸ã®ãƒªãƒ³ã‚¯ã‚’ãƒ¡ãƒ¼ãƒ«ã§å—ã‘å–ã£ã¦ãã ã•ã„ã€‚', 177, 'jp', NULL, NULL, NULL),
('resource_email', 'introtext', 'ãƒ¡ãƒ¼ãƒ«ã§ç´ æ—©ãä»–ã®ãƒ¦ãƒ¼ã‚¶ã¨ã“ã®ãƒªã‚½ãƒ¼ã‚¹ã‚’å…±æœ‰ã§ãã¾ã™ã€‚ è‡ªå‹•çš„ã«ãƒªãƒ³ã‚¯ã‚’é€ä¿¡ã—ã¾ã™ã€‚ ã¾ãŸã€ã‚ãªãŸã¯ãƒ¡ãƒ¼ãƒ«ã®ä¸€éƒ¨ã¨ã—ã¦ä»»æ„ã®ãƒ¡ãƒƒã‚»ãƒ¼ã‚¸ã‚‚å…¥ã‚Œã‚‹ã“ã¨ãŒã§ãã¾ã™ã€‚', 178, 'jp', NULL, NULL, NULL),
('resource_request', 'introtext', 'ã”è¦æœ›ã®ãƒªã‚½ãƒ¼ã‚¹ã¯ã‚ªãƒ³ãƒ©ã‚¤ãƒ³ã§åˆ©ç”¨å¯èƒ½ã§ã¯ã‚ã‚Šã¾ã›ã‚“ã€‚ ãƒªã‚½ãƒ¼ã‚¹æƒ…å ±ã¯ãƒ¡ãƒ¼ãƒ«ã«è‡ªå‹•çš„ã«å«ã¾ã‚Œã¦ã„ã¾ã™ãŒã€æœ›ã‚€ãªã‚‰ã€ã‚ãªãŸã¯è¿½åŠ ã‚³ãƒ¡ãƒ³ãƒˆã‚’åŠ ãˆã‚‹ã“ã¨ãŒã§ãã¾ã™ã€‚', 179, 'jp', NULL, NULL, NULL),
('tag', 'introtext', 'ãƒªã‚½ãƒ¼ã‚¹ã«ã‚¿ã‚°ä»˜ã‘ã‚’ã™ã‚‹ã“ã¨ã«ã‚ˆã£ã¦æ¤œç´¢çµæžœã‚’æ”¹å–„ã§ãã¾ã™ã€‚ ä¾‹ãˆã°ã€ç©ºç™½ã‹ã‚³ãƒ³ãƒžã«ã‚ˆã£ã¦åˆ†å‰²ã•ã‚ŒãŸ: çŠ¬,å®¶,ãƒœãƒ¼ãƒ«,ãƒãƒ¼ã‚¹ãƒ‡ãƒ¼ã‚±ãƒ¼ã‚­ã€‚ å†™çœŸã«å†™ã£ã¦ã„ã‚‹äººã®ãƒ•ãƒ«ãƒãƒ¼ãƒ ã‚„åˆ†ã‹ã£ã¦ã„ã‚‹åœ°åã‚’å…¥ã‚Œã¦ãã ã•ã„ã€‚', 180, 'jp', NULL, NULL, NULL),
('team_archive', 'introtext', 'å€‹ã€…ã®ã‚¢ãƒ¼ã‚«ã‚¤ãƒ–ãƒªã‚½ãƒ¼ã‚¹ã‚’ç·¨é›†ã™ã‚‹ãŸã‚ã«ã€ãƒªã‚½ãƒ¼ã‚¹ã‚’å˜ç´”æ¤œç´¢ã—ã¦ãã ã•ã„ã€ãã—ã¦ã€ãƒªã‚½ãƒ¼ã‚¹ã‚¹ã‚¯ãƒªãƒ¼ãƒ³ã®ä¸Šã®ãƒªã‚½ãƒ¼ã‚¹ãƒ»ãƒ„ãƒ¼ãƒ« ãƒ‘ãƒãƒ«ã®ç·¨é›†ã‚’ã‚¯ãƒªãƒƒã‚¯ã—ã¦ãã ã•ã„ã€‚ ã‚¢ãƒ¼ã‚«ã‚¤ãƒ–ã•ã‚Œã‚‹æº–å‚™ãŒã§ãã¦ã„ã‚‹ãƒªã‚½ãƒ¼ã‚¹ãŒè¨˜è¼‰ã•ã‚ŒãŸãƒªã‚½ãƒ¼ã‚¹ãƒ»ãƒšãƒ³ãƒ‡ã‚£ãƒ³ã‚° ãƒªã‚¹ãƒˆã§ã™ã€‚ ã“ã®ãƒªã‚¹ãƒˆã‹ã‚‰ã€ã‚¢ãƒ¼ã‚«ã‚¤ãƒ–ã«è©³ç´°æƒ…å ±ã®è¿½åŠ ã¨ã€ãƒªã‚½ãƒ¼ã‚¹ãƒ»ãƒ¬ã‚³ãƒ¼ãƒ‰ã‚’ç§»ã™ã®ã¯å¯èƒ½ã§ã™ã€‚', 181, 'jp', NULL, NULL, NULL),
('team_batch', 'introtext', NULL, 182, 'jp', NULL, NULL, NULL),
('team_batch_select', 'introtext', NULL, 183, 'jp', NULL, NULL, NULL),
('team_batch_upload', 'introtext', NULL, 184, 'jp', NULL, NULL, NULL);
INSERT INTO `site_text` (`page`, `name`, `text`, `ref`, `language`, `ignore_me`, `specific_to_group`, `custom`) VALUES
('team_copy', 'introtext', 'ã‚ãªãŸãŒã‚³ãƒ”ãƒ¼ã—ãŸã„ãƒªã‚½ãƒ¼ã‚¹ã®IDã‚’å…¥åŠ›ã—ã¦ãã ã•ã„ã€‚ ãƒªã‚½ãƒ¼ã‚¹ãƒ‡ãƒ¼ã‚¿ã ã‘ãŒã‚³ãƒ”ãƒ¼ã•ã‚Œã‚‹ã§ã—ã‚‡ã†--ã‚¢ãƒƒãƒ—ãƒ­ãƒ¼ãƒ‰ã•ã‚ŒãŸãƒ•ã‚¡ã‚¤ãƒ«ã¯ã‚³ãƒ”ãƒ¼ã•ã‚Œãªã„ã§ã—ã‚‡ã†ã€‚', 185, 'jp', NULL, NULL, NULL),
('team_home', 'introtext', 'ãƒãƒ¼ãƒ ã‚»ãƒ³ã‚¿ãƒ¼ã¸ã‚ˆã†ã“ãã€‚ ä»¥ä¸‹ã®ãƒªãƒ³ã‚¯ã‚’ä½¿ç”¨ã—ã¦ã€ãƒªã‚½ãƒ¼ã‚¹ã®ç®¡ç†ã€ãƒªã‚½ãƒ¼ã‚¹ã®è¦æ±‚ã¸å¯¾å¿œã€ãƒ†ãƒ¼ãƒžã®ç®¡ç†ã€ã‚·ã‚¹ãƒ†ãƒ è¨­å®šã®å¤‰æ›´ã‚’ã—ã¦ãã ã•ã„ã€‚', 186, 'jp', NULL, NULL, NULL),
('team_report', 'introtext', 'ãƒ¬ãƒãƒ¼ãƒˆã¨æ—¥ä»˜ã®ç¯„å›²ã‚’é¸ã‚“ã§ãã ã•ã„ã€‚ Microsoft Excelã‹åŒæ§˜ã®ã‚¹ãƒ—ãƒ¬ãƒƒãƒ‰ã‚·ãƒ¼ãƒˆã‚¢ãƒ—ãƒªã‚±ãƒ¼ã‚·ãƒ§ãƒ³ã§ãƒ¬ãƒãƒ¼ãƒˆã‚’é–‹ãã“ã¨ãŒã§ãã¾ã™ã€‚', 187, 'jp', NULL, NULL, NULL),
('team_research', 'introtext', 'èª¿æŸ»è¦æ±‚ã®çµ„ç¹”åŒ–ã¨ç®¡ç†ã€‚ <Br><Br>èª¿æŸ»ã®ç·¨é›†ã‚’é¸ã‚“ã§ã€è¦æ±‚ã®è©³ç´°ã‚’å†æ¤œè¨Žã—ã¦ã€èª¿æŸ»ã‚’ãƒãƒ¼ãƒ ãƒ¡ãƒ³ãƒãƒ¼ã«å‰²ã‚Šå½“ã¦ã¦ãã ã•ã„ã€‚ ç·¨é›†ç”»é¢ã«ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³IDã‚’å…¥åŠ›ã™ã‚‹ã“ã¨ã«ã‚ˆã£ã¦èª¿æŸ»è¦æ±‚ã‚’ä»¥å‰ã®ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã«åŸºç¤Žã¥ã‘ã‚‹ã®ã¯å¯èƒ½ã§ã™ã€‚ <Br><Br>èª¿æŸ»è¦æ±‚ãŒã„ã£ãŸã‚“å‰²ã‚Šå½“ã¦ã‚‰ã‚ŒãŸå¾Œã€ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ç·¨é›†ã‚’é¸ã‚“ã§ã€ãƒžã‚¤ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ãƒ‘ãƒãƒ«ã«èª¿æŸ»è¦æ±‚ã‚’åŠ ãˆã¦ãã ã•ã„ã€‚ æ¨™æº–ã®ãƒ„ãƒ¼ãƒ«ã‚’ä½¿ç”¨ã—ã¦ã€ãã—ã¦ã€ãƒªã‚½ãƒ¼ã‚¹ã‚’èª¿æŸ»ã«è¿½åŠ ã™ã‚‹ã®ã¯å¯èƒ½ã§ã™ã€‚ <Br><Br>èª¿æŸ»ãŒã„ã£ãŸã‚“å®Œäº†ã™ã‚‹ã¨èª¿æŸ»ç·¨é›†ã‚’é¸ã‚“ã§ã€ã‚¹ãƒ†ãƒ¼ã‚¿ã‚¹ã‚’å®Œäº†ã«å¤‰ãˆã¦ãã ã•ã„ã€‚ãã†ã™ã‚Œã°ã€è‡ªå‹•çš„ã«èª¿æŸ»ã‚’è¦æ±‚ã—ãŸãƒ¦ãƒ¼ã‚¶ã«ãƒ¡ãƒ¼ãƒ«ã‚’é€ã‚Šã¾ã™ã€‚ ãƒ¡ãƒ¼ãƒ«ã¯èª¿æŸ»ã¸ã®ãƒªãƒ³ã‚¯ã‚’å«ã‚“ã§ã„ã¾ã™ã€ãã—ã¦ã€ã¾ãŸã€ãã‚Œã¯è‡ªå‹•çš„ã«å½¼ã‚‰ã®ãƒžã‚¤ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ãƒ‘ãƒãƒ«ã«åŠ ãˆã‚‰ã‚Œã¾ã™ã€‚', 188, 'jp', NULL, NULL, NULL),
('team_resource', 'introtext', 'å€‹åˆ¥ã«ãƒªã‚½ãƒ¼ã‚¹è¿½åŠ ã‹ãƒªã‚½ãƒ¼ã‚¹ã‚’ãƒãƒƒãƒã§ã‚¢ãƒƒãƒ—ãƒ­ãƒ¼ãƒ‰ã—ã¦ãã ã•ã„ã€‚ å€‹ã€…ã®ãƒªã‚½ãƒ¼ã‚¹ã‚’ç·¨é›†ã™ã‚‹ã«ã¯ã€å˜ç´”æ¤œç´¢ã§ãƒªã‚½ãƒ¼ã‚¹ã‚’æ¤œç´¢ã—ã¦ã€ãƒªã‚½ãƒ¼ã‚¹ã‚¹ã‚¯ãƒªãƒ¼ãƒ³ä¸Šã®ãƒªã‚½ãƒ¼ã‚¹ãƒ„ãƒ¼ãƒ«ãƒ‘ãƒãƒ«ã®ç·¨é›†ã‚’ã‚¯ãƒªãƒƒã‚¯ã—ã¾ã™ ã€‚', 189, 'jp', NULL, NULL, NULL),
('team_stats', 'introtext', 'ãƒãƒ£ãƒ¼ãƒˆã¯ãƒ©ã‚¤ãƒ–ãƒ‡ãƒ¼ã‚¿ã‹ã‚‰è¦æ±‚ã«å¿œã˜ã¦ä½œã‚‰ã‚Œã¾ã™ã€‚ é¸æŠžã•ã‚ŒãŸå¹´ã®ã™ã¹ã¦ã®ãƒãƒ£ãƒ¼ãƒˆã‚’å°åˆ·ã™ã‚‹ã«ã¯ãƒã‚§ãƒƒã‚¯ãƒœãƒƒã‚¯ã‚¹ã‚’ã‚ªãƒ³ã«ã—ã¾ã™ã€‚', 190, 'jp', NULL, NULL, NULL),
('team_user', 'introtext', 'ãƒ¦ãƒ¼ã‚¶ã®è¿½åŠ ã€å‰Šé™¤ã€å¤‰æ›´ã‚’ã™ã‚‹ã«ã¯ã“ã®ã‚»ã‚¯ã‚·ãƒ§ãƒ³ã‚’ä½¿ç”¨ã—ã¦ãã ã•ã„ã€‚', 191, 'jp', NULL, NULL, NULL),
('terms', 'introtext', 'å…ˆã«é€²ã‚ã‚‹ãŸã‚ã«ã¯ã‚ãªãŸã¯æ¡ä»¶ã«åŒæ„ã—ãªã‘ã‚Œã°ãªã‚Šã¾ã›ã‚“ã€‚', 192, 'jp', NULL, NULL, NULL),
('terms', 'terms', 'â˜…ä½¿ç”¨æ¡ä»¶ã‚’è¨˜å…¥ã—ã¦ãã ã•ã„â˜…', 193, 'jp', NULL, NULL, NULL),
('terms and conditions', 'terms and conditions', 'â˜…ä½¿ç”¨æ¡ä»¶ã‚’è¨˜å…¥ã—ã¦ãã ã•ã„â˜…', 194, 'jp', NULL, NULL, NULL),
('themes', 'introtext', 'ãƒ†ãƒ¼ãƒžã¯ãƒªã‚½ãƒ¼ã‚¹ã®ã‚°ãƒ«ãƒ¼ãƒ—ã§ã™ã€‚', 195, 'jp', NULL, NULL, NULL),
('themes', 'manage', 'ã‚ªãƒ³ãƒ©ã‚¤ãƒ³ã§åˆ©ç”¨å¯èƒ½ãªãƒ†ãƒ¼ãƒžã‚’çµ„ç¹”åŒ–ã—ã€ç·¨é›†ã—ã¦ãã ã•ã„ã€‚ ãƒ†ãƒ¼ãƒžã¯ç‰¹ã«ã€æŽ¨è–¦ã•ã‚ŒãŸã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã§ã™ã€‚ <br /><br /> <strong>1 ãƒ†ãƒ¼ãƒžã®ä¸‹ã«æ–°è¦ã‚¨ãƒ³ãƒˆãƒªãƒ¼ä½œæˆ -  ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³æ§‹ç¯‰</strong><br /> ãƒ¡ã‚¤ãƒ³ã®ãƒˆãƒƒãƒ—ãƒ¡ãƒ‹ãƒ¥ãƒ¼ã‹ã‚‰<strong>ãƒžã‚¤ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³</strong>ã‚’é¸æŠžã—ã€ æ–°è¦ã®<strong>å…¬é–‹</strong> ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’è¨­å®šã€‚ ã‚»ãƒƒãƒˆã‚¢ãƒƒãƒ—ã®é–“ã€å¿˜ã‚Œãšã«ãƒ†ãƒ¼ãƒžåã‚’å«ã‚“ã§ãã ã•ã„ã€‚ ç¾åœ¨ã®ãƒ†ãƒ¼ãƒžã®ä¸‹ã§ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’åˆ†é¡žã™ã‚‹ã®ã«æ—¢å­˜ã®ãƒ†ãƒ¼ãƒžåã‚’ä½¿ç”¨ã™ã‚‹ã‹(å…¨ãåŒä¸€)ã€ã¾ãŸã¯æ–°ã—ã„ã‚¿ã‚¤ãƒˆãƒ«ã‚’é¸ã‚“ã§ã€çœŸæ–°ã—ã„ãƒ†ãƒ¼ãƒžã‚’ä½œæˆã—ã¦ãã ã•ã„ã€‚ ãƒ¦ãƒ¼ã‚¶ã«ãƒ†ãƒ¼ãƒžåŒ–ã•ã‚ŒãŸã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‹ã‚‰ãƒªã‚½ãƒ¼ã‚¹ã‚’è¿½åŠ /å‰Šé™¤ã•ã›ãªã„ã‚ˆã†ã«ã—ã¦ãã ã•ã„ã€‚<br /> <br /><strong>2 ãƒ†ãƒ¼ãƒžã®ä¸‹ã®æ—¢å­˜ã®ã‚¨ãƒ³ãƒˆãƒªãƒ¼ã®å†…å®¹ã‚’ç·¨é›†</strong><br /><strong>ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ç·¨é›†</strong>ã‚’é¸æŠžã€‚ ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã®ã‚¢ã‚¤ãƒ†ãƒ ã¯ç”»é¢ä¸‹éƒ¨ã®<strong>ãƒžã‚¤ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³</strong> ãƒ‘ãƒãƒ«ã«è¡¨ç¤ºã•ã‚Œã¾ã™ã€‚ãƒªã‚½ãƒ¼ã‚¹ã®ç·¨é›†ã€å‰Šé™¤ã€è¿½åŠ ã®ãŸã‚ã«æ¨™æº–ãƒ„ãƒ¼ãƒ«ã‚’ä½¿ç”¨ã€‚<br /> <br /><strong>3 ãƒ†ãƒ¼ãƒžåã®å¤‰æ›´ã‚„åˆ¥ã®ãƒ†ãƒ¼ãƒžã®ä¸‹ã«è¡¨ç¤ºã•ã‚Œã‚‹ã‚ˆã†ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’ç§»å‹•</strong><br /><strong>ãƒ—ãƒ­ãƒ‘ãƒ†ã‚£ç·¨é›†</strong>ã‚’é¸æŠžã—ãƒ†ãƒ¼ãƒžã‚«ãƒ†ã‚´ãƒªã‚„ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³åã‚’ç·¨é›†ã€‚ç¾åœ¨ã®ãƒ†ãƒ¼ãƒžã®ä¸‹ã§ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³ã‚’åˆ†é¡žã™ã‚‹ã®ã«æ—¢å­˜ã®ãƒ†ãƒ¼ãƒžåã‚’ä½¿ç”¨ã™ã‚‹ã‹(å…¨ãåŒä¸€)ã€ã¾ãŸã¯æ–°ã—ã„ã‚¿ã‚¤ãƒˆãƒ«ã‚’é¸ã‚“ã§ã€çœŸæ–°ã—ã„ãƒ†ãƒ¼ãƒžã‚’ä½œæˆã—ã¦ãã ã•ã„ã€‚ <br /> <br /><strong>4 ãƒ†ãƒ¼ãƒžã‹ã‚‰ã‚³ãƒ¬ã‚¯ã‚·ãƒ§ãƒ³å‰Šé™¤</strong><br /><strong>ãƒ—ãƒ­ãƒ‘ãƒ†ã‚£ç·¨é›†</strong>ã‚’é¸æŠžã—ãƒ†ãƒ¼ãƒžã‚«ãƒ†ã‚´ãƒªæ¬„ã®æ–‡å­—ã‚’å‰Šé™¤ã€‚', 196, 'jp', NULL, NULL, NULL),
('upload', 'introtext', NULL, 197, 'jp', NULL, NULL, NULL),
('upload_swf', 'introtext', NULL, 198, 'jp', NULL, NULL, NULL),
('collection_public', 'introtext', 'Les collections publiques sont crÃ©Ã©es par d''autres utilisateurs', 199, 'fr', NULL, NULL, NULL),
('all', 'searchpanel', 'Recherchez en utilisant les descriptions, mots clÃ©s et numÃ©ros de documents', 200, 'fr', NULL, NULL, NULL),
('home', 'themes', 'Les meilleurs documents, sÃ©lectionnÃ©s et regroupÃ©s', 201, 'fr', NULL, NULL, NULL),
('home', 'mycollections', 'Organisez et Ã©changez vos documents. Ces outils vous aident Ã  travailler plus efficacement.', 202, 'fr', NULL, NULL, NULL),
('home', 'help', 'Aide et conseils pour obtenir le meilleur de ResourceSpace.', 203, 'fr', NULL, NULL, NULL),
('home', 'welcometitle', 'Bienvenue dans ResourceSpace', 204, 'fr', NULL, NULL, NULL),
('home', 'welcometext', 'Votre texte d''introduction ici.', 205, 'fr', NULL, NULL, NULL),
('themes', 'introtext', 'Les thÃ¨mes sont des groupes de ressources.', 206, 'fr', NULL, NULL, NULL),
('edit', 'multiple', 'Veuillez choisir les champs que vous souhaitez Ã©craser. Les champs non sÃ©lectionnÃ©s resteront inchangÃ©s.', 207, 'fr', NULL, NULL, NULL),
('team_archive', 'introtext', 'Pour modifier un document archivÃ©, recherchez simplement le document, et cliquez sur Modifier dans le panneau ''Outils de document'' sur l''Ã©cran du document. Tous les documents qui sont prÃªts Ã  Ãªtre archivÃ©s sont listÃ©s dans la liste des documents en attente. Depuis cette liste, il est possible d''ajouter des informations supplÃ©mentaires et d''archiver le document.', 208, 'fr', NULL, NULL, NULL),
('research_request', 'introtext', 'Nos professionnels sont lÃ  pour vous assister dans la recherche des meilleurs documents pour vos projets. Remplissez ce formulaire aussi prÃ©cisÃ©ment que possible pour que nous soyons capables de rÃ©pondre prÃ©cisÃ©ment Ã  votre requÃªte. <br/><br/> Un membre de notre Ã©quipe de recherche sera assignÃ© Ã  votre requÃªte. Nous garderons le contact via e-mail tout au long du processus. Et une fois la recherche terminÃ©e, vous recevrez par e-mail un lien vers tous les documents que nous recommandons.  ', 209, 'fr', NULL, NULL, NULL),
('collection_manage', 'introtext', 'Organisez et gÃ©rez votre travail en groupant les documents. CrÃ©ez des ''Collections'' selon votre mÃ©thode de travail. Vous pouvez grouper les documents par projet, les partager avec l''Ã©quipe du projet, ou simplement conserver vos documents prÃ©fÃ©rÃ©s en un seul endroit. Toutes vos collections sont listÃ©es dans le panneau ''Mes collections'' en bas de l''Ã©cran.', 210, 'fr', NULL, NULL, NULL),
('collection_manage', 'findpublic', 'Les collections publiques sont des groupes de documents rendus disponibles pour tous par d''autres utilisateurs du systÃ¨me. Saisissez un numÃ©ro de collection, ou tout ou partie d''un nom de collection ou d''utilisateur pour trouver une collection publique. Ajoutez-les Ã  votre liste de collections pour accÃ©der Ã  leur contenu.', 211, 'fr', NULL, NULL, NULL),
('team_home', 'introtext', 'Bienvenue dans l''espace Ã©quipe. Utilisez les liens ci-dessous pour administrer les documents, rÃ©pondre aux requÃªtes de documents, gÃ©rer les thÃ¨mes et modifier les rÃ©glages du systÃ¨me.', 212, 'fr', NULL, NULL, NULL),
('help', 'introtext', 'Obtenez le meilleur de ResourceSpace. Ces guides vous aideront Ã  utiliser le systÃ¨me et les documents plus efficacement. </p>\n\n\n\n<p>Utilisez le menu "ThÃ¨mes" pour parcourir les ressources par thÃ¨me ou utilisez la recherche simple pour trouver des ressources spÃ©cifiques.</p>\n\n\n<p><a href="http://www.montala.net/downloads/ResourceSpace-GettingStarted.pdf">TÃ©lÃ©chargez le guide utilisateur (fichier PDF) (anglais)</a>\n\n\n<p><a target="_blank" href="http://wiki.resourcespace.org/index.php/Main_Page">Documentation en ligne (Wiki) (anglais)</a>', 213, 'fr', NULL, NULL, NULL),
('terms and conditions', 'terms and conditions', 'Placez ici vos termes et conditions.', 214, 'fr', NULL, NULL, NULL),
('contribute', 'introtext', 'Vous pouvez proposer vos propres documents. Quand vous crÃ©ez un nouveau document, son statut est "En attente de soumission". Quand vous avez dÃ©posÃ© votre fichier et modifiÃ© les champs, affectez la valeur "En attente de validation" au champ statut. Votre document sera alors vÃ©rifiÃ© par l''Ã©quipe ressources.', 215, 'fr', NULL, NULL, NULL),
('done', 'user_password', 'Un mÃ©l contenant vos identifiant et mot de passe a Ã©tÃ© envoyÃ©.', 216, 'fr', NULL, NULL, NULL),
('user_password', 'introtext', 'Saisissez votre adresse Ã©lectronique pour recevoir vos identifiant et mot de passe.', 217, 'fr', NULL, NULL, NULL),
('edit', 'batch', NULL, 218, 'fr', NULL, NULL, NULL),
('team_copy', 'introtext', 'SpÃ©cifiez le numÃ©ro du document que vous voulez copier. Seules les donnÃ©es du document seront copiÃ©es â€“ aucun fichier ne sera copiÃ©.', 219, 'fr', NULL, NULL, NULL),
('delete', 'introtext', 'Veuillez saisir votre mot de passe afin de confirmer la suppression de ce document.', 220, 'fr', NULL, NULL, NULL),
('team_report', 'introtext', 'Veuillez choisir un type de rapport et une plage de dates. Le rapport peut Ãªtre ouvert avec Microsoft Excel ou tout tableur similaire.', 221, 'fr', NULL, NULL, NULL),
('terms', 'introtext', 'Avant de continuer, vous devez accepter les termes et conditions.', 222, 'fr', NULL, NULL, NULL),
('download_progress', 'introtext', 'Votre tÃ©lÃ©chargement va dÃ©marrer rapidement. Quand le tÃ©lÃ©chargement sera terminÃ©, utilisez les liens ci-dessous pour continuer.', 223, 'fr', NULL, NULL, NULL),
('view', 'storyextract', 'Historique d''extraction:', 224, 'fr', NULL, NULL, NULL),
('contact', 'contact', 'Placez les dÃ©tails pour vous contacter ici.', 225, 'fr', NULL, NULL, NULL),
('search_advanced', 'introtext', '<strong>Astuce de recherche</strong><br />Toute section que vous laissez vide ou dÃ©cochÃ©e inclura TOUS les documents dans la recherche. Par exemple, si vous laissez vide le choix du pays, la recherche renverra des rÃ©sultats de tous les pays. Si vous choisissez seulement ''Afrique'' alors les rÃ©sultats contiendront SEULEMENT des documents de l''''Afrique''.', 226, 'fr', NULL, NULL, NULL),
('all', 'researchrequest', 'Laissez notre Ã©quipe documents trouver les documents dont vous avez besoin.', 227, 'fr', NULL, NULL, NULL),
('done', 'research_request', 'Un membre de l''Ã©quipe documents sera assignÃ© Ã  votre demande. Nous resterons en contact par mÃ©l au cours du processus, et une fois la recherche terminÃ©e, vous recevrez par mÃ©l un lien vers tous les documents que nous recommandons.', 228, 'fr', NULL, NULL, NULL),
('done', 'collection_email', 'Un mÃ©l contenant un lien vers la collection a Ã©tÃ© envoyÃ© aux utilisateurs spÃ©cifiÃ©s. La collection a Ã©tÃ© ajoutÃ©e Ã  leur liste de ''Collections''.', 229, 'fr', NULL, NULL, NULL),
('done', 'resource_email', 'Un mÃ©l contenant un lien vers le document a Ã©tÃ© envoyÃ© aux utilisateurs spÃ©cifiÃ©s.', 230, 'fr', NULL, NULL, NULL),
('themes', 'manage', 'Organiser et gÃ©rer les thÃ¨mes disponibles. Les thÃ¨mes sont des collections particuliÃ¨rement mises en avant.<br/><br/><strong>1. Pour crÃ©er une nouvelle entrÃ©e dans un thÃ¨me â€“ constuire une collection</strong><br/> Choisissez <strong>Mes collections</strong> depuis le menu principal et crÃ©ez une nouvelle collection <strong>publique</strong>. Rapellez-vous d''inclure un nom de thÃ¨me durant la crÃ©ation de la collection. Utilisez un nom de thÃ¨me existant pour grouper la collection dans un thÃ¨me dÃ©jÃ  prÃ©sent (assurez-vous de le saisir Ã  l''identique), ou choisissez un nouveau nom pour crÃ©er un nouveau thÃ¨me. N''autorisez jamais vos utilisateurs Ã  ajouter / retirer des documents de collections thÃ©matiques.<br/><br/><strong>2. Pour modifier le contenu d''une collection thÃ©matique</strong><br/> Choisissez <strong>Modifier la collection</strong>. Les Ã©lÃ©ments dans cette collection apparaÃ®tront dans le panneau <strong>Mes collections</strong> en bas de l''Ã©cran. Utilisez les outils standard pour modifier, retirer ou ajouter des documents.<br/><br/><strong>3. Pour modifier un nom de thÃ¨me ou dÃ©placer une collection pour qu''elle apparaisse dans un autre thÃ¨me</strong><br/> Choisissez <strong>Modifier les propriÃ©tÃ©s</strong> et modifiez la catÃ©gorie du thÃ¨me ou le nom de la collection. Utilisez un nom de thÃ¨me existant pour grouper la collection dans un thÃ¨me dÃ©jÃ  prÃ©sent (assurez-vous de le saisir Ã  l''identique), ou choisissez un nouveau nom pour crÃ©er un nouveau thÃ¨me.<br/><br/><strong>4. Pour retirer une collection d''un thÃ¨me</strong><br/> Choisissez <strong>Modifier les propriÃ©tÃ©s</strong>et supprimez les mots dans le champ de catÃ©gorie du thÃ¨me.', 231, 'fr', NULL, NULL, NULL),
('terms', 'terms', 'Placez ici vos termes et conditions.', 232, 'fr', NULL, NULL, NULL),
('done', 'resource_request', 'Votre requÃªte a Ã©tÃ© soumise et vous serez contactÃ© rapidement.', 233, 'fr', NULL, NULL, NULL),
('user_request', 'introtext', 'Veuillez complÃ©ter le formulaire ci-dessous pour demander un compte utilisateur.', 234, 'fr', NULL, NULL, NULL),
('themes', 'findpublic', 'Les collections publiques sont des collections de documents qui ont Ã©tÃ© partagÃ©es par d''autres utilisateurs.', 235, 'fr', NULL, NULL, NULL),
('done', 'user_request', 'Votre demande de compte utilisateur a Ã©tÃ© envoyÃ©e. Vos identifiant et mot de passe vous seront envoyÃ©s prochainement.', 236, 'fr', NULL, NULL, NULL),
('about', 'about', 'Placez ici votre texte d''Ã  propos.', 237, 'fr', NULL, NULL, NULL),
('team_content', 'introtext', NULL, 238, 'fr', NULL, NULL, NULL),
('done', 'deleted', 'Le document a Ã©tÃ© supprimÃ©.', 239, 'fr', NULL, NULL, NULL),
('upload', 'introtext', NULL, 240, 'fr', NULL, NULL, NULL),
('home', 'restrictedtitle', '<h1>Bienvenue dans ResourceSpace</h1>', 241, 'fr', NULL, NULL, NULL),
('home', 'restrictedtext', 'Veuillez cliquer sur le lien qui vous a Ã©tÃ© envoyÃ© par mÃ©l pour accÃ©der aux documents sÃ©lectionnÃ©s pour vous.', 242, 'fr', NULL, NULL, NULL),
('resource_email', 'introtext', 'Partagez rapidement par mÃ©l ce document avec d''autres utilisateurs. Un lien sera automatiquement envoyÃ©. Vous pouvez aussi inclure un message dans le mÃ©l.', 243, 'fr', NULL, NULL, NULL),
('team_resource', 'introtext', 'Ajoutez un document individuel ou un ensemble de documents. Pour modifier individuellement les documents, rechercher simplement le document et cliquez sur Modifier dans le panneau ''Outils de document'' sur l''Ã©cran du document..', 244, 'fr', NULL, NULL, NULL),
('team_user', 'introtext', 'Utilisez cette section pour ajouter, supprimer et modifier les utilisateurs.', 245, 'fr', NULL, NULL, NULL),
('team_research', 'introtext', 'Organisez et gÃ©rer les ''requÃªtes de recherche''. <br/><br/> Choisir ''Modifier la recherche'' pour vÃ©rifier les dÃ©tails de la requÃªte et assigner la recherche Ã  un membre de l''Ã©quipe. Il est possible de baser une requÃªte de recherche sur une collection existante en saisissant le numÃ©ro de cette collection dans l''Ã©cran de modification.<br/><br/> Une fois la requÃªte assignÃ©e, choisissez ''Modifier la collection'' pour ajouter la requÃªte de recherche au panneau ''Mes collections''. En utilisant les outils standard, il est alors possible d''ajouter des documents Ã  la recherche. <br/><br/> Une fois que la recherche est terminÃ©e, choisissez ''Modifier la recherche'', changez le statut pour ''terminÃ©e'' ; un mÃ©l sera automatiquement envoyÃ© Ã  l''utilisateur qui a demandÃ© la recherche. Le mÃ©l contient un lien vers la recherche et la recherche est automatiquement ajoutÃ©e aux collections de l''utilisateur.', 246, 'fr', NULL, NULL, NULL),
('collection_edit', 'introtext', 'Organisez et gÃ©rez votre travail en groupant les documents. CrÃ©ez des ''Collections'' pour correspondre Ã  votre mÃ©thode de travail.<br/> Toutes vos collections apparaissent dans le panneau ''Mes Collections'' en bas de l''Ã©cran <br/><br/> L''<strong>accÃ¨s privÃ©</strong> vous autorise, vous et les utilisateurs sÃ©lectionnÃ©s, Ã  voir la collection. IdÃ©al pour grouper les documents par projet sur lesquels vous travaillez et partager les documetns avec les membres de l''Ã©quipe. <br/><br/> L''<strong>accÃ¨s public</strong> autorise tous les utilisateurs du systÃ¨me Ã  voir et rechercher dans la collection. C''est utile si vous souhaitez partager des collections de documents que vous pensez bÃ©nÃ©fiques pour d''autres utilisateurs. <br/><br/> Vous pouvez choisir si vous souhaitez ou non autoriser d''autres utilisateurs (tous, ou uniquement ceux que vous avez ajoutÃ©s Ã  votre collection privÃ©e) Ã  ajouter et retirer des documents ou simplement les visualiser pour rÃ©fÃ©rence.', 247, 'fr', NULL, NULL, NULL),
('team_stats', 'introtext', 'Les graphiques sont crÃ©Ã©s Ã  la demande en fonction de donnÃ©es temps rÃ©el. Cochez la case pour imprimer tous les graphiques de l''annÃ©e sÃ©lectionnÃ©e.', 248, 'fr', NULL, NULL, NULL),
('resource_request', 'introtext', 'La ressource que vous avez demandÃ©e n''est pas disponible en ligne. Les informations sur la ressource sont automatiquement insÃ©rÃ©es dans le mÃ©l, mais vous pouvez ajouter des commentaires additionnels si vous le souhaitez.', 249, 'fr', NULL, NULL, NULL),
('team_batch', 'introtext', NULL, 250, 'fr', NULL, NULL, NULL),
('team_batch_upload', 'introtext', NULL, 251, 'fr', NULL, NULL, NULL),
('team_batch_select', 'introtext', NULL, 252, 'fr', NULL, NULL, NULL),
('download_click', 'introtext', 'Pour tÃ©lÃ©charger le fichier ressource, cliquez droit sur le lien ci-dessous et choisissez "Enregistrer sous...". Il vous sera alors demandÃ© l''emplacement oÃ¹ vous souhaitez sauvegarder le fichier. Pour ouvrir le fichier dans votre navigateur, cliquez simplement sur le lien.', 253, 'fr', NULL, NULL, NULL),
('collection_manage', 'newcollection', 'Pour crÃ©er une nouvelle collection, entrez un nom court.', 254, 'fr', NULL, NULL, NULL),
('collection_email', 'introtext', 'Remplissez le formulaire ci-dessous pour envoyer cette collection par mÃ©l. Les utilisateurs recevront un lien vers la ressource plutÃ´t que des fichiers attachÃ©s pour qu''ils puissent choisir et tÃ©lÃ©charger les ressources appropriÃ©es.', 255, 'fr', NULL, NULL, NULL),
('all', 'footer', 'Ce site utilise le systÃ¨me de gestion des actifs numÃ©riques <a href="http://www.resourcespace.org/">ResourceSpace</a>.', 256, 'fr', NULL, NULL, NULL),
('change_language', 'introtext', 'Veuillez sÃ©lectionner votre langue ci-dessous.', 257, 'fr', NULL, NULL, NULL),
('change_password', 'introtext', 'Saisissez un nouveau mot de passe ci-dessous pour changer votre mot de passe.', 258, 'fr', NULL, NULL, NULL),
('login', 'welcomelogin', 'Bienvenue dans ResourceSpace, veuillez vous identifier...', 259, 'fr', NULL, NULL, NULL),
('all', 'emailresource', '[img_gfx/whitegry/titles/title.gif]<br />\n[fromusername] [lang_hasemailedyouaresource]<br /><br />\n[message]<br /><br />\n<a href="[url]">[embed_thumbnail]</a><br /><br />\n[lang_clicktoviewresource]<br /><a href="[url]">[resourcename] - [url]</a><br /><br />\n[text_footer]\n', 260, 'en', NULL, NULL, 1),
('all', 'emailnewresearchrequestwaiting', '[img_gfx/whitegry/titles/title.gif]<br />\n[username] ([userfullname] - [useremail])\n[lang_haspostedresearchrequest]<br /><br />\n[lang_nameofproject]:[name]<br /><br />\n[lang_descriptionofproject]:[description]<br /><br />\n[lang_deadline]:[deadline]<br /><br />\n[lang_contacttelephone]:[contact]<br /><br />\n[lang_finaluse]: [finaluse]<br /><br />\n[lang_shaperequired]: [shape]<br /><br />\n[lang_noresourcesrequired]: [noresources]<br /><br />\n<a href="[url]">[url]</a><br /><br />\n<a href="[teamresearchurl]">[teamresearchurl]</a><br /><br />\n[text_footer]\n', 261, 'en', NULL, NULL, 1),
('all', 'emailresearchrequestassigned', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_researchrequestassignedmessage]<br /><br />\n[text_footer]\n', 262, 'en', NULL, NULL, 1),
('all', 'emailresearchrequestcomplete', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_researchrequestcompletemessage] <br /><br /> \n[lang_clicklinkviewcollection] <br /><br /> \n<a href="[url]">[url]</a><br /><br />\n[text_footer]\n', 263, 'en', NULL, NULL, 1),
('all', 'emailnotifyresourcessubmitted', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_userresourcessubmitted]\n[list] <br />\n[lang_viewalluserpending] <br /><br /> \n<a href="[url]">[url]</a><br /><br />\n[text_footer]\n', 264, 'en', NULL, NULL, 1),
('all', 'emailnotifyresourcesunsubmitted', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_userresourcesunsubmitted]\n[list] <br />\n[lang_viewalluserpending] <br /><br /> \n<a href="[url]">[url]</a><br /><br />\n[text_footer]\n', 265, 'en', NULL, NULL, 1),
('all', 'emaillogindetails', '[img_gfx/whitegry/titles/title.gif]<br />\n[welcome]<br /><br /> \n[lang_newlogindetails]<br /><br /> \n[lang_username] : [username] <br /><br />\n[lang_password] : [password]<br /><br />\n<a href="[url]">[url]</a><br /><br />\n[text_footer]\n', 266, 'en', NULL, NULL, 1),
('all', 'emailreminder', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_newlogindetails] <br /><br />\n[lang_username] : [username] <br /> \n[lang_password]  : [password] <br /><br />\n<a href="[url]">[url]</a><br /><br />\n[text_footer]\n', 267, 'en', NULL, NULL, 1),
('all', 'emailresourcerequest', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_username] : [username] <br />\n[list] <br />\n[details]<br /><br />\n[lang_clicktoviewresource] <br /><br />\n<a href="[url]">[url]</a>\n', 268, 'en', NULL, NULL, 1),
('all', 'emailcollection', '[img_gfx/whitegry/titles/title.gif]<br />\n[fromusername] [lang_emailcollectionmessage] <br /><br /> \n[lang_message] : [message]<br /><br /> \n[lang_clicklinkviewcollection] [list]\n', 269, 'en', NULL, NULL, 1),
('all', 'emailbulk', '[img_gfx/whitegry/titles/title.gif]<br /><br />\n[text]<br /><br />\n[text_footer]\n', 270, 'en', NULL, NULL, 1),
('change_language', 'introtext', 'Bitte wÃ¤hlen Sie Ihre Sprache aus:', 271, 'de', NULL, NULL, 0),
('all', 'researchrequest', 'Lassen Sie unser Team nach den benÃ¶tigten Resourcen suchen.', 272, 'de', NULL, NULL, 0),
('all', 'searchpanel', 'Suche nach Beschreibung, Schlagworten und Ressourcen IDs', 273, 'de', NULL, NULL, 0),
('about', 'about', 'Ihr Text zu "Ãœber uns" hier.', 274, 'de', NULL, NULL, 0),
('change_password', 'introtext', 'Neues Passwort unten eingeben, um es zu Ã¤ndern.', 275, 'de', NULL, NULL, 0),
('collection_edit', 'introtext', 'Organisieren und verwalten Sie Ihre Arbeit, indem Sie Ressourcen in Gruppen zusammenstellen. Erstellen Sie Kollektionen wie Sie sie benÃ¶tigen.\n\n<br />\n\nAlle Kollektionen in Ihrer Liste erscheinen im "Meine Kollektionen" MenÃ¼ am unteren Ende des Fensters.\n\n<br /><br />\n\n<strong>Privater Zugriff</strong> erlaubt nur Ihnen und ausgewÃ¤hlten Benutzern, die Kollektion zu anzusehen. Ideal, um Ressourcen fÃ¼r die eigene Arbeit zusammenzustellen und im Team weiterzugeben.\n\n<br /><br />\n\n<strong>Ã–ffentlicher Zugriff</strong> erlaubt allen Benutzern, die Kollektion zu finden und anzusehen.\n\n<br /><br />\n\nSie kÃ¶nnen aussuchen, ob Sie anderen Benutzern (Ã¶ffentlicher Zugriff oder ausgewÃ¤hlte Benutzer beim privaten Zugriff) erlauben, Ressourcen hinzuzufÃ¼gen oder zu lÃ¶schen.', 276, 'de', NULL, NULL, 0),
('collection_email', 'introtext', 'Bitte fÃ¼llen Sie das untenstehende Formular aus, um die Kollektion per E-Mail weiterzugeben. Der/die Benutzer werden statt eines Dateianhangs einen Link zu dieser Kollektion erhalten und kÃ¶nnen dann die passenden Ressourcen auswÃ¤hlen und herunterladen.', 277, 'de', NULL, NULL, 0),
('collection_manage', 'findpublic', 'Ã–ffentliche Kollektionen sind fÃ¼r alle Benutzer zugÃ¤ngliche Gruppen von Ressourcen. Um Ã¶ffentliche Kollektionen zu finden, geben Sie die ID, oder einen Teil des Kollektions- bzw. Benutzernamens ein. FÃ¼gen Sie dann die Kollektion zu Ihren Kollektionen hinzu, um auf die Ressourcen zuzugreifen.', 278, 'de', NULL, NULL, 0),
('collection_manage', 'introtext', 'Organisieren und verwalten Sie Ihre Arbeit, indem Sie Ressourcen in Gruppen zusammenstellen. Erstellen Sie Kollektionen wie Sie sie benÃ¶tigen. Sie kÃ¶nnen Kollektionen an andere weitergeben oder einfach Gruppen von Ressourcen zusammen halten. Alle Kollektionen in Ihrer Liste finden Sie im "Meine Kollektionen" MenÃ¼ am unteren Ende des Fensters.', 279, 'de', NULL, NULL, 0),
('collection_manage', 'newcollection', 'Um eine neue Kollektion zu erstellen, geben Sie bitte einen Kurznamen an.', 280, 'de', NULL, NULL, 0),
('collection_public', 'introtext', 'Ã–ffentliche Kollektionen werden von anderen Benutzern erstellt und freigegeben.', 281, 'de', NULL, NULL, 0),
('contact', 'contact', 'Ihre Kontaktdaten hier.', 282, 'de', NULL, NULL, 0),
('contribute', 'introtext', 'Sie kÃ¶nnen Ihre eigenen Ressourcen hochladen. Wenn Sie eine Ressource erstellen, wird diese zunÃ¤chst durch uns geprÃ¼ft. Nachdem Sie die Datei hochgeladen und die Felder ausgefÃ¼llt haben, setzen Sie bitte den Status auf "Benutzer-BeitrÃ¤ge: ÃœberprÃ¼fung noch nicht erledigt".', 283, 'de', NULL, NULL, 0),
('delete', 'introtext', 'Bitte geben Sie Ihr Passwort ein, um zu bestÃ¤tigen, dass Sie diese Ressource lÃ¶schen wollen.', 284, 'de', NULL, NULL, 0),
('done', 'collection_email', 'Eine E-Mail mit Link zur Kollektion wurde an die angegebenen Benutzer gesendet. Die Kollektion wurde zur Liste Ihrer Kollektionen hinzugefÃ¼gt.', 285, 'de', NULL, NULL, 0),
('done', 'deleted', 'Die Ressource wurde gelÃ¶scht.', 286, 'de', NULL, NULL, 0),
('done', 'research_request', 'Ein Mitglied unseres Teams wird sich um Ihre Anfrage kÃ¼mmern. Wir werden Sie per e-mail Ã¼ber den aktuellen Stand informieren. Wenn Ihre Anfrage bearbeitet ist, erhalten Sie eine e-mail mit einem Link zu den Ressourcen, die wir fÃ¼r Ihre Anfrage empfehlen.', 287, 'de', NULL, NULL, 0),
('done', 'resource_email', 'Eine E-Mail mit Link zur Ressource wurde an die angegebenen Benutzer gesendet.', 288, 'de', NULL, NULL, 0),
('done', 'resource_request', 'Ihre Anfrage wurde abgeschickt und wird in KÃ¼rze bearbeitet.', 289, 'de', NULL, NULL, 0),
('done', 'user_password', 'Eine E-Mail mit Ihrem Benutzernamen und Passwort wurde an Sie gesendet.', 290, 'de', NULL, NULL, 0),
('done', 'user_request', 'Ihre Anfrage nach einem Zugang wurde abgeschickt und wird in KÃ¼rze bearbeitet.', 291, 'de', NULL, NULL, 0),
('download_click', 'introtext', 'Um die Datei herunterzuladen, klicken Sie bitte mit der rechten Maustaste auf den untenstehenden Link und wÃ¤hlen Sie "Speichern unter...". Sie kÃ¶nnen dann auswÃ¤hlen an welchem Ort Sie die Datei abspeichern wollen. Um die Datei im Browser zu Ã¶ffnen, klicken Sie den Link bitte mit der linken Maustaste.', 292, 'de', NULL, NULL, 0),
('download_progress', 'introtext', 'Ihr Download wird in KÃ¼rze starten. Nachdem der Download abgeschlossen ist, wÃ¤hlen Sie bitte einen der folgenden Links.', 293, 'de', NULL, NULL, 0),
('edit', 'batch', NULL, 294, 'de', NULL, NULL, 0),
('edit', 'multiple', 'Bitte wÃ¤hlen Sie die Felder aus, die Sie verÃ¤ndern wollen. Felder, die Sie nicht anwÃ¤hlen, werden nicht verÃ¤ndert.', 295, 'de', NULL, NULL, 0),
('help', 'introtext', 'Diese Anleitungen helfen Ihnen, ResourceSpace und Ihre Medien effektiv zu nutzen.</p>\n<p>Benutzen Sie "Themen", um Ressourcen nach Themen zu erkunden oder nutzen Sie die Suche um spezifische Ressourcen zu finden.</p>\n<p><a href="http://www.montala.net/downloads/resourcespace-GettingStarted.pdf">User Guide</a> (PDF, englisch)<br /><a target="_blank" href="http://wiki.resourcespace.org/index.php/Main_Page">Online Dokumentation</a> (Wiki)</p>', 296, 'de', NULL, NULL, 0),
('home', 'help', 'Hilfe fÃ¼r die Arbeit mit ResourceSpace', 297, 'de', NULL, NULL, 0),
('home', 'mycollections', 'Hier kÃ¶nnen Sie Ihre Kollektionen organisieren, verwalten und weitergeben.', 298, 'de', NULL, NULL, 0),
('home', 'restrictedtext', 'Bitte klicken Sie auf den Link, den Sie per E-Mail erhalten haben, um auf die fÃ¼r Sie ausgesuchten Ressourcen zuzugreifen.', 299, 'de', NULL, NULL, 0),
('home', 'restrictedtitle', '<h1>Willkommen bei ResourceSpace</h1>', 300, 'de', NULL, NULL, 0),
('home', 'themes', 'Von unserem Team vorausgewÃ¤hlte Bilder', 301, 'de', NULL, NULL, 0),
('home', 'welcometext', 'Ihr Einleitungstext hier', 302, 'de', NULL, NULL, 0),
('home', 'welcometitle', 'Willkommen bei ResourceSpace', 303, 'de', NULL, NULL, 0),
('login', 'welcomelogin', 'Willkommen bei ResourceSpace. Bitte loggen Sie sich ein...', 304, 'de', NULL, NULL, 0),
('research_request', 'introtext', 'Unser Team unterstÃ¼tzt Sie dabei, die optimalen Ressourcen fÃ¼r Ihre Projekte zu finden. FÃ¼llen Sie dieses Formular bitte mÃ¶glichst vollstÃ¤ndig aus, damit wir Ihre Anforderungen erfÃ¼llen kÃ¶nnen.\n<br /><br />\nWir werden Sie kontinuierlich informieren. Sobald wir Ihre Anfrage bearbeitet haben, werden Sie eine E-Mail mit einem Link zu den von uns empfohlenen Bildern erhalten.', 305, 'de', NULL, NULL, 0),
('resource_email', 'introtext', 'Geben Sie dieses Bild per E-Mail weiter. Es wird ein Link versendet. Sie kÃ¶nnen auÃŸerdem eine persÃ¶nliche Nachricht in die E-Mail einfÃ¼gen.', 306, 'de', NULL, NULL, 0),
('resource_request', 'introtext', 'Die Ressource, die Sie herunterladen mÃ¶chten, ist nicht online verfÃ¼gbar. Die Informationen zur Ressource werden automatisch per E-Mail versendet. ZusÃ¤tzlich kÃ¶nnen Sie weitere Bemerkungen hinzufÃ¼gen.', 307, 'de', NULL, NULL, 0),
('search_advanced', 'introtext', '<strong>Suchtipp</strong><br />\nJeder Bereich, den Sie nicht ausfÃ¼llen / anklicken, liefert alle Ergebnisse aus dem Bereich.', 308, 'de', NULL, NULL, 0),
('tag', 'introtext', 'Verbessern Sie die Suchergebnisse, indem Sie Ressourcen taggen. Sagen Sie, was Sie sehen, getrennt durch Leerzeichen oder Komma... z.B.: Hund, Haus, Ball, Geburtstag, Kuchen. Geben Sie den vollen Namen von Personen in Fotos und die Ort der Aufnahme an, wenn bekannt.', 309, 'de', NULL, NULL, 0),
('terms', 'introtext', 'Sie mÃ¼ssen zuerst die Nutzungsbedingungen akzeptieren.\n\n', 310, 'de', NULL, NULL, 0),
('terms', 'terms', 'Ihre Nutzungsbedingungen hier.', 311, 'de', NULL, NULL, 0),
('terms and conditions', 'terms and conditions', 'Ihre Nutzungsbedingungen hier.', 312, 'de', NULL, NULL, 0),
('themes', 'findpublic', 'Ã–ffentliche Kollektionen sind Kollektionen, die von anderen Benutzern freigegeben wurden.', 313, 'de', NULL, NULL, 0),
('themes', 'introtext', 'Themen sind von unserem Team zusammengestellte Gruppen von Ressourcen.', 314, 'de', NULL, NULL, 0),
('user_password', 'introtext', 'Bitte geben Sie Ihre E-Mail Adresse ein. Ihre Zugangsdaten werden dann an per E-Mail an Sie versendet.', 315, 'de', NULL, NULL, 0),
('user_request', 'introtext', 'Um einen Zugang anzufordern, fÃ¼llen Sie bitte das untenstehende Formular aus.', 316, 'de', NULL, NULL, 0),
('team_archive', 'introtext', 'Um einzelne Ressourcen im Archiv zu bearbeiten, suchen Sie einfach nach den Ressourcen und klicken auf "bearbeiten" unter "Ressourcen-Werkzeuge". Alle Ressourcen, die archiviert werden sollen, werden in der Liste "Archivierung noch nicht erledigt" angezeigt. Von dieser Liste aus kÃ¶nnen Sie weitere Informationen ergÃ¤nzen und die Ressource ins Archiv verschieben.', 317, 'de', NULL, NULL, 0),
('team_batch', 'introtext', NULL, 318, 'de', NULL, NULL, 0),
('team_batch_select', 'introtext', NULL, 319, 'de', NULL, NULL, 0),
('team_batch_upload', 'introtext', NULL, 320, 'de', NULL, NULL, 0),
('team_copy', 'introtext', 'Geben Sie die ID der Ressource ein, die Sie kopieren mÃ¶chten. Nur die Metadaten der Ressource werden kopiert â€“ hochgeladene Dateien werden nicht kopiert.', 321, 'de', NULL, NULL, 0),
('team_home', 'introtext', 'Willkommen in der Administration. Bitte benutzen Sie die untenstehenden Links, um die Ressourcen zu verwalten, auf Ressourcenanfragen zu antworten, Themen zu verwalten und die Systemeinstellungen zu bearbeiten.', 322, 'de', NULL, NULL, 0),
('team_report', 'introtext', 'Bitte wÃ¤hlen Sie einen Bericht und einen Zeitraum. Der Bericht kann in Microsoft Excel oder einer anderen Tabellenkalkulation geÃ¶ffnet werden.', 323, 'de', NULL, NULL, 0),
('team_research', 'introtext', 'Organisieren und verwalten Sie Ihre "Ressourcenanfragen".<br /><br />WÃ¤hlen Sie "Anfrage bearbeiten", um die Details der Anfrage zu sehen und sie einem Teammitglied zuzuweisen. Es ist mÃ¶glich, eine Antwort auf einer existierenden Kollektion aufzubauen. Geben Sie dazu die Kollektions-ID in der Ansicht zur Bearbeitung ein.<br /><br />Wenn die Ressourcenanfrage zugewiesen ist, wÃ¤hlen Sie "Kollektion bearbeiten", um die Anfrage zu Ihren Kollektionen hinzuzufÃ¼gen. So kÃ¶nnen Sie Ressourcen zu dieser Kollektion hinzufÃ¼gen.<br /><br />Wenn die Kollektion vollstÃ¤ndig ist, wÃ¤hlen Sie "Anfrage bearbeiten", stellen Sie den Status auf "abgeschlossen" und eine E-Mail wird automatisch an den Anfrager geschickt. Diese E-Mail enthÃ¤lt einen Link zur erstellten Kollektion, welche auÃŸerdem automatisch zu den Kollektionen des Benutzers hinzugefÃ¼gt wird.', 324, 'de', NULL, NULL, 0),
('view', 'storyextract', 'Story:', 325, 'de', NULL, NULL, 0),
('team_resource', 'introtext', 'FÃ¼gen Sie einzelne Ressourcen hinzu oder nutzen Sie den Stapelupload. Um einzelne Ressourcen zu bearbeiten, suchen Sie nach der Ressource und wÃ¤hlen Sie "bearbeiten" unter den "Ressourcen-Werkzeugen".', 326, 'de', NULL, NULL, 0),
('team_stats', 'introtext', 'Statistiken werden auf Basis der aktuellsten Daten erstellt. Aktivieren Sie die Checkbox, um alle Statistiken fÃ¼r das gewÃ¤hlte Jahr auszugeben.', 327, 'de', NULL, NULL, 0),
('team_user', 'introtext', 'In diesem Bereich kÃ¶nnen Sie Benutzer hinzufÃ¼gen, lÃ¶schen und verÃ¤ndern.', 328, 'de', NULL, NULL, 0),
('themes', 'manage', 'Organisieren und bearbeiten Sie Ihre Themen. Themen sind besonders hervorgehobene Kollektionen. <br /><br /><strong>1 Um einen neuen Eintrag in einem Thema anzulegen, mÃ¼ssen Sie zuerst eine neue Kollektion anlegen</strong><br />WÃ¤hlen Sie <strong>Meine Kollektionen</strong> aus der oberen Navigation und legen Sie eine neue <strong>Ã¶ffentliche</strong> Kollektion an. Stellen Sie sicher, dass Sie einen Namen fÃ¼r Ihr Thema eingeben. Um die aktuelle Kollektion einem bestehenden Thema zuzuordnen, nutzen Sie einen bestehenden Themennamen. Wenn Sie einen noch nicht vergebenen Themennamen angeben, erstellen Sie ein neues Thema. <br /><br /><strong>2 Um den Inhalt eines bestehenden Themas zu Ã¤ndern, </strong><br />wÃ¤hlen Sie <strong>''Kollektion bearbeiten''</strong>. Die Ressourcen in dieser Kollektion erscheinen unten im <strong>''Meine Kollektionen''</strong> Bereich. Nutzen Sie die Standardwerkzeuge um Resourcen zu bearbeiten, hizuzufÃ¼gen oder zu lÃ¶schen.<br /><br /><strong>3 Um eine Kollektion umzubenennen oder unter einem anderen Thema anzuzeigen,</strong><br />wÃ¤hlen Sie <strong>''bearbeiten''</strong> und bearbeiten Sie die Themenkategorie oder die Kollektionsnamen. <br /><br /><strong>4 Um eine Kollektion aus einem Thema zu entfernen,</strong><br />wÃ¤hlen Sie<strong> ''bearbeiten''</strong> und lÃ¶schen Sie den Eintrag im Feld "Themen-Kategorie".', 329, 'de', NULL, NULL, 0),
('upload', 'introtext', NULL, 330, 'de', NULL, NULL, 0),
('upload_swf', 'introtext', NULL, 331, 'de', NULL, NULL, 0),
('home', 'welcometitle', 'VÃ¤lkommen till ResourceSpace', 332, 'sv', NULL, NULL, 0),
('home', 'welcometext', 'Skriv en introduktion hÃ¤r â€¦', 333, 'sv', NULL, NULL, 0),
('all', 'footer', '<a target="_blank" href="http://www.resourcespace.org/">ResourceSpace</a>: Digital materialfÃ¶rvaltning (dam) med Ã¶ppen kÃ¤llkod', 334, 'sv', NULL, NULL, 0),
('all', 'searchpanel', 'SÃ¶k efter material genom att ange beskrivning, nyckelord eller materialnr.', 335, 'sv', NULL, NULL, 0),
('resource_email', 'introtext', 'Dela snabbt och enkelt material med andra. Ett e-postmeddelande innehÃ¥llande en webblÃ¤nk till materialen skapas och skickas automatiskt. Du kan Ã¤ven lÃ¤gga till ett eget meddelande.', 336, 'sv', NULL, NULL, 0),
('home', 'themes', 'De bÃ¤sta materialen, speciellt utvalda och sorterade.', 337, 'sv', NULL, NULL, 0),
('home', 'restrictedtitle', '<h1>VÃ¤lkommen till ResourceSpace</h1>', 338, 'sv', NULL, NULL, 0),
('about', 'about', 'Din egen text fÃ¶r â€™Om ossâ€™ â€¦', 339, 'sv', NULL, NULL, 0),
('home', 'help', 'HjÃ¤lp och tips som ser till att du fÃ¥r ut det mesta mÃ¶jliga av ResourceSpace.', 340, 'sv', NULL, NULL, 0),
('home', 'mycollections', 'Organisera dina material och samarbeta med andra. De hÃ¤r verktygen hjÃ¤lper dig att arbeta mer effektivt.', 341, 'sv', NULL, NULL, 0),
('terms', 'terms', 'Dina regler â€¦', 342, 'sv', NULL, NULL, 0),
('terms and conditions', 'terms and conditions', 'Dina regler och villkor â€¦', 343, 'sv', NULL, NULL, 0),
('home', 'restrictedtext', 'Klicka pÃ¥ webblÃ¤nken som skickades till dig om du vill komma Ã¥t materialen som Ã¤r utvalda fÃ¶r dig.', 344, 'sv', NULL, NULL, 0),
('change_language', 'introtext', 'VÃ¤lj ditt Ã¶nskade sprÃ¥k nedan.', 345, 'sv', NULL, NULL, 0),
('all', 'researchrequest', 'LÃ¥t vÃ¥rt team hitta materialen du Ã¤r ute efter.', 346, 'sv', NULL, NULL, 0),
('change_password', 'introtext', 'Skriv in ett nytt lÃ¶senord nedan om du vill byta lÃ¶senord.', 347, 'sv', NULL, NULL, 0),
('contact', 'contact', 'Dina kontaktuppgifter â€¦', 348, 'sv', NULL, NULL, 0),
('view', 'storyextract', 'Textutdrag:', 349, 'sv', NULL, NULL, 0),
('team_home', 'introtext', 'VÃ¤lkommen till sidan Administration. AnvÃ¤nd lÃ¤nkarna nedan om du vill administrera material, svara pÃ¥ fÃ¶rfrÃ¥gningar, hantera teman och Ã¤ndra systeminstÃ¤llningar.', 350, 'sv', NULL, NULL, 0),
('themes', 'introtext', 'Teman Ã¤r grupper av material som har valts ut av administratÃ¶rerna som exempel pÃ¥ vilka material som finns i systemet.', 351, 'sv', NULL, NULL, 0),
('collection_manage', 'findpublic', 'Gemensamma samlingar Ã¤r grupper av material som har gjorts allmÃ¤nt tillgÃ¤ngliga av anvÃ¤ndare i systemet. Skriv in ett samlingsnr, hela eller delar av samlingsnamnet, eller ett anvÃ¤ndarnamn nÃ¤r du vill sÃ¶ka efter en gemensam samling. LÃ¤gg till den hittade samlingen till din lista Ã¶ver samlingar om du vill kunna nÃ¥ materialen enkelt.', 352, 'sv', NULL, NULL, 0),
('collection_public', 'introtext', 'Gemensamma samlingar Ã¤r skapade av andra anvÃ¤ndare.', 353, 'sv', NULL, NULL, 0),
('themes', 'findpublic', 'Gemensamma samlingar Ã¤r samlingar med material som har delats ut av andra anvÃ¤ndare.', 354, 'sv', NULL, NULL, 0),
('help', 'introtext', 'FÃ¥ ut det mesta mÃ¶jliga av ResourceSpace. Instruktionerna nedan hjÃ¤lper dig att anvÃ¤nda systemet och materialen effektivare.</p>\n\n<p>AnvÃ¤nd Teman om du vill blÃ¤ddra bland material per tema eller anvÃ¤nd Enkel sÃ¶kning om du vill sÃ¶ka efter specifikt material.</p>\n\n<p><a target="_blank" href="http://www.montala.net/downloads/resourcespace-GettingStarted.pdf">HÃ¤mta den engelska anvÃ¤ndarhandboken (pdf-fil)</a>\n\n<p><a target="_blank" href="http://wiki.resourcespace.org/index.php/Main_Page">Dokumentation pÃ¥ webben (engelsksprÃ¥kig wiki)</a>', 355, 'sv', NULL, NULL, 0),
('collection_email', 'introtext', 'Dela snabbt och enkelt materialet i denna samling med andra. Ett e-postmeddelande innehÃ¥llande en webblÃ¤nk till samlingarna skapas och skickas automatiskt. Du kan Ã¤ven lÃ¤gga till ett eget meddelande.', 356, 'sv', NULL, NULL, 0),
('collection_edit', 'introtext', 'Organisera och hantera dina material genom att dela upp dem i samlingar.\n\n<br />\n\nDu nÃ¥r alla dina samlingar frÃ¥n panelen <b>Mina&nbsp;samlingar</b> i nederkant av skÃ¤rmen.\n\n<br /><br />\n\n<strong>Privat Ã¥tkomst</strong> tillÃ¥ter endast dig och dina utvalda anvÃ¤ndare att se samlingen. Idealiskt om du vill samla material i projekt som du jobbar med enskilt eller i en grupp.\n\n<br /><br />\n\n<strong>Gemensam Ã¥tkomst</strong> tillÃ¥ter alla interna anvÃ¤ndare att sÃ¶ka efter och se samlingen. AnvÃ¤ndbart om du vill dela materialet med andra som skulle kunna ha nytta av det. \n\n<br /><br />\n\nDu kan vÃ¤lja om du vill att de andra anvÃ¤ndarna ska kunna lÃ¤gga till och ta bort material eller bara kunna visa materialen.', 357, 'sv', NULL, NULL, 0),
('edit', 'multiple', 'Markera de fÃ¤lt du vill uppdatera med ny information. Omarkerade fÃ¤lt lÃ¤mnas ofÃ¶rÃ¤ndrade.', 358, 'sv', NULL, NULL, 0),
('collection_manage', 'introtext', 'Organisera och hantera ditt material genom att dela upp det i samlingar. Skapa samlingar fÃ¶r ett eget projekt, om du vill underlÃ¤tta samarbetet i en projektgrupp eller om du vill samla dina favoriter pÃ¥ ett stÃ¤lle. Du nÃ¥r alla dina samlingar frÃ¥n panelen <b>Mina&nbsp;samlingar</b> i nederkant av skÃ¤rmen.', 359, 'sv', NULL, NULL, 0),
('delete', 'introtext', 'Du mÃ¥ste ange ditt lÃ¶senord fÃ¶r att bekrÃ¤fta att du vill ta bort det hÃ¤r materialet.', 360, 'sv', NULL, NULL, 0),
('done', 'deleted', 'Materialet har tagits bort.', 361, 'sv', NULL, NULL, 0),
('collection_manage', 'newcollection', 'Fyll i ett samlingsnamn om du vill skapa en ny samling.', 362, 'sv', NULL, NULL, 0),
('contribute', 'introtext', 'Du kan bidra med eget material. NÃ¤r du fÃ¶rst skapar materialet fÃ¥r det statusen â€™Under registreringâ€™. Ã–verfÃ¶r en eller flera filer och fyll i fÃ¤lten med relevant metadata. SÃ¤tt statusen till â€™VÃ¤ntande pÃ¥ granskningâ€™ nÃ¤r du Ã¤r klar.', 363, 'sv', NULL, NULL, 0),
('done', 'collection_email', 'Ett e-postmeddelande innehÃ¥llande en webblÃ¤nk har skickats till anvÃ¤ndarna du specificerade. Samlingen har lagts till i respektive anvÃ¤ndares panel <b>Mina&nbsp;samlingar</b>.', 364, 'sv', NULL, NULL, 0),
('done', 'research_request', 'En medlem av researchteamet kommer att fÃ¥ i uppdrag att besvara din researchfÃ¶rfrÃ¥gan. Vi kommer att hÃ¥lla kontakt genom e-post under arbetets gÃ¥ng. NÃ¤r vi har slutfÃ¶rt arbetet kommer du att fÃ¥ ett e-postmeddelande med en webblÃ¤nk till alla material vi rekommenderar.', 365, 'sv', NULL, NULL, 0),
('done', 'resource_email', 'Ett e-postmeddelande innehÃ¥llande en webblÃ¤nk till materialen har skickats till anvÃ¤ndarna du specificerade.', 366, 'sv', NULL, NULL, 0),
('done', 'resource_request', 'Din begÃ¤ran har mottagits och vi kommer att hÃ¶ra av oss inom kort.', 367, 'sv', NULL, NULL, 0),
('done', 'user_password', 'Ett e-postmeddelande innehÃ¥llande ditt anvÃ¤ndarnamn och lÃ¶senord har skickats.', 368, 'sv', NULL, NULL, 0),
('done', 'user_request', 'Din ansÃ¶kan om ett anvÃ¤ndarkonto har skickats. Dina inloggningsuppgifter kommer att skickas till dig inom kort.', 369, 'sv', NULL, NULL, 0),
('download_click', 'introtext', 'HÃ¶gerklicka pÃ¥ lÃ¤nken nedan och vÃ¤lj <b>Spara&nbsp;som</b> om du vill hÃ¤mta materialet. Du kommer att fÃ¥ frÃ¥gan var du vill spara filen. Ã–ppna filen i din webblÃ¤sare genom att klicka pÃ¥ webblÃ¤nken.', 370, 'sv', NULL, NULL, 0),
('download_progress', 'introtext', 'Din hÃ¤mtning startas inom kort. NÃ¤r hÃ¤mtningen Ã¤r klar kan du fortsÃ¤tta genom att klicka pÃ¥ lÃ¤nkarna nedan.', 371, 'sv', NULL, NULL, 0),
('edit', 'batch', NULL, 372, 'sv', NULL, NULL, 0),
('team_resource', 'introtext', 'LÃ¤gg till material ett och ett eller i grupp. Om du vill redigera ett material kan du enklast sÃ¶ka efter det och sedan klicka pÃ¥ <b>Redigera</b> pÃ¥ sidan som visar materialet.', 373, 'sv', NULL, NULL, 0),
('login', 'welcomelogin', 'VÃ¤lkommen till ResourceSpace', 374, 'sv', NULL, NULL, 0);
INSERT INTO `site_text` (`page`, `name`, `text`, `ref`, `language`, `ignore_me`, `specific_to_group`, `custom`) VALUES
('user_request', 'introtext', 'Fyll i formulÃ¤ret nedan om du vill ansÃ¶ka om ett anvÃ¤ndarkonto.', 375, 'sv', NULL, NULL, 0),
('research_request', 'introtext', 'Researchteamet hjÃ¤lper dig att finna de bÃ¤sta materialen till dina projekt. Fyll i formulÃ¤ret nedan sÃ¥ noggrant som mÃ¶jligt sÃ¥ att vi kan ge dig ett relevant svar. <br /><br />En medlem av teamet kommer att fÃ¥ i uppdrag att besvara din researchfÃ¶rfrÃ¥gan. Vi kommer att hÃ¥lla kontakt genom e-post under arbetets gÃ¥ng. NÃ¤r vi har slutfÃ¶rt arbetet kommer du att fÃ¥ ett e-postmeddelande med en webblÃ¤nk till alla material vi rekommenderar.', 376, 'sv', NULL, NULL, 0),
('resource_request', 'introtext', 'Din begÃ¤ran Ã¤r nÃ¤stan slutfÃ¶rd. Ange anledningen till din begÃ¤ran sÃ¥ att vi kan besvara den snabbt och effektivt.', 377, 'sv', NULL, NULL, 0),
('search_advanced', 'introtext', '<strong>SÃ¶ktips</strong><br />Ett avsnitt som du lÃ¤mnar tomt eller omarkerat medfÃ¶r att <i>allt</i> inkluderas i sÃ¶kningen. Om du till exempel lÃ¤mnar alla lÃ¤nders kryssrutor omarkerade, begrÃ¤nsas sÃ¶kningen inte med avseende pÃ¥ land. Om du dÃ¤remot sedan markerar kryssrutan â€™Sverigeâ€™ ger sÃ¶kningen endast material frÃ¥n just Sverige.', 378, 'sv', NULL, NULL, 0),
('tag', 'introtext', 'HjÃ¤lp till att fÃ¶rbÃ¤ttra framtida sÃ¶kresultat genom att fÃ¶rse materialen med relevant metadata. Ange till exempel nyckelord som beskrivning av vad du ser pÃ¥ en bild: kanin, hus, boll, fÃ¶delsedagstÃ¥rta. Separera nyckelorden med kommatecken eller mellanslag. Ange fullstÃ¤ndiga namn pÃ¥ alla personer som fÃ¶rekommer pÃ¥ ett fotografi. Ange platsen fÃ¶r ett fotografi om den Ã¤r kÃ¤nd.', 379, 'sv', NULL, NULL, 0),
('user_password', 'introtext', 'Fyll i din e-postadress och ditt anvÃ¤ndarnamn sÃ¥ kommer ett nytt lÃ¶senord att skickas till dig.', 380, 'sv', NULL, NULL, 0),
('upload_swf', 'introtext', NULL, 381, 'sv', NULL, NULL, 0),
('upload', 'introtext', NULL, 382, 'sv', NULL, NULL, 0),
('themes', 'manage', 'Organisera och redigera tillgÃ¤ngliga teman. Teman Ã¤r grupper av material som har valts ut av administratÃ¶rerna som exempel pÃ¥ vilka material som finns i systemet.<br /><br /><strong>Skapa teman</strong><br /><Om du vill skapa ett nytt tema mÃ¥ste du fÃ¶rst skapa en samling.<br />GÃ¥ till <b>Mina&nbsp;samlingar</b> och skapa en ny <strong>gemensam samling</strong>. VÃ¤lj en temakategori frÃ¥n listan om du vill lÃ¤gga till samlingen i en existerande temakategori eller ange ett nytt namn om du vill skapa en ny temakategori. TillÃ¥t inte anvÃ¤ndare att lÃ¤gga till/ta bort material frÃ¥n teman.<br /><br /><strong>Redigera teman</strong><br />Om du vill redigera materialen i ett existerande tema vÃ¤ljer du verktyget <strong>VÃ¤lj samling</strong>. Materialen i samlingen blir dÃ¥ Ã¥tkomliga i panelen <b>Mina&nbsp;samlingar</b> i nederkanten av skÃ¤rmen. AnvÃ¤nd de vanliga verktygen om du vill redigera, lÃ¤gga till eller ta bort material.<br /><br /><strong>Byta namn pÃ¥ teman och flytta samlingar</strong><br />VÃ¤lj verktyget <strong>Redigera samling</strong>. Ange ett nytt namn i fÃ¤ltet Namn om du vill byta namn pÃ¥ temat. VÃ¤lj en temakategori frÃ¥n listan om du vill flytta samlingen till en existerande temakategori eller ange ett nytt namn om du vill skapa en ny temakategori och flytta samlingen dit.<br /><br /><strong>Ta bort en samling frÃ¥n ett tema</strong><br />VÃ¤lj verktyget <strong>Redigera samling</strong> och rensa fÃ¤ltet Temakategori och fÃ¤ltet dÃ¤r nya temakategorinamn anges.', 383, 'sv', NULL, NULL, 0),
('terms', 'introtext', 'Innan du kan fortsÃ¤tta mÃ¥ste du acceptera reglerna och villkoren.', 384, 'sv', NULL, NULL, 0),
('team_user', 'introtext', 'AnvÃ¤nd den hÃ¤r delen om du vill lÃ¤gga till, ta bort eller redigera anvÃ¤ndare.', 385, 'sv', NULL, NULL, 0),
('team_stats', 'introtext', 'En statistikrapport kan skapas vid behov, baserad pÃ¥ aktuell data. Markera kryssrutan om du vill skriva ut all statistik fÃ¶r det valda Ã¥ret.', 386, 'sv', NULL, NULL, 0),
('team_research', 'introtext', 'Organisera och hantera researchfÃ¶rfrÃ¥gningar.<br /><br />VÃ¤lj verktyget <b>Redigera researchfÃ¶rfrÃ¥gan</b> om du vill granska fÃ¶rfrÃ¥gan och tilldela en medlem i researchteamet uppdraget att besvara fÃ¶rfrÃ¥gan. Med samma verktyg kan du lÃ¤gga till en befintlig samling till researchen genom att ange samlingens nummer.<br /><br />NÃ¤r en medlem har tilldelats researchen blir den tillgÃ¤nglig fÃ¶r medlemmen i panelen <b>Mina&nbsp;samlingar</b>. AnvÃ¤nd de vanliga verktygen om du vill lÃ¤gga till material till researchen.<br /><br />NÃ¤r researchen Ã¤r slutfÃ¶rd vÃ¤ljer du Ã¥terigen verktyget <b>Redigera researchfÃ¶rfrÃ¥gan</b> och Ã¤ndrar status till â€™Besvaradâ€™. NÃ¤r du klickar pÃ¥ <b>Spara</b> skickas automatiskt ett e-postmeddelande till anvÃ¤ndaren som skickade researchfÃ¶rfrÃ¥gan. Meddelandet innehÃ¥ller en webblÃ¤nk som leder till researchen och den lÃ¤ggs ocksÃ¥ till i anvÃ¤ndarens panel<b>Mina&nbsp;samlingar</b>.', 387, 'sv', NULL, NULL, 0),
('team_report', 'introtext', 'VÃ¤lj en rapport och en period. Rapporten kan Ã¶ppnas i till exempel MS Excel eller LibreOffice Calc.', 388, 'sv', NULL, NULL, 0),
('team_copy', 'introtext', 'Ange numret fÃ¶r materialet du vill kopiera. Endast materialets metadata kommer att kopieras â€“ eventuella filer kommer inte att kopieras.', 389, 'sv', NULL, NULL, 0),
('team_batch_upload', 'introtext', NULL, 390, 'sv', NULL, NULL, 0),
('team_batch_select', 'introtext', NULL, 391, 'sv', NULL, NULL, 0),
('team_batch', 'introtext', NULL, 392, 'sv', NULL, NULL, 0),
('team_archive', 'introtext', 'Om du vill redigera ett arkiverat material gÃ¶r du det enklast genom att sÃ¶ka efter det hÃ¤r och sedan klicka pÃ¥ <b>Redigera</b> pÃ¥ sidan som visar materialet. Alla material som vÃ¤ntar pÃ¥ arkivering kan enkelt nÃ¥s frÃ¥n lÃ¤nken nedan. LÃ¤gg till eventuell relevant information innan du flyttar materialet till arkivet.', 393, 'sv', NULL, NULL, 0),
('all', 'comments_policy', NULL, 394, 'en', NULL, NULL, NULL),
('all', 'comments_removal_message', NULL, 395, 'en', NULL, NULL, NULL),
('all', 'comments_flag_notification_email_subject', NULL, 396, 'en', NULL, NULL, NULL),
('all', 'comments_flag_notification_email_body', NULL, 397, 'en', NULL, NULL, NULL),
('team_home', 'introtext', 'Welcome to the team center. Use the links below to administer resources, respond to resource requests, manage themes and alter system settings.', 398, 'en-US', NULL, NULL, NULL),
('themes', 'manage', 'Organize and edit the themes available online. Themes are specially promoted collections. <br /><br /> <strong>1 To create a new entry under a Theme -  build a collection</strong><br /> Choose <strong>My Collections</strong> from the main top menu and set up a brand new <strong>public</strong> collection. Remember to include a theme name during the setup. Use an existing theme name to group the collection under a current theme (make sure you type it exactly the same), or choose a new title to create a brand new theme. Never allow users to add/remove resources from themed collections. <br /> <br /><strong>2 To edit the content of an existing entry under a theme </strong><br /> Choose <strong>edit collection</strong>. The items in that collection will appear in the <strong>My Collections</strong> panel at the bottom of the screen. Use the standard tools to edit, remove or add resources. <br /> <br /><strong>3 To alter a theme name or move a collection to appear under a different theme</strong><br /> Choose <strong>edit properties</strong> and edit theme category or collection name. Use an existing theme name to group the collection under an current theme (make sure you type it exactly the same), or choose a new title to create a brand new theme. <br /> <br /><strong>4 To remove a collection from a theme </strong><br /> Choose <strong>edit properties</strong> and delete the words in the theme category box. ', 399, 'en-US', NULL, NULL, NULL),
('collection_manage', 'introtext', 'Organize and manage your work by grouping resources together. Create â€˜Collectionsâ€™ to suit your way of working. You may want to group resources under projects that you are working on independently, share resources amongst a project team or simply keep your favourite resources together in one place. All the collections in your list appear in the â€˜My Collectionsâ€™ panel at the bottom of the screen.', 400, 'en-US', NULL, NULL, NULL),
('team_research', 'introtext', 'Organize and manage â€˜Research Requestsâ€™. <br /><br />Choose â€˜edit researchâ€™ to review the request details and assign the research to a team member. It is possible to base a research request on a previous collection by entering the collection ID in the â€˜editâ€™ screen. <br /><br />Once the research request is assigned, choose â€˜edit collectionâ€™ to add the research request to â€˜My collectionâ€™ panel. Using the standard tools, it is then possible to add resources to the research. <br /><br />Once the research is complete, choose â€˜edit researchâ€™,  change the status to complete and an email is automatically  sent to the user who requested the research. The email contains a link to the research and it is also automatically added to their â€˜My Collectionâ€™ panel.', 401, 'en-US', NULL, NULL, NULL),
('collection_edit', 'introtext', 'Organize and manage your work by grouping resources together. Create â€˜Collectionsâ€™ to suit your way of working.\n\n<br />\n\nAll the collections in your list appear in the â€˜My Collectionsâ€™ panel at the bottom of the screen\n\n<br /><br />\n\n<strong>Private Access</strong> allows only you and and selected users to see the collection. Ideal for grouping resources under projects that you are working on independently and share resources amongst a project team.\n\n<br /><br />\n\n<strong>Public Access</strong> allows all users of the system to search and see the collection. Useful if you wish to share collections of resources that you think others would benefit from using.\n\n<br /><br />\n\nYou can choose whether you allow other users (public or users you have added to your private collection) to add and remove resources or simply view them for reference.', 402, 'en-US', NULL, NULL, NULL),
('all', 'emailnotifyresourcesapproved', '[img_gfx/whitegry/titles/title.gif]<br />\n[lang_userresourcesapproved]\n[list] <br />\n[lang_viewcontributedsubittedl] <br /><br /> \n<a href="[url]">[url]</a><br /><br />\n[text_footer]\n', 403, 'en', NULL, NULL, 1),
('collection_email', 'introtextthemeshare', 'Dela snabbt och enkelt alla teman i denna temakategori med andra. Ett e-postmeddelande innehÃ¥llande en webblÃ¤nk till respektive tema skapas och skickas automatiskt. Du kan Ã¤ven lÃ¤gga till ett eget meddelande.', 404, 'sv', NULL, NULL, NULL),
('all', 'emailcontactadmin', '[img_../gfx/whitegry/titles/title.gif]<br />[fromusername] ([emailfrom])[lang_contactadminemailtext]<br /><br />[message]<br /><br /><a href="[url]">[embed_thumbnail]</a><br /><br />[text_footer]', 405, 'en', NULL, NULL, NULL),
('all', 'emailcollectionexternal', '[img_gfx/whitegry/titles/title.gif]<br />\n[fromusername] [lang_emailcollectionmessageexternal] <br /><br /> \n[lang_message] : [message]<br /><br /> \n[lang_clicklinkviewcollection] [list]\n', 100123, 'en', NULL, NULL, 1),
('collection_email', 'introtextthemeshare', 'Complete the form below to e-mail the themes in this theme category. The recipients will receive an email containing links to each of the themes.', 100124, 'en', NULL, NULL, NULL);

