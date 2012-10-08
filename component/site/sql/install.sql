DROP TABLE IF EXISTS `#__kaltura_config`;
CREATE TABLE  `#__kaltura_config` (
  `name` varchar(100) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
);
INSERT INTO `#__kaltura_config` VALUES('player_regular_light','1347302');
INSERT INTO `#__kaltura_config` VALUES('player_regular_dark','1347352');
INSERT INTO `#__kaltura_config` VALUES('player_regular_light with share','1676202');
INSERT INTO `#__kaltura_config` VALUES('player_regular_dark with share','1347462');
INSERT INTO `#__kaltura_config` VALUES('player_mix_light','1433562');
INSERT INTO `#__kaltura_config` VALUES('player_mix_dark','1435082');
INSERT INTO `#__kaltura_config` VALUES('player_mix_light with share','1435152');
INSERT INTO `#__kaltura_config` VALUES('player_mix_dark with share','1435202');
INSERT INTO `#__kaltura_config` VALUES('server_uri','http://www.kaltura.com');
INSERT INTO `#__kaltura_config` VALUES('editor','1002226');
INSERT INTO `#__kaltura_config` VALUES('uploader_regular','1013682');
INSERT INTO `#__kaltura_config` VALUES('uploader_mix','1013692');
