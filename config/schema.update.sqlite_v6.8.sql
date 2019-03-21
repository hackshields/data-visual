ALTER TABLE dc_user ADD groupid TEXT DEFAULT "";

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