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
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class License extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $is_master = $this->config->item("dbface_master_host");
        if (empty($is_master)) {
            exit("Not master installation");
        }
        $this->_easure_schema();
    }
    public function _easure_schema()
    {
        if (!$this->db->table_exists("dbfacephp_license") && ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli")) {
            $this->db->query("          CREATE TABLE IF NOT EXISTS `dbfacephp_license` (\r\n            `clientcode`    varchar(64) default null,\r\n            `licensecode`   varchar(64) default null,\r\n            `email`         varchar(255) default NULL,\r\n            `version`       varchar(64) default NULL,\r\n            `buildid`       varchar(64) default NULL,\r\n            `hostname`      varchar(64) default NULL,\r\n            `ip`            varchar(255) default null,\r\n            `httprefer`     text default null,\r\n            `date`          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP\r\n           ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
        if (!$this->db->table_exists("dbfacephp_licensestatus") && ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli")) {
            $this->db->query("          CREATE TABLE IF NOT EXISTS `dbfacephp_licensestatus` (\r\n            `clientcode`     varchar(64) default null,\r\n            `licensecode`    varchar(64) default NULL,\r\n            `status`         tinyint default 0\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
        }
    }
    public function ipn()
    {
        echo "ipn called";
    }
    public function check()
    {
        $this->load->database();
        $s = $this->input->get_post("s");
        $e = $this->input->get_post("k");
        $licensestatus = 1;
        if (md5("jsding" . $s . "1983") != $e) {
            $licensestatus = 3;
        } else {
            $info = explode("|", $s);
            if (6 <= count($info)) {
                list($clientcode, $licenseemail, $licensecode, $version, $buildid, $hostname) = $info;
                $creator_email = isset($info[6]) ? $info[6] : "";
                if (empty($licenseemail)) {
                    $licenseemail = $creator_email;
                }
                $this->db->insert("dbfacephp_license", array("clientcode" => $clientcode, "licensecode" => $licensecode, "email" => $licenseemail, "version" => $version, "buildid" => $buildid, "hostname" => $hostname, "ip" => $this->input->ip_address(), "httprefer" => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "unknow"));
                $licensestatus = 0;
                if (!empty($licensecode)) {
                    $query = $this->db->query("select status from dbfacephp_licensestatus where licensecode = ? limit 1", array($licensecode));
                    if ($query && 0 < $query->num_rows()) {
                        $licensestatus = $query->row()->status;
                    }
                } else {
                    $query = $this->db->query("select status from dbfacephp_licensestatus where clientcode = ? limit 1", array($clientcode));
                    if ($query && 0 < $query->num_rows()) {
                        $licensestatus = $query->row()->status;
                    }
                }
                echo md5("jsding.20150128" . $licensestatus);
                return NULL;
            }
        }
    }
    public function errorReport()
    {
    }
    public function _init_email_settings()
    {
        $email_settings = $this->config->item("email_settings");
        if ($email_settings && is_array($email_settings) && isset($email_settings["protocol"])) {
            $this->email->initialize($email_settings);
        }
    }
    public function send()
    {
        $e = $this->input->get("e");
        $p = $this->input->get("p");
        if (!empty($e) && $p == "jsding1983") {
            $l = md5("dbfacephp15pro" . $e . "!@");
            $licensecode = "EXC_" . strtoupper($l);
            $this->load->library("email");
            $this->_init_email_settings();
            $this->email->from("support@dbface.com", "DbFace");
            $this->email->to($e);
            $this->email->cc("ding.jiansheng@dbface.com");
            $this->email->subject("Thanks for purchasing DbFace");
            $this->load->library("smartyview");
            $this->smartyview->assign("order_email", $e);
            $this->smartyview->assign("order_licensecode", $licensecode);
            $emailcontent = $this->smartyview->fetch("email/license_email.tpl");
            $this->email->message($emailcontent);
            $this->email->send();
            echo $this->email->print_debugger();
            echo "License Email Sent<p/>" . $emailcontent;
        } else {
            echo "invalid request";
        }
    }
    public function get_latest_version()
    {
        $version = $this->config->item("version");
        $buildid = $this->config->item("buildid");
        echo json_encode(array("version" => $version, "buildid" => $buildid));
    }
    public function a0()
    {
        echo json_encode(array("codes" => array(), "clients" => array()));
    }
}

?>