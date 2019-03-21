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
class Admin extends BaseController
{
    public function _get_user_options($creatorid)
    {
        $query = $this->db->query("select name, value from dc_user_options where creatorid = ?", array($creatorid));
        if (0 < $query->num_rows()) {
            return $query->result_array();
        }
        return false;
    }
    public function backup()
    {
        $this->_backup();
    }
    public function restore()
    {
    }
    public function install()
    {
        $p = $this->config->item("password_encrypt");
        $ps = $this->input->get("p");
        if ($ps != $p) {
            echo json_encode(array("status" => 0, "message" => "Not Allowed."));
        }
        if (file_exists(FCPATH . "user" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . ".install")) {
            echo json_encode(array("status" => 1, "message" => "already installed."));
        }
        $directoriesToCheck = array("/user");
        $this->load->helper("dbface_common");
        foreach ($directoriesToCheck as $directoryToCheck) {
            if (!preg_match("/^" . preg_quote(FCPATH, "/") . "/", $directoryToCheck)) {
                $directoryToCheck = FCPATH . $directoryToCheck;
            }
            if (!file_exists($directoryToCheck)) {
                DbFace_Common::mkdir($directoryToCheck);
            }
            $directory = DbFace_Common::realpath($directoryToCheck);
            $directoryCheck = false;
            if ($directory !== false && is_writable($directoryToCheck)) {
                $directoryCheck = true;
            }
            if (!$directoryCheck) {
                echo json_encode(array("status" => 0, "message" => "Please make " . $directoryToCheck . " writable."));
            }
        }
        $email = $this->input->post("a");
        $name = $this->input->post("b");
        $password = $this->input->post("c");
        $this->load->database();
        $query = $this->db->query("select 1 from dc_user where permission=0 and creatorid=0");
        $ip_address = $this->input->ip_address();
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_user", array("creatorid" => 0, "email" => $email, "name" => $name, "password" => md5($password . $this->config->item("password_encrypt")), "permission" => 0, "status" => 0, "regip" => $ip_address, "plan" => "level1", "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")));
        } else {
            $this->db->update("dc_user", array("email" => $email, "name" => $name, "password" => md5($password . $this->config->item("password_encrypt")), "regip" => $ip_address, "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")), array("creatorid" => 0, "permission" => 0));
        }
        $this->_update_signature();
        $this->load->helper("file");
        write_file(FCPATH . "user" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . ".install", time());
        echo json_encode(array("status" => 1));
    }
    public function _backup()
    {
        $p = $this->config->item("password_encrypt");
        $ps = $this->input->get("p");
        if ($ps != $p) {
            echo "Not Allowed";
        } else {
            $dir_backup = FCPATH . "backup";
            if (!file_exists($dir_backup)) {
                mkdir($dir_backup);
            }
            $dbdriver = $this->db->dbdriver;
            if ($dbdriver == "mysqli") {
                $this->load->dbutil();
                $backup = $this->dbutil->backup(array("format" => "txt"));
                $this->load->helper("file");
                write_file(FCPATH . "user" . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "db_backup.sql", $backup);
            }
            $this->load->library("zip");
            $filename = "backup_" . date("Ymd") . ".zip";
            $zip_filename = $dir_backup . DIRECTORY_SEPARATOR . $filename;
            $this->zip->read_dir(FCPATH . "user", false, FCPATH);
            $this->zip->read_dir(FCPATH . "config", false, FCPATH);
            $this->zip->archive($zip_filename);
            $this->zip->download($filename);
        }
    }
    public function _restore($revision)
    {
    }
    public function da()
    {
        $p = $this->config->item("password_encrypt");
        $ps = $this->input->get("p");
        if ($ps != $p) {
            return NULL;
        }
        $this->load->database();
        $this->db->delete("dc_user_options", array("name" => "license_code"));
        $this->db->delete("dc_user_options", array("name" => "license_email"));
        $this->db->update("dc_user", array("expiredate" => time(), "plan" => "level0"), array("creatorid" => 0));
    }
    public function dumpconfig()
    {
        $p = $this->config->item("password_encrypt");
        $ps = $this->input->get("p");
        if ($ps != $p) {
            return NULL;
        }
        $servers = $_SERVER;
        $headers = getallheaders();
        var_dump($servers);
        var_dump($headers);
    }
    public function phpinfo()
    {
        $is_ajax = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest";
        if (!$is_ajax) {
            $p = $this->input->get("p");
            $real_p = $this->config->item("password_encrypt");
            if ($p == $real_p) {
                echo phpinfo();
            } else {
                show_error("500, Request can not be fullfilled, That's all we know.", 500);
            }
        } else {
            if (!$this->_is_admin_or_developer()) {
                $this->smartyview->assign("phpinfo", "No PHP Information Available");
                $this->smartyview->display("phpinfo.tpl");
            } else {
                $this->load->library("smartyview");
                ob_start();
                phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES | INFO_VARIABLES);
                $phpinfo = ob_get_clean();
                $phpinfo = trim($phpinfo);
                preg_match_all("#<body[^>]*>(.*)</body>#si", $phpinfo, $output);
                if (empty($phpinfo) || empty($output[1][0])) {
                    trigger_error("NO_PHPINFO_AVAILABLE", 512);
                }
                $output = $output[1][0];
                if (preg_match("#<a[^>]*><img[^>]*></a>#", $output)) {
                    $output = preg_replace("#<tr class=\"v\"><td>(.*?<a[^>]*><img[^>]*></a>)(.*?)</td></tr>#s", "<tr class=\"row1\"><td><table class=\"type2\"><tr><td>\\2</td><td>\\1</td></tr></table></td></tr>", $output);
                } else {
                    $output = preg_replace("#<tr class=\"v\"><td>(.*?)</td></tr>#s", "<tr class=\"row1\"><td><table class=\"type2\"><tr><td>\\1</td></tr></table></td></tr>", $output);
                }
                $output = preg_replace("#<table[^>]+>#i", "<table class=\"table table-responsive\">", $output);
                $output = str_replace("<table>", "<table class=\"table table-striped table-bordered\">", $output);
                $output = preg_replace("#<img border=\"0\"#i", "<img", $output);
                $output = str_replace(array("class=\"e\"", "class=\"v\"", "class=\"h\"", "<hr />", "<font", "</font>"), array("class=\"row1\"", "class=\"row2\"", "", "", "<span", "</span>"), $output);
                if (empty($output)) {
                    trigger_error("NO_PHPINFO_AVAILABLE", 512);
                }
                $orig_output = $output;
                preg_match_all("#<div class=\"center\">(.*)</div>#siU", $output, $output);
                $output = !empty($output[1][0]) ? $output[1][0] : $orig_output;
                $this->smartyview->assign("phpinfo", $output);
                $this->smartyview->display("phpinfo.tpl");
            }
        }
    }
    public function view_log()
    {
        $p = $this->config->item("password_encrypt");
        $ps = $this->input->get("p");
        if ($ps != $p) {
            return NULL;
        }
        $date = $this->input->get("date");
        $file = APPPATH . "logs/log-" . $date . ".php";
        if (file_exists($file)) {
            echo file_get_contents($file);
        }
    }
    public function license()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $creatorid = $this->session->userdata("login_userid");
        $thirdpartlogin = $this->session->userdata("login_thirdpart");
        if ($thirdpartlogin) {
            $this->smartyview->assign("login_thirdpart", true);
        }
        $query = $this->db->query("select * from dc_user where userid=?", array($creatorid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $permission = $row["permission"];
            $this->smartyview->assign("expiredate", date("Y-m-d", $row["expiredate"]));
            $this->smartyview->assign("account", $row);
            $cur_ipaddress = $this->input->ip_address();
            $this->smartyview->assign("cur_ipaddress", $cur_ipaddress);
            $hasSetWelcome = false;
            $user_options = $this->_get_user_options($creatorid);
            $license_email = false;
            $license_code = false;
            if ($user_options) {
                foreach ($user_options as $user_option) {
                    $name = $user_option["name"];
                    if ($name == "userwelcome") {
                        $hasSetWelcome = true;
                    }
                    if ($name == "license_email") {
                        $license_email = $user_option["value"];
                    }
                    if ($name == "license_code") {
                        $license_code = $user_option["value"];
                    }
                    $this->smartyview->assign($name, $user_option["value"]);
                }
            }
            if ($license_email && $license_code) {
                $expired = $this->session->userdata("_EXPIRED_");
                if (!$expired && ce1($license_email, $license_code)) {
                    $this->smartyview->assign("valid_license", true);
                }
            }
            if (!$hasSetWelcome) {
                $userwelcome = $this->_compile_tpl("new/userwelcome.tpl");
                $this->smartyview->assign("userwelcome", $userwelcome);
            }
            $userlanguage = $this->session->userdata("userlanguage");
            $this->smartyview->assign("userlanguage", $userlanguage);
            $this->smartyview->display("new/license.tpl");
        }
    }
}

?>