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
class Structure2 extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->database();
        $this->load->helper("dbface");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get("connid");
        $query = $this->db->select("dbdriver, hostname, database")->where("connid", $dbid)->where("creatorid", $creatorid)->limit(1)->get("dc_conn");
        $dbdriver = $query->row()->dbdriver;
        $dbname = $query->row()->database;
        $plugin_db = $query->row()->hostname == "dbface:plugin";
        $db = $this->_get_db($creatorid, $dbid);
        $tablenames = list_tables($db, true);
        $tables = array();
        foreach ($tablenames as $table) {
            $count = $db->count_all($table);
            $tables[] = array("TABLE_NAME" => $table, "TABLE_ROWS" => $count);
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("tables", $tables);
        $this->smartyview->assign("dbname", $dbname);
        $sql = $this->input->get_post("sql");
        if ($sql) {
            $message = $this->input->get_post("message");
            $flag = $this->input->get_post("flag");
            $this->_show_confirmsql($sql, $message, $flag);
        }
        if (($num_fields = $this->session->userdata("num_fields")) && ($table = $this->session->userdata("table"))) {
            $this->smartyview->assign("num_fields", $num_fields);
            $this->smartyview->assign("table", $table);
        }
        $is_mongodb = $this->_is_mongodb($creatorid, $dbid);
        if ($is_mongodb) {
            $this->smartyview->assign("mongodb", $is_mongodb);
            $this->_assign_mongodb_info($creatorid, $dbid);
        }
        $file_encodings = array("UTF-8", "UTF-16LE", "UTF-16BE");
        if (function_exists("mb_list_encodings")) {
            $file_encodings = mb_list_encodings();
        }
        $this->smartyview->assign("file_encodings", $file_encodings);
        $this->smartyview->assign("connid", $dbid);
        $this->smartyview->assign("dbdriver", $dbdriver);
        $this->smartyview->assign("plugin_db", $plugin_db);
        $this->smartyview->display("sqlite3/db_structure.tpl");
    }
    public function get_mongo_cmd()
    {
        $cmd = $this->input->post("cmd");
        $this->config->load("mongocmds", true);
        $mongo_cmds = $this->config->item("mongo_cmds", "mongocmds");
        if (isset($mongo_cmds[$cmd])) {
            echo json_encode($mongo_cmds[$cmd]);
        } else {
            echo json_encode(array());
        }
    }
    public function run_mongo_cmd()
    {
        $cmd = $this->input->post("cmd");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $mongodb = $this->_get_mongodb($creatorid, $connid);
        if ($cmd == "ping") {
            $result = $mongodb->command(array("ping" => 1));
            echo json_encode(array("cmd_result" => json_encode($result, JSON_PRETTY_PRINT)));
        } else {
            if ($cmd == "whatsmyuri") {
                $result = $mongodb->command(array("whatsmyuri" => 1));
                echo json_encode(array("cmd_result" => json_encode($result, JSON_PRETTY_PRINT)));
            }
        }
    }
    public function _assign_mongodb_info($creatorid, $connid)
    {
        $mongo_db = $this->_get_mongodb($creatorid, $connid);
        $collections = array();
        $cls = $mongo_db->listCollections();
        foreach ($cls as $cl) {
            $collection = array();
            $collection["name"] = $cl;
            $collection["stats"] = $mongo_db->collStats($cl);
            $collections[] = $collection;
        }
        $this->smartyview->assign("collections", $collections);
        $userInfos = $mongo_db->usersInfo();
        $this->smartyview->assign("userInfos", $userInfos);
        $dbStats = $mongo_db->dbStats();
        $this->smartyview->assign("dbStats", $dbStats);
        $this->smartyview->assign("dbStats_json", json_encode($dbStats, JSON_PRETTY_PRINT));
    }
    public function createtable()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $table = $this->input->get_post("table");
        $dbname = $this->input->get_post("db");
        $num_fields = $this->input->get_post("num_fields");
        $added_fields = $this->input->get_post("added_fields");
        $do_save_data = $this->input->post("do_save_data");
        $submit_num_fields = $this->input->post("submit_num_fields");
        $this->session->set_userdata("table", $table);
        $this->session->set_userdata("num_fields", $num_fields);
        if ($submit_num_fields !== false && is_numeric($added_fields)) {
            $num_fields = intval($added_fields) + $num_fields;
        }
        $this->load->helper("dbface");
        $this->load->library("session");
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->get_post("connid");
        $db = $this->_get_db($creatorid, $dbid);
        $field_names = $this->input->post("field_name");
        if (!is_array($field_names)) {
            $field_names = array();
        }
        $field_types = $this->input->post("field_type");
        if (!is_array($field_types)) {
            $field_types = array();
        }
        $field_default_types = $this->input->post("field_default_type");
        if (!is_array($field_default_types)) {
            $field_default_types = array();
        }
        $field_default_values = $this->input->post("field_default_value");
        if (!is_array($field_default_values)) {
            $field_default_values = array();
        }
        $field_nulls = $this->input->post("field_null");
        if (!is_array($field_nulls)) {
            $field_nulls = array();
        }
        $field_keys = $this->input->post("field_key");
        if (!is_array($field_keys)) {
            $field_keys = array();
        }
        $field_extras = $this->input->post("field_extra");
        if (!is_array($field_extras)) {
            $field_extras = array();
        }
        $data_types = get_data_types($db);
        $this->smartyview->assign("data_types", $data_types);
        $columns = array();
        for ($i = 0; $i < $num_fields; $i++) {
            if (count($field_names) <= $i) {
                array_push($field_names, "");
            }
            if (count($field_types) <= $i) {
                array_push($field_types, "");
            }
            if (count($field_default_types) <= $i) {
                array_push($field_default_types, "");
            }
            if (count($field_default_values) <= $i) {
                array_push($field_default_values, "");
            }
            if (count($field_nulls) <= $i) {
                array_push($field_nulls, "");
            }
            if (count($field_keys) <= $i) {
                array_push($field_keys, "");
            }
            if (count($field_extras) <= $i) {
                array_push($field_extras, "");
            }
            array_push($columns, array("field_name" => $field_names[$i], "field_type" => $field_types[$i], "field_default_type" => $field_default_types[$i], "field_default_value" => $field_default_values[$i], "field_null" => $field_nulls[$i], "field_key" => $field_keys[$i], "field_extra" => $field_extras[$i]));
        }
        if ($do_save_data) {
            $sql_query = $this->_get_createtable_sql($db, $num_fields);
            $result = $db->query($sql_query);
            if (!$result) {
                $error = $db->error();
                $title = "Create Table Failed!";
                $message = "<b>Message:</b><br/><b>" . $error["code"] . ": </b>" . $error["message"];
                $message .= "<br/><br/><b>SQL Query:</b><br/>" . $sql_query;
                echo json_encode(array("status" => 0, "title" => $title, "message" => $message));
            } else {
                echo json_encode(array("status" => 1, "table" => $table));
            }
        } else {
            $this->smartyview->assign("connid", $dbid);
            $this->smartyview->assign("dbname", $dbname);
            $this->smartyview->assign("table", $table);
            $this->smartyview->assign("num_fields", $num_fields);
            $this->smartyview->assign("columns", $columns);
            $this->smartyview->display("sqlite3/tablecreate.tpl");
        }
    }
    public function _get_createtable_sql($db, $num_fields)
    {
        $name = $this->input->post("table");
        $primary_keys = array();
        for ($i = 0; $i < $num_fields; $i++) {
            if ($this->input->post($i . "_field") != "" && $this->input->post($i . "_primarykey") != "") {
                $primary_keys[] = $this->input->post($i . "_field");
            }
        }
        $query = "CREATE TABLE " . $db->escape_identifiers($name) . " (";
        for ($i = 0; $i < $num_fields; $i++) {
            if (!empty($_POST[$i . "_field"])) {
                $query .= $db->escape_identifiers($_POST[$i . "_field"]) . " ";
                $query .= $_POST[$i . "_type"] . " ";
                if (isset($_POST[$i . "_primarykey"])) {
                    if (count($primary_keys) == 1) {
                        $query .= "PRIMARY KEY ";
                        if (isset($_POST[$i . "_autoincrement"])) {
                            $query .= "AUTOINCREMENT ";
                        }
                    }
                    $query .= "NOT NULL ";
                }
                if (!isset($_POST[$i . "_primarykey"]) && isset($_POST[$i . "_notnull"])) {
                    $query .= "NOT NULL ";
                }
                if ($_POST[$i . "_defaultoption"] != "defined" && $_POST[$i . "_defaultoption"] != "none" && $_POST[$i . "_defaultoption"] != "expr") {
                    $query .= "DEFAULT " . $_POST[$i . "_defaultoption"] . " ";
                } else {
                    if ($_POST[$i . "_defaultoption"] == "expr") {
                        $query .= "DEFAULT (" . $_POST[$i . "_defaultvalue"] . ") ";
                    } else {
                        if (isset($_POST[$i . "_defaultvalue"]) && $_POST[$i . "_defaultoption"] == "defined") {
                            $typeAffinity = get_type_affinity($_POST[$i . "_type"]);
                            if (($typeAffinity == "INTEGER" || $typeAffinity == "REAL" || $typeAffinity == "NUMERIC") && is_numeric($_POST[$i . "_defaultvalue"])) {
                                $query .= "DEFAULT " . $_POST[$i . "_defaultvalue"] . "  ";
                            } else {
                                $query .= "DEFAULT " . $db->escape_identifiers($_POST[$i . "_defaultvalue"]) . " ";
                            }
                        }
                    }
                }
                $query = substr($query, 0, sizeof($query) - 2);
                $query .= ", ";
            }
        }
        if (1 < count($primary_keys)) {
            $compound_key = "";
            foreach ($primary_keys as $primary_key) {
                $compound_key .= ($compound_key == "" ? "" : ", ") . $db->escape_identifiers($primary_key);
            }
            $query .= "PRIMARY KEY (" . $compound_key . "), ";
        }
        $query = substr($query, 0, sizeof($query) - 3);
        $query .= ")";
        return $query;
    }
    public function upload_csv_file()
    {
        $testconn = false;
        $database = NULL;
        $this->load->dbforge($testconn);
        $this->dbforge->drop_table($database, true);
        $columns = $this->input->post("columns");
        $datatypes = $this->input->post("datatypes");
        $size = count($columns);
        $fields = array();
        $cs = array();
        for ($i = 0; $i < $size; $i++) {
            $column = $columns[$i];
            $column = preg_replace("/\\s+/", "", $column);
            if ($datatypes && isset($datatypes[$i])) {
                $datatype = $datatypes[$i];
            } else {
                $datatype = "text";
            }
            $fields[$column] = array("type" => $datatype);
            $cs[] = $column;
        }
        $this->dbforge->add_field($fields);
        $result = $this->dbforge->create_table($database);
        $creatorid = $this->session->userdata("login_creatorid");
        if ($result) {
            $fullpath = USERPATH . "cache" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "userfile" . $creatorid . DIRECTORY_SEPARATOR . "tmp_upload.csv";
            $delimiter = convert_delimiter($this->input->post("delimiter"));
            $linedelimiter = convert_linedelimiter($this->input->post("linedelimiter"));
            $enclosure = convert_enclosure($this->input->post("enclosure"));
            $this->load->helper("file");
            $csv_source = file_get_contents($fullpath);
            if (function_exists("mb_convert_encoding")) {
                $from_encoding = $this->input->post("file_encoding");
                if (!empty($from_encoding)) {
                    $csv_source = mb_convert_encoding($csv_source, "UTF-8", $from_encoding);
                }
            }
            $this->load->library("CsvReader");
            $csv_data = $this->csvreader->unsetMaxRow()->setDelimiter($delimiter)->setNewline($linedelimiter)->setEnclosure($enclosure)->setSource($csv_source)->parse();
            array_shift($csv_data);
            foreach ($csv_data as $row) {
                $d = array();
                $idx = 0;
                foreach ($cs as $c) {
                    $d[$c] = $row[$idx++];
                }
                $testconn->insert($database, $d);
            }
            log_message("error", count($csv_data) . " rows appended.");
        }
    }
}

?>