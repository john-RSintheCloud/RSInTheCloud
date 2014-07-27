
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


--  SET NAMES utf8 ;

--
-- Table structure for table `organisation`
--

CREATE TABLE IF NOT EXISTS `organisation` (
  `ref` varchar(100) NOT NULL,
  `org_name` varchar(100) NOT NULL,
  `org_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `organisation` ADD PRIMARY KEY(`ref`);
ALTER TABLE  `organisation` ADD UNIQUE (`org_name`);

ALTER TABLE  `user` ADD  `organisation` VARCHAR( 100 ) NULL AFTER  `usergroup` ;
ALTER TABLE  `user` ADD INDEX (  `organisation` ) ;

ALTER TABLE  `resource` ADD  `organisation` VARCHAR( 100 ) NULL AFTER  `title` ;
ALTER TABLE  `resource` ADD INDEX (  `organisation` ) ;


