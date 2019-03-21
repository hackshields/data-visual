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
class Warehouse extends BaseController
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
            $connections = $this->_get_connections($creatorid);
            $this->smartyview->assign("conns", $connections);
            $default_connid = $this->session->userdata("_default_connid_");
            if (!empty($default_connid)) {
                $this->smartyview->assign("default_connid", $default_connid);
            }
            $file_encodings = array("UTF-8", "UTF-16LE", "UTF-16BE");
            if (function_exists("mb_list_encodings")) {
                $file_encodings = mb_list_encodings();
            }
            $this->smartyview->assign("file_encodings", $file_encodings);
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "name" => "_dbface_warehouse"))->get("dc_conn");
            if (0 < $query->num_rows()) {
                $connid = $query->row()->connid;
                $this->_assign_warehouse_tables($creatorid, $connid);
                $this->_assign_warehouse_settings($creatorid);
                $this->smartyview->display("warehouse/db_structure.tpl");
            } else {
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
                        $this->_create_warehouse_conn($creatorid, $host_config);
                    }
                    $this->_assign_warehouse_settings($creatorid);
                    $this->smartyview->display("warehouse/db_structure.tpl");
                } else {
                    $db = @$this->load->database($host_config, true);
                    if ($db && $db->conn_id) {
                        $this->_create_warehouse_conn($creatorid, $host_config);
                    } else {
                        $error = $db->error();
                        $this->smartyview->assign("hasError", true);
                        $this->smartyview->assign("error_title", "Warehouse database connection failed.");
                        $this->smartyview->assign("error_message", $error["message"]);
                    }
                    $this->_assign_warehouse_settings($creatorid);
                    $this->smartyview->display("warehouse/db_structure.tpl");
                }
            }
        }
    }
    public function _assign_warehouse_settings($creatorid)
    {
        $query = $this->db->where(array("creatorid" => $creatorid, "name" => "_dbface_warehouse"))->get("dc_conn");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $warehouse_settings = $query->row_array();
        $hostname = $warehouse_settings["hostname"];
        $username = $warehouse_settings["username"];
        $password = $warehouse_settings["password"];
        $dbdriver = $warehouse_settings["dbdriver"];
        $database = $warehouse_settings["database"];
        $this->smartyview->assign("dbdriver", $dbdriver);
        $this->smartyview->assign("connid", $warehouse_settings["connid"]);
        if ($dbdriver != "sqlite3") {
            $this->smartyview->assign("hostname", $hostname);
            $this->smartyview->assign("username", $username);
            $this->smartyview->assign("password", $this->_decrypt_conn_password($password));
            $this->smartyview->assign("database", $database);
        }
    }
    /**
     * 同步view的数据到本地数据库
     */
    public function sync()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $view = $this->input->post("view");
        $query = $this->db->where(array("connid" => $connid, "name" => $view, "creatorid" => $creatorid))->get("dc_conn_views");
        if ($query->num_rows() == 0) {
            echo json_encode(array("status" => 0, "message" => "View not available"));
        } else {
            $view_type = $query->row()->type;
            if ($view_type == "table") {
            } else {
                if ($view_type == "materialview") {
                    $view_settings = json_decode($query->row()->value, true);
                    $result = $this->_materialize_sql_view($creatorid, $connid, $view, $view_settings);
                    if ($result) {
                        echo json_encode(array("status" => 1, "message" => "SQL view build successful."));
                        return NULL;
                    }
                } else {
                    if ($view_type == "json") {
                    } else {
                        if ($view_type == "dataset") {
                            $datasetid = $query->row()->value;
                            $result = $this->_sync_dataset_into_warehouse($creatorid, $datasetid, $connid);
                            if ($result && is_array($result)) {
                                echo json_encode($result);
                                return NULL;
                            }
                        } else {
                            if ($view_type == "remotefile") {
                                echo json_encode(array("status" => 1, "message" => "Remote file build successful."));
                                return NULL;
                            }
                        }
                    }
                }
            }
            echo json_encode(array("status" => 0, "message" => "This feature not available on this installation."));
        }
    }
    public function _sync_dataset_into_warehouse($creatorid, $datasetId, $connid)
    {
        $query = $this->db->select("name, data, _updated_at")->where(array("id" => $datasetId, "creatorid" => $creatorid))->get("dc_dataset");
        if ($query->num_rows() == 0) {
            return array("status" => 0, "message" => "Data set was not exists anymore");
        }
        $table = $query->row()->name;
        $_upadted_at = $query->row()->_updated_at;
        $db = $this->_get_db($creatorid, $connid);
        $data = $this->_review_dataset_datas(json_decode($query->row()->data, true), $db);
        $fields = $data["fields"];
        $datas = $data["data"];
        dbface_log("info", $fields);
        dbface_log("debug", "create table " . $table);
        $this->load->dbforge($db);
        $this->dbforge->drop_table($table, true);
        dbface_log("info", "execute Query: " . $db->last_query());
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table($table);
        dbface_log("info", "execute Query: " . $db->last_query());
        foreach ($datas as $row) {
            $db->insert($table, $row);
        }
        $db->close();
        $this->db->update("dc_conn_views", array("lastsyncdate" => $_upadted_at), array("creatorid" => $creatorid, "connid" => $connid, "type" => "dataset", "value" => $datasetId));
        return array("status" => 1, "message" => "Sync successfully.");
    }
    /**
     *
     * renew datasets, remove empty lines, remove empty fields, detect field column
     *
     * @param $org_datas
     * @param $db
     * @return array
     */
    public function _review_dataset_datas(&$org_datas, $db)
    {
        $org_datas["data"] = array_filter($org_datas["data"], function ($val) {
            foreach ($val as $item) {
                if (!empty($item)) {
                    return true;
                }
            }
            return false;
        });
        $org_headers = $org_datas["headers"];
        $org_datas = $org_datas["data"];
        $fields = array();
        $datas = array();
        $headerIdx = 0;
        $valid_idx = array();
        foreach ($org_headers as $org_header) {
            $valid_header = false;
            $header_type = "string";
            foreach ($org_datas as $org_data) {
                if (!empty($org_data[$headerIdx])) {
                    $valid_header = true;
                    break;
                }
            }
            if ($valid_header) {
                $fields[$org_header] = array("name" => $org_header, "type" => $header_type);
                $valid_idx[] = $headerIdx;
            }
            $headerIdx++;
        }
        foreach ($org_datas as $org_data) {
            $row = array();
            $idx = 0;
            foreach ($fields as $field) {
                $row[$field["name"]] = $org_data[$valid_idx[$idx++]];
            }
            $datas[] = $row;
        }
        return array("fields" => $fields, "data" => $datas);
    }
    /**
     * 创建SQL View的内部缓存。
     * 1. 检查View数据是否存在，如果存在，表drop掉
     * 2. 根据检查的内容，将数据copy到表中
     *
     * @param $creatorid
     * @param $connid
     * @param $view
     * @param $view_settings
     */
    public function _materialize_sql_view($creatorid, $connid, $view, $view_settings)
    {
        $db = $this->_get_db($creatorid, $connid);
        $todriver = $db->dbdriver;
        $dbforge = $this->load->dbforge($db, true);
        $dbforge->drop_table($view, true);
        dbface_log("debug", $db->last_query());
        $from_connid = $view_settings["connid"];
        $from_db = $this->_get_db($creatorid, $from_connid);
        $from_driver = $from_db->dbdriver;
        $sql = $view_settings["sql"];
        $query = $from_db->query($sql);
        $field_data = $query->field_data();
        $result_set = $query->result_array();
        foreach ($field_data as $field) {
            $field_name = $field->name;
            $field_datatype = get_mapped_datatype($from_driver, $field->type, $todriver);
            if ($field->primary_key == "1") {
                $dbforge->add_key($field_name, true);
            }
            dbface_log("debug", "Add Field: " . $field_name . " " . $field_datatype);
            $dbforge->add_field($field_name . " " . $field_datatype);
        }
        $dbforge->create_table($view);
        dbface_log("debug", $db->last_query());
        foreach ($result_set as $row) {
            $db->insert($view, $row);
        }
        return true;
    }
    public function _create_warehouse_conn($creatorid, $host_config)
    {
        $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => "_dbface_warehouse", "hostname" => $host_config["hostname"], "username" => $host_config["username"], "password" => $this->_encrypt_conn_password($host_config["password"]), "database" => $host_config["database"], "dbdriver" => $host_config["dbdriver"], "dbprefix" => $host_config["dbprefix"], "pconnect" => $host_config["pconnect"], "char_set" => $host_config["char_set"], "dbcollat" => $host_config["dbcollat"], "swap_pre" => $host_config["swap_pre"], "stricton" => $host_config["stricton"], "port" => isset($host_config["port"]) ? $host_config["port"] : 0, "createdate" => time()));
    }
    public function _assign_warehouse_tables($creatorid, $connid)
    {
        $query = $this->db->where(array("connid" => $connid, "creatorid" => $creatorid))->get("dc_conn_views");
        $tables = array();
        foreach ($query->result_array() as $table) {
            $tables[] = $table;
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("tables", $tables);
    }
    public function mktablenames()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $tablelist = list_tables($db);
        $this->load->library("smartyview");
        $this->smartyview->assign("tables", $tablelist);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->display("warehouse/table.helper.tpl");
    }
    /**
     * delete the warehouse and rebuild
     */
    public function rebuild()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("connid")->where(array("name" => "_dbface_warehouse", "creatorid" => $creatorid))->get("dc_conn");
        $warehouse_connid = $query->row()->connid;
    }
    public function dropview()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $view = $this->input->post("view");
        $connid = $this->input->post("connid");
        $db = $this->_get_db($creatorid, $connid);
        $dbforge = $this->load->dbforge($db, true);
        $dbforge->drop_table($view, true);
        $this->db->delete("dc_conn_views", array("creatorid" => $creatorid, "connid" => $connid, "name" => $view));
        echo json_encode(array("status" => 1));
    }
    public function build_tableview()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $table = $this->input->post("table");
        $table_connid = $this->input->post("table_conn");
        $view = $this->input->post("view");
        if (empty($table) || empty($table_connid) || empty($connid)) {
            echo json_encode(array("status" => 0, "message" => "Access Denied"));
        } else {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "connid" => $connid, "name" => $view))->get("dc_conn_views");
            if ($query->num_rows() == 1) {
                echo json_encode(array("status" => 0, "message" => "This view name is already in use, please choose another one."));
            } else {
                $this->db->insert("dc_conn_views", array("creatorid" => $creatorid, "connid" => $connid, "name" => $view, "type" => "table", "value" => json_encode(array("connid" => $table_connid, "table" => $table)), "date" => time(), "lastsyncdate" => 0));
                echo json_encode(array("status" => 1));
            }
        }
    }
    public function build_materialview()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $viewname = $this->input->post("view");
        $sql = $this->input->post("sql");
        $connid = $this->input->post("connid");
        if (empty($viewname) || empty($sql) || empty($connid)) {
            echo json_encode(array("status" => 0, "message" => "Access Denied"));
        } else {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "connid" => $connid))->get("dc_conn");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                $query = $this->db->select("connid")->where(array("name" => "_dbface_warehouse", "creatorid" => $creatorid))->get("dc_conn");
                $warehouse_connid = $query->row()->connid;
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "connid" => $warehouse_connid, "name" => $viewname))->get("dc_conn_views");
                if ($query->num_rows() == 1) {
                    echo json_encode(array("status" => 0, "message" => "This view name is already in use, please choose another one."));
                } else {
                    $this->db->insert("dc_conn_views", array("creatorid" => $creatorid, "connid" => $warehouse_connid, "name" => $viewname, "type" => "materialview", "value" => json_encode(array("connid" => $connid, "sql" => $sql)), "date" => time(), "lastsyncdate" => 0));
                    echo json_encode(array("status" => 1));
                }
            }
        }
    }
    public function csvupload()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "!message!Permission Denied";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if (!file_exists(USERPATH . "cache" . DIRECTORY_SEPARATOR . "uploads")) {
                mkdir(USERPATH . "cache" . DIRECTORY_SEPARATOR . "uploads");
            }
            $useruploaddir = USERPATH . "cache" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "userfile" . $creatorid;
            if (!file_exists($useruploaddir)) {
                mkdir($useruploaddir);
            }
            $config = array();
            $config["upload_path"] = $useruploaddir;
            $config["allowed_types"] = array("csv", "tab", "txt");
            $config["remove_spaces"] = true;
            $config["file_name"] = "tmp_upload.csv";
            $config["overwrite"] = true;
            $config["detect_mime"] = true;
            $this->load->library("upload", $config);
            if (!$this->upload->do_upload("userfile")) {
                $error = array("error" => $this->upload->display_errors());
                echo "!message!" . $this->upload->display_errors();
            } else {
                $data = $this->upload->data();
                $fullpath = $data["full_path"];
                $this->load->helper("file");
                $csv_source = file_get_contents($fullpath);
                if (function_exists("mb_convert_encoding")) {
                    $from_encoding = $this->input->get("file_encoding");
                    if (!empty($from_encoding)) {
                        $csv_source = mb_convert_encoding($csv_source, "UTF-8", $from_encoding);
                    }
                }
                $this->load->library("CsvReader");
                $delimiter = convert_delimiter($this->input->get("delimiter"));
                $linedelimiter = convert_linedelimiter($this->input->get("linedelimiter"));
                $enclosure = convert_enclosure($this->input->get("enclosure"));
                $result = $this->csvreader->setDelimiter($delimiter)->setNewline($linedelimiter)->setEnclosure($enclosure)->setSource($csv_source)->parse();
                $this->load->library("smartyview");
                $fields = array_shift($result);
                $this->session->set_userdata("csv_filepath", $fullpath);
                $this->smartyview->assign("csv_filepath", $fullpath);
                $this->smartyview->assign("fields", $fields);
                $this->smartyview->assign("datas", $result);
                $this->smartyview->display("csvtable.tpl");
            }
        }
    }
    public function confirm_csv_upload()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0));
        } else {
            $csv_filepath = $this->session->userdata("csv_filepath");
            $creatorid = $this->session->userdata("login_creatorid");
            $connid = $this->_get_warehouse_connid();
            $table = $this->input->post("csv_table_input");
            if (empty($csv_filepath) || empty($connid) || empty($table)) {
                echo json_encode(array("status" => 0));
            } else {
                $db = $this->_get_db($creatorid, $connid);
                if (!$db) {
                    echo json_encode(array("status" => 0));
                } else {
                    $this->load->dbforge($db);
                    if ($db->table_exists($table)) {
                        $this->dbforge->drop_table($table);
                    }
                    $fields = array();
                    $field_names = array();
                    $columns = $this->input->post("columns");
                    $datatypes = $this->input->post("datatypes");
                    $size = count($columns);
                    $cs = array();
                    $fields["_rowno_"] = array("type" => "INTEGER PRIMARY KEY");
                    $field_names[] = "_rowno_";
                    for ($i = 0; $i < $size; $i++) {
                        $column = $columns[$i];
                        $column = preg_replace("/\\s+/", "", $column);
                        if ($datatypes && isset($datatypes[$i])) {
                            $datatype = $datatypes[$i];
                        } else {
                            $datatype = "text";
                        }
                        $fields[$column] = array("type" => $datatype);
                        $field_names[] = $column;
                        $cs[] = $column;
                    }
                    $this->dbforge->add_field($fields);
                    $result = $this->dbforge->create_table($table);
                    if (!$result) {
                        echo json_encode(array("status" => false, "message" => "Can not create table " . $table));
                    } else {
                        $this->load->helper("file");
                        $csv_source = file_get_contents($csv_filepath);
                        if (function_exists("mb_convert_encoding")) {
                            $from_encoding = $this->input->post("file_encoding");
                            if (!empty($from_encoding)) {
                                $csv_source = mb_convert_encoding($csv_source, "UTF-8", $from_encoding);
                            }
                        }
                        $this->load->library("CsvReader");
                        $delimiter = convert_delimiter($this->input->post("delimiter"));
                        $linedelimiter = convert_linedelimiter($this->input->post("linedelimiter"));
                        $enclosure = convert_enclosure($this->input->post("enclosure"));
                        $result = $this->csvreader->setDelimiter($delimiter)->setNewline($linedelimiter)->setEnclosure($enclosure)->unsetMaxRow()->setSource($csv_source)->parse();
                        array_shift($result);
                        log_message("debug", "intend upload " . count($result) . " lines");
                        $insert_array = array();
                        $rowno = 1;
                        foreach ($result as $row) {
                            $arr = array();
                            $i = 0;
                            foreach ($field_names as $field_name) {
                                if ($field_name == "_rowno_") {
                                    $arr[$field_name] = $rowno++;
                                } else {
                                    $arr[$field_name] = isset($row[$i]) ? $row[$i] : "";
                                    $i++;
                                }
                            }
                            $insert_array[] = $arr;
                        }
                        $result = insert_batch($db, $table, $insert_array);
                        $flag = 1;
                        $message = "";
                        if (!$result) {
                            $flag = 0;
                            $error = $db->error();
                            if ($error) {
                                $message .= $error["code"] . ": " . $error["message"];
                            }
                        }
                        echo json_encode(array("status" => $flag, "message" => $message));
                    }
                }
            }
        }
    }
    /**
     * connect remote file into table, currently only support csv file
     */
    public function connect_remote_file()
    {
        $table = $this->input->post("table");
        $remote_file_url = $this->input->post("remote_file_url");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission denied to access this area. Your session might be timed out"));
        } else {
            $warehouse_dbid = $this->_get_warehouse_connid($creatorid);
            $warehouse_db = $this->_get_db($creatorid, $warehouse_dbid);
            if (!$warehouse_db) {
                echo json_encode(array("status" => 0, "message" => "Warehouse disconnected!"));
            } else {
                $this->load->dbforge($warehouse_db);
                if ($warehouse_db->table_exists($table)) {
                    echo json_encode(array("status" => 0, "message" => "The table name is alreay used."));
                } else {
                    require_once APPPATH . "third_party/guzzle/autoloader.php";
                    try {
                        $client = new GuzzleHttp\Client();
                        $saved_file_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "rf_" . time() . ".csv";
                        $response = $client->request("GET", $remote_file_url, array("sink" => $saved_file_path));
                        $code = $response->getStatusCode();
                        if ($code != "200") {
                            echo json_encode(array("status" => 0, "message" => "Remote file download failed, responseCode: " . $code . ", message: " . $response->getReasonPhrase()));
                            return NULL;
                        }
                        $result = $this->_import_csvfile_to_db($saved_file_path, $table, $warehouse_db);
                        @unlink($saved_file_path);
                        $this->db->insert("dc_conn_views", array("creatorid" => $creatorid, "connid" => $warehouse_dbid, "name" => $table, "type" => "remotefile", "value" => $remote_file_url, "date" => time(), "lastsyncdate" => time()));
                        if ($result) {
                            echo json_encode(array("status" => 1));
                        } else {
                            echo json_encode(array("status" => 0, "message" => "Wrong CSV file, can not import remote file into table."));
                        }
                    } catch (Exception $e) {
                        echo json_encode(array("status" => 0, "message" => "Remote file url failed." . $e->getMessage()));
                        return NULL;
                    }
                }
            }
        }
    }
    public function _import_csvfile_to_db($csv_filepath, $table, $db)
    {
        $this->load->dbforge($db);
        if ($db->table_exists($table)) {
            $this->dbforge->drop_table($table);
        }
        $fields = array();
        $field_names = array();
        $columns = $this->input->post("columns");
        $datatypes = $this->input->post("datatypes");
        $size = count($columns);
        $cs = array();
        $fields["_rowno_"] = array("type" => "INTEGER PRIMARY KEY");
        $field_names[] = "_rowno_";
        for ($i = 0; $i < $size; $i++) {
            $column = $columns[$i];
            $column = preg_replace("/\\s+/", "", $column);
            if ($datatypes && isset($datatypes[$i])) {
                $datatype = $datatypes[$i];
            } else {
                $datatype = "text";
            }
            $fields[$column] = array("type" => $datatype);
            $field_names[] = $column;
            $cs[] = $column;
        }
        $this->dbforge->add_field($fields);
        $result = $this->dbforge->create_table($table);
        if (!$result) {
            echo json_encode(array("status" => false, "message" => "Can not create table " . $table));
            return false;
        }
        $this->load->helper("file");
        $csv_source = file_get_contents($csv_filepath);
        if (function_exists("mb_convert_encoding")) {
            $from_encoding = $this->input->get_post("file_encoding");
            if (!empty($from_encoding)) {
                $csv_source = mb_convert_encoding($csv_source, "UTF-8", $from_encoding);
            }
        }
        $this->load->library("CsvReader");
        $delimiter = convert_delimiter($this->input->get_post("delimiter"));
        $linedelimiter = convert_linedelimiter($this->input->get_post("linedelimiter"));
        $enclosure = convert_enclosure($this->input->get_post("enclosure"));
        $result = $this->csvreader->setDelimiter($delimiter)->setNewline($linedelimiter)->setEnclosure($enclosure)->unsetMaxRow()->setSource($csv_source)->parse();
        array_shift($result);
        dbface_log("debug", "intend upload " . count($result) . " lines");
        $insert_array = array();
        $rowno = 1;
        foreach ($result as $row) {
            $arr = array();
            $i = 0;
            foreach ($field_names as $field_name) {
                if ($field_name == "_rowno_") {
                    $arr[$field_name] = $rowno++;
                } else {
                    $arr[$field_name] = isset($row[$i]) ? $row[$i] : "";
                    $i++;
                }
            }
            $insert_array[] = $arr;
        }
        insert_batch($db, $table, $insert_array);
        return true;
    }
}

?>