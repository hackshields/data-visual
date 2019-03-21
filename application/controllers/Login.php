<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
defined("BASEPATH") or exit("No direct script access.");
class Login extends BaseController
{
    public function index()
    {
        if (!$this->_check_install()) {
            return NULL;
        }
        $this->_easure_schema_compatibility();
        $token = $this->input->get("token");
        if (!empty($token)) {
            $this->_login_with_token($token);
        } else {
            $daily_retry_login_times = $this->config->item("daily_retry_login_times");
            if ($daily_retry_login_times && 0 < $daily_retry_login_times) {
                $ip = $this->input->ip_address();
                $duration = $this->config->item("daily_retry_login_time_duration");
                if (!$duration) {
                    $duration = 86400;
                }
                $elapse = time() - $duration;
                $query = $this->db->query("select count(1) as count from dc_uservisitlog where type = ? and ip = ? and date > ?", array("login_fail", $ip, $elapse));
                if ($query && 0 < $query->num_rows()) {
                    $count = $query->row()->count;
                    if ($daily_retry_login_times < $count) {
                        $this->_log_uservisit("0", "block_user", "login retry block, ip: " . $ip);
                        exit("DbFace detected unexpected login retry, if you think this is wrong, please contact your DbFace administrator.");
                    }
                }
            }
            $force_reset_accounts = $this->config->item("force_reset_accounts");
            if (!empty($force_reset_accounts)) {
                foreach ($force_reset_accounts as $account) {
                    $update_arr = array();
                    $email = $account["email"];
                    if (isset($account["name"])) {
                        $update_arr["name"] = $account["name"];
                    }
                    if (isset($account["password"])) {
                        $update_arr["password"] = md5($account["password"] . $this->config->item("password_encrypt"));
                    }
                    if (empty($email) || count($update_arr) == 0) {
                        continue;
                    }
                    $this->db->update("dc_user", $update_arr, array("email" => $email));
                }
            }
            $this->load->library("smartyview");
            if (file_exists(FCPATH . ".maintenance")) {
                $this->smartyview->assign("in_maintenance", true);
            }
            if (function_exists("api_login") && ($logininfo = api_login()) != false) {
                $username = $logininfo["username"];
                $email = $logininfo["email"];
                $permission = $logininfo["permission"];
                if ($permission == "admin") {
                    $result = $this->db->query("select userid, creatorid, name, permission, plan, status from dc_user where creatorid=0 and permission = 0 limit 1");
                    if ($result->num_rows() == 1) {
                        $row = $result->row();
                    }
                }
            }
            $tag = $this->input->post("tag");
            $ref = $this->input->get("ref");
            $signin_with_cookie = false;
            $this->smartyview->assign("ref", $ref);
            $username = $this->input->post("username");
            $password = md5($this->input->post("password") . $this->config->item("password_encrypt"));
            $timezone = $this->input->post("timezone");
            $userlanguage = $this->input->post("userlanguage");
            if (empty($tag)) {
                $this->load->helper("cookie");
                $this->load->helper("clientdata");
                $client_data = get_data(KEY_COOKIE);
                if ($client_data && $client_data["a"] == "1") {
                    $username = $client_data["u"];
                    $password = $client_data["k"];
                    $signin_with_cookie = true;
                    $tag = "confirm";
                } else {
                    $self_host = $this->config->item("self_host");
                    if ($self_host) {
                        $count = $this->db->count_all("dc_user");
                        if ($count == 1) {
                            $query = $this->db->select("1")->where(array("name" => "admin", "email" => "admin@dbface.com", "password" => md5("admin" . $this->config->item("password_encrypt"))))->get("dc_user");
                            if ($query->num_rows() == 1) {
                                $username = "admin";
                                $password = md5("admin" . $this->config->item("password_encrypt"));
                                $tag = "confirm";
                            }
                        }
                    }
                }
            }
            $self_host = $this->config->item("self_host");
            if ($self_host) {
                $query = $this->db->query("select name, value from dc_user_options where name = ?", array("customlogo"));
                if (1 <= $query->num_rows()) {
                    $custom_logo = $query->row()->value;
                    $this->smartyview->assign("custom_logo", $custom_logo);
                }
            }
            $this->smartyview->assign("self_host", $self_host);
            if ($tag == "confirm") {
                $autologin = $this->input->post("autologin");
                $result = $this->db->query("select userid, creatorid, name, email, permission, plan, status from dc_user where (name=? and password=?) or (email=? and password=?)", array($username, $password, $username, $password));
                if ($result->num_rows() == 1) {
                    $row = $result->row();
                    if ($row->status == USER_STATUS_DELETED) {
                        $this->_log_uservisit($row->userid, "Login", "Account Deleted");
                        $this->smartyview->assign("message", array("title" => "Error", "content" => "This account has been deleted."));
                        $this->smartyview->display("login/login.tpl");
                        return NULL;
                    }
                    if ($this->config->item("strict_email_check") && $row->creatorid == 0 && $row->status == 9) {
                        $this->_log_uservisit($row->userid, "Login", "Email Not Activated");
                        $this->smartyview->assign("message", array("title" => "Error", "content" => $this->lang->line("strCheckActivation")));
                        $this->smartyview->display("login/login.tpl");
                        return NULL;
                    }
                    $this->load->helper("cookie");
                    $this->load->helper("clientdata");
                    if ($autologin == 1) {
                        save_data(KEY_COOKIE, array("u" => $username, "k" => $password, "a" => 1));
                    } else {
                        if (!$signin_with_cookie) {
                            delete_data(KEY_COOKIE);
                        }
                    }
                    $this->session->set_userdata("login_userid", $row->userid);
                    $this->session->set_userdata("login_username", $row->name);
                    $this->session->set_userdata("login_email", $row->email);
                    $login_permission = $row->permission;
                    $this->session->set_userdata("login_permission", $login_permission);
                    $this->session->set_userdata("login_plan", $row->plan);
                    $quoteinfo = $this->config->item("plan_quote");
                    if (!isset($quoteinfo[$row->plan])) {
                        $this->db->update("dc_user", array("plan" => "level1"), array("userid" => $row->userid));
                    }
                    $this->session->set_userdata("login_timezone", $timezone);
                    if ($row->status == 9) {
                        $this->session->set_userdata("email_not_activation", true);
                    } else {
                        $this->session->unset_userdata("email_not_activation");
                    }
                    $creatorid = $row->creatorid;
                    if (!$creatorid) {
                        $creatorid = $row->userid;
                    }
                    $userid = $row->userid;
                    $this->session->set_userdata("login_creatorid", $creatorid);
                    $licensed = $this->_check_licensed($creatorid);
                    $this->smartyview->assign("licensed", $licensed);
                    $userlanguageInDb = $this->_get_userlanguageInDb($creatorid);
                    if ($userlanguageInDb) {
                        $this->session->set_userdata("userlanguage", $userlanguageInDb);
                        $this->config->set_item("language", $userlanguageInDb);
                    } else {
                        if (!empty($userlanguage) && in_array($userlanguage, array("zh-CN", "english"))) {
                            $this->session->set_userdata("userlanguage", $userlanguage);
                            $this->config->set_item("language", $userlanguage);
                        }
                    }
                    $onlydefaultapps = $this->_get_only_showapps_in_default($creatorid);
                    if ($onlydefaultapps) {
                        $this->session->set_userdata("onlydefaultapps", $onlydefaultapps);
                    } else {
                        $this->session->set_userdata("onlydefaultapps", false);
                    }
                    if (!$this->_check_ipwhitelist($creatorid)) {
                        $this->smartyview->assign("username", $username);
                        $ip_address = $this->input->ip_address();
                        $this->smartyview->assign("message", array("title" => "Error", "content" => "You are not allowed to access this resource! If you think this is wrong, please send your IP Address: " . $ip_address . " to your administrator."));
                    } else {
                        $is_expired = $this->_check_and_assigned_expired($creatorid);
                        if ($is_expired) {
                            $this->smartyview->assign("account_expired", true);
                            $this->session->set_userdata("_EXPIRED_", true);
                        }
                        $this->_execute_trigger($creatorid, "login", array("creatorid" => $creatorid, "userid" => $userid));
                        $this->_log_uservisit($row->userid, "Login", "Login OK");
                        $this->_log_session($creatorid);
                        $this->load->helper("url");
                        $product_mode = $this->config->item("dbface_product_mode");
                        $is_ma = $this->input->get("ma");
                        if ($is_ma == "1") {
                            redirect("?module=Ma");
                        } else {
                            if ($product_mode == "openkit") {
                                redirect("?module=Openkit#module=Analytize");
                            } else {
                                redirect("?module=CoreHome#module=Dashboard");
                            }
                        }
                        return NULL;
                    }
                } else {
                    if ($this->_check_sign_premium($username, $password)) {
                        return NULL;
                    }
                    $this->_log_uservisit("0", "login_fail", "Invalid password");
                    $this->smartyview->assign("username", $username);
                    $this->smartyview->assign("message", array("title" => "Error", "content" => "Invalid password"));
                }
            }
            $allow_reset_password_on_premise = $this->config->item("allow_reset_password_on_premise");
            if ($allow_reset_password_on_premise) {
                $this->smartyview->assign("allow_reset_password_on_premise", $allow_reset_password_on_premise);
            }
            $logo_settings = $this->config->item("login_logo_settings");
            if ($logo_settings) {
                $this->smartyview->assign("login_logo_settings", $logo_settings);
            }
            $this->smartyview->display("login/login.tpl");
        }
    }
    public function resetpassword()
    {
        $email = $this->input->post("email");
        $this->load->database();
        $this->load->helper("json");
        $query = $this->db->query("select userid from dc_user where email = ?", array($email));
        if ($query->num_rows() == 1) {
            $userid = $query->row()->userid;
            $this->load->library("email");
            $this->_init_email_settings();
            $this->email->to($email);
            $email_title = $this->config->item("email_title_forgotpassword");
            if (empty($email_title)) {
                $email_title = "DbFace Password";
            }
            $this->email->subject($email_title);
            $this->load->library("smartyview");
            $token = $this->_get_forgot_password_encrypt($userid, $email);
            $resetpassword_url = $this->config->item("base_url") . "?module=Password_change&KEY=" . $token . "&e=" . $email;
            $this->smartyview->assign("resetpassword_url", $resetpassword_url);
            $this->email->message($this->smartyview->fetch("email/forgetpassword.tpl"));
            $this->email->send();
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => "1")));
        } else {
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => "0")));
        }
    }
    public function forgotpassword()
    {
        $this->load->library("smartyview");
        $logo_settings = $this->config->item("login_logo_settings");
        if ($logo_settings) {
            $this->smartyview->assign("login_logo_settings", $logo_settings);
        }
        $this->smartyview->display("login/forgetpassword.tpl");
    }
    public function _easure_schema_compatibility()
    {
        if (!$this->db->table_exists("dc_conn_views")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_conn_views` (\r\n            `viewid`    int unsigned NOT NULL AUTO_INCREMENT,  \r\n            `creatorid` int unsigned NOT NULL,\r\n            `connid` int unsigned DEFAULT 0,\r\n            `name` varchar(64) DEFAULT NULL,\r\n            `type` varchar(64) DEFAULT NULL,\r\n            `value` TEXT NOT NULL,\r\n            `date` int unsigned NOT NULL,\r\n            `lastsyncdate` int unsigned NOT NULL,\r\n            PRIMARY KEY  (`viewid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_conn_views (\r\n            viewid  INTEGER PRIMARY KEY,\r\n            creatorid INTEGER NOT NULL,\r\n            connid INTEGER NOT NULL,\r\n            name TEXT NOT NULL,\r\n            type TEXT NOT NULL,\r\n            value TEXT NOT NULL,\r\n            date INTEGER NOT NULL,\r\n            lastsyncdate INTEGER NOT NULL\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_user_app_favorite")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_user_app_favorite` (\r\n            `userid` int unsigned NOT NULL,\r\n            `appid` int unsigned DEFAULT 0,\r\n            `date` int unsigned default 0,\r\n            PRIMARY KEY (`userid`, `appid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_user_app_favorite (\r\n            userid INTEGER NOT NULL,\r\n            appid INTEGER NOT NULL,\r\n            date INTEGER NOT NULL\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_filter")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_filter` (\r\n            `filterid`  int unsigned NOT NULL AUTO_INCREMENT,  \r\n            `creatorid` int unsigned NOT NULL,\r\n            `connid` int unsigned DEFAULT 0,\r\n            `name` varchar(64) NOT NULL,\r\n            `type` tinyint(1) DEFAULT 0,\r\n            `value` TEXT NOT NULL,\r\n            `cached` TEXT DEFAULT NULL,\r\n            `single` tinyint(1) default 0,\r\n            `isdefault` tinyint(1) default 0,\r\n            `expression` TEXT DEFAULT NULL,\r\n            `ttl`  int unsigned DEFAULT 0,\r\n            `lastupdate` int unsigned NOT NULL,\r\n            PRIMARY KEY  (`filterid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_filter (\r\n            filterid  INTEGER PRIMARY KEY,\r\n            creatorid INTEGER NOT NULL,\r\n            connid INTEGER NOT NULL,\r\n            name TEXT NOT NULL,\r\n            type INTEGER DEFAULT 0,\r\n            value TEXT NOT NULL,\r\n            cached TEXT DEFAULT NULL,\r\n            single INTEGER DEFAULT 0,\r\n            isdefault INTEGER DEFAULT 0,\r\n            expression TEXT DEFAULT NULL,\r\n            ttl INTEGER DEFAULT 0,\r\n            lastupdate INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if ($this->db->table_exists("dc_sqlalert") && !$this->db->field_exists("params", "dc_sqlalert")) {
            $this->load->dbforge();
            $this->dbforge->drop_table("dc_sqlalert");
        }
        if ($this->db->table_exists("dc_app") && !$this->db->field_exists("sort", "dc_app")) {
            $this->db->query("ALTER TABLE dc_app ADD sort integer default 0");
        }
        if ($this->db->table_exists("dc_category") && !$this->db->field_exists("sort", "dc_category")) {
            $this->db->query("ALTER TABLE dc_category ADD sort integer default 0");
        }
        if (!$this->db->table_exists("dc_sqlalert")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_sqlalert` (\r\n            `alertid`  int unsigned NOT NULL AUTO_INCREMENT,  \r\n            `creatorid` int unsigned NOT NULL,\r\n            `connid` int unsigned DEFAULT 0,\r\n            `name` varchar(64) NOT NULL,\r\n            `criteriatype` tinyint(1) DEFAULT 0,\r\n            `criteriavalue` varchar(256) NOT NULL,\r\n            `frequency` tinyint(1) DEFAULT 0,\r\n            `action` varchar(32) NOT NULL,\r\n            `email` TEXT DEFAULT NULL,\r\n            `params` TEXT DEFAULT NULL,\r\n            `description` varchar(256) DEFAULT NULL,\r\n            `sql` TEXT DEFAULT NULL,\r\n            `status` tinyint(1) default 1,\r\n            `_created_at` int unsigned NOT NULL,\r\n            `_updated_at` int unsigned NOT NULL,\r\n            PRIMARY KEY  (`alertid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_sqlalert (\r\n            alertid  INTEGER PRIMARY KEY,  \r\n            creatorid INTEGER NOT NULL,\r\n            connid INTEGER NOT NULL,\r\n            name TEXT NOT NULL,\r\n            criteriatype INTEGER DEFAULT 0,\r\n            criteriavalue TEXT NOT NULL,\r\n            frequency INTEGER DEFAULT 0,\r\n            action TEXT NOT NULL,\r\n            email TEXT DEFAULT NULL,\r\n            params TEXT DEFAULT NULL,\r\n            description TEXT DEFAULT NULL,\r\n            sql TEXT DEFAULT NULL,\r\n            status INTEGER DEFAULT 1,\r\n            _created_at INTEGER NOT NULL,\r\n            _updated_at INTEGER NOT NULL\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_sqlalert_log")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_sqlalert_log` (\r\n            `alertid`  int unsigned NOT NULL,  \r\n            `status` tinyint(1) default 1,\r\n            `message`  TEXT DEFAULT NULL,\r\n            `time` int unsigned NOT NULL\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_sqlalert_log (\r\n            alertid  INTEGER NOT NULL,  \r\n            status INTEGER DEFAULT 1,\r\n            message  TEXT DEFAULT NULL,\r\n            time INTEGER NOT NULL\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_app_history")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_app_history` (\r\n            `appid`           int unsigned NOT NULL,\r\n            `userid`       int unsigned NOT NULL,\r\n            `params`          TEXT DEFAULT NULL,\r\n            `data`            TEXT DEFAULT NULL,\r\n            `_created_at` int unsigned NOT NULL\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_app_history (\r\n            appid           INTEGER NOT NULL,\r\n            userid       INTEGER NOT NULL,\r\n            params          TEXT DEFAULT NULL,\r\n            data           TEXT DEFAULT NULL,\r\n            _created_at     INTEGER NOT NULL\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_user_premium")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_user_premium` (\r\n            `userid`        int unsigned NOT NULL AUTO_INCREMENT,\r\n            `email`         varchar(255) NOT NULL,\r\n            `name`          varchar(64) NOT NULL,\r\n            `password`      varchar(32) default NULL,\r\n            `slug`          varchar(128) NOT NULL,\r\n            `customdomain`  varchar(256) DEFAULT NULL,\r\n            `full_url`      varchar(256) NOT NULL,\r\n            `container_id`  varchar(128) DEFAULT NULL,\r\n            `status`        tinyint default 0,\r\n            `regip`         varchar(15) default NULL,\r\n            `regdate`       int unsigned NOT NULL,\r\n            `expiredate`    int unsigned NOT NULL,\r\n            PRIMARY KEY  (`userid`),\r\n            UNIQUE KEY `email` (`email`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_user_premium (\r\n            userid        INTEGER PRIMARY KEY,\r\n            email         TEXT NOT NULL,\r\n            name          TEXT NOT NULL,\r\n            password      TEXT default NULL,\r\n            slug          TEXT NOT NULL,\r\n            customdomain  TEXT DEFAULT NULL,\r\n            full_url      TEXT NOT NULL,\r\n            container_id  TEXT DEFAULT NULL,\r\n            status        INTEGER default 0,\r\n            regip         TEXT default NULL,\r\n            regdate       INTEGER NOT NULL,\r\n            expiredate    INTEGER NOT NULL\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_user_credit")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_user_credit` (\r\n            `creatorid`  int unsigned NOT NULL,\r\n            `credits`    int unsigned NOT NULL DEFAULT 0,\r\n            PRIMARY KEY  (`creatorid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_user_credit (\r\n            creatorid  INTEGER PRIMARY KEY,\r\n            credits    INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_user_credit_log")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_user_credit_log` (\r\n            `creatorid`   int unsigned NOT NULL,\r\n            `old`         int unsigned NOT NULL DEFAULT 0,\r\n            `changed`     int unsigned NOT NULL DEFAULT 0,\r\n            `price`       int unsigned NOT NULL DEFAULT 0,\r\n            `date`        int unsigned NOT NULL\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_user_credit_log (\r\n            creatorid  INTEGER NOT NULL,\r\n            old        INTEGER DEFAULT 0,\r\n            changed    INTEGER DEFAULT 0,\r\n            price      INTEGER DEFAULT 0,\r\n            date       INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_app_version")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_app_version` (\r\n            `appid`           int unsigned NOT NULL,\r\n            `version`         int unsigned NOT NULL default 1,\r\n            `version_desc`    varchar(255) default NULL,\r\n            `connid`          int unsigned NOT NULL,\r\n            `creatorid`       int unsigned NOT NULL,\r\n            `type`            varchar(32) NOT NULL,\r\n            `name`            varchar(64) default NULL,\r\n            `title`           varchar(64) default NULL,\r\n            `desc`            varchar(255) default NULL,\r\n            `categoryid`      int unsigned default NULL,\r\n            `form`            text default NULL,\r\n            `form_org`        text default NULL,\r\n            `script`          text default NULL,\r\n            `script_org`      text default NULL,\r\n            `scripttype`      varchar(32) default NULL,\r\n            `confirm`         text default NULL,\r\n            `format`          varchar(32) default \"tabular\",\r\n            `options`         text default NULL,\r\n            `userid`          int unsigned NOT NULL,\r\n            `ip`              varchar(64) default NULL,\r\n            `date`            int unsigned NOT NULL,\r\n            PRIMARY KEY  (`appid`, `version`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_app_version (\r\n            appid           INTEGER,\r\n            version         INTEGER DEFAULT 1,\r\n            version_desc    TEXT,\r\n            connid          INTEGER,\r\n            creatorid       INTEGER,\r\n            type            TEXT,\r\n            name            TEXT,\r\n            title           TEXT,\r\n            desc            TEXT,\r\n            categoryid      INTEGER,\r\n            form            TEXT,\r\n            form_org        TEXT,\r\n            script          TEXT,\r\n            script_org      TEXT,\r\n            scripttype      TEXT,\r\n            confirm         TEXT,\r\n            format          TEXT DEFAULT \"tabular\",\r\n            options         TEXT,\r\n            userid          INTEGER,\r\n            ip              TEXT,\r\n            date            INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_auditlog")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_auditlog` (\r\n            `creatorid` int unsigned NOT NULL,\r\n            `userid` int unsigned NOT NULL,\r\n            `ip`  varchar(45) NOT NULL,\r\n            `level`  tinyint default 0,\r\n            `useragent` varchar(255) DEFAULT NULL,\r\n            `content`  text default NULL,\r\n            `date` int unsigned NOT NULL,\r\n            KEY `userid` (`userid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_auditlog (\r\n            creatorid INTEGER DEFAULT 0,\r\n            userid INTEGER DEFAULT 0,\r\n            ip  TEXT DEFAULT NULL,\r\n            level  INTEGER DEFAULT 0,\r\n            useragent TEXT DEFAULT NULL,\r\n            content  TEXT DEFAULT NULL,\r\n            date INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_insights_settings")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_insights_settings` (\r\n            `creatorid` int unsigned NOT NULL,\r\n            `connid` int unsigned NOT NULL,\r\n            `tablename`  varchar(128) NOT NULL,\r\n            `content`  text default NULL,\r\n            `_created_at` int unsigned NOT NULL,\r\n            `_updated_at` int unsigned NOT NULL\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_insights_settings (\r\n            creatorid   INTEGER DEFAULT 0,\r\n            connid      INTEGER,\r\n            tablename       TEXT,\r\n            content     TEXT,\r\n            _created_at INTEGER DEFAULT 0,\r\n            _updated_at INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_insights_result")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_insights_result` (\r\n            `id`          varchar(32) NOT NULL,\r\n            `creatorid`   int unsigned NOT NULL,\r\n            `connid`      int unsigned NOT NULL,\r\n            `tablename`   varchar(128) NOT NULL,\r\n            `appid`       int unsigned DEFAULT NULL,\r\n            `_created_at` int unsigned NOT NULL,\r\n            `_updated_at` int unsigned NOT NULL\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_insights_result (\r\n            id          TEXT NOT NULL,\r\n            creatorid   INTEGER DEFAULT 0,\r\n            connid      INTEGER,\r\n            tablename   TEXT,\r\n            appid       INTEGER,\r\n            _created_at INTEGER DEFAULT 0,\r\n            _updated_at INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_dataset")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_dataset` (\r\n            `id`          varchar(32) NOT NULL,\r\n            `creatorid`   INTEGER DEFAULT 0,\r\n            `name`        varchar(32) NOT NULL,\r\n            `data`        text default null,\r\n            `_created_at` int unsigned NOT NULL DEFAULT 0,\r\n            `_updated_at` int unsigned NOT NULL DEFAULT 0,\r\n            PRIMARY KEY  (`id`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_dataset (\r\n            id          TEXT NOT NULL,\r\n            creatorid   INTEGER DEFAULT 0,\r\n            name        TEXT,\r\n            data        TEXT,\r\n            _created_at INTEGER DEFAULT 0,\r\n            _updated_at INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_loginsessions")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_loginsessions` (\r\n            `id`          int unsigned NOT NULL AUTO_INCREMENT,\r\n            `creatorid`   INTEGER DEFAULT 0,\r\n            `userid`      INTEGER DEFAULT 0,\r\n            `ip`          varchar(255) default null,\r\n            `useragent`   varchar(255) default null,\r\n            `logout_at`      int unsigned NOT NULL DEFAULT 0,\r\n            `_created_at` int unsigned NOT NULL DEFAULT 0,\r\n            `_updated_at` int unsigned NOT NULL DEFAULT 0,\r\n            PRIMARY KEY  (`id`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_loginsessions (\r\n            id        INTEGER PRIMARY KEY,\r\n            creatorid   INTEGER DEFAULT 0,\r\n            userid      INTEGER DEFAULT 0,\r\n            ip          TEXT default null,\r\n            useragent   TEXT default null,\r\n            logout_at   INTEGER DEFAULT 0,\r\n            _created_at INTEGER DEFAULT 0,\r\n            _updated_at INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        if (!$this->db->table_exists("dc_scheduled_jobs")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_scheduled_jobs` (\r\n            `creatorid` int unsigned NOT NULL,\r\n            `jobid` varchar(128) NOT NULL,\r\n            `title` varchar(255) DEFAULT NULL,\r\n            `content` TEXT NOT NULL,\r\n            `sort` tinyint default 0,\r\n            `status` tinyint default 0,\r\n            `_created_at` int unsigned NOT NULL,\r\n            `_updated_at` int unsigned NOT NULL DEFAULT 0,\r\n            PRIMARY KEY (`creatorid`, `jobid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;\r\n          \r\n          CREATE TABLE IF NOT EXISTS `dc_scheduled_jobs_logs` (\r\n            `jobid` varchar(128) NOT NULL,\r\n            `status` TEXT DEFAULT NULL,\r\n            `result` TEXT DEFAULT NULL,\r\n            `start_time` int unsigned NOT NULL default 0,\r\n            `end_time` int unsigned NOT NULL default 0\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_scheduled_jobs (\r\n            creatorid INTEGER DEFAULT 0,\r\n            jobid TEXT NOT NULL,\r\n            title TEXT DEFAULT NULL,\r\n            content TEXT NOT NULL,\r\n            sort tinyint default 0,\r\n            status tinyint default 0,\r\n            _created_at INTEGER DEFAULT 0,\r\n            _updated_at INTEGER DEFAULT 0\r\n          );\r\n          CREATE TABLE IF NOT EXISTS dc_scheduled_jobs_logs (\r\n            jobid TEXT NOT NULL,\r\n            status TEXT DEFAULT NULL,\r\n            result TEXT DEFAULT NULL,\r\n            start_time INTEGER DEFAULT 0,\r\n            end_time INTEGER DEFAULT 0\r\n          )");
                }
            }
        }
        $this->_easure_openkit_schema();
    }
    public function _easure_openkit_schema()
    {
        if (!$this->db->table_exists("ok_queries")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `ok_queries` (\r\n            `qid` varchar(64) NOT NULL,\r\n            `icon` varchar(64) DEFAULT NULL,\r\n            `name` varchar(64) NOT NULL,\r\n            `desc` varchar(255) DEFAULT NULL,\r\n            `query` TEXT DEFAULT NULL,\r\n            `display` varchar(32) DEFAULT NULL,\r\n            `options` TEXT DEFAULT NULL,\r\n            `creatorid` INTEGER DEFAULT 0,\r\n            `userid` INTEGER DEFAULT 0,\r\n            `connid` INTEGER DEFAULT 0,\r\n            `rows` int unsigned NOT NULL default 0,\r\n            `star` TINYINT NOT NULL DEFAULT 0,\r\n            `cost_time` int unsigned NOT NULL DEFAULT 0,\r\n            `_created_at` int unsigned NOT NULL DEFAULT 0,\r\n            `_updated_at` int unsigned NOT NULL DEFAULT 0,\r\n            PRIMARY KEY (`qid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;\r\n          \r\n          CREATE TABLE IF NOT EXISTS `ok_dashboards` (\r\n            `did` varchar(64) NOT NULL,\r\n            `name` varchar(128) DEFAULT NULL,\r\n            `creatorid` INTEGER DEFAULT 0,\r\n            `userid` INTEGER DEFAULT 0,\r\n            `filter` TEXT DEFAULT NULL,\r\n            `cover` varchar(255) DEFAULT NULL,\r\n            `layout` TEXT DEFAULT NULL,\r\n            `options` TEXT DEFAULT NULL,\r\n            `star` TINYINT NOT NULL DEFAULT 0,\r\n            `_created_at` int unsigned NOT NULL DEFAULT 0,\r\n            `_updated_at` int unsigned NOT NULL DEFAULT 0,\r\n            PRIMARY KEY (`did`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;\r\n          \r\n          CREATE TABLE IF NOT EXISTS `ok_tags` (\r\n            `tag` varchar(32) NOT NULL,\r\n            `qid` varchar(64) DEFAULT NULL,\r\n            `did` varchar(64) DEFAULT NULL,\r\n            `creatorid` INTEGER DEFAULT 0,\r\n            `_created_at` int unsigned NOT NULL DEFAULT 0,\r\n            `_updated_at` int unsigned NOT NULL DEFAULT 0\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS ok_queries (\r\n            qid TEXT NOT NULL,\r\n            icon TEXT DEFAULT NULL,\r\n            name TEXT NOT NULL,\r\n            desc TEXT NOT NULL,\r\n            query TEXT DEFAULT NULL,\r\n            display TEXT DEFAULT NULL,\r\n            options TEXT DEFAULT NULL,\r\n            creatorid INTEGER DEFAULT 0,\r\n            userid INTEGER DEFAULT 0,\r\n            connid INTEGER DEFAULT 0,\r\n            rows DEFAULT 0,\r\n            star INTEGER DEFAULT 0,\r\n            cost_time DEFAULT 0,\r\n            _created_at DEFAULT 0,\r\n            _updated_at DEFAULT 0\r\n          );\r\n          CREATE TABLE IF NOT EXISTS ok_dashboards (\r\n            did TEXT NOT NULL,\r\n            name TEXT DEFAULT NULL,\r\n            creatorid INTEGER DEFAULT 0,\r\n            userid INTEGER DEFAULT 0,\r\n            filter TEXT DEFAULT NULL,\r\n            cover TEXT DEFAULT NULL,\r\n            layout TEXT DEFAULT NULL,\r\n            options TEXT DEFAULT NULL,\r\n            star INTEGER DEFAULT 0,\r\n            _created_at DEFAULT 0,\r\n            _updated_at DEFAULT 0\r\n          );\r\n          CREATE TABLE IF NOT EXISTS ok_tags (\r\n            tag TEXT NOT NULL,\r\n            qid TEXT DEFAULT NULL,\r\n            did TEXT DEFAULT NULL,\r\n            creatorid INTEGER DEFAULT 0,\r\n            _created_at DEFAULT 0,\r\n            _updated_at DEFAULT 0\r\n          );");
                }
            }
        }
    }
    public function _login_with_token($token)
    {
        require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
        $key = $this->config->item("app_access_key");
        try {
            $decoded = Firebase\JWT\JWT::decode(urldecode($token), $key, array("HS256"));
            $creatorid = $decoded->creatorid;
            $userid = $decoded->userid;
            $date = $decoded->date;
            $action = $decoded->action;
            if ($action != "account_login") {
                echo "Invalid access, that's all we know.";
                return NULL;
            }
            $ttl = $this->config->item("account_access_url_ttl");
            if (empty($ttl) || !is_numeric($ttl) || $ttl < time() - $date) {
                echo "URL expired.";
                return NULL;
            }
            $query = $this->db->query("select userid, creatorid, name, email, permission, plan, status from dc_user where userid = ?", array($userid));
            if ($query->num_rows() == 0) {
                echo "Invalid Login.";
                return NULL;
            }
            $row = $query->row();
            if ($row->status == USER_STATUS_DELETED) {
                echo "This account has been deleted";
                return NULL;
            }
            $username = $row->name;
            $this->session->set_userdata("login_userid", $row->userid);
            $this->session->set_userdata("login_username", $row->name);
            $this->session->set_userdata("login_email", $row->email);
            $login_permission = $row->permission;
            $this->session->set_userdata("login_permission", $login_permission);
            $this->session->set_userdata("login_plan", $row->plan);
            $quoteinfo = $this->config->item("plan_quote");
            if (!isset($quoteinfo[$row->plan])) {
                $this->db->update("dc_user", array("plan" => "level1"), array("userid" => $row->userid));
            }
            $this->session->set_userdata("login_timezone", "");
            if ($row->status == 9) {
                $this->session->set_userdata("email_not_activation", true);
            } else {
                $this->session->unset_userdata("email_not_activation");
            }
            $creatorid = $row->creatorid;
            if (!$creatorid) {
                $creatorid = $row->userid;
            }
            $this->session->set_userdata("login_creatorid", $creatorid);
            $userlanguageInDb = $this->_get_userlanguageInDb($creatorid);
            if ($userlanguageInDb) {
                $this->session->set_userdata("userlanguage", $userlanguageInDb);
                $this->config->set_item("language", $userlanguageInDb);
            } else {
                if (!empty($userlanguage) && in_array($userlanguage, array("zh-CN", "english"))) {
                    $this->session->set_userdata("userlanguage", $userlanguage);
                    $this->config->set_item("language", $userlanguage);
                }
            }
            $onlydefaultapps = $this->_get_only_showapps_in_default($creatorid);
            if ($onlydefaultapps) {
                $this->session->set_userdata("onlydefaultapps", $onlydefaultapps);
            } else {
                $this->session->set_userdata("onlydefaultapps", false);
            }
            if (!$this->_check_ipwhitelist($creatorid)) {
                $this->smartyview->assign("username", $username);
                $ip_address = $this->input->ip_address();
                $this->smartyview->assign("message", array("title" => "Error", "content" => "You are not allowed to access this resource! If you think this is wrong, please send your IP Address: " . $ip_address . " to your administrator."));
            } else {
                $is_expired = $this->_check_and_assigned_expired($creatorid);
                if ($is_expired) {
                    $this->smartyview->assign("account_expired", true);
                    $this->session->set_userdata("_EXPIRED_", true);
                }
                $this->_execute_trigger($creatorid, "login", array("creatorid" => $creatorid, "userid" => $userid));
                $this->_log_uservisit($row->userid, "Login", "Login OK");
                $this->_log_session($creatorid);
                $this->load->helper("url");
                redirect("?module=CoreHome#module=Dashboard");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return NULL;
        }
    }
    public function _check_sign_premium($username, $password)
    {
        $self_host = $this->config->item("self_host");
        if ($self_host) {
            return false;
        }
        $query = $this->db->query("select userid, name, email from dc_user_premium where (name=? and password=?) or (email=? and password=?)", array($username, $password, $username, $password));
        if (0 < $query->num_rows()) {
            $login_info = $query->row_array();
            $this->session->set_userdata("login_userid", $login_info["userid"]);
            $this->session->set_userdata("login_creatorid", $login_info["userid"]);
            $this->session->set_userdata("login_username", $login_info["name"]);
            $this->session->set_userdata("login_email", $login_info["email"]);
            $this->load->helper("url");
            redirect("?module=CoreHome&action=managedIndex");
            return true;
        }
        return false;
    }
}

?>