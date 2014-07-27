
CREATE TABLE IF NOT EXISTS `res` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(100)  NOT NULL,
  `status` char(1) DEFAULT '0',
  `owner` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime DEFAULT NULL,
  `original_asset` varchar(100)  NOT NULL,
  `preview_asset` varchar(100)  NOT NULL,
  `basket` varchar(100)  NOT NULL,
  `alt_basket` varchar(100)  NOT NULL,
  PRIMARY KEY (`ref`),
  UNIQUE KEY (`slug`),
  KEY  (`date_created`),
  KEY (`owner`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
