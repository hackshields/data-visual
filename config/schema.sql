CREATE TABLE IF NOT EXISTS `dc_user` (
  `userid`        int unsigned NOT NULL AUTO_INCREMENT,
  `creatorid`     int unsigned default NULL,
  `email`         varchar(255) default NULL,
  `name`          varchar(64) NOT NULL,
  `password`      varchar(32) default NULL,
  `permission`    tinyint default 0,
  `status`        tinyint default 0,
  `regip`         varchar(15) default NULL,
  `regdate`       int unsigned NOT NULL,
  `expiredate`    int unsigned NOT NULL,
  `plan`          varchar(16) NOT NULL DEFAULT "level1",
  `groupid`       varchar(32) default NULL,
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_conn` (
  `connid`     int unsigned NOT NULL AUTO_INCREMENT,
  `creatorid`  int unsigned NOT NULL,
  `name`       varchar(255) NOT NULL,
  `hostname`   varchar(255) NOT NULL,
  `username`   varchar(32) default NULL,
  `password`   varchar(128) NOT NULL,
  `database`   varchar(128) NOT NULL,
  `dbdriver`   varchar(32) default NULL,
  `dbprefix`   varchar(16) default NULL,
  `pconnect`   tinyint default 0,
  `char_set`   varchar(32) default NULL,
  `dbcollat`   varchar(32) default NULL,
  `swap_pre`   varchar(32) default NULL,
  `stricton`   varchar(32) default NULL,
  `port`       varchar(32) default NULL,
  `createdate` int unsigned NOT NULL,
  PRIMARY KEY  (`connid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_category` (
  `categoryid` int unsigned NOT NULL AUTO_INCREMENT,
  `creatorid`  int unsigned NOT NULL,
  `name`       varchar(255) NOT NULL,
  `icon`       varchar(255) DEFAULT NULL,
  `sort`       int default 0,
  `parentid`   int unsigned,
  PRIMARY KEY  (`categoryid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app` (
  `appid`           int unsigned NOT NULL AUTO_INCREMENT,
  `connid`          int unsigned NOT NULL,
  `creatorid`       int unsigned NOT NULL,
  `type`            varchar(32) NOT NULL,
  `name`            varchar(64) default NULL,
  `title`           varchar(64) default NULL,
  `desc`            varchar(255) default NULL,
  `categoryid`      int unsigned default NULL,
  `form`            text default NULL,
  `form_org`        text default NULL,
  `script`          text default NULL,
  `script_org`      text default NULL,
  `scripttype`      varchar(32) default NULL,
  `confirm`         text default NULL,
  `format`          varchar(32) default "tabular",
  `options`         text default NULL,
  `status`          varchar(16) default "draft",
  `embedcode`       varchar(64) default NULL,
  `sort`            int default 0,
  `createdate`      int unsigned NOT NULL,
  PRIMARY KEY  (`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app_permission` (
  `appid`           int unsigned NOT NULL,
  `userid`          int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_uservisitlog` (
  `userid`  int unsigned NOT NULL,
  `type`    varchar(32) NOT NULL,
  `module`  varchar(32) default NULL,
  `action`  varchar(32) default NULL,
  `appid`   int unsigned DEFAULT NULL,
  `message` text default NULL,
  `url`     varchar(255) default NULL,
  `ip`      varchar(15) default NULL,
  `show`    tinyint default 0,
  `date`    int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_customcss` (
  `creatorid`   int unsigned NOT NULL,
  `css`         text default NULL,
  `date`        int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_customjs` (
  `creatorid`   int unsigned NOT NULL,
  `js`          text default NULL,
  `date`        int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_user_options` (
  `creatorid` int unsigned NOT NULL,
  `name`   varchar(64) NOT NULL,
  `type`  varchar(16) default NULL,
  `value` varchar(64) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app_options` (
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned NOT NULL,
  `appid`  int unsigned NOT NULL,
  `key`   varchar(64) NOT NULL,
  `type`  varchar(64) NOT NULL,
  `value`  text default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_cache` (
  `creatorid`   int unsigned NOT NULL,
  `type`        varchar(32) NOT NULL,
  `name`        varchar(255) default NULL,
  `datatype`    varchar(32) default 'string',
  `value`       text default NULL,
  `date`        int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_user_dashboard` (
  `iddashboard` varchar(16) NOT NULL,
  `menu` varchar(64) default "Dashboard",
  `name` varchar(100) DEFAULT NULL,
  `layout` text DEFAULT NULL,
  `embedcode`       varchar(64) default NULL,
  PRIMARY KEY (`iddashboard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_tablelinks` (
  `connid` int unsigned NOT NULL,
  `srctable` varchar(128) NOT NULL,
  `srccolumn` varchar(128) NOT NULL,
  `dsttable` varchar(128) NOT NULL,
  `dstcolumn` varchar(128) NOT NULL,
  `creatorid` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_code` (
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned NOT NULL,
  `api` varchar(128) NOT NULL,
  `public` tinyint(1) DEFAULT 0,
  `filename` varchar(128) NOT NULL,
  `content` TEXT NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`creatorid`, `api`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_parameter` (
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `type` tinyint(1) DEFAULT 0,
  `value` TEXT NOT NULL,
  `cached` TEXT NOT NULL,
  `ttl`  int unsigned DEFAULT 0,
  `public` tinyint(1) DEFAULT 0,
  `lastupdate` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app_log` (
  `creatorid` int unsigned NOT NULL,
  `appid` int unsigned DEFAULT 0,
  `name` varchar(64) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `value` TEXT NOT NULL,
  `date` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_conn_option` (
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned DEFAULT 0,
  `name` varchar(64) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `value` TEXT NOT NULL,
  `date` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_checkpoint` (
  `cpid` int unsigned NOT NULL AUTO_INCREMENT,
  `appid` int unsigned DEFAULT 0,
  `paramkey` varchar(64) default NULL,
  `recorddate` int unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`cpid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_product` (
  `pid` int unsigned NOT NULL AUTO_INCREMENT,
  `creatorid` int unsigned NOT NULL,
  `name` varchar(32) DEFAULT NULL,
  `url` varchar(128) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `theme` varchar(32) DEFAULT NULL,
  `logintype` tinyint(1) DEFAULT 0,
  `brand` varchar(64) DEFAULT NULL,
  `brandurl` varchar(255) DEFAULT NULL,
  `menutype` tinyint(1) DEFAULT 0,
  `menuposition` tinyint(1) DEFAULT 0,
  `settings` text DEFAULT NULL,
  `apps` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 0,
  PRIMARY KEY  (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_template` (
  `creatorid` int unsigned NOT NULL,
  `filename` varchar(128) NOT NULL,
  `content` TEXT NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`creatorid`, `filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_sys_template` (
  `creatorid` int unsigned NOT NULL,
  `filename` varchar(128) NOT NULL,
  `content` TEXT NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`creatorid`, `filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_market_item` (
  `itemkey` varchar(32) NOT NULL,
  `creatorid` int unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` varchar(32) NOT NULL,
  `thumb` varchar(128) DEFAULT NULL,
  `summary` TEXT DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `price` int DEFAULT 0,
  `status` int DEFAULT 0,
  `target` varchar(64) NOT NULL,
  `param1` TEXT DEFAULT NULL,
  `createdate` int unsigned NOT NULL,
  `updatedate` int unsigned NOT NULL,
  PRIMARY KEY (`itemkey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_market_vendor` (
  `creatorid` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `email` varchar(128) NOT NULL,
  `avatar` varchar(256) NOT NULL,
  `clientcode` varchar(128) NOT NULL,
  `refer` varchar(256) NOT NULL,
  `userid` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY  (`creatorid`),
  UNIQUE KEY `clientcode` (`clientcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_market_attachment` (
  `itemkey` varchar(32) NOT NULL,
  `creatorid` int unsigned NOT NULL,
  `content` TEXT NOT NULL,
  `createdate` int unsigned NOT NULL,
  `updatedate` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_market_tag` (
  `itemkey` varchar(32) NOT NULL,
  `tag` varchar(64) NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`itemkey`, `tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_market_screenshot` (
  `itemkey` varchar(32) NOT NULL,
  `image` varchar(256) NOT NULL,
  `date` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `dc_market_follow` (
  `itemkey` varchar(32) NOT NULL,
  `creatorid` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`itemkey`, `creatorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_market_order` (
  `itemkey` varchar(32) NOT NULL,
  `creatorid` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`itemkey`, `creatorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `dc_crontab` (
  `cronid` varchar(32) NOT NULL,
  `code` varchar(32) NOT NULL,
  `type` tinyint(1) DEFAULT 0,
  `interval` int unsigned DEFAULT 0,
  `hour` int unsigned DEFAULT 0,
  `minute` int unsigned DEFAULT 0,
  `creatorid` int unsigned NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`cronid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_crontab_log` (
  `cronid` varchar(32) NOT NULL,
  `startdate` int unsigned DEFAULT 0,
  `enddate` int unsigned default 0,
  `status` int unsigned DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_user_app_favorite` (
  `userid` int unsigned NOT NULL,
  `appid` int unsigned DEFAULT 0,
  `date` int unsigned default 0,
  PRIMARY KEY (`userid`, `appid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_conn_views` (
  `viewid`    int unsigned NOT NULL AUTO_INCREMENT,  
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned DEFAULT 0,
  `name` varchar(64) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `value` TEXT NOT NULL,
  `date` int unsigned NOT NULL,
  `lastsyncdate` int unsigned NOT NULL,
  PRIMARY KEY  (`viewid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_filter` (
  `filterid`  int unsigned NOT NULL AUTO_INCREMENT,  
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `type` tinyint(1) DEFAULT 0,
  `value` TEXT NOT NULL,
  `cached` TEXT DEFAULT NULL,
  `single` tinyint(1) default 0,
  `isdefault` tinyint(1) default 0,
  `expression` TEXT DEFAULT NULL,
  `ttl`  int unsigned DEFAULT 0,
  `lastupdate` int unsigned NOT NULL,
  PRIMARY KEY  (`filterid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_sqlalert` (
  `alertid`  int unsigned NOT NULL AUTO_INCREMENT,  
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `criteriatype` tinyint(1) DEFAULT 0,
  `criteriavalue` varchar(256) NOT NULL,
  `frequency` tinyint(1) DEFAULT 0,
  `action` varchar(32) NOT NULL,
  `email` TEXT DEFAULT NULL,
  `params` TEXT DEFAULT NULL,
  `description` varchar(256) DEFAULT NULL,
  `sql` TEXT DEFAULT NULL,
  `status` tinyint(1) default 1,
  `_created_at` int unsigned NOT NULL,
  `_updated_at` int unsigned NOT NULL,
  PRIMARY KEY  (`alertid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_sqlalert_log` (
  `alertid`  int unsigned NOT NULL,  
  `status` tinyint(1) default 1,
  `message`  TEXT DEFAULT NULL,
  `time` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app_history` (
  `appid`           int unsigned NOT NULL,
  `userid`       int unsigned NOT NULL,
  `params`          TEXT DEFAULT NULL,
  `data`            TEXT DEFAULT NULL,
  `_created_at` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_user_premium` (
  `userid`        int unsigned NOT NULL AUTO_INCREMENT,
  `email`         varchar(255) NOT NULL,
  `name`          varchar(64) NOT NULL,
  `password`      varchar(32) default NULL,
  `slug`          varchar(128) NOT NULL,
  `customdomain`  varchar(256) DEFAULT NULL,
  `full_url`      varchar(256) NOT NULL,
  `container_id`  varchar(128) DEFAULT NULL,
  `status`        tinyint default 0,
  `regip`         varchar(15) default NULL,
  `regdate`       int unsigned NOT NULL,
  `expiredate`    int unsigned NOT NULL,
  PRIMARY KEY  (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_user_credit` (
  `creatorid`  int unsigned NOT NULL,
  `credits`    int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`creatorid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_user_credit_log` (
  `creatorid`   int unsigned NOT NULL,
  `old`         int unsigned NOT NULL DEFAULT 0,
  `changed`     int unsigned NOT NULL DEFAULT 0,
  `price`       int unsigned NOT NULL DEFAULT 0,
  `date`        int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_app_version` (
  `appid`           int unsigned NOT NULL,
  `version`         int unsigned NOT NULL default 1,
  `version_desc`    varchar(255) default NULL,
  `connid`          int unsigned NOT NULL,
  `creatorid`       int unsigned NOT NULL,
  `type`            varchar(32) NOT NULL,
  `name`            varchar(64) default NULL,
  `title`           varchar(64) default NULL,
  `desc`            varchar(255) default NULL,
  `categoryid`      int unsigned default NULL,
  `form`            text default NULL,
  `form_org`        text default NULL,
  `script`          text default NULL,
  `script_org`      text default NULL,
  `scripttype`      varchar(32) default NULL,
  `confirm`         text default NULL,
  `format`          varchar(32) default "tabular",
  `options`         text default NULL,
  `userid`          int unsigned NOT NULL,
  `ip`              varchar(64) default NULL,
  `date`            int unsigned NOT NULL,
  PRIMARY KEY  (`appid`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `df_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
);

CREATE TABLE IF NOT EXISTS `dc_auditlog` (
  `creatorid` int unsigned NOT NULL,
  `userid` int unsigned NOT NULL,
  `ip`  varchar(45) NOT NULL,
  `level`  tinyint default 0,
  `useragent` varchar(255) DEFAULT NULL,
  `content`  text default NULL,
  `date` int unsigned NOT NULL,
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_insights_settings` (
  `creatorid` int unsigned NOT NULL,
  `connid` int unsigned NOT NULL,
  `tablename`  varchar(128) NOT NULL,
  `content`  text default NULL,
  `_created_at` int unsigned NOT NULL,
  `_updated_at` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_insights_result` (
  `id`          varchar(32) NOT NULL,
  `creatorid`   int unsigned NOT NULL,
  `connid`      int unsigned NOT NULL,
  `tablename`   varchar(128) NOT NULL,
  `appid`       int unsigned DEFAULT NULL,
  `_created_at` int unsigned NOT NULL,
  `_updated_at` int unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_dataset` (
  `id`          varchar(32) NOT NULL,
  `creatorid`   INTEGER DEFAULT 0,
  `name`        varchar(32) NOT NULL,
  `data`        text default null,
  `_created_at` int unsigned NOT NULL DEFAULT 0,
  `_updated_at` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_loginsessions` (
  `id`          int unsigned NOT NULL AUTO_INCREMENT,
  `creatorid`   INTEGER DEFAULT 0,
  `userid`      INTEGER DEFAULT 0,
  `ip`          varchar(255) default null,
  `useragent`   varchar(255) default null,
  `logout_at`      int unsigned NOT NULL DEFAULT 0,
  `_created_at` int unsigned NOT NULL DEFAULT 0,
  `_updated_at` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_scheduled_jobs` (
  `creatorid` int unsigned NOT NULL,
  `jobid` varchar(128) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `sort` tinyint default 0,
  `status` tinyint default 0,
  `_created_at` int unsigned NOT NULL,
  `_updated_at` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`creatorid`, `jobid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `dc_scheduled_jobs_logs` (
  `jobid` varchar(128) NOT NULL,
  `status` TEXT DEFAULT NULL,
  `result` TEXT DEFAULT NULL,
  `start_time` int unsigned NOT NULL default 0,
  `end_time` int unsigned NOT NULL default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ok_queries` (
  `qid` varchar(64) NOT NULL,
  `icon` varchar(64) DEFAULT NULL,
  `name` varchar(64) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `query` TEXT DEFAULT NULL,
  `display` varchar(32) DEFAULT NULL,
  `options` TEXT DEFAULT NULL,
  `creatorid` INTEGER DEFAULT 0,
  `userid` INTEGER DEFAULT 0,
  `connid` INTEGER DEFAULT 0,
  `rows` int unsigned NOT NULL default 0,
  `star` TINYINT NOT NULL DEFAULT 0,
  `cost_time` int unsigned NOT NULL DEFAULT 0,
  `_created_at` int unsigned NOT NULL DEFAULT 0,
  `_updated_at` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ok_dashboards` (
  `did` varchar(64) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `creatorid` INTEGER DEFAULT 0,
  `userid` INTEGER DEFAULT 0,
  `filter` TEXT DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `layout` TEXT DEFAULT NULL,
  `options` TEXT DEFAULT NULL,
  `star` TINYINT NOT NULL DEFAULT 0,
  `_created_at` int unsigned NOT NULL DEFAULT 0,
  `_updated_at` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`did`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ok_tags` (
  `tag` varchar(32) NOT NULL,
  `qid` varchar(64) DEFAULT NULL,
  `did` varchar(64) DEFAULT NULL,
  `creatorid` INTEGER DEFAULT 0,
  `_created_at` int unsigned NOT NULL DEFAULT 0,
  `_updated_at` int unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;













