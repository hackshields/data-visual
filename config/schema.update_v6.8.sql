ALTER TABLE dc_user ADD `groupid` varchar(32) DEFAULT NULL; 

CREATE TABLE IF NOT EXISTS `dc_usergroup` (
  `groupid` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `creatorid` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`groupid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_usergroup_permission` (
  `groupid` varchar(32) NOT NULL,
  `appid` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`groupid`, `appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE dc_user_premium ADD `container_id` varchar(128) DEFAULT NULL;
ALTER TABLE dc_user_premium ADD container_id TEXT DEFAULT NULL;
