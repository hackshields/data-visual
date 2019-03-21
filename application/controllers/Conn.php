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
class Conn extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $default_connid = $this->session->userdata("_default_connid_");
        $refresh = $this->input->get("refresh");
        if ($refresh == "1") {
            $this->smartyview->assign("refresh", "1");
        }
        $this->smartyview->assign("default_connid", $default_connid);
        $this->smartyview->assign("conns", $this->_get_connections($creatorid, true));
        $this->smartyview->display("new/conn.list.tpl");
    }
    public function sync()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin()) {
            echo json_decode(array("status" => 0, "message" => "Not allowed"));
        } else {
            $connid = $this->input->post("connid");
            $query = $this->db->query("select dbdriver, name, hostname, database from dc_conn where connid = ? and creatorid=?", array($connid, $creatorid));
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "title" => "Failed to Sync", "message" => "Not Found"));
            } else {
                $dbinfo = $query->row_array();
                $dbdriver = $dbinfo["dbdriver"];
                $rest_url = $dbinfo["hostname"];
                $database = $dbinfo["database"];
                if ($dbdriver == "mongodb") {
                    $this->sync_all_mongo_view($connid);
                } else {
                    if ($dbdriver == "dbface:plugin") {
                        $this->sync_all_plugin_view($connid);
                    } else {
                        $new_created_database = $database . ".sync";
                        $json = $this->_get_rest_api_response($rest_url);
                        $cache_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "storage";
                        if (!file_exists($cache_dir)) {
                            mkdir($cache_dir);
                        }
                        $hostname = "dbfacestorage";
                        $username = "csv";
                        $password = "";
                        $dbdriver = "sqlite3";
                        $db_config["hostname"] = $hostname;
                        $db_config["username"] = $username;
                        $db_config["password"] = $password;
                        $db_config["database"] = $new_created_database;
                        $db_config["dbdriver"] = $dbdriver;
                        $db_config["pconnect"] = false;
                        $db_config["db_debug"] = false;
                        $db_config["cache_on"] = false;
                        $db_config["autoinit"] = false;
                        $testconn = $this->load->database($db_config, true);
                        if ($testconn && $testconn->conn_id) {
                            $this->load->dbforge($testconn);
                            log_message("debug", "REST Response: " . $json);
                            $data = json_decode($json, true);
                            $tables = array_keys($data);
                            foreach ($tables as $table) {
                                $schemas = $data[$table]["schema"];
                                log_message("debug", "create table " . $table);
                                $fields = array();
                                $fields["_rowno_"] = array("type" => "INTEGER PRIMARY KEY");
                                foreach ($schemas as $schema) {
                                    $fields[$schema["name"]] = array("type" => $schema["type"]);
                                }
                                $this->dbforge->add_field($fields);
                                $this->dbforge->drop_table($table, true);
                                $this->dbforge->create_table($table);
                                $rows = $data[$table]["data"];
                                foreach ($rows as $row) {
                                    $testconn->insert($table, $row);
                                }
                            }
                            $testconn->close();
                            copy($new_created_database, $database);
                            unlink($new_created_database);
                            echo json_encode(array("status" => 1));
                            return NULL;
                        } else {
                            echo json_encode(array("status" => 0, "title" => "Failed to Sync", "message" => "Unknow error"));
                        }
                    }
                }
            }
        }
    }
    public function _get_rest_api_response($rest_url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $rest_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $output = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        return $output;
    }
    /**
     * preview_rest_api: show the reset api resonse and check the response is ok for local store
     */
    public function preview_rest_api()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $rest_url = $this->input->get_post("rest_url");
        $dbname = $this->input->post("name");
        if (!$this->_is_admin() || empty($rest_url)) {
            echo json_encode(array("status" => false, "message" => "Empty rest api URL."));
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $rest_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $output = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
            if ($output === false) {
                echo json_encode(array("status" => false, "message" => "CURL Error: " . $error));
            } else {
                $json = json_decode($output, true);
                $this->load->helper("file");
                if (!file_exists(USERPATH . "files")) {
                    mkdir(USERPATH . "files");
                }
                if (!file_exists(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid)) {
                    mkdir(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid);
                }
                if (!file_exists(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "restapi")) {
                    mkdir(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "restapi");
                }
                echo json_encode(array("json" => $output), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }
    }
    public function confirm_csv_upload()
    {
        $csv_filepath = $this->session->userdata("csv_filepath");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
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
                        $from_encoding = $this->input->get_post("file_encoding");
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
    public function csvupload()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (!file_exists(USERPATH . "cache/uploads")) {
            mkdir(USERPATH . "cache/uploads");
        }
        $useruploaddir = USERPATH . "cache/uploads/userfile" . $creatorid;
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
                $from_encoding = $this->input->get_post("file_encoding");
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
    public function _get_demo_db()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $serverName = $_SERVER["SERVER_NAME"];
        return array("creatorid" => $creatorid, "name" => "classicmodels", "hostname" => "dbinstance.cggbbf8uwxvu.ap-southeast-1.rds.amazonaws.com", "username" => "demo", "password" => "demo", "database" => "classicmodels", "dbdriver" => "mysqli", "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => "", "createdate" => time());
    }
    public function demo()
    {
        $this->load->database();
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select 1 from dc_conn where creatorid =? and name=?", array($creatorid, "classicmodels@demo"));
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_conn", $this->_get_demo_db());
        }
        $this->log_event($creatorid, "Connect", "User connect to demo database");
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
    }
    public function create()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $tag = $this->input->post("tag");
        $base_url = $this->_get_url_base();
        $sample_rest_api = $base_url . "?module=Sample&action=rest";
        if ($tag == "confirm") {
            $this->_createconn();
        } else {
            $this->smartyview->assign("self_host", $this->config->item("self_host"));
            $this->smartyview->assign("sample_rest_api", $sample_rest_api);
            $this->smartyview->display("new/conn.create.tpl");
        }
    }
    public function _createconn()
    {
        $dbdriver = trim($this->input->post("dbdriver"));
        $name = trim($this->input->post("name"));
        $database = trim($this->input->post("database"));
        $creatorid = $this->session->userdata("login_creatorid");
        $hostname = trim($this->input->post("hostname"));
        $username = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $autocreatedata = $this->input->post("autocreatedata");
        $adv_opt_keys = $this->input->post("adv_options_key[]");
        $adv_opt_vals = $this->input->post("adv_options_val[]");
        $adv_options = array();
        if (is_string($adv_opt_keys)) {
            $adv_opt_keys = array($adv_opt_keys);
            $adv_opt_vals = array($adv_opt_vals);
        }
        $opt_size = count($adv_opt_keys);
        for ($i = 0; $i < $opt_size; $i++) {
            $k = $adv_opt_keys[$i];
            $v = $adv_opt_vals[$i];
            if (!empty($k) && !empty($v)) {
                $adv_options[$k] = $v;
            }
        }
        if ($dbdriver == "mysqli") {
            $enable_ssl = $this->input->post("enable_ssl");
            $ssl_key = $this->input->post("ssl_key");
            $ssl_cert = $this->input->post("ssl_cert");
            $ssl_ca = $this->input->post("ssl_ca");
            $ssl_cipher = $this->input->post("ssl_cipher");
            $adv_options["enable_ssl"] = $enable_ssl;
            $adv_options["ssl_key"] = $ssl_key;
            $adv_options["ssl_cert"] = $ssl_cert;
            $adv_options["ssl_ca"] = $ssl_ca;
            $adv_options["ssl_cipher"] = $ssl_cipher;
        }
        if (($dbdriver == "mysqli" || $dbdriver == "pgsql" || $dbdriver == "sqlsrv") && empty($database)) {
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Database name can not be empty.")));
        } else {
            $db_config = array();
            $rest_url = $this->input->get_post("rest_url");
            $port = 0;
            $is_rest_api = false;
            if ($dbdriver == "restapi") {
                $cache_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "storage";
                if (!file_exists($cache_dir)) {
                    mkdir($cache_dir);
                }
                $hostname = "dbfacestorage";
                $username = "restapi";
                $password = "";
                $sqlite3db = $cache_dir . DIRECTORY_SEPARATOR . $name . ".db";
                $database = $sqlite3db;
                $dbdriver = "sqlite3";
                $is_rest_api = true;
                $db_config["hostname"] = $hostname;
                $db_config["username"] = $username;
                $db_config["password"] = $password;
                $db_config["database"] = $sqlite3db;
                $db_config["dbdriver"] = $dbdriver;
                $db_config["pconnect"] = false;
                $db_config["db_debug"] = false;
                $db_config["cache_on"] = false;
                $db_config["autoinit"] = false;
                $this->_set_db_adv_options_by_array($db_config, $adv_options);
                $testconn = $this->load->database($db_config, true);
                $connected = @$testconn->initialize();
                if ($connected) {
                    $this->load->dbforge($testconn);
                    $json = $this->_get_rest_api_response($rest_url);
                    dbface_log("debug", "REST Response: " . $json);
                    $data = json_decode($json, true);
                    $tables = array_keys($data);
                    foreach ($tables as $table) {
                        $schemas = $data[$table]["schema"];
                        dbface_log("debug", "create table " . $table);
                        $fields = array();
                        $fields["_rowno_"] = array("type" => "INTEGER PRIMARY KEY");
                        foreach ($schemas as $schema) {
                            $fields[$schema["name"]] = array("type" => $schema["type"]);
                        }
                        $this->dbforge->add_field($fields);
                        if ($testconn->table_exists($table)) {
                            $this->dbforge->drop_table($table);
                        }
                        $this->dbforge->create_table($table);
                        $rows = $data[$table]["data"];
                        foreach ($rows as $row) {
                            $testconn->insert($table, $row);
                        }
                    }
                }
            } else {
                if ($dbdriver == "csv") {
                    $cache_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "storage";
                    if (!file_exists($cache_dir)) {
                        mkdir($cache_dir);
                    }
                    $hostname = "dbfacestorage";
                    $username = "csv";
                    $password = "";
                    $sqlite3db = $cache_dir . DIRECTORY_SEPARATOR . $database . ".db";
                    $dbdriver = "sqlite3";
                    $port = 0;
                    $db_config["hostname"] = $hostname;
                    $db_config["username"] = $username;
                    $db_config["password"] = $password;
                    $db_config["database"] = $sqlite3db;
                    $db_config["dbdriver"] = $dbdriver;
                    $db_config["pconnect"] = false;
                    $db_config["db_debug"] = false;
                    $db_config["cache_on"] = false;
                    $db_config["autoinit"] = false;
                    $this->_set_db_adv_options_by_array($db_config, $adv_options);
                    $testconn = $this->load->database($db_config, true);
                    $connected = @$testconn->initialize();
                    if ($connected) {
                    }
                } else {
                    if ($dbdriver == "pgsql" || $dbdriver == "sqlsrv") {
                        $hosts = explode(":", $hostname);
                        $port = 5432;
                        if ($dbdriver == "sqlsrv") {
                            $port = 1433;
                        }
                        if (count($hosts) == 2) {
                            list($hostname, $port) = $hosts;
                        }
                        if ($dbdriver == "sqlsrv") {
                            $db_config["dsn"] = (string) $dbdriver . ":Server=" . $hostname . ";Database=" . $database;
                        } else {
                            $db_config["dsn"] = (string) $dbdriver . ":host=" . $hostname . ";port=" . $port . ";dbname=" . $database;
                        }
                        $db_config["db_debug"] = false;
                        $db_config["dbdriver"] = "pdo";
                        $db_config["username"] = $username;
                        $db_config["password"] = $password;
                        $schema = $this->config->item("pgsql_default_schema");
                        if ($dbdriver == "pgsql" && !empty($schema)) {
                            $db_config["schema"] = $schema;
                        }
                    } else {
                        if ($dbdriver == "firebird") {
                            $db_config["db_debug"] = false;
                            $db_config["dbdriver"] = "pdo";
                            $db_config["subdriver"] = "firebird";
                            $db_config["hostname"] = $hostname;
                            $db_config["username"] = $username;
                            $db_config["password"] = $password;
                            $db_config["database"] = $database;
                            $db_config["char_set"] = "utf8";
                            $db_config["dbcollat"] = "utf8_general_ci";
                            $port = 0;
                        } else {
                            if ($dbdriver == "sqlite") {
                                $db_config["hostname"] = "";
                                $db_config["username"] = "";
                                $db_config["password"] = "";
                                $db_config["database"] = $database;
                                $db_config["dbdriver"] = "sqlite3";
                                $db_config["pconnect"] = false;
                                $db_config["db_debug"] = false;
                                $db_config["cache_on"] = false;
                                $db_config["autoinit"] = false;
                                $db_config["cachedir"] = "";
                                $db_config["char_set"] = "utf8";
                                $db_config["dbcollat"] = "utf8_general_ci";
                                $port = 0;
                            } else {
                                if ($dbdriver == "oci" || $dbdriver == "oci8") {
                                    $db_config["hostname"] = $hostname;
                                    $db_config["username"] = $username;
                                    $db_config["password"] = $password;
                                    $db_config["database"] = "";
                                    $db_config["dbdriver"] = "oci8";
                                    $db_config["db_debug"] = false;
                                    $db_config["cache_on"] = false;
                                    $db_config["autoinit"] = false;
                                    $db_config["cachedir"] = "";
                                    $db_config["char_set"] = "utf8";
                                    $db_config["dbcollat"] = "utf8_general_ci";
                                    $port = 0;
                                } else {
                                    if ($dbdriver == "4d" || $dbdriver == "ibm" || $dbdriver == "informix") {
                                        $db_config["db_debug"] = false;
                                        $db_config["dbdriver"] = "pdo";
                                        $db_config["subdriver"] = $dbdriver;
                                        $db_config["hostname"] = $hostname;
                                        $db_config["username"] = $username;
                                        $db_config["password"] = $password;
                                        $db_config["database"] = $database;
                                        $db_config["char_set"] = "utf8";
                                        $db_config["dbcollat"] = "utf8_general_ci";
                                        $port = 0;
                                    } else {
                                        if ($dbdriver == "access") {
                                            $db_config["db_debug"] = false;
                                            $db_config["dsn"] = $database;
                                            $db_config["dbdriver"] = "odbc";
                                            $db_config["hostname"] = "";
                                            $db_config["username"] = $username;
                                            $db_config["password"] = $password;
                                            $db_config["database"] = "";
                                            $db_config["char_set"] = "";
                                            $db_config["dbcollat"] = "";
                                            $port = 0;
                                        } else {
                                            if ($dbdriver == "odbc") {
                                                $db_config["db_debug"] = false;
                                                $db_config["dsn"] = $database;
                                                $db_config["dbdriver"] = "odbc";
                                                $db_config["hostname"] = "";
                                                $db_config["username"] = $username;
                                                $db_config["password"] = $password;
                                                $db_config["database"] = "";
                                                $db_config["char_set"] = "";
                                                $db_config["dbcollat"] = "";
                                                $port = 0;
                                            } else {
                                                if ($dbdriver == "dsn") {
                                                    $db_config["db_debug"] = false;
                                                    $db_config["dsn"] = $database;
                                                    $db_config["dbdriver"] = "pdo";
                                                    $db_config["hostname"] = "";
                                                    $db_config["username"] = $username;
                                                    $db_config["password"] = $password;
                                                    $db_config["database"] = "";
                                                    $db_config["char_set"] = "";
                                                    $db_config["dbcollat"] = "";
                                                    $port = 0;
                                                } else {
                                                    if ($dbdriver == "mongodb") {
                                                        $db_config["hostname"] = $hostname;
                                                        $db_config["username"] = $username;
                                                        $db_config["password"] = $password;
                                                        $db_config["database"] = $database;
                                                        require APPPATH . "/libraries/Mongo_db.php";
                                                        try {
                                                            $result = new Mongo_db($db_config);
                                                            if ($result->is_connected()) {
                                                                $this->_save_mongo_connection();
                                                            } else {
                                                                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to connect to your database server using the provided settings. <a href=\"//docs.dbface.com/supported-datasources/\" target=\"_blank\">Please refer this page to get how to make it work</a>")));
                                                            }
                                                        } catch (Exception $e) {
                                                            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to connect to your database server using the provided settings." . $e->getMessage())));
                                                        }
                                                        return NULL;
                                                    }
                                                    if ($dbdriver == "bigquery") {
                                                        $db_config["hostname"] = $this->input->post("bigquery_projectid");
                                                        $db_config["username"] = $this->input->post("bigquery_service_account");
                                                        $db_config["database"] = $this->input->post("database");
                                                        require_once APPPATH . "/libraries/BigQuery_db.php";
                                                        $bigquery_db = new BigQuery_db($db_config);
                                                        if ($bigquery_db->is_connected()) {
                                                            $this->_save_bigquery_connection();
                                                        } else {
                                                            $error = $bigquery_db->error();
                                                            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to connect to your database server using the provided settings." . $error["message"])));
                                                        }
                                                        return NULL;
                                                    }
                                                    if ($dbdriver == "dynamodb") {
                                                        $db_config["hostname"] = $this->input->post("aws_region");
                                                        $db_config["username"] = $this->input->post("aws_access_key");
                                                        $db_config["password"] = $this->input->post("aws_secret_key");
                                                        require_once APPPATH . "/libraries/DynamoDb.php";
                                                        $dynamoDb = new DynamoDb($db_config);
                                                        if ($dynamoDb->is_connected()) {
                                                            $this->_save_dynamodb_connection();
                                                        } else {
                                                            $e = $dynamoDb->error();
                                                            if (!empty($e)) {
                                                                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "<strong>Error connect to Amazon DynamoDB</strong>, code: " . $e["code"] . ", error: " . $e["error"])));
                                                            } else {
                                                                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to connect to your database server using the provided settings. <a href=\"//docs.dbface.com/supported-datasources/\" target=\"_blank\">Please refer this page to get how to make it work</a>")));
                                                            }
                                                        }
                                                        return NULL;
                                                    }
                                                    if ($dbdriver == "dbface:plugin") {
                                                        $plugin_url = $this->input->post("plugin_url");
                                                        if (!check_ds_plugin($plugin_url)) {
                                                            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Invalid Plugin URL, the right value should be facebookad://account_id?sec=aaa")));
                                                            return NULL;
                                                        }
                                                        $this->_save_plugin_connection();
                                                        return NULL;
                                                    }
                                                    $plugin = $this->_get_plugin_datasource($dbdriver);
                                                    if ($plugin) {
                                                        $db_config["dbdriver"] = $dbdriver;
                                                        $db_config["database"] = $database;
                                                        require APPPATH . "/libraries/Plugin_db.php";
                                                        try {
                                                            $plugin_db = new Plugin_db($db_config);
                                                            if ($plugin_db->is_valid()) {
                                                                $this->_save_plugin_connection();
                                                            } else {
                                                                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to setup the data source plugin." . $plugin_db->last_message())));
                                                            }
                                                        } catch (Exception $e) {
                                                            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to setup the data source plugin using the provided settings.")));
                                                        }
                                                        return NULL;
                                                    }
                                                    $db_config = array();
                                                    $hosts = explode(":", $hostname);
                                                    if (count($hosts) == 2) {
                                                        $hostname = $hosts[0];
                                                        $port = intval($hosts[1]);
                                                    }
                                                    $db_config["hostname"] = $hostname;
                                                    if ($port != 0) {
                                                        $db_config["port"] = $port;
                                                    } else {
                                                        $db_config["port"] = "";
                                                    }
                                                    $db_config["dsn"] = false;
                                                    $db_config["username"] = $username;
                                                    $db_config["password"] = $password;
                                                    $db_config["database"] = $database;
                                                    $db_config["dbdriver"] = $dbdriver;
                                                    $db_config["pconnect"] = false;
                                                    $db_config["db_debug"] = false;
                                                    $db_config["cache_on"] = false;
                                                    $db_config["autoinit"] = false;
                                                    $db_config["cachedir"] = "";
                                                    $db_config["char_set"] = "utf8";
                                                    $db_config["dbcollat"] = "utf8_general_ci";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $this->_set_db_adv_options_by_array($db_config, $adv_options);
            $this->load->helper("json");
            $db_config["db_debug"] = true;
            $testconn = $this->load->database($db_config, true);
            if ($testconn && $testconn->conn_id) {
                $connid = $this->input->post("connid");
                if (empty($connid)) {
                    $query = $this->db->query("select 1 from dc_conn where creatorid=? and name=?", array($creatorid, $name));
                    if (0 < $query->num_rows()) {
                        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "The connection name is alreay used, please select other one.")));
                        return NULL;
                    }
                    $query = $this->db->query("select count(connid) as numconn from dc_conn where creatorid = ?", array($creatorid));
                    $row = $query->row_array();
                    $nowconnNum = $row["numconn"];
                    $quote = $this->_check_quote("max_connection", $nowconnNum);
                    if ($quote) {
                        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Please upgrade your plan to allow saving new connections.")));
                        return NULL;
                    }
                    $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => $name, "hostname" => $is_rest_api ? $rest_url : $hostname, "username" => $username, "password" => $this->_encrypt_conn_password($password), "database" => $database, "dbdriver" => $is_rest_api ? "restapi" : $dbdriver, "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => isset($port) ? $port : 0, "createdate" => time()));
                    $connid = $this->db->insert_id();
                    $this->_save_conn_adv_options($creatorid, $connid);
                    if ($autocreatedata == "1") {
                        $this->_create_table_editors($testconn, $creatorid, $connid);
                    }
                    $this->_db_insights($creatorid, $connid);
                    $setdefault = $this->input->post("setdefault");
                    if ($setdefault == "1") {
                        $this->_check_and_set_default_conn($creatorid, $connid);
                    }
                    if ($this->db->affected_rows() == 1) {
                        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been saved.", "connid" => $connid)));
                    } else {
                        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not save the database connection, please try again.")));
                    }
                } else {
                    $this->db->update("dc_conn", array("name" => $name, "hostname" => $is_rest_api ? $rest_url : $hostname, "username" => $username, "password" => $this->_encrypt_conn_password($password), "database" => $database, "dbdriver" => $is_rest_api ? "restapi" : $dbdriver, "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => 0, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid));
                    $this->_save_conn_adv_options($creatorid, $connid);
                    if ($autocreatedata == "1") {
                        $this->_create_table_editors($testconn, $creatorid, $connid);
                    }
                    $setdefault = $this->input->post("setdefault");
                    if ($setdefault == "1") {
                        $this->_check_and_set_default_conn($creatorid, $connid);
                    }
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been updated.", "connid" => $connid)));
                }
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Unable to connect to your database server using the provided settings. <a href=\"//www.dbface.com/documents/supported-datasources/\" target=\"_blank\">Please refer this page to get how to make it work</a>")));
            }
        }
    }
    public function _save_plugin_connection()
    {
        $dbdriver = trim($this->input->post("dbdriver"));
        $name = trim($this->input->post("name"));
        $hostname = trim($this->input->post("plugin_url"));
        $database = trim($this->input->post("database"));
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        if (empty($connid)) {
            $query = $this->db->query("select 1 from dc_conn where creatorid=? and name=?", array($creatorid, $name));
            if (0 < $query->num_rows()) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "The connection name is alreay used, please select other one.")));
                return NULL;
            }
            $query = $this->db->query("select count(connid) as numconn from dc_conn where creatorid = ?", array($creatorid));
            $row = $query->row_array();
            $nowconnNum = $row["numconn"];
            $quote = $this->_check_quote("max_connection", $nowconnNum);
            if ($quote) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Please upgrade your plan to allow saving new connections.")));
                return NULL;
            }
            $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => $name, "hostname" => $hostname, "username" => "", "password" => "", "database" => $database, "dbdriver" => $dbdriver, "char_set" => "", "dbcollat" => "", "port" => 0, "createdate" => time()));
            $connid = $this->db->insert_id();
            $this->_easure_internal_storage_connection($connid);
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            if ($this->db->affected_rows() == 1) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been saved.", "connid" => $connid)));
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not save the database connection, please try again.")));
            }
        } else {
            $this->db->update("dc_conn", array("name" => $name, "hostname" => $hostname, "username" => "", "password" => "", "database" => $database, "dbdriver" => $dbdriver, "char_set" => "", "dbcollat" => "", "port" => 0, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid));
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been updated.", "connid" => $connid)));
        }
    }
    public function _save_dynamodb_connection()
    {
        $dbdriver = trim($this->input->post("dbdriver"));
        $name = trim($this->input->post("name"));
        $creatorid = $this->session->userdata("login_creatorid");
        $aws_region = $this->input->post("aws_region");
        $aws_access_key = $this->input->post("aws_access_key");
        $aws_secret_key = $this->input->post("aws_secret_key");
        $connid = $this->input->post("connid");
        if (empty($connid)) {
            $query = $this->db->query("select 1 from dc_conn where creatorid=? and name=?", array($creatorid, $name));
            if (0 < $query->num_rows()) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "The connection name is alreay used, please select other one.")));
                return NULL;
            }
            $query = $this->db->query("select count(connid) as numconn from dc_conn where creatorid = ?", array($creatorid));
            $row = $query->row_array();
            $nowconnNum = $row["numconn"];
            $quote = $this->_check_quote("max_connection", $nowconnNum);
            if ($quote) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Please upgrade your plan to allow saving new connections.")));
                return NULL;
            }
            $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => $name, "hostname" => $aws_region, "username" => $aws_access_key, "password" => $aws_secret_key, "database" => "", "dbdriver" => $dbdriver, "char_set" => "", "dbcollat" => "", "port" => 0, "createdate" => time()));
            $connid = $this->db->insert_id();
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            if ($this->db->affected_rows() == 1) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been saved.", "connid" => $connid)));
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not save the database connection, please try again.")));
            }
        } else {
            $this->db->update("dc_conn", array("name" => $name, "hostname" => $aws_region, "username" => $aws_access_key, "password" => $aws_secret_key, "database" => "", "dbdriver" => $dbdriver, "char_set" => "", "dbcollat" => "", "port" => 0, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid));
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been updated.", "connid" => $connid)));
        }
    }
    public function _save_bigquery_connection()
    {
        $dbdriver = trim($this->input->post("dbdriver"));
        $name = trim($this->input->post("name"));
        $database = trim($this->input->post("database"));
        $creatorid = $this->session->userdata("login_creatorid");
        $service_account = trim($this->input->post("bigquery_service_account"));
        $projectId = trim($this->input->post("bigquery_projectid"));
        $autocreatedata = $this->input->post("autocreatedata");
        $connid = $this->input->post("connid");
        if (empty($connid)) {
            $query = $this->db->query("select 1 from dc_conn where creatorid=? and name=?", array($creatorid, $name));
            if (0 < $query->num_rows()) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "The connection name is alreay used, please select other one.")));
                return NULL;
            }
            $query = $this->db->query("select count(connid) as numconn from dc_conn where creatorid = ?", array($creatorid));
            $row = $query->row_array();
            $nowconnNum = $row["numconn"];
            $quote = $this->_check_quote("max_connection", $nowconnNum);
            if ($quote) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Please upgrade your plan to allow saving new connections.")));
                return NULL;
            }
            $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => $name, "hostname" => $projectId, "username" => $service_account, "password" => "google", "database" => $database, "dbdriver" => $dbdriver, "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => 0, "createdate" => time()));
            $connid = $this->db->insert_id();
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            if ($this->db->affected_rows() == 1) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been saved.", "connid" => $connid)));
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not save the database connection, please try again.")));
            }
        } else {
            $this->db->update("dc_conn", array("name" => $name, "hostname" => $projectId, "username" => $service_account, "password" => "google", "database" => $database, "dbdriver" => $dbdriver, "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => 0, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid));
            $this->_easure_internal_storage_connection($connid);
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been updated.", "connid" => $connid)));
        }
    }
    public function _save_mongo_connection()
    {
        $dbdriver = trim($this->input->post("dbdriver"));
        $name = trim($this->input->post("name"));
        $database = trim($this->input->post("database"));
        $creatorid = $this->session->userdata("login_creatorid");
        $hostname = trim($this->input->post("hostname"));
        $username = trim($this->input->post("username"));
        $password = trim($this->input->post("password"));
        $autocreatedata = $this->input->post("autocreatedata");
        $adv_opt_keys = $this->input->post("adv_options_key[]");
        $adv_opt_vals = $this->input->post("adv_options_val[]");
        $hosts = explode(":", $hostname);
        $port = 27017;
        if (count($hosts) == 2) {
            $hostname = $hosts[0];
            $port = intval($hosts[1]);
        }
        $adv_options = array();
        if (is_string($adv_opt_keys)) {
            $adv_opt_keys = array($adv_opt_keys);
            $adv_opt_vals = array($adv_opt_vals);
        }
        $opt_size = count($adv_opt_keys);
        for ($i = 0; $i < $opt_size; $i++) {
            $k = $adv_opt_keys[$i];
            $v = $adv_opt_vals[$i];
            if (!empty($k) && !empty($v)) {
                $adv_options[$k] = $v;
            }
        }
        $connid = $this->input->post("connid");
        if (empty($connid)) {
            $query = $this->db->query("select 1 from dc_conn where creatorid=? and name=?", array($creatorid, $name));
            if (0 < $query->num_rows()) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "The connection name is alreay used, please select other one.")));
                return NULL;
            }
            $query = $this->db->query("select count(connid) as numconn from dc_conn where creatorid = ?", array($creatorid));
            $row = $query->row_array();
            $nowconnNum = $row["numconn"];
            $quote = $this->_check_quote("max_connection", $nowconnNum);
            if ($quote) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Please upgrade your plan to allow saving new connections.")));
                return NULL;
            }
            $this->db->insert("dc_conn", array("creatorid" => $creatorid, "name" => $name, "hostname" => $hostname, "username" => $username, "password" => $password, "database" => $database, "dbdriver" => $dbdriver, "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => $port, "createdate" => time()));
            $connid = $this->db->insert_id();
            $this->_save_conn_adv_options($creatorid, $connid);
            $this->_easure_internal_storage_connection($connid);
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            if ($this->db->affected_rows() == 1) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been saved.", "connid" => $connid)));
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not save the database connection, please try again.")));
            }
        } else {
            $this->db->update("dc_conn", array("name" => $name, "hostname" => $hostname, "username" => $username, "password" => $password, "database" => $database, "dbdriver" => $dbdriver, "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "port" => $port, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid));
            $this->_save_conn_adv_options($creatorid, $connid);
            $this->_easure_internal_storage_connection($connid);
            $setdefault = $this->input->post("setdefault");
            if ($setdefault == "1") {
                $this->_check_and_set_default_conn($creatorid, $connid);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Database connection has been updated.", "connid" => $connid)));
        }
    }
    public function _save_conn_adv_options($creatorid, $connid)
    {
        $adv_opt_keys = $this->input->post("adv_options_key[]");
        $adv_opt_vals = $this->input->post("adv_options_val[]");
        if (is_string($adv_opt_keys)) {
            $adv_opt_keys = array($adv_opt_keys);
            $adv_opt_vals = array($adv_opt_vals);
        }
        if (empty($creatorid) || empty($connid)) {
            return NULL;
        }
        $dbdriver = trim($this->input->post("dbdriver"));
        if ($dbdriver == "mysqli") {
            $enable_ssl = $this->input->post("enable_ssl");
            if ($enable_ssl != "no") {
                $ssl_key = $this->input->post("ssl_key");
                $ssl_cert = $this->input->post("ssl_cert");
                $ssl_ca = $this->input->post("ssl_ca");
                $ssl_cipher = $this->input->post("ssl_cipher");
                $adv_opt_keys[] = "enable_ssl";
                $adv_opt_keys[] = "ssl_key";
                $adv_opt_keys[] = "ssl_cert";
                $adv_opt_keys[] = "ssl_ca";
                $adv_opt_keys[] = "ssl_cipher";
                $adv_opt_vals[] = $enable_ssl;
                $adv_opt_vals[] = $ssl_key;
                $adv_opt_vals[] = $ssl_cert;
                $adv_opt_vals[] = $ssl_ca;
                $adv_opt_vals[] = $ssl_cipher;
            }
        }
        if (count($adv_opt_keys) != count($adv_opt_vals) || count($adv_opt_vals) == 0) {
            return NULL;
        }
        $this->db->delete("dc_conn_option", array("creatorid" => $creatorid, "connid" => $connid, "type" => "string"));
        $size = count($adv_opt_keys);
        $arr = array();
        for ($i = 0; $i < $size; $i++) {
            $k = $adv_opt_keys[$i];
            $v = $adv_opt_vals[$i];
            if (!empty($k) && !empty($v)) {
                $arr[] = array("creatorid" => $creatorid, "connid" => $connid, "name" => $k, "type" => "string", "value" => $v, "date" => time());
            }
        }
        if (0 < count($arr)) {
            insert_batch($this->db, "dc_conn_option", $arr);
        }
    }
    public function _create_table_editors($db, $creatorid, $connid)
    {
        $query = $this->db->query("select categoryid from dc_category where name = ? and creatorid = ? limit 1", array($this->config->item("default_data_category_name"), $creatorid));
        $categoryid = $this->_get_default_data_category($creatorid);
        $tables = list_tables($db);
        foreach ($tables as $table) {
            $select = list_fields($db, $table);
            $script = array("tablename" => array($table), "select" => $select);
            $this->db->delete("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "name" => $table, "status" => "system"));
            $this->db->insert("dc_app", array("connid" => $connid, "creatorid" => $creatorid, "type" => "list", "name" => $table, "title" => $table, "categoryid" => $categoryid, "scripttype" => 1, "format" => "tableeditor", "status" => "system", "script" => json_encode($script)));
        }
        $primary_keys = array();
        $table_columns = array();
        foreach ($tables as $table) {
            $fields = $db->field_data($table);
            $table_columns[$table] = array();
            foreach ($fields as $field) {
                if ($field->primary_key == 1) {
                    $primary_keys[$field->name] = $table;
                }
                $table_columns[$table][] = array("name" => $field->name, "primary" => $field->primary_key == 1);
            }
        }
        foreach ($table_columns as $table => $fields) {
            foreach ($fields as $field) {
                $fielname = $field["name"];
                $isprimary = $field["primary"];
                if (!$isprimary && isset($primary_keys[$fielname]) && $primary_keys[$fielname] != $table) {
                    $this->_make_table_link($table, $fielname, $primary_keys[$fielname], $fielname, $creatorid, $connid);
                }
            }
        }
    }
    public function edit()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $tag = $this->input->post("tag");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->get("connid");
        $query = $this->db->query("select * from dc_conn where connid = ? and creatorid =?", array($connid, $creatorid));
        $conn_info = $query->row_array();
        $conn_info["password"] = $this->_decrypt_conn_password($conn_info["password"]);
        $this->smartyview->assign("conn", $conn_info);
        $default_port = get_db_default_port($conn_info["dbdriver"]);
        $this->smartyview->assign("default_port", $default_port);
        $query = $this->db->select("name,value")->where(array("creatorid" => $creatorid, "connid" => $connid, "type" => "string"))->get("dc_conn_option");
        $adv_options = array();
        $result = $query->result_array();
        foreach ($result as $option) {
            if ($option["name"] == "enable_ssl") {
                $enable_ssl = $option["value"];
                $this->smartyview->assign("enable_ssl", $enable_ssl);
            } else {
                if ($option["name"] == "ssl_key") {
                    $ssl_key = $option["value"];
                    $this->smartyview->assign("ssl_key", $ssl_key);
                } else {
                    if ($option["name"] == "ssl_cert") {
                        $ssl_cert = $option["value"];
                        $this->smartyview->assign("ssl_cert", $ssl_cert);
                    } else {
                        if ($option["name"] == "ssl_ca") {
                            $ssl_ca = $option["value"];
                            $this->smartyview->assign("ssl_ca", $ssl_ca);
                        } else {
                            if ($option["name"] == "ssl_cipher") {
                                $ssl_cipher = $option["value"];
                                $this->smartyview->assign("ssl_cipher", $ssl_cipher);
                            } else {
                                $adv_options[] = $option;
                            }
                        }
                    }
                }
            }
        }
        $this->smartyview->assign("adv_options", $adv_options);
        $this->smartyview->assign("self_host", $this->config->item("self_host"));
        $plugins = $this->_list_plugin_datasources();
        if ($plugins && is_array($plugins) && 0 < count($plugins)) {
            $this->smartyview->assign("plugin_ds", $plugins);
        }
        $this->smartyview->display("new/conn.create.tpl");
    }
    public function del()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        if ($creatorid != $userid) {
            exit;
        }
        $connid = $this->input->post("connid");
        $this->load->database();
        $this->db->select("database, dbdriver");
        $this->db->where("creatorid", $creatorid);
        $this->db->where("connid", $connid);
        $this->db->limit(1);
        $query = $this->db->get("dc_conn");
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $driver = $row["dbdriver"];
            $database = $row["database"];
            if ($driver == "sqlite3") {
                $db = $this->_get_db($creatorid, $connid);
            }
            $this->db->delete("dc_conn", array("connid" => $connid, "creatorid" => $creatorid));
            $this->db->delete("dc_app", array("connid" => $connid, "creatorid" => $creatorid));
            $refresh = false;
            $default_connid = $this->session->userdata("_default_connid_");
            if ($default_connid == $connid) {
                $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "default_connid", "type" => "string"));
                $this->_check_and_set_default_conn($creatorid);
                $refresh = true;
            }
            if ($this->db->affected_rows() == 1) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "refresh" => $refresh, "message" => "Connection removed")));
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not remove the connection.")));
            }
        }
    }
    public function setdefault2()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $update_connid = $this->_check_and_set_default_conn($creatorid, $connid);
        if ($update_connid) {
            echo "1";
        } else {
            echo "0";
        }
    }
    public function setdefault_json()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $update_connid = $this->_check_and_set_default_conn($creatorid, $connid);
        if ($update_connid) {
            echo json_encode(array("result" => "ok", "connid" => $connid));
        } else {
            echo json_encode(array("result" => "fail", "connid" => $connid));
        }
    }
    public function setdefault()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $update_connid = $this->_check_and_set_default_conn($creatorid, $connid);
        if ($update_connid) {
            $this->load->library("smartyview");
            $conns = $this->_get_connections($creatorid);
            $this->smartyview->assign("conns", $conns);
            $this->smartyview->assign("default_connid", $update_connid);
            $this->smartyview->display("new/conn.table.tpl");
        } else {
            echo "0";
        }
    }
    public function getdblist()
    {
        $host = $this->input->post("hostname");
        $username = $this->input->post("username");
        $password = $this->input->post("password");
        $dbdriver = $this->input->post("dbdriver");
        $this->load->helper("json");
        if (function_exists("pick_database_names")) {
            $database_names = call_user_func_array("pick_database_names", array($dbdriver, $host, $username, $password));
            if ($database_names && is_array($database_names)) {
                $dbs = array();
                foreach ($database_names as $dbname) {
                    $dbs[] = array("Database" => $dbname);
                }
                $this->load->library("smartyview");
                $this->smartyview->assign("dbs", $dbs);
                $this->smartyview->display("dblist.tpl");
                return NULL;
            }
        }
        if ($dbdriver == "mongodb") {
            require_once APPPATH . "/libraries/Mongo_db.php";
            $mongo_db = new Mongo_db(array("hostname" => $host, "username" => $username, "password" => $password));
            $tmp = $mongo_db->listDatabases();
            $databases = array();
            foreach ($tmp as $item) {
                $databases[] = array("Database" => $item->getName());
            }
            $this->load->library("smartyview");
            $this->smartyview->assign("dbs", $databases);
            $this->smartyview->display("dblist.tpl");
            return NULL;
        } else {
            if ($dbdriver == "mysqli") {
                $db = @$this->load->database(array("hostname" => $host, "username" => $username, "password" => $password, "dbdriver" => "mysqli", "db_debug" => false), true, true);
                if (!is_resource($db->conn_id) && !is_object($db->conn_id)) {
                    echo "Unable to connect to your database server using the provided settings";
                } else {
                    $databases = $db->query("show databases")->result_array();
                    if ($this->config->item("hideSystemDatabase")) {
                        $tmp = array();
                        foreach ($databases as $dbnames) {
                            if (!$this->_is_system_database($dbnames["Database"])) {
                                $tmp[] = array("Database" => $dbnames["Database"]);
                            }
                        }
                        $databases = $tmp;
                    }
                    $this->load->library("smartyview");
                    $this->smartyview->assign("dbs", $databases);
                    $this->smartyview->display("dblist.tpl");
                }
            } else {
                echo "Not supported on this connection";
            }
        }
    }
    public function workshop_dialog()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->display("new/box.sqlworkshop.indialog.tpl");
    }
    public function editview()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $view = $this->input->get_post("view");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $db = $this->_get_db($creatorid, $connid);
        if ($db) {
            $this->smartyview->assign("db_escape_char", $db->get_escape_char());
        }
        $this->smartyview->assign("connid", $connid);
        $this->_createview_for_mongo($connid, $view);
        $this->smartyview->display("mongodb/box.sqlworkshop.tpl");
    }
    public function dropview()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $view = $this->input->post("view");
        $db = $this->_get_db($creatorid, $connid);
        if (!$db) {
            echo json_encode(array(0 < "status", "message" => "1001: The database file has been removed."));
        } else {
            $forge = $this->load->dbforge($db, true);
            $flag = $forge->drop_table($view, true);
            $this->db->delete("dc_conn_views", array("creatorid" => $creatorid, "connid" => $connid, "name" => $view, "type" => "json"));
            $delete_app_where = array("creatorid" => $creatorid, "connid" => $connid, "type" => "list", "format" => "tableeditor", "name" => $view, "status" => "system");
            $query = $this->db->select("appid")->where($delete_app_where)->get("dc_app");
            if ($query->num_rows() == 1) {
                $appid = $query->row()->appid;
                $this->db->delete("dc_app", array("creatorid" => $creatorid, "appid" => $appid));
                $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid));
                $this->db->delete("dc_app_permission", array("appid" => $appid));
            }
            echo json_encode(array("status" => 1, "message" => "The table view has been removed."));
        }
    }
    public function _create_dynamodb_view()
    {
        $this->load->library("smartyview");
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $db = $this->_get_db($creatorid, $connid);
        if ($db) {
            $this->smartyview->assign("db_escape_char", $db->get_escape_char());
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->display("dynamodb/box.sqlworkshop.tpl");
    }
    public function createview()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $db = $this->_get_db($creatorid, $connid);
        if ($db) {
            $this->smartyview->assign("db_escape_char", $db->get_escape_char());
        }
        $this->smartyview->assign("connid", $connid);
        $this->_createview_for_mongo($connid);
        $this->smartyview->display("mongodb/box.sqlworkshop.tpl");
    }
    public function _createview_for_mongo($connid, $view = false)
    {
        require APPPATH . "/libraries/Mongo_db.php";
        $db_config = $this->_get_mongo_db_config($connid);
        try {
            $mongo_db = new Mongo_db($db_config);
            $collections = $mongo_db->listCollections();
            $this->smartyview->assign("collections", $collections);
            if ($view) {
                $creatorid = $this->session->userdata("login_creatorid");
                $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "name" => $view, "type" => "json"))->get("dc_conn_views");
                if ($query->num_rows() == 1) {
                    $settings = json_decode($query->row()->value, true);
                    $this->smartyview->assign("view_settings", $settings);
                    $this->smartyview->assign("action_tag", "update");
                    $this->smartyview->assign("view", $view);
                }
            }
        } catch (Exception $e) {
            $this->smartyview->assign("error_message", $e->getMessage());
        }
    }
    public function workshop()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $db = $this->_get_db($creatorid, $connid);
        if (isset($db->mongodb_config)) {
            $this->createview();
        } else {
            if (isset($db->dynamodb_config)) {
                $this->_create_dynamodb_view();
            } else {
                if ($db) {
                    $this->smartyview->assign("db_escape_char", $db->get_escape_char());
                }
                $this->smartyview->assign("connid", $connid);
                $this->smartyview->display("new/box.sqlworkshop.tpl");
            }
        }
    }
    public function get_tagged_sql()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "tagged_sql"))->get("dc_app_options");
        $tagged_sql = $query->result_array();
        $this->load->library("smartyview");
        $this->smartyview->assign("tagged_sql", $tagged_sql);
        $this->smartyview->display("new/sqlworkshop.tagged.tpl");
    }
    public function tag_sql()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $name = $this->input->post("value");
        $oldname = $this->input->post("oldvalue");
        $sql = $this->input->post("sql");
        $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "tagged_sql", "key" => $name, "value" => $sql));
        $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "tagged_sql", "key" => $oldname));
        echo json_encode(array("newValue" => $name));
    }
    public function trash_sql()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $type = $this->input->post("type");
        $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => $type));
        echo json_encode(array("status" => 1));
    }
    public function remove_sql()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $type = $this->input->post("type");
        $k = $this->input->post("k");
        $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "key" => $k, "type" => $type));
        echo json_encode(array("status" => 1));
    }
    public function get_history_sql()
    {
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "history_sql"))->limit(20)->order_by("key", "desc")->get("dc_app_options");
        $tagged_sql = $query->result_array();
        $this->load->library("smartyview");
        $this->smartyview->assign("history_sql", $tagged_sql);
        $this->smartyview->display("new/sqlworkshop.history.tpl");
    }
    public function runsql()
    {
        $sql = $this->input->post("content");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $run_selected = $this->input->post("part") == "1";
        if (empty($connid) || empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "history_sql"))->order_by("key", "desc")->limit(1)->get("dc_app_options");
        $last_query = false;
        $last_key = false;
        if (0 < $query->num_rows()) {
            $row = $query->row();
            $last_query = $row->value;
            $last_key = $row->key;
        }
        if (!$run_selected) {
            if ($last_key && $last_query == $sql) {
                $this->db->update("dc_app_options", array("key" => time()), array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "history_sql", "key" => $last_key));
            } else {
                $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => 0, "type" => "history_sql", "key" => time(), "value" => $sql));
            }
        }
        $this->load->library("smartyview");
        $db = $this->_get_db($creatorid, $connid);
        if ($db) {
            $smarty = $this->_get_template_engine($db, $creatorid, $connid);
            $sql = $this->_compile_string($smarty, $sql);
            $query = $db->query($sql);
            if ($query) {
                if (is_object($query)) {
                    $fields = $query->list_fields();
                    $datas = $query->result_array();
                    $this->smartyview->assign("ID_RESULTSET", "_resultset");
                    $this->smartyview->assign("pagerows", 20);
                    $this->smartyview->assign("fields", $fields);
                    $this->smartyview->assign("fieldnum", count($fields));
                    $this->smartyview->assign("totalrows", count($datas));
                    $this->smartyview->assign("datas", $datas);
                    $this->smartyview->display("new/sqlworkshop_result.tpl");
                } else {
                    $fields = array("Result");
                    $affected_rows = $db->affected_rows();
                    $datas = array(array("Result" => "SQL scripts executed, affected rows: " . $affected_rows));
                    $this->smartyview->assign("ID_RESULTSET", "_resultset");
                    $this->smartyview->assign("pagerows", 20);
                    $this->smartyview->assign("fields", $fields);
                    $this->smartyview->assign("fieldnum", count($fields));
                    $this->smartyview->assign("totalrows", count($datas));
                    $this->smartyview->assign("datas", $datas);
                    $this->smartyview->display("new/sqlworkshop_result.tpl");
                }
            } else {
                $error = $db->error();
                $this->smartyview->assign("title", "Query Error");
                $this->smartyview->assign("message", "<b>Query:</b><br/>" . $db->last_query() . "<p/>The script of the application contains the following errors:<br/><b>" . $error["code"] . ": </b>" . $error["message"]);
                $this->smartyview->display("inc/app.error.body.tpl");
            }
        } else {
            $this->smartyview->assign("title", "Connection Lost");
            $this->smartyview->assign("message", "Database connection lost");
            $this->smartyview->display("inc/app.error.body.tpl");
        }
    }
    public function gettablelist()
    {
        $connid = $this->input->get_post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $tablelist = list_tables($db);
        $this->load->library("smartyview");
        $this->smartyview->assign("tables", $tablelist);
        $this->smartyview->display("qb/gettablelist.tpl");
    }
    public function check_ds_supported()
    {
        $dbdriver = $this->input->post("dbdriver");
        $status = 1;
        $message = NULL;
        if ($dbdriver == "mysqli") {
            if (!extension_loaded("mysqli")) {
                $status = 0;
                $message = "Please enable mysqli extension to enable MySQL database support";
            }
        } else {
            if ($dbdriver == "pgsql") {
            } else {
                if ($dbdriver == "ibase") {
                } else {
                    if ($dbdriver == "cubrid") {
                    } else {
                        if ($dbdriver == "sqlsrv") {
                            if (!extension_loaded("pdo_sqlsrv")) {
                                $status = 0;
                                $message = "Please <a target='_blank' href='https://docs.microsoft.com/en-us/sql/connect/php/loading-the-php-sql-driver'>install PDO SQLSRV driver to enable SQL Server database connection</a>.";
                            }
                        } else {
                            if ($dbdriver == "oci8") {
                                if (!extension_loaded("oci8")) {
                                    $status = 0;
                                    $message = "Your account does not support Oracle. The oracle driver not installed.";
                                }
                            } else {
                                if ($dbdriver == "sqlite3") {
                                } else {
                                    if ($dbdriver == "access") {
                                    } else {
                                        if ($dbdriver == "dns") {
                                        } else {
                                            if ($dbdriver == "mongodb") {
                                                if (!extension_loaded("mongodb")) {
                                                    $status = 0;
                                                    $message = "The MongoDB PECL extension has not been installed or enabled. <a target='_blank'  href='http://php.net/manual/en/mongodb.installation.php'>How to install MongoDb extension?</a>";
                                                }
                                            } else {
                                                if ($dbdriver == "dynamodb") {
                                                    $aws_lib_path = APPPATH . "third_party" . DIRECTORY_SEPARATOR . "Aws";
                                                    if (!file_exists($aws_lib_path)) {
                                                        $status = 0;
                                                        $message = "Your account does not support Amazon DynamoDB, please contact support to activate this driver.";
                                                    }
                                                } else {
                                                    if ($dbdriver == "bigquery") {
                                                    } else {
                                                        if ($dbdriver == "json") {
                                                        } else {
                                                            if ($dbdriver == "csv") {
                                                            } else {
                                                                if ($dbdriver == "restapi") {
                                                                } else {
                                                                    if ($dbdriver == "dbface_cloud") {
                                                                    } else {
                                                                        if ($dbdriver == "db2") {
                                                                            if (!extension_loaded("db2")) {
                                                                                $status = 0;
                                                                                $message = "The IBM DB2 extension has not been installed or enabled. <a target='_blank'  href='http://php.net/manual/en/ibm-db2.setup.php'>How to install IBM DB2 extension?</a>";
                                                                            }
                                                                        } else {
                                                                            if ($dbdriver == "cassandra") {
                                                                                if (!extension_loaded("cassandra")) {
                                                                                    $status = 0;
                                                                                    $message = "The DataStax PHP Driver for Apache Cassandra  has not been installed or enabled. <a target='_blank'  href='http://datastax.github.io/php-driver/'>How to install DataStax PHP Driver for Apache Cassandra?</a>";
                                                                                }
                                                                            } else {
                                                                                if ($dbdriver == "redis") {
                                                                                    if (!extension_loaded("redis")) {
                                                                                        $status = 0;
                                                                                        $message = "The Redis PHP Driver has not been installed or enabled. <a target='_blank'  href='https://github.com/phpredis/phpredis/blob/develop/INSTALL.markdown'>How to install PHP Redis Driver?</a>";
                                                                                    }
                                                                                } else {
                                                                                    $plugin = $this->_get_plugin_datasource($dbdriver);
                                                                                    if ($plugin) {
                                                                                        $status = 2;
                                                                                        $message = $plugin["description"];
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($status != 1) {
            echo json_encode(array("status" => $status, "message" => $message));
        } else {
            echo json_encode(array("status" => 1));
        }
    }
    public function query_for_dynamodb_view()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $content = $this->input->post("content");
        $old_view = $this->input->post("old_view");
        $is_download = $this->input->get_post("download");
        $is_download = $is_download == "1" ? true : false;
        $error = false;
        try {
            $dynamodb = $this->_get_dynamodb($creatorid, $connid);
        } catch (Exception $e) {
            dbface_log("error", $e);
            echo json_encode(array("message" => $e->getMessage()));
            return NULL;
        }
        $cmd = json_decode($content, true);
        if ($cmd == NULL) {
            $cmd = array();
        }
        dbface_log("info", "Query Dynamodb ", $cmd);
        $result = $dynamodb->tryJSONCommand($cmd);
    }
    public function query_for_view()
    {
        $connid = $this->input->post("connid");
        $collection = $this->input->post("collection");
        $content = $this->input->post("content");
        $old_view = $this->input->post("old_view");
        $is_download = $this->input->get_post("download");
        $is_download = $is_download == "1" ? true : false;
        $error = false;
        try {
            require APPPATH . "/libraries/Mongo_db.php";
            $db_config = $this->_get_mongo_db_config($connid);
            $mongo_db = new Mongo_db($db_config);
        } catch (Exception $e) {
            dbface_log("error", $e);
            echo json_encode(array("message" => $e->getMessage()));
            return NULL;
        }
        if ($error) {
            $fields = array("Result");
            $datas = array(array("Result" => $error));
        } else {
            $filter = json_decode($content, true);
            if ($filter == NULL) {
                $filter = array();
            }
            if (!$error) {
                if (!$filter) {
                    $filter = array();
                }
                dbface_log("info", "Query MongoDB " . $collection, $filter);
                try {
                    $result = $mongo_db->find($collection, $filter);
                } catch (Exception $e) {
                }
                $datas = array();
                $fields = array();
                foreach ($result as $row) {
                    try {
                        $row = $row->jsonSerialize();
                    } catch (Exception $e) {
                    }
                    $row_data = array();
                    foreach ($row as $k => $v) {
                        if (is_object($v)) {
                            if (method_exists($v, "__toString")) {
                                $row_data[$k] = (string) $v;
                            } else {
                                $row_data[$k] = serialize($v);
                            }
                        } else {
                            if (is_array($v)) {
                                $row_data[$k] = json_encode($v);
                            } else {
                                $row_data[$k] = $v;
                            }
                        }
                    }
                    $datas[] = $row_data;
                    foreach ($row_data as $field => $value) {
                        if (!in_array($field, $fields)) {
                            $fields[] = $field;
                        }
                    }
                }
            }
        }
        if ($is_download) {
            $csv_content = "";
            $enclosure = "\"";
            $newline = "\n";
            $delimiter = ",";
            $export_csv_settings = $this->config->item("export_csv_settings");
            if ($export_csv_settings && is_array($export_csv_settings)) {
                if (isset($export_csv_settings["delimiter"]) && !empty($export_csv_settings["delimiter"])) {
                    $delimiter = $export_csv_settings["delimiter"];
                }
                if (isset($export_csv_settings["newline"]) && !empty($export_csv_settings["newline"])) {
                    $newline = $export_csv_settings["newline"];
                }
                if (isset($export_csv_settings["enclosure"]) && !empty($export_csv_settings["enclosure"])) {
                    $enclosure = $export_csv_settings["enclosure"];
                }
            }
            foreach ($fields as $name) {
                $csv_content .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $name) . $enclosure . $delimiter;
            }
            $csv_content = substr($csv_content, 0, 0 - strlen($delimiter)) . $newline;
            foreach ($datas as $row) {
                $line = array();
                foreach ($row as $k => $item) {
                    $line[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $item) . $enclosure;
                }
                $csv_content .= implode($delimiter, $line) . $newline;
            }
            $csv_excel_compatible = $this->config->item("csv_excel_compatible");
            if ($csv_excel_compatible !== false) {
                if (function_exists("mb_convert_encoding")) {
                    $csv_content = chr(255) . chr(254) . mb_convert_encoding($csv_content, "UTF-16LE", "UTF-8");
                } else {
                    dbface_log("error", "mbstring module required for Excel Compatible CSV.");
                }
            }
            $this->load->helper("download");
            $filename = "export.csv";
            force_download($filename, $csv_content);
            return NULL;
        } else {
            $this->load->library("smartyview");
            $this->smartyview->assign("old_view", $old_view);
            $this->smartyview->assign("data_types", array("TEXT", "INTEGER", "REAL", "BLOB", "NUMERIC", "BOOLEAN", "DATETIME"));
            $this->smartyview->assign("connid", $connid);
            $this->smartyview->assign("ID_RESULTSET", "_resultset");
            $this->smartyview->assign("pagerows", 20);
            $this->smartyview->assign("fields", $fields);
            $this->smartyview->assign("fieldnum", count($fields));
            $this->smartyview->assign("totalrows", count($datas));
            $this->smartyview->assign("datas", $datas);
            $this->smartyview->display("mongodb/sqlworkshop_result.tpl");
        }
    }
    public function update_mongo_view()
    {
    }
    public function _sync_plugin_view($connid, $view)
    {
        $dbconfig = $this->_get_db_config($connid);
        $plugin = $this->_get_plugin_datasource_instance($dbconfig["dbdriver"]);
        $plugin->setup($dbconfig);
        $creatorid = $this->session->userdata("login_creatorid");
        $schemas = $plugin->get_schemas();
        if (!$schemas || !is_array($schemas) || count($schemas) == 0) {
            echo json_encode(array("status" => 0, "message" => "No view found in the plugin, did you implemented the get_schemas function correctly?"));
        } else {
            if (!isset($schemas[$view])) {
                echo json_encode(array("status" => 0, "message" => "View " . $view . " not found."));
            } else {
                $this->_remove_db_schema_cache($connid);
                $internal_db = $this->_get_db($creatorid, $connid);
                $dbforge = $this->load->dbforge($internal_db, true);
                $dbforge->drop_table($view, true);
                $settings = $schemas[$view];
                $fields = $settings["fields"];
                $dbforge->add_field($fields);
                $dbforge->create_table($view);
                dbface_log("debug", $internal_db->last_query());
                $datas = $plugin->get_datas($view);
                if ($datas && is_array($datas)) {
                    insert_batch($internal_db, $view, $datas);
                }
                echo json_encode(array("status" => 1, "message" => "View Data synced!"));
            }
        }
    }
    public function sync_all_plugin_view($connid)
    {
        $dbconfig = $this->_get_db_config($connid);
        require_once APPPATH . "libraries" . DIRECTORY_SEPARATOR . "Plugin_db.php";
        $plugin = new Plugin_db($dbconfig["hostname"]);
        $this->_remove_db_schema_cache($connid);
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $plugin->setDb($db);
        $result = $plugin->sync();
        echo json_encode(array("status" => 1, "sync_result" => $result, "message" => "Plugin Data synced!"));
    }
    public function sync_all_mongo_view($connid)
    {
        $mongo_db = $this->_get_mongo_db($connid);
        if (is_string($mongo_db)) {
            echo json_encode(array("status" => 0, "message" => $mongo_db));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("name")->where(array("creatorid" => $creatorid, "connid" => $connid, "type" => "json"))->get("dc_conn_views");
            if (0 < $query->num_rows()) {
                $result_array = $query->result_array();
                foreach ($result_array as $row) {
                    $view = $row["name"];
                    $this->sync_mongo_view($connid, $view);
                    $this->import_mongo_to_view($connid, $view, $mongo_db);
                }
            }
            echo json_encode(array("status" => 1, "message" => "Data synced!"));
        }
    }
    public function syncview()
    {
        $connid = $this->input->post("connid");
        $view = $this->input->post("view");
        $inc = $this->input->post("inc") == "1";
        $plugin = $this->input->post("plugin") == "1";
        if ($plugin) {
            $this->_sync_plugin_view($connid, $view);
        } else {
            $mongo_db = $this->_get_mongo_db($connid);
            if (is_string($mongo_db)) {
                dbface_log("info", $mongo_db);
                echo json_encode(array("status" => 0, "message" => $mongo_db));
            } else {
                $result = $this->import_mongo_to_view($connid, $view, $mongo_db, $inc);
                if (!$result || is_string($result)) {
                    echo json_encode(array("status" => 0, "message" => "2002: Import data from MongoDB failed."));
                } else {
                    echo json_encode(array("status" => 1, "message" => "View has been updated.", "result" => $result));
                }
            }
        }
    }
    public function import_mongo_to_view($connid, $view, $mongo_db, $inc = false)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "name" => $view))->get("dc_conn_views");
        if ($query->num_rows() == 0) {
            return false;
        }
        $internal_db = $this->_get_db($creatorid, $connid);
        if (!$internal_db) {
            return false;
        }
        $settings = json_decode($query->row()->value, true);
        $script = $settings["script"];
        $collection = $settings["collection"];
        $filter = json_decode($script, true);
        if (!$filter) {
            $filter = array();
        }
        $inc = isset($filter["inc"]) ? $filter["inc"] : false;
        $should_truncate_view = true;
        if ($inc) {
            $field = $filter["inc"];
            $query = $internal_db->select_max($field, "max_value")->get($view);
            $cur_value = false;
            if (0 < $query->num_rows()) {
                $cur_value = $query->row()->max_value;
            }
            dbface_log("info", "current value cursor: " . $cur_value);
            if ($cur_value) {
                $filter[$inc] = array("\$gt" => new MongoDB\BSON\UTCDateTime($cur_value));
                $should_truncate_view = false;
            }
            unset($settings["inc"]);
        }
        $result = $mongo_db->find($collection, $filter);
        if ($result) {
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
            dbface_log("info", "importing mongodb data into internal storage: " . count($datas));
            if ($should_truncate_view) {
                $internal_db->truncate($view);
            }
            foreach ($datas as $row) {
                try {
                    $internal_db->insert($view, $row);
                } catch (Exception $e) {
                }
            }
            $rows = $internal_db->count_all($view);
            return $rows;
        } else {
            return false;
        }
    }
    public function sync_mongo_view($connid, $view, $old_view = false)
    {
        dbface_log("debug", "sync mongo view : " . $view);
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "name" => $view))->get("dc_conn_views");
        if ($query->num_rows() == 0) {
            return false;
        }
        $this->_remove_db_schema_cache($connid);
        $settings = json_decode($query->row()->value, true);
        $internal_db = $this->_get_db($creatorid, $connid);
        $dbforge = $this->load->dbforge($internal_db, true);
        $dbforge->drop_table($view, true);
        if ($old_view && !empty($old_view)) {
            $dbforge->drop_table($old_view, true);
        }
        $fields = $settings["fields"];
        $real_fields = array();
        foreach ($fields as $field_name => $field_meta) {
            $f = $field_meta["datatype"];
            if ($field_meta["pk"] == "1") {
                $dbforge->add_key($field_name, true);
            }
            dbface_log("debug", "Add Field: " . $field_name . " " . $f);
            $dbforge->add_field($field_name . " " . $f);
        }
        $dbforge->create_table($view);
        dbface_log("debug", $internal_db->last_query());
        dbface_log("debug", "sync mongo view end");
        return true;
    }
    public function save_mongo_view()
    {
        $connid = $this->input->post("connid");
        $view = $this->input->post("view");
        $collection = $this->input->post("collection");
        $script = $this->input->post("script");
        $creatorid = $this->session->userdata("login_creatorid");
        $old_view = $this->input->post("old_view");
        $value = array("collection" => $collection, "script" => $script, "type" => "mongodb");
        $value["fields"] = $this->input->post("fields");
        if (empty($old_view)) {
            $this->db->delete("dc_conn_views", array("creatorid" => $creatorid, "connid" => $connid, "name" => $view));
            $this->db->insert("dc_conn_views", array("creatorid" => $creatorid, "connid" => $connid, "name" => $view, "type" => "json", "value" => json_encode($value), "date" => time(), "lastsyncdate" => time()));
        } else {
            $this->db->update("dc_conn_views", array("value" => json_encode($value), "lastsyncdate" => time(), "name" => $view), array("creatorid" => $creatorid, "connid" => $connid, "name" => $old_view, "type" => "json"));
        }
        $mongo_db = $this->_get_mongo_db($connid);
        if (is_string($mongo_db)) {
            echo json_encode(array("status" => 0, "message" => $mongo_db));
        } else {
            $this->sync_mongo_view($connid, $view, $old_view);
            $result = $this->import_mongo_to_view($connid, $view, $mongo_db);
            if (is_string($result)) {
                echo json_encode(array("status" => 0, "message" => $result));
            } else {
                echo json_encode(array("status" => 1));
            }
        }
    }
    public function _get_mongo_db($connid)
    {
        try {
            require APPPATH . "/libraries/Mongo_db.php";
            $db_config = $this->_get_mongo_db_config($connid);
            $mongo_db = new Mongo_db($db_config);
            return $mongo_db;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function display_plugins()
    {
        $result = $this->_get_plugin_datasource_instance("sample_plugin");
        var_dump($result);
    }
    /**
     * save warehouse settings to MySQL db
     */
    public function save_warehouse_settings()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "fail"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $connid = $this->input->post("connid");
            $dbdriver = $this->input->post("dbdriver");
            $hostname = $this->input->post("hostname");
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $database = $this->input->post("database");
            if ($dbdriver == "sqlite3") {
                $file_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "warehouse";
                if (!file_exists($file_path)) {
                    mkdir($file_path, 511, true);
                }
                $host_config = array("dsn" => "", "hostname" => "", "username" => "", "password" => "", "database" => $file_path . DIRECTORY_SEPARATOR . "data.db", "dbdriver" => "sqlite3", "dbprefix" => "", "pconnect" => false, "db_debug" => true, "cache_on" => false, "cachedir" => "", "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "swap_pre" => "", "autoinit" => true, "stricton" => false, "failover" => array());
                $db = @$this->load->database($host_config, true);
                if ($db && $db->conn_id) {
                    $this->load->dbforge($db);
                    $db->close();
                    $this->db->update("dc_conn", array("hostname" => $host_config["hostname"], "username" => $host_config["username"], "password" => $this->_encrypt_conn_password($host_config["password"]), "database" => $host_config["database"], "dbdriver" => $host_config["dbdriver"], "dbprefix" => $host_config["dbprefix"], "pconnect" => $host_config["pconnect"], "char_set" => $host_config["char_set"], "dbcollat" => $host_config["dbcollat"], "swap_pre" => $host_config["swap_pre"], "stricton" => $host_config["stricton"], "port" => isset($host_config["port"]) ? $host_config["port"] : 0, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid, "name" => "_dbface_warehouse"));
                    echo json_encode(array("result" => "ok"));
                    return NULL;
                }
            } else {
                $host_config = array("dsn" => "", "hostname" => $hostname, "username" => $username, "password" => $password, "database" => $database, "dbdriver" => $dbdriver, "dbprefix" => "", "pconnect" => false, "db_debug" => true, "cache_on" => false, "cachedir" => "", "char_set" => "utf8", "dbcollat" => "utf8_general_ci", "swap_pre" => "", "autoinit" => true, "stricton" => false, "failover" => array());
                $db = @$this->load->database($host_config, true);
                if ($db && $db->conn_id) {
                    $this->db->update("dc_conn", array("hostname" => $host_config["hostname"], "username" => $host_config["username"], "password" => $this->_encrypt_conn_password($host_config["password"]), "database" => $host_config["database"], "dbdriver" => $host_config["dbdriver"], "dbprefix" => $host_config["dbprefix"], "pconnect" => $host_config["pconnect"], "char_set" => $host_config["char_set"], "dbcollat" => $host_config["dbcollat"], "swap_pre" => $host_config["swap_pre"], "stricton" => $host_config["stricton"], "port" => isset($host_config["port"]) ? $host_config["port"] : 0, "createdate" => time()), array("connid" => $connid, "creatorid" => $creatorid, "name" => "_dbface_warehouse"));
                    echo json_encode(array("result" => "ok"));
                    return NULL;
                }
            }
            echo json_encode(array("result" => "fail"));
        }
    }
}

?>