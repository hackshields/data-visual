CREATE TABLE IF NOT EXISTS dc_customjs (
  creatorid   INTEGER,
  js          TEXT,
  date        INTEGER
);

CREATE TABLE IF NOT EXISTS dc_app_options (
  creatorid INTEGER,
  connid    INTEGER,
  appid     INTEGER,
  key      TEXT,
  type     TEXT,
  value     TEXT
);

ALTER TABLE dc_parameter ADD public INTEGER DEFAULT 0;



