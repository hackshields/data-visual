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
class Sync extends CI_Controller
{
    public function index()
    {
        $this->_sync_mongodb_to_mysql();
    }
    public function _sync_mongodb_to_mysql()
    {
        require APPPATH . "/libraries/Mongo_db.php";
        $db_config = array("hostname" => "10.80.1.12", "port" => 27017, "database" => "parse", "username" => "");
        $mongo_db = new Mongo_db($db_config);
        $views = array("GiftPackage");
        $target_mysql = array("dsn" => "", "hostname" => "127.0.0.1", "username" => "root", "password" => "root", "database" => "parse", "dbdriver" => "mysqli", "dbprefix" => "", "pconnect" => false, "db_debug" => true, "cache_on" => false, "cachedir" => "", "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "swap_pre" => "", "autoinit" => true, "stricton" => false, "failover" => array());
        $db = $this->load->database($target_mysql, true);
        foreach ($views as $view) {
            $this->sync_mongo_view($db, $view);
        }
    }
    public function sync_mongo_view($db, $view, $old_view = false)
    {
        $dbforge = $this->load->dbforge($db, true);
        $dbforge->drop_table($view, true);
        if ($old_view && !empty($old_view)) {
            $dbforge->drop_table($old_view, true);
        }
        $fields = $settings["fields"];
        $real_fields = array();
        foreach ($fields as $field_name => $field_meta) {
            $f = array("type" => $field_meta["datatype"]);
            if ($field_meta["pk"] == "1") {
                $dbforge->add_key($field_name, true);
            }
            $real_fields[$field_name] = $f;
        }
        $dbforge->add_field($real_fields);
        $dbforge->create_table($view);
        dbface_log("debug", $internal_db->last_query());
        dbface_log("debug", "sync mongo view end");
        return true;
    }
    public function import_mongo_to_view($connid, $view, $mongo_db)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "name" => $view))->get("dc_conn_views");
        if ($query->num_rows() == 0) {
            return false;
        }
        $settings = json_decode($query->row()->value, true);
        $script = $settings["script"];
        $collection = $settings["collection"];
        $filter = json_decode($script, true);
        if (!$filter) {
            $filter = array();
        }
        $result = $mongo_db->find($collection, $filter);
        $datas = array();
        foreach ($result as $row) {
            $row = $row->jsonSerialize();
            $row_data = array();
            foreach ($row as $k => $v) {
                if (is_object($v)) {
                    $row_data[$k] = (string) $v;
                } else {
                    if (is_array($v)) {
                        $row_data[$k] = json_encode($v);
                    } else {
                        $row_data[$k] = $v;
                    }
                }
            }
            $datas[] = $row_data;
        }
        $internal_db = $this->_get_db($creatorid, $connid);
        if ($internal_db) {
            insert_batch($internal_db, $view, $datas);
        }
        return true;
    }
}

?>