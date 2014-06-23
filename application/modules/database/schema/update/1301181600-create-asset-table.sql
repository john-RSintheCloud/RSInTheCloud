
CREATE TABLE IF NOT EXISTS `asset` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
  `status` char(1) DEFAULT '0',
  `owner` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_extension` varchar(5) NOT NULL,
  `file_checksum` varchar(32) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`ref`),
  UNIQUE KEY (`slug`),
  KEY (`file_checksum`),
  KEY (`owner`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;

