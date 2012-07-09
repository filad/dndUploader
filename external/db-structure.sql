

CREATE DATABASE `dnduploader` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `dnduploader`;


CREATE TABLE IF NOT EXISTS `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fileData` varchar(511) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fileid` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `token` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
  `original_filename` varchar(511) COLLATE utf8_bin NOT NULL DEFAULT '',
  `upload_date` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fileid` (`fileid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
