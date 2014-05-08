
CREATE TABLE IF NOT EXISTS `basket` (
  `ref` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(100)  NOT NULL,
  `asset` varchar(100)  NOT NULL,
  `type` char(2) DEFAULT NULL,
  PRIMARY KEY (`ref`),
  UNIQUE KEY `asset` (`asset`,`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs ;

