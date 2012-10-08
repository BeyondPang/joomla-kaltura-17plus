DROP TABLE IF EXISTS `#__kaltura_config`;
CREATE TABLE  `#__kaltura_config` (
  `name` varchar(100) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
);
INSERT INTO `#__kaltura_config` VALUES('player_regular_light','1011902');
INSERT INTO `#__kaltura_config` VALUES('player_regular_dark','1011912');
INSERT INTO `#__kaltura_config` VALUES('player_regular_light with share','1011932');
INSERT INTO `#__kaltura_config` VALUES('player_regular_dark with share','1011922');
INSERT INTO `#__kaltura_config` VALUES('player_mix_light','1011902');
INSERT INTO `#__kaltura_config` VALUES('player_mix_dark','1011912');
INSERT INTO `#__kaltura_config` VALUES('player_mix_light with share','1011932');
INSERT INTO `#__kaltura_config` VALUES('player_mix_dark with share','1011922');
INSERT INTO `#__kaltura_config` VALUES('server_uri','http://www.kaltura.com');
INSERT INTO `#__kaltura_config` VALUES('editor','1002226');
INSERT INTO `#__kaltura_config` VALUES('uploader_regular','1013682');
INSERT INTO `#__kaltura_config` VALUES('uploader_mix','1013692');
