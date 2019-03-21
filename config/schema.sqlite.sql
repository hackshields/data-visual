CREATE TABLE IF NOT EXISTS dc_user (
  userid        INTEGER PRIMARY KEY,
  creatorid     INTEGER DEFAULT 0,
  email         TEXT,
  name          TEXT UNIQUE,
  password      TEXT,
  permission    TINYINT DEFAULT 0,
  status        TINYINT DEFAULT 0,
  regip         TEXT,
  regdate       INTEGER,
  expiredate    INTEGER,
  plan          TEXT DEFAULT "level1",
  groupid       TEXT DEFAULT ""
);

CREATE TABLE IF NOT EXISTS dc_conn (
  connid     INTEGER PRIMARY KEY,
  creatorid  INTEGER,
  name       TEXT,
  hostname   TEXT,
  username   TEXT,
  password   TEXT,
  database   TEXT,
  dbdriver   TEXT,
  dbprefix   TEXT,
  pconnect   TINYINT default 0,
  char_set   TEXT,
  dbcollat   TEXT,
  swap_pre   TEXT,
  stricton   TEXT,
  port       TEXT,
  createdate INTEGER
);

CREATE TABLE IF NOT EXISTS dc_category (
  categoryid INTEGER PRIMARY KEY,
  creatorid  INTEGER,
  name       TEXT,
  icon       TEXT,
  sort       INTEGER DEFAULT 0,
  parentid   INTEGER
);

CREATE TABLE IF NOT EXISTS dc_app (
  appid           INTEGER PRIMARY KEY,
  connid          INTEGER,
  creatorid       INTEGER,
  type            TEXT,
  name            TEXT,
  title           TEXT,
  desc            TEXT,
  categoryid      INTEGER,
  form            TEXT,
  form_org        TEXT,
  script          TEXT,
  script_org      TEXT,
  scripttype      TEXT,
  confirm         TEXT,
  format          TEXT DEFAULT "tabular",
  options         TEXT,
  status          TEXT DEFAULT "draft",
  embedcode       TEXT,
  sort            INTEGER default 0,
  createdate      INTEGER
);

CREATE TABLE IF NOT EXISTS dc_app_permission (
  appid           INTEGER,
  userid          INTEGER
);

CREATE TABLE IF NOT EXISTS dc_uservisitlog (
  userid  INTEGER,
  type    TEXT,
  module  TEXT,
  action  TEXT,
  appid   INTEGER,
  message TEXT,
  url     TEXT,
  ip      TEXT,
  show    TINYINT,
  date    INTEGER
);

CREATE TABLE IF NOT EXISTS dc_customcss (
  creatorid   INTEGER,
  css         TEXT,
  date        INTEGER
);

CREATE TABLE IF NOT EXISTS dc_customjs (
  creatorid   INTEGER,
  js          TEXT,
  date        INTEGER
);

CREATE TABLE IF NOT EXISTS dc_user_options (
  creatorid INTEGER,
  name      TEXT,
  type      TEXT,
  value     TEXT
);

CREATE TABLE IF NOT EXISTS dc_app_options (
  creatorid INTEGER,
  connid    INTEGER,
  appid     INTEGER,
  key      TEXT,
  type     TEXT,
  value     TEXT
);

CREATE TABLE IF NOT EXISTS dc_cache (
  creatorid   INTEGER,
  type        TEXT,
  datatype    TEXT default 'string',
  name        TEXT,
  value       TEXT,
  date        INTEGER
);

CREATE TABLE IF NOT EXISTS dc_user_dashboard (
  iddashboard TEXT NOT NULL,
  menu        TEXT default 'Dashboard',
  name        TEXT,
  layout      TEXT DEFAULT NULL,
  embedcode   TEXT
);

CREATE TABLE IF NOT EXISTS dc_tablelinks (
  connid INTEGER NOT NULL,
  srctable TEXT NOT NULL,
  srccolumn TEXT NOT NULL,
  dsttable TEXT NOT NULL,
  dstcolumn TEXT NOT NULL,
  creatorid INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_code (
  creatorid INTEGER NOT NULL,
  connid INTEGER NOT NULL,
  api TEXT NOT NULL,
  public INTEGER DEFAULT 0,
  filename TEXT NOT NULL,
  content TEXT NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_parameter (
  creatorid INTEGER NOT NULL,
  connid INTEGER DEFAULT 0,
  name TEXT NOT NULL,
  type INTEGER DEFAULT 0,
  value TEXT NOT NULL,
  cached TEXT NOT NULL,
  ttl INTEGER DEFAULT 0,
  public INTEGER DEFAULT 0,
  lastupdate INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_app_log (
  creatorid INTEGER NOT NULL,
  appid INTEGER DEFAULT 0,
  name TEXT DEFAULT NULL,
  type TEXT DEFAULT NULL,
  value TEXT NOT NULL,
  date INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_conn_option (
  creatorid INTEGER NOT NULL,
  connid INTEGER DEFAULT 0,
  name TEXT DEFAULT NULL,
  type TEXT DEFAULT NULL,
  value TEXT NOT NULL,
  date INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_checkpoint (
  cpid INTEGER PRIMARY KEY,
  appid INTEGER DEFAULT 0,
  paramkey TEXT DEFAULT NULL,
  recorddate INTEGER NOT NULL,
  content TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_product (
  pid INTEGER PRIMARY KEY,
  creatorid INTEGER NOT NULL,
  name TEXT DEFAULT NULL,
  url TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  theme TEXT DEFAULT NULL,
  logintype INTEGER DEFAULT 0,
  brand TEXT DEFAULT NULL,
  brandurl TEXT DEFAULT NULL,
  menutype INTEGER DEFAULT 0,
  menuposition INTEGER DEFAULT 0,
  settings TEXT DEFAULT NULL,
  apps TEXT DEFAULT NULL,
  active INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_template (
  creatorid INTEGER NOT NULL,
  filename TEXT NOT NULL,
  content TEXT NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_sys_template (
  creatorid INTEGER NOT NULL,
  filename TEXT NOT NULL,
  content TEXT NOT NULL,
  date INTEGER NOT NULL
);


CREATE TABLE IF NOT EXISTS dc_market_item (
  itemkey TEXT NOT NULL,
  creatorid INTEGER NOT NULL,
  name TEXT NOT NULL,
  type TEXT NOT NULL,
  thumb TEXT DEFAULT NULL,
  summary TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  price INTEGER NOT NULL DEFAULT 0,
  status INTEGER NOT NULL DEFAULT 0,
  target TEXT NOT NULL,
  param1 TEXT DEFAULT NULL,
  createdate INTEGER NOT NULL,
  updatedate INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_market_vendor (
  creatorid INTEGER PRIMARY KEY,
  name TEXT NOT NULL,
  email TEXT NOT NULL,
  avatar TEXT DEFAULT NULL,
  clientcode TEXT DEFAULT NULL,
  refer TEXT DEFAULT NULL,
  userid INTEGER DEFAULT 0,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_market_attachment (
  itemkey TEXT NOT NULL,
  creatorid INTEGER NOT NULL,
  content TEXT NOT NULL,
  createdate INTEGER NOT NULL,
  updatedate INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_market_tag (
  itemkey TEXT NOT NULL,
  tag TEXT NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_market_screenshot (
  itemkey TEXT NOT NULL,
  image TEXT NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_market_follow (
  itemkey TEXT NOT NULL,
  creatorid INTEGER NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_market_order (
  itemkey TEXT NOT NULL,
  creatorid INTEGER NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_usergroup (
  groupid TEXT NOT NULL,
  name TEXT NOT NULL,
  creatorid INTEGER NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_usergroup_permission (
  groupid TEXT NOT NULL,
  appid INTEGER NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_crontab (
  cronid TEXT NOT NULL,
  code TEXT NOT NULL,
  type INTEGER DEFAULT 0,
  interval INTEGER DEFAULT 0,
  hour INTEGER DEFAULT 0,
  minute INTEGER DEFAULT 0,
  creatorid INTEGER NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_crontab_log (
  cronid TEXT NOT NULL,
  startdate INTEGER NOT NULL,
  enddate INTEGER NOT NULL,
  status INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_user_app_favorite (
  userid INTEGER NOT NULL,
  appid INTEGER NOT NULL,
  date INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_conn_views (
  viewid  INTEGER PRIMARY KEY,
  creatorid INTEGER NOT NULL,
  connid INTEGER NOT NULL,
  name TEXT NOT NULL,
  type TEXT NOT NULL,
  value TEXT NOT NULL,
  date INTEGER NOT NULL,
  lastsyncdate INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_filter (
  filterid  INTEGER PRIMARY KEY,
  creatorid INTEGER NOT NULL,
  connid INTEGER NOT NULL,
  name TEXT NOT NULL,
  type INTEGER DEFAULT 0,
  value TEXT NOT NULL,
  cached TEXT DEFAULT NULL,
  single INTEGER DEFAULT 0,
  isdefault INTEGER DEFAULT 0,
  expression TEXT DEFAULT NULL,
  ttl INTEGER DEFAULT 0,
  lastupdate INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_sqlalert (
  alertid  INTEGER PRIMARY KEY,  
  creatorid INTEGER NOT NULL,
  connid INTEGER NOT NULL,
  name TEXT NOT NULL,
  criteriatype INTEGER DEFAULT 0,
  criteriavalue TEXT NOT NULL,
  frequency INTEGER DEFAULT 0,
  action TEXT NOT NULL,
  email TEXT DEFAULT NULL,
  params TEXT DEFAULT NULL,
  description TEXT DEFAULT NULL,
  sql TEXT DEFAULT NULL,
  status INTEGER DEFAULT 1,
  _created_at INTEGER NOT NULL,
  _updated_at INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_sqlalert_log (
  alertid  INTEGER NOT NULL,  
  status INTEGER DEFAULT 1,
  message  TEXT DEFAULT NULL,
  time INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_app_history (
  appid           INTEGER NOT NULL,
  userid       INTEGER NOT NULL,
  params          TEXT DEFAULT NULL,
  data           TEXT DEFAULT NULL,
  _created_at     INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_user_premium (
  userid        INTEGER PRIMARY KEY,
  email         TEXT NOT NULL,
  name          TEXT NOT NULL,
  password      TEXT default NULL,
  slug          TEXT NOT NULL,
  customdomain  TEXT DEFAULT NULL,
  full_url      TEXT NOT NULL,
  container_id  TEXT DEFAULT NULL,
  status        INTEGER default 0,
  regip         TEXT default NULL,
  regdate       INTEGER NOT NULL,
  expiredate    INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_user_credit (
  creatorid  INTEGER PRIMARY KEY,
  credits    INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_user_credit_log (
  creatorid  INTEGER NOT NULL,
  old        INTEGER DEFAULT 0,
  changed    INTEGER DEFAULT 0,
  price      INTEGER DEFAULT 0,
  date       INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_app_version (
  appid           INTEGER,
  version         INTEGER DEFAULT 1,
  version_desc    TEXT,
  connid          INTEGER,
  creatorid       INTEGER,
  type            TEXT,
  name            TEXT,
  title           TEXT,
  desc            TEXT,
  categoryid      INTEGER,
  form            TEXT,
  form_org        TEXT,
  script          TEXT,
  script_org      TEXT,
  scripttype      TEXT,
  confirm         TEXT,
  format          TEXT DEFAULT "tabular",
  options         TEXT,
  userid          INTEGER,
  ip              TEXT,
  date            INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS df_sessions (
  id TEXT NOT NULL,
  ip_address TEXT NOT NULL,
  timestamp INTEGER  DEFAULT 0 NOT NULL,
  data TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS dc_auditlog (
  creatorid INTEGER DEFAULT 0,
  userid INTEGER DEFAULT 0,
  ip  TEXT DEFAULT NULL,
  level  INTEGER DEFAULT 0,
  useragent TEXT DEFAULT NULL,
  content  TEXT DEFAULT NULL,
  date INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_insights_settings (
  creatorid   INTEGER DEFAULT 0,
  connid      INTEGER,
  tablename   TEXT,
  content     TEXT,
  _created_at INTEGER DEFAULT 0,
  _updated_at INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_insights_result (
  id          TEXT NOT NULL,
  creatorid   INTEGER DEFAULT 0,
  connid      INTEGER,
  tablename   TEXT,
  appid       INTEGER,
  _created_at INTEGER DEFAULT 0,
  _updated_at INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_dataset (
  id          TEXT NOT NULL,
  creatorid   INTEGER DEFAULT 0,
  name        TEXT,
  data        TEXT,
  _created_at INTEGER DEFAULT 0,
  _updated_at INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_loginsessions (
  id          INTEGER PRIMARY KEY,
  creatorid   INTEGER DEFAULT 0,
  userid      INTEGER DEFAULT 0,
  ip          TEXT default null,
  useragent   TEXT default null,
  logout_at   INTEGER DEFAULT 0,
  _created_at INTEGER DEFAULT 0,
  _updated_at INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_scheduled_jobs (
  creatorid INTEGER DEFAULT 0,
  jobid TEXT NOT NULL,
  title TEXT DEFAULT NULL,
  content TEXT NOT NULL,
  sort tinyint default 0,
  status tinyint default 0,
  _created_at INTEGER DEFAULT 0,
  _updated_at INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS dc_scheduled_jobs_logs (
  jobid TEXT NOT NULL,
  status TEXT DEFAULT NULL,
  result TEXT DEFAULT NULL,
  start_time INTEGER DEFAULT 0,
  end_time INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS ok_queries (
  qid TEXT NOT NULL,
  icon TEXT DEFAULT NULL,
  name TEXT NOT NULL,
  desc TEXT NOT NULL,
  query TEXT DEFAULT NULL,
  display TEXT DEFAULT NULL,
  options TEXT DEFAULT NULL,
  creatorid INTEGER DEFAULT 0,
  userid INTEGER DEFAULT 0,
  connid INTEGER DEFAULT 0,
  rows DEFAULT 0,
  star INTEGER DEFAULT 0,
  cost_time DEFAULT 0,
  _created_at DEFAULT 0,
  _updated_at DEFAULT 0
);

CREATE TABLE IF NOT EXISTS ok_dashboards (
  did TEXT NOT NULL,
  name TEXT DEFAULT NULL,
  creatorid INTEGER DEFAULT 0,
  userid INTEGER DEFAULT 0,
  filter TEXT DEFAULT NULL,
  cover TEXT DEFAULT NULL,
  layout TEXT DEFAULT NULL,
  options TEXT DEFAULT NULL,
  star INTEGER DEFAULT 0,
  _created_at DEFAULT 0,
  _updated_at DEFAULT 0
);

CREATE TABLE IF NOT EXISTS ok_tags (
  tag TEXT NOT NULL,
  qid TEXT DEFAULT NULL,
  did TEXT DEFAULT NULL,
  creatorid INTEGER DEFAULT 0,
  _created_at DEFAULT 0,
  _updated_at DEFAULT 0
);







