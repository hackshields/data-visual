CREATE TABLE IF NOT EXISTS `dc_customjs` (
  `creatorid`   int unsigned NOT NULL,
  `js`          text default NULL,
  `date`        int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app_options` (
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned NOT NULL,
  `appid`  int unsigned NOT NULL,
  `key`   varchar(64) NOT NULL,
  `type`  varchar(64) NOT NULL,
  `value`  text default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE dc_parameter ADD `public` tinyint(1) DEFAULT 0;
