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
require_once APPPATH . "third_party/docker-php/vendor/autoload.php";
class Signup extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $is_master = $this->config->item("dbface_master_host");
        if (empty($is_master)) {
            exit("Not master installation");
        }
    }
    public function index()
    {
        $this->_easure_schema();
        $email = $this->input->post("email");
        $username = $this->input->post("username");
        $org_slug = $this->input->post("org_slug");
        $alternate_domain = $this->input->post("alternate_domain");
        $tag = $this->input->post("tag");
        $docker_remote_base_url = $this->config->item("docker_remote_base_url");
        $this->load->library("smartyview");
        $this->smartyview->assign("docker_remote_base_url", $docker_remote_base_url);
        if (!empty($tag) && $tag == "confirm") {
            $this->_confirm();
        } else {
            $this->_display_page($email, $username, $org_slug, $alternate_domain);
        }
    }
    public function _easure_schema()
    {
        if (!$this->db->table_exists("dc_user_premium")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          CREATE TABLE IF NOT EXISTS `dc_user_premium` (\r\n            `userid`        int unsigned NOT NULL AUTO_INCREMENT,\r\n            `email`         varchar(255) NOT NULL,\r\n            `name`          varchar(64) NOT NULL,\r\n            `password`      varchar(32) default NULL,\r\n            `slug`          varchar(128) NOT NULL,\r\n            `customdomain`  varchar(256) DEFAULT NULL,\r\n            `full_url`      varchar(256) NOT NULL,\r\n            `status`        tinyint default 0,\r\n            `regip`         varchar(15) default NULL,\r\n            `regdate`       int unsigned NOT NULL,\r\n            `expiredate`    int unsigned NOT NULL,\r\n            PRIMARY KEY  (`userid`),\r\n            UNIQUE KEY `email` (`email`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          CREATE TABLE IF NOT EXISTS dc_user_premium (\r\n            userid        INTEGER PRIMARY KEY,\r\n            email         TEXT NOT NULL,\r\n            name          TEXT NOT NULL,\r\n            password      TEXT default NULL,\r\n            slug          TEXT NOT NULL,\r\n            customdomain  TEXT DEFAULT NULL,\r\n            full_url      TEXT NOT NULL,\r\n            status        INTEGER default 0,\r\n            regip         TEXT default NULL,\r\n            regdate       INTEGER NOT NULL,\r\n            expiredate    INTEGER NOT NULL\r\n          )");
                }
            }
        }
    }
    public function _display_page($email, $name, $org_slug, $alternate_domain, $messages = false)
    {
        $this->smartyview->assign("email", $email);
        $this->smartyview->assign("username", $name);
        $this->smartyview->assign("org_slug", $org_slug);
        $this->smartyview->assign("alternate_domain", $alternate_domain);
        if (!empty($messages)) {
            $this->smartyview->assign("message", array("title" => "Error", "content" => $messages));
        }
        $logo_settings = $this->config->item("login_logo_settings");
        if ($logo_settings) {
            $this->smartyview->assign("login_logo_settings", $logo_settings);
        }
        $this->smartyview->display("login/login.premium.tpl");
    }
    public function _confirm()
    {
        $email = trim($this->input->post("email"));
        $name = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $org_slug = trim($this->input->post("org_slug"));
        $alternate_domain = trim($this->input->post("alternate_domain"));
        if (empty($name) || empty($email) || empty($password) || empty($org_slug)) {
            $messages = "Please fill the required information";
            $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
        } else {
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $messages = "Please enter a valid email address.";
                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
            } else {
                if ($name == $password) {
                    $messages = "Having the same password as your username will make you a high risk for hacking";
                    $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                } else {
                    if (strlen($password) < 6) {
                        $messages = "Password is too short, at least 6 characters";
                        $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                    } else {
                        if (in_array($name, $this->config->item("reserved_username"))) {
                            $messages = "Username is reserved, please choose another one.";
                            $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                        } else {
                            if (!empty($email)) {
                                $this->config->load("disallowed_domain", true, true);
                                $ban_email_domains = $this->config->item("ban_email_domains", "disallowed_domain");
                                foreach ($ban_email_domains as $ban_email) {
                                    if (preg_match("/\\b" . $ban_email . "\\b/i", $email)) {
                                        $messages = "Sorry, that email address is not available.";
                                        $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                        return NULL;
                                    }
                                }
                            }
                            $query = $this->db->query("select 1 from dc_user_premium where email=?", array($email));
                            if (0 < $query->num_rows()) {
                                $messages = "Email has been used, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                return NULL;
                            }
                            $query = $this->db->query("select 1 from dc_user where email=?", array($email));
                            if (0 < $query->num_rows()) {
                                $messages = "Email has been used, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                return NULL;
                            }
                            $query = $this->db->query("select 1 from dc_user_premium where name=?", array($name));
                            if (0 < $query->num_rows()) {
                                $messages = "Username is in use, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                return NULL;
                            }
                            $query = $this->db->query("select 1 from dc_user where name=?", array($name));
                            if (0 < $query->num_rows()) {
                                $messages = "Username is in use, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                return NULL;
                            }
                            $query = $this->db->query("select 1 from dc_user_premium where slug=?", array($org_slug));
                            if (0 < $query->num_rows()) {
                                $messages = "URL Slug is in use, please try another one.";
                                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                return NULL;
                            }
                            $cooltime = time() - 3 * 60;
                            $ip_address = $this->input->ip_address();
                            $query = $this->db->query("select 1 from dc_user_premium where regip = ? and regdate > " . $cooltime, array($ip_address));
                            if (0 < $query->num_rows()) {
                                $messages = "400. That’s an error.";
                                $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                                return NULL;
                            }
                            $docker_remote_base_url = $this->config->item("docker_remote_base_url");
                            $full_url = $docker_remote_base_url . $org_slug;
                            $this->db->insert("dc_user_premium", array("email" => $email, "name" => $name, "password" => md5($password . $this->config->item("password_encrypt")), "slug" => $org_slug, "customdomain" => $alternate_domain, "full_url" => $full_url, "status" => 9, "regip" => $ip_address, "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")));
                            $userid = $this->db->insert_id();
                            if ($this->db->affected_rows() == 1) {
                                $this->load->helper("url");
                                redirect("?module=CoreHome&action=managedIndex");
                                return NULL;
                            }
                            $messages = "401. That’s an error.";
                            $this->_display_page($email, $name, $org_slug, $alternate_domain, $messages);
                        }
                    }
                }
            }
        }
    }
}

?>