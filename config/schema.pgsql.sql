CREATE SEQUENCE dc_user_seq;

CREATE TABLE IF NOT EXISTS dc_user (
  userid        int check (userid > 0) NOT NULL DEFAULT NEXTVAL ('dc_user_seq'),
  creatorid     int check (creatorid > 0) default NULL,
  email         varchar(255) default NULL,
  name          varchar(64) NOT NULL,
  password      varchar(32) default NULL,
  permission    smallint default 0,
  status        smallint default 0,
  regip         varchar(15) default NULL,
  regdate       int check (regdate > 0) NOT NULL,
  expiredate    int check (expiredate > 0) NOT NULL,
  plan          varchar(16) NOT NULL DEFAULT "level1",
  groupid       varchar(32) default NULL,
  PRIMARY KEY  (userid),
  CONSTRAINT name UNIQUE  (name)
) ;

CREATE SEQUENCE dc_conn_seq;

CREATE TABLE IF NOT EXISTS dc_conn (
  connid     int check (connid > 0) NOT NULL DEFAULT NEXTVAL ('dc_conn_seq'),
  creatorid  int check (creatorid > 0) NOT NULL,
  name       varchar(255) NOT NULL,
  hostname   varchar(255) NOT NULL,
  username   varchar(32) default NULL,
  password   varchar(128) NOT NULL,
  database   varchar(128) NOT NULL,
  dbdriver   varchar(32) default NULL,
  dbprefix   varchar(16) default NULL,
  pconnect   smallint default 0,
  char_set   varchar(32) default NULL,
  dbcollat   varchar(32) default NULL,
  swap_pre   varchar(32) default NULL,
  stricton   varchar(32) default NULL,
  port       varchar(32) default NULL,
  createdate int check (createdate > 0) NOT NULL,
  PRIMARY KEY  (connid)
) ;

CREATE SEQUENCE dc_category_seq;

CREATE TABLE IF NOT EXISTS dc_category (
  categoryid int check (categoryid > 0) NOT NULL DEFAULT NEXTVAL ('dc_category_seq'),
  creatorid  int check (creatorid > 0) NOT NULL,
  name       varchar(255) NOT NULL,
  icon       varchar(255) DEFAULT NULL,
  sort       int default 0,
  parentid   int check (parentid > 0),
  PRIMARY KEY  (categoryid)
) ;

CREATE SEQUENCE dc_app_seq;

CREATE TABLE IF NOT EXISTS dc_app (
  appid           int check (appid > 0) NOT NULL DEFAULT NEXTVAL ('dc_app_seq'),
  connid          int check (connid > 0) NOT NULL,
  creatorid       int check (creatorid > 0) NOT NULL,
  type            varchar(32) NOT NULL,
  name            varchar(64) default NULL,
  title           varchar(64) default NULL,
  desc            varchar(255) default NULL,
  categoryid      int check (categoryid > 0) default NULL,
  form            text default NULL,
  form_org        text default NULL,
  script          text default NULL,
  script_org      text default NULL,
  scripttype      varchar(32) default NULL,
  confirm         text default NULL,
  format          varchar(32) default "tabular",
  options         text default NULL,
  status          varchar(16) default "draft",
  embedcode       varchar(64) default NULL,
  sort            int default 0,
  createdate      int check (createdate > 0) NOT NULL,
  PRIMARY KEY  (appid)
) ;

CREATE TABLE IF NOT EXISTS dc_app_permission (
  appid           int check (appid > 0) NOT NULL,
  userid          int check (userid > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_uservisitlog (
  userid  int check (userid > 0) NOT NULL,
  type    varchar(32) NOT NULL,
  module  varchar(32) default NULL,
  action  varchar(32) default NULL,
  appid   int check (appid > 0) DEFAULT NULL,
  message text default NULL,
  url     varchar(255) default NULL,
  ip      varchar(15) default NULL,
  show    smallint default 0,
  date    int check (date > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_customcss (
  creatorid   int check (creatorid > 0) NOT NULL,
  css         text default NULL,
  date        int check (date > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_customjs (
  creatorid   int check (creatorid > 0) NOT NULL,
  js          text default NULL,
  date        int check (date > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_user_options (
  creatorid int check (creatorid > 0) NOT NULL,
  name   varchar(64) NOT NULL,
  type  varchar(16) default NULL,
  value varchar(64) default NULL
) ;

CREATE TABLE IF NOT EXISTS dc_app_options (
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) NOT NULL,
  appid  int check (appid > 0) NOT NULL,
  key   varchar(64) NOT NULL,
  type  varchar(64) NOT NULL,
  value  text default NULL
) ;

CREATE TABLE IF NOT EXISTS dc_cache (
  creatorid   int check (creatorid > 0) NOT NULL,
  type        varchar(32) NOT NULL,
  name        varchar(255) default NULL,
  datatype    varchar(32) default 'string',
  value       text default NULL,
  date        int check (date > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_user_dashboard (
  iddashboard varchar(16) NOT NULL,
  menu varchar(64) default "Dashboard",
  name varchar(100) DEFAULT NULL,
  layout text DEFAULT NULL,
  embedcode       varchar(64) default NULL,
  PRIMARY KEY (iddashboard)
) ;

CREATE TABLE IF NOT EXISTS dc_tablelinks (
  connid int check (connid > 0) NOT NULL,
  srctable varchar(128) NOT NULL,
  srccolumn varchar(128) NOT NULL,
  dsttable varchar(128) NOT NULL,
  dstcolumn varchar(128) NOT NULL,
  creatorid int check (creatorid > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_code (
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) NOT NULL,
  api varchar(128) NOT NULL,
  public smallint DEFAULT 0,
  filename varchar(128) NOT NULL,
  content TEXT NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (creatorid, api)
) ;

CREATE TABLE IF NOT EXISTS dc_parameter (
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) DEFAULT 0,
  name varchar(64) NOT NULL,
  type smallint DEFAULT 0,
  value TEXT NOT NULL,
  cached TEXT NOT NULL,
  ttl  int check (ttl > 0) DEFAULT 0,
  public smallint DEFAULT 0,
  lastupdate int check (lastupdate > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_app_log (
  creatorid int check (creatorid > 0) NOT NULL,
  appid int check (appid > 0) DEFAULT 0,
  name varchar(64) DEFAULT NULL,
  type varchar(64) DEFAULT NULL,
  value TEXT NOT NULL,
  date int check (date > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_conn_option (
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) DEFAULT 0,
  name varchar(64) DEFAULT NULL,
  type varchar(64) DEFAULT NULL,
  value TEXT NOT NULL,
  date int check (date > 0) NOT NULL
) ;

CREATE SEQUENCE dc_checkpoint_seq;

CREATE TABLE IF NOT EXISTS dc_checkpoint (
  cpid int check (cpid > 0) NOT NULL DEFAULT NEXTVAL ('dc_checkpoint_seq'),
  appid int check (appid > 0) DEFAULT 0,
  paramkey varchar(64) default NULL,
  recorddate int check (recorddate > 0) NOT NULL,
  content text NOT NULL,
  PRIMARY KEY  (cpid)
) ;

CREATE SEQUENCE dc_product_seq;

CREATE TABLE IF NOT EXISTS dc_product (
  pid int check (pid > 0) NOT NULL DEFAULT NEXTVAL ('dc_product_seq'),
  creatorid int check (creatorid > 0) NOT NULL,
  name varchar(32) DEFAULT NULL,
  url varchar(128) DEFAULT NULL,
  description text DEFAULT NULL,
  theme varchar(32) DEFAULT NULL,
  logintype smallint DEFAULT 0,
  brand varchar(64) DEFAULT NULL,
  brandurl varchar(255) DEFAULT NULL,
  menutype smallint DEFAULT 0,
  menuposition smallint DEFAULT 0,
  settings text DEFAULT NULL,
  apps text DEFAULT NULL,
  active smallint DEFAULT 0,
  PRIMARY KEY  (pid)
) ;

CREATE TABLE IF NOT EXISTS dc_template (
  creatorid int check (creatorid > 0) NOT NULL,
  filename varchar(128) NOT NULL,
  content TEXT NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (creatorid, filename)
) ;

CREATE TABLE IF NOT EXISTS dc_sys_template (
  creatorid int check (creatorid > 0) NOT NULL,
  filename varchar(128) NOT NULL,
  content TEXT NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (creatorid, filename)
) ;

CREATE TABLE IF NOT EXISTS dc_market_item (
  itemkey varchar(32) NOT NULL,
  creatorid int check (creatorid > 0) NOT NULL,
  name varchar(128) NOT NULL,
  type varchar(32) NOT NULL,
  thumb varchar(128) DEFAULT NULL,
  summary TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  price int DEFAULT 0,
  status int DEFAULT 0,
  target varchar(64) NOT NULL,
  param1 TEXT DEFAULT NULL,
  createdate int check (createdate > 0) NOT NULL,
  updatedate int check (updatedate > 0) NOT NULL,
  PRIMARY KEY (itemkey)
) ;

CREATE SEQUENCE dc_market_vendor_seq;

CREATE TABLE IF NOT EXISTS dc_market_vendor (
  creatorid int check (creatorid > 0) NOT NULL DEFAULT NEXTVAL ('dc_market_vendor_seq'),
  name varchar(64) NOT NULL,
  email varchar(128) NOT NULL,
  avatar varchar(256) NOT NULL,
  clientcode varchar(128) NOT NULL,
  refer varchar(256) NOT NULL,
  userid int check (userid > 0) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY  (creatorid),
  CONSTRAINT clientcode UNIQUE  (clientcode)
) ;

CREATE TABLE IF NOT EXISTS dc_market_attachment (
  itemkey varchar(32) NOT NULL,
  creatorid int check (creatorid > 0) NOT NULL,
  content TEXT NOT NULL,
  createdate int check (createdate > 0) NOT NULL,
  updatedate int check (updatedate > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_market_tag (
  itemkey varchar(32) NOT NULL,
  tag varchar(64) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (itemkey, tag)
) ;

CREATE TABLE IF NOT EXISTS dc_market_screenshot (
  itemkey varchar(32) NOT NULL,
  image varchar(256) NOT NULL,
  date int check (date > 0) NOT NULL
) ;


CREATE TABLE IF NOT EXISTS dc_market_follow (
  itemkey varchar(32) NOT NULL,
  creatorid int check (creatorid > 0) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (itemkey, creatorid)
) ;

CREATE TABLE IF NOT EXISTS dc_market_order (
  itemkey varchar(32) NOT NULL,
  creatorid int check (creatorid > 0) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (itemkey, creatorid)
) ;

CREATE TABLE IF NOT EXISTS dc_usergroup (
  groupid varchar(32) NOT NULL,
  name varchar(32) NOT NULL,
  creatorid int check (creatorid > 0) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (groupid)
) ;

CREATE TABLE IF NOT EXISTS dc_usergroup_permission (
  groupid varchar(32) NOT NULL,
  appid int check (appid > 0) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (groupid, appid)
) ;

CREATE TABLE IF NOT EXISTS dc_crontab (
  cronid varchar(32) NOT NULL,
  code varchar(32) NOT NULL,
  type smallint DEFAULT 0,
  interval int check (interval > 0) DEFAULT 0,
  hour int check (hour > 0) DEFAULT 0,
  minute int check (minute > 0) DEFAULT 0,
  creatorid int check (creatorid > 0) NOT NULL,
  date int check (date > 0) NOT NULL,
  PRIMARY KEY (cronid)
) ;

CREATE TABLE IF NOT EXISTS dc_crontab_log (
  cronid varchar(32) NOT NULL,
  startdate int check (startdate > 0) DEFAULT 0,
  enddate int check (enddate > 0) default 0,
  status int check (status > 0) DEFAULT 0
) ;

CREATE TABLE IF NOT EXISTS dc_user_app_favorite (
  userid int check (userid > 0) NOT NULL,
  appid int check (appid > 0) DEFAULT 0,
  date int check (date > 0) default 0,
  PRIMARY KEY (userid, appid)
) ;

CREATE SEQUENCE dc_conn_views_seq;

CREATE TABLE IF NOT EXISTS dc_conn_views (
  viewid    int check (viewid > 0) NOT NULL DEFAULT NEXTVAL ('dc_conn_views_seq'),  
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) DEFAULT 0,
  name varchar(64) DEFAULT NULL,
  type varchar(64) DEFAULT NULL,
  value TEXT NOT NULL,
  date int check (date > 0) NOT NULL,
  lastsyncdate int check (lastsyncdate > 0) NOT NULL,
  PRIMARY KEY  (viewid)
) ;

CREATE SEQUENCE dc_filter_seq;

CREATE TABLE IF NOT EXISTS dc_filter (
  filterid  int check (filterid > 0) NOT NULL DEFAULT NEXTVAL ('dc_filter_seq'),  
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) DEFAULT 0,
  name varchar(64) NOT NULL,
  type smallint DEFAULT 0,
  value TEXT NOT NULL,
  cached TEXT DEFAULT NULL,
  single smallint default 0,
  isdefault smallint default 0,
  expression TEXT DEFAULT NULL,
  ttl  int check (ttl > 0) DEFAULT 0,
  lastupdate int check (lastupdate > 0) NOT NULL,
  PRIMARY KEY  (filterid)
) ;

CREATE SEQUENCE dc_sqlalert_seq;

CREATE TABLE IF NOT EXISTS dc_sqlalert (
  alertid  int check (alertid > 0) NOT NULL DEFAULT NEXTVAL ('dc_sqlalert_seq'),  
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) DEFAULT 0,
  name varchar(64) NOT NULL,
  criteriatype smallint DEFAULT 0,
  criteriavalue varchar(256) NOT NULL,
  frequency smallint DEFAULT 0,
  action varchar(32) NOT NULL,
  email TEXT DEFAULT NULL,
  params TEXT DEFAULT NULL,
  description varchar(256) DEFAULT NULL,
  sql TEXT DEFAULT NULL,
  status smallint default 1,
  _created_at int check (_created_at > 0) NOT NULL,
  _updated_at int check (_updated_at > 0) NOT NULL,
  PRIMARY KEY  (alertid)
) ;

CREATE TABLE IF NOT EXISTS dc_sqlalert_log (
  alertid  int check (alertid > 0) NOT NULL,  
  status smallint default 1,
  message  TEXT DEFAULT NULL,
  time int check (time > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_app_history (
  appid           int check (appid > 0) NOT NULL,
  userid       int check (userid > 0) NOT NULL,
  params          TEXT DEFAULT NULL,
  data            TEXT DEFAULT NULL,
  _created_at int check (_created_at > 0) NOT NULL
) ;

CREATE SEQUENCE dc_user_premium_seq;

CREATE TABLE IF NOT EXISTS dc_user_premium (
  userid        int check (userid > 0) NOT NULL DEFAULT NEXTVAL ('dc_user_premium_seq'),
  email         varchar(255) NOT NULL,
  name          varchar(64) NOT NULL,
  password      varchar(32) default NULL,
  slug          varchar(128) NOT NULL,
  customdomain  varchar(256) DEFAULT NULL,
  full_url      varchar(256) NOT NULL,
  status        smallint default 0,
  regip         varchar(15) default NULL,
  regdate       int check (regdate > 0) NOT NULL,
  expiredate    int check (expiredate > 0) NOT NULL,
  PRIMARY KEY  (userid),
  CONSTRAINT email UNIQUE  (email)
) ;

CREATE TABLE IF NOT EXISTS dc_user_credit (
  creatorid  int check (creatorid > 0) NOT NULL,
  credits    int check (credits > 0) NOT NULL DEFAULT 0,
  PRIMARY KEY  (creatorid)
) ;

CREATE TABLE IF NOT EXISTS dc_user_credit_log (
  creatorid   int check (creatorid > 0) NOT NULL,
  old         int check (old > 0) NOT NULL DEFAULT 0,
  changed     int check (changed > 0) NOT NULL DEFAULT 0,
  price       int check (price > 0) NOT NULL DEFAULT 0,
  date        int check (date > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_app_version (
  appid           int check (appid > 0) NOT NULL,
  version         int check (version > 0) NOT NULL default 1,
  version_desc    varchar(255) default NULL,
  connid          int check (connid > 0) NOT NULL,
  creatorid       int check (creatorid > 0) NOT NULL,
  type            varchar(32) NOT NULL,
  name            varchar(64) default NULL,
  title           varchar(64) default NULL,
  desc            varchar(255) default NULL,
  categoryid      int check (categoryid > 0) default NULL,
  form            text default NULL,
  form_org        text default NULL,
  script          text default NULL,
  script_org      text default NULL,
  scripttype      varchar(32) default NULL,
  confirm         text default NULL,
  format          varchar(32) default "tabular",
  options         text default NULL,
  userid          int check (userid > 0) NOT NULL,
  ip              varchar(64) default NULL,
  date            int check (date > 0) NOT NULL,
  PRIMARY KEY  (appid, version)
) ;

CREATE TABLE IF NOT EXISTS df_sessions (
  id varchar(128) NOT NULL,
  ip_address varchar(45) NOT NULL,
  timestamp int check (timestamp > 0) DEFAULT 0 NOT NULL,
  data bytea NOT NULL
);

CREATE INDEX ci_sessions_timestamp ON df_sessions (timestamp);

CREATE TABLE IF NOT EXISTS dc_auditlog (
  creatorid int check (creatorid > 0) NOT NULL,
  userid int check (userid > 0) NOT NULL,
  ip  varchar(45) NOT NULL,
  level  smallint default 0,
  useragent varchar(255) DEFAULT NULL,
  content  text default NULL,
  date int check (date > 0) NOT NULL
) ;

CREATE INDEX userid ON dc_auditlog (userid);

CREATE TABLE IF NOT EXISTS dc_insights_settings (
  creatorid int check (creatorid > 0) NOT NULL,
  connid int check (connid > 0) NOT NULL,
  tablename  varchar(128) NOT NULL,
  content  text default NULL,
  _created_at int check (_created_at > 0) NOT NULL,
  _updated_at int check (_updated_at > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_insights_result (
  id          varchar(32) NOT NULL,
  creatorid   int check (creatorid > 0) NOT NULL,
  connid      int check (connid > 0) NOT NULL,
  tablename   varchar(128) NOT NULL,
  appid       int check (appid > 0) DEFAULT NULL,
  _created_at int check (_created_at > 0) NOT NULL,
  _updated_at int check (_updated_at > 0) NOT NULL
) ;

CREATE TABLE IF NOT EXISTS dc_dataset (
  id          varchar(32) NOT NULL,
  creatorid   INTEGER DEFAULT 0,
  name        varchar(32) NOT NULL,
  data        text default null,
  _created_at int check (_created_at > 0) NOT NULL DEFAULT 0,
  _updated_at int check (_updated_at > 0) NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
) ;

CREATE SEQUENCE dc_loginsessions_seq;

CREATE TABLE IF NOT EXISTS dc_loginsessions (
  id          int check (id > 0) NOT NULL DEFAULT NEXTVAL ('dc_loginsessions_seq'),
  creatorid   INTEGER DEFAULT 0,
  userid      INTEGER DEFAULT 0,
  ip          varchar(255) default null,
  useragent   varchar(255) default null,
  logout_at      int check (logout_at > 0) NOT NULL DEFAULT 0,
  _created_at int check (_created_at > 0) NOT NULL DEFAULT 0,
  _updated_at int check (_updated_at > 0) NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
);