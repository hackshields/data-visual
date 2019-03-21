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
class Update extends CI_Controller
{
    public function update_55_from_54()
    {
        $this->load->database();
        $this->db->query("ALTER TABLE dc_category ADD icon TEXT");
    }
    public function update_69_from_67()
    {
        $this->load->database();
        if (!$this->db->table_exists("dc_usergroup")) {
            if ($this->db->dbdriver == "mysql" || $this->db->dbdriver == "mysqli") {
                $this->db->query("          ALTER TABLE dc_user ADD `groupid` varchar(32) DEFAULT NULL;\r\n          CREATE TABLE IF NOT EXISTS `dc_usergroup` (\r\n            `groupid` varchar(32) NOT NULL,\r\n            `name` varchar(32) NOT NULL,\r\n            `creatorid` int unsigned NOT NULL,\r\n            `date` int unsigned NOT NULL,\r\n            PRIMARY KEY (`groupid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;\r\n          CREATE TABLE IF NOT EXISTS `dc_usergroup_permission` (\r\n            `groupid` varchar(32) NOT NULL,\r\n            `appid` int unsigned NOT NULL,\r\n            `date` int unsigned NOT NULL,\r\n            PRIMARY KEY (`groupid`, `appid`)\r\n          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            } else {
                if ($this->db->dbdriver == "sqlite3") {
                    $this->db->query("          ALTER TABLE dc_user ADD groupid TEXT DEFAULT \"\";\r\n          CREATE TABLE IF NOT EXISTS dc_usergroup (\r\n            groupid TEXT NOT NULL,\r\n            name TEXT NOT NULL,\r\n            creatorid INTEGER NOT NULL,\r\n            date INTEGER NOT NULL\r\n          );\r\n          CREATE TABLE IF NOT EXISTS dc_usergroup_permission (\r\n            groupid TEXT NOT NULL,\r\n            appid INTEGER NOT NULL,\r\n            date INTEGER NOT NULL\r\n          );");
                }
            }
        }
    }
}

?>