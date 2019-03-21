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
class Createdatabase extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $this->load->library("smartyview");
        if (!$this->config->item("enable_createdatabase")) {
            $this->smartyview->assign("hasError", true);
            $this->smartyview->assign("error_title", "Warehouse not enabled on your account.");
            $this->smartyview->assign("error_message", "Please <a href=\"https://ticket.dbface.com/open.php\" class=\"alert-link\">contact support</a> to get how to enable caching.");
            $this->smartyview->display("warehouse/db_structure.tpl");
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $host_config = $this->config->item("hostdb");
            if (strcasecmp($host_config, "auto") == 0) {
                $file_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "warehouse";
                if (!file_exists($file_path)) {
                    mkdir($file_path, 511, true);
                }
                $host_config = array("dsn" => "", "hostname" => "", "username" => "", "password" => "", "database" => $file_path . DIRECTORY_SEPARATOR . "data.db", "dbdriver" => "sqlite3", "dbprefix" => "", "pconnect" => false, "db_debug" => true, "cache_on" => false, "cachedir" => "", "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "swap_pre" => "", "autoinit" => true, "stricton" => false, "failover" => array());
                $db = @$this->load->database($host_config, true);
                if ($db && $db->conn_id) {
                    $this->load->dbforge($db);
                    $db->close();
                }
                $this->smartyview->display("warehouse/db_structure.tpl");
            } else {
                $db = @$this->load->database($host_config, true);
                if ($db && $db->conn_id) {
                } else {
                    $error = $db->error();
                    $this->smartyview->assign("hasError", true);
                    $this->smartyview->assign("error_title", "Warehouse database connection failed.");
                    $this->smartyview->assign("error_message", $error["message"]);
                }
                $this->smartyview->display("warehouse/db_structure.tpl");
            }
        }
    }
    public function create()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        if (!$this->config->item("enable_createdatabase")) {
            echo json_encode(array("status" => 0, "message" => "This feature was not supported on this version."));
        } else {
            $dbname = trim($this->input->post("dbname"));
            if (empty($dbname)) {
                echo json_encode(array("status" => 0, "message" => "Database name can not be empty."));
            } else {
                $this->load->database();
                $host_db_config = $this->config->item("hostdb");
                $host_db_config["db_debug"] = false;
                $db = $this->load->database($host_db_config, true);
                if ($db && !$db->conn_id) {
                    $error = $db->error();
                    echo json_encode(array("status" => 0, "message" => $error["message"]));
                } else {
                    $result = false;
                    $collations = $host_db_config["dbcollat"];
                    if (!empty($collations)) {
                        $result = $db->query("CREATE DATABASE " . PMA_backquote($dbname) . " DEFAULT COLLATE " . $collations);
                    } else {
                        $result = $db->query("CREATE DATABASE " . PMA_backquote($dbname));
                    }
                    if (!$result) {
                        $error = $db->error();
                        echo json_encode(array("status" => 0, "message" => $error["message"]));
                    } else {
                        $create_sperated_user_for_hostdb = $this->config->item("create_sperated_user_for_hostdb");
                        $db_username = $db->username;
                        $db_password = $db->password;
                        if ($create_sperated_user_for_hostdb) {
                            $db_username = uniqid("mu_");
                            $db_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);
                            $host = $this->config->item("hostdb_user_host");
                            if (empty($host)) {
                                $host = "%";
                            }
                            $result = $db->query("GRANT ALL PRIVILEGES ON " . $dbname . ".* TO " . $db->escape($db_username) . "@" . $db->escape($host) . " IDENTIFIED BY " . $db->escape($db_password));
                            if (!$result) {
                                $error = $db->error();
                                echo json_encode(array("status" => 0, "message" => $error["message"]));
                                return NULL;
                            }
                        }
                        $creatorid = $this->session->userdata("login_creatorid");
                        $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => $dbname, "hostname" => $db->hostname, "username" => $db_username, "password" => $db_password, "database" => $dbname, "dbdriver" => $db->dbdriver, "char_set" => $db->char_set, "dbcollat" => $collations, "createdate" => time()));
                        $dbid = $this->db->insert_id();
                        echo json_encode(array("status" => 1, "connid" => $dbid));
                    }
                }
            }
        }
    }
}

?>