
CREATE TABLE IF NOT EXISTS `asset` (
  `ref` varchar(100) NOT NULL,
  `status` char(1) DEFAULT '0',
  `owner` int(11) NOT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_extension` varchar(5) NOT NULL,
  `file_checksum` varchar(32) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`slug`),
  KEY (`file_checksum`),
  KEY (`owner`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;

