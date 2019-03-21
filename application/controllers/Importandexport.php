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
class Importandexport extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $this->load->library("session");
        $this->load->library("smartyview");
        $dbid = $this->input->get("dbid");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $dbid);
        $tables = $db->list_tables();
        $this->smartyview->assign("dbid", $dbid);
        $this->smartyview->assign("dbs", $db->query("show databases")->result_array());
        $this->smartyview->assign("tables", $tables);
        $this->smartyview->assign("tableselcount", count($tables) < 10 ? count($tables) : 10);
        $this->smartyview->display("importandexport/index.tpl");
    }
    public function export()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $dbid = $this->input->post("dbid");
        if (empty($creatorid) || empty($dbid)) {
            return NULL;
        }
        $this->load->helper("dbface");
        $this->load->library("sqlexport");
        $compression = $this->input->post("compression");
        $tables = $this->input->post("table_select");
        if (!$tables || count($tables) <= 0) {
            $this->load->library("smartyview");
            $this->smartyview->assign("message_css", "js_warning");
            $this->smartyview->assign("message", "You must select one table at least!");
            $this->index();
        } else {
            $db = $this->_get_db($creatorid, $dbid);
            $dbname = $db->database;
            $filename = $dbname . ".sql";
            if ($compression == "bzip") {
                $filename .= ".bz2";
                $mime_type = "application/x-bzip2";
            } else {
                if ($compression == "gzip") {
                    $filename .= ".gz";
                    $mime_type = "application/x-gzip";
                } else {
                    if ($compression == "zip") {
                        $filename .= ".zip";
                        $mime_type = "application/zip";
                    }
                }
            }
            $filepath = APPPATH . "/cache/" . $filename;
            $handle = @fopen($filepath, "w");
            $this->sqlexport->tables = $tables;
            $this->sqlexport->compression = $compression;
            $this->sqlexport->dbname = $dbname;
            $this->sqlexport->filename = $filename;
            $this->sqlexport->filepath = $filepath;
            $this->sqlexport->file_handle = $handle;
            $this->sqlexport->doExport($db);
            @fclose($handle);
            $handle = fopen($filepath, "rb");
            $contents = fread($handle, filesize($filepath));
            fclose($handle);
            unlink($filepath);
            $this->load->helper("download");
            force_download($filename, $contents);
        }
    }
    public function import()
    {
        $this->load->helper("dbface");
        $this->load->library("session");
        $dbid = $this->session->userdata("lastdbid");
        $db = $this->get_db($dbid);
        $compression = $this->input->post("compression");
        $config["upload_path"] = "./application/cache/";
        $this->load->library("upload", $config);
        $import_file_info = false;
        $error = false;
        if (!$this->upload->do_upload("import_file")) {
            $this->load->library("smartyview");
            $this->smartyview->assign("message_css", "js_error");
            $this->smartyview->assign("optype", "import");
            $this->smartyview->assign("message", $this->upload->display_errors());
            $this->index();
            exit;
        }
        $import_file_info = $this->upload->data();
        if ($import_file_info) {
            $compression = PMA_detectCompression($import_file_info["full_path"]);
            if ($compression === false) {
                $message = "File can not be read!";
                exit($message);
            }
            switch ($compression) {
                case "application/bzip2":
                    if (@function_exists("bzopen")) {
                        $import_handle = @bzopen($import_file_info["full_path"], "r");
                    } else {
                        $message = "You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.";
                        $error = true;
                    }
                    break;
                case "application/gzip":
                    if (@function_exists("gzopen")) {
                        $import_handle = @gzopen($import_file_info["full_path"], "r");
                    } else {
                        $message = "You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.";
                        $error = true;
                    }
                    break;
                case "application/zip":
                    if (@function_exists("zip_open")) {
                        $result = PMA_getZipContents($import_file_info["full_path"]);
                        if (!empty($result["error"])) {
                            $message = "ERROR ZIP FORMAT!";
                            $error = true;
                        } else {
                            $import_text = $result["data"];
                        }
                    } else {
                        $message = "You attempted to load file with unsupported compression (%s). Either support for it is not implemented or disabled by your configuration.";
                        $error = true;
                    }
                    break;
                case "none":
                    $import_handle = @fopen($import_file_info["full_path"], "r");
                    break;
                default:
                    $message = PMA_Message::error("strUnsupportedCompressionDetected");
                    $message->addParam($compression);
                    $error = true;
                    break;
            }
            if (!$error && isset($import_handle) && $import_handle === false) {
                $message = PMA_Message::error("strFileCouldNotBeRead");
                $error = true;
            }
        }
        $timeout_passed = false;
        $error = false;
        $read_multiply = 1;
        $finished = false;
        $offset = 0;
        $max_sql_len = 0;
        $file_to_unlink = "";
        $sql_query = "";
        $sql_query_disabled = false;
        $go_sql = false;
        $executed_queries = 0;
        $run_query = true;
        $charset_conversion = false;
        $reset_charset = false;
        $bookmark_created = false;
        $buffer = "";
        $sql = "";
        $start_pos = 0;
        $i = 0;
        $len = 0;
        $big_value = -2147483649.0;
        $delimiter_keyword = "DELIMITER ";
        $length_of_delimiter_keyword = strlen($delimiter_keyword);
        if (isset($_POST["sql_delimiter"])) {
            $sql_delimiter = $_POST["sql_delimiter"];
        } else {
            $sql_delimiter = ";";
        }
        $finished = false;
        switch ($compression) {
            case "application/bzip2":
                $buffer = bzread($import_handle);
                break;
            case "application/gzip":
                $buffer = gzread($import_handle);
                break;
            case "application/zip":
                $buffer = $import_text;
                break;
            case "none":
                $buffer = fread($import_handle, filesize($import_file_info["full_path"]));
                break;
        }
        $len = strlen($buffer);
        $executenum = 0;
        while ($i < $len) {
            $found_delimiter = false;
            $old_i = $i;
            if (preg_match("/('|\"|#|-- |\\/\\*|`|(?i)" . $delimiter_keyword . ")/", $buffer, $matches, PREG_OFFSET_CAPTURE, $i)) {
                $first_position = $matches[1][1];
            } else {
                $first_position = $big_value;
            }
            $first_sql_delimiter = strpos($buffer, $sql_delimiter, $i);
            if ($first_sql_delimiter === false) {
                $first_sql_delimiter = $big_value;
            } else {
                $found_delimiter = true;
            }
            $i = min($first_position, $first_sql_delimiter);
            if ($i == $big_value) {
                $i = $old_i;
                if (!$finished) {
                    break;
                }
                if (trim($buffer) == "") {
                    $buffer = "";
                    $len = 0;
                    break;
                }
                $i = strlen($buffer) - 1;
            }
            $ch = $buffer[$i];
            if (strpos("'\"`", $ch) !== false) {
                $quote = $ch;
                $endq = false;
                while (!$endq) {
                    $pos = strpos($buffer, $quote, $i + 1);
                    if ($pos === false) {
                        if ($finished) {
                            $endq = true;
                            $i = $len - 1;
                        }
                        $found_delimiter = false;
                        break;
                    }
                    for ($j = $pos - 1; $buffer[$j] == "\\"; $j--) {
                    }
                    $endq = ($pos - 1 - $j) % 2 == 0;
                    $i = $pos;
                    if ($first_sql_delimiter < $pos) {
                        $found_delimiter = false;
                    }
                }
                if (!$endq) {
                    break;
                }
                $i++;
                if ($finished && $i == $len) {
                    $i--;
                } else {
                    continue;
                }
            }
            if (($i == $len - 1 && ($ch == "-" || $ch == "/") || $i == $len - 2 && ($ch == "-" && $buffer[$i + 1] == "-" || $ch == "/" && $buffer[$i + 1] == "*")) && !$finished) {
                break;
            }
            if ($ch == "#" || $i < $len - 1 && $ch == "-" && $buffer[$i + 1] == "-" && ($i < $len - 2 && $buffer[$i + 2] <= " " || $i == $len - 1 && $finished) || $i < $len - 1 && $ch == "/" && $buffer[$i + 1] == "*") {
                if ($start_pos != $i) {
                    $sql .= substr($buffer, $start_pos, $i - $start_pos);
                }
                $start_of_comment = $i;
                $i = strpos($buffer, $ch == "/" ? "*/" : "\n", $i);
                if ($i === false) {
                    if ($finished) {
                        $i = $len - 1;
                    } else {
                        break;
                    }
                }
                if ($ch == "/") {
                    $i++;
                }
                $i++;
                $sql .= substr($buffer, $start_of_comment, $i - $start_of_comment);
                $start_pos = $i;
                if ($i == $len) {
                    $i--;
                } else {
                    continue;
                }
            }
            if (strtoupper(substr($buffer, $i, $length_of_delimiter_keyword)) == $delimiter_keyword && $i + $length_of_delimiter_keyword < $len) {
                $new_line_pos = strpos($buffer, "\n", $i + $length_of_delimiter_keyword);
                if (false === $new_line_pos) {
                    $new_line_pos = $len;
                }
                $sql_delimiter = substr($buffer, $i + $length_of_delimiter_keyword, $new_line_pos - $i - $length_of_delimiter_keyword);
                $i = $new_line_pos + 1;
                $start_pos = $i;
                continue;
            }
            if ($found_delimiter || $finished && $i == $len - 1) {
                $tmp_sql = $sql;
                if ($start_pos < $len) {
                    $length_to_grab = $i - $start_pos;
                    if (!$found_delimiter) {
                        $length_to_grab++;
                    }
                    $tmp_sql .= substr($buffer, $start_pos, $length_to_grab);
                    unset($length_to_grab);
                }
                if (!preg_match("/^([\\s]*;)*\$/", trim($tmp_sql))) {
                    $sql = $tmp_sql;
                    $db->query($sql);
                    $executenum++;
                    $buffer = substr($buffer, $i + strlen($sql_delimiter));
                    $len = strlen($buffer);
                    $sql = "";
                    $i = 0;
                    $start_pos = 0;
                    if (strpos($buffer, $sql_delimiter) === false && !$finished) {
                        break;
                    }
                } else {
                    $i++;
                    $start_pos = $i;
                }
            }
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("message_css", "js_success");
        $this->smartyview->assign("optype", "import");
        $this->smartyview->assign("message", (string) $executenum . " queries executed!");
        $this->index();
    }
    public function synchronize()
    {
        $this->load->library("session");
        $dbid = $this->session->userdata("lastdbid");
        $src_db = $this->input->post("src_db");
        $src_db_sel = $this->input->post("src_db_sel");
        $src_host = $this->input->post("src_host");
        $src_pass = $this->input->post("src_pass");
        $src_port = $this->input->post("src_port");
        $src_socket = $this->input->post("src_socket");
        $src_type = $this->input->post("src_type");
        $src_username = $this->input->post("src_username");
        $trg_db = $this->input->post("trg_db");
        $trg_db_sel = $this->input->post("trg_db_sel");
        $trg_host = $this->input->post("trg_host");
        $trg_pass = $this->input->post("trg_pass");
        $trg_port = $this->input->post("trg_port");
        $trg_socket = $this->input->post("trg_socket");
        $trg_type = $this->input->post("trg_type");
        $trg_username = $this->input->post("trg_username");
        $error = false;
        $message = "";
        $this->load->database();
        $dbinfo = $this->db->query("select host, port, dbuser, dbpassword from t_db where dbid = ?", array($dbid))->row_array();
        if ($src_type == "rmt") {
            $src_conn = $this->load->database(array("hostname" => $src_host . !empty($src_port) ? ":" . $src_port : "", "username" => $src_username, "password" => $src_pass, "database" => $src_db, "dbdriver" => "mysql"), true, true);
            if ($src_conn->conn_id === false) {
                $message .= "Can not connecto to the source database.<br/>";
                $error = true;
            }
        } else {
            $src_conn = $this->load->database(array("hostname" => $dbinfo["host"], "username" => $dbinfo["dbuser"], "password" => $dbinfo["dbpassword"], "database" => $src_db_sel, "dbdriver" => "mysql"), true, true);
        }
        if ($trg_type == "rmt") {
            $trg_conn = $this->load->database(array("hostname" => $trg_host . !empty($trg_port) ? ":" . $trg_port : "", "username" => $trg_username, "password" => $trg_pass, "database" => $trg_db, "dbdriver" => "mysql"), true, true);
            if ($trg_conn->conn_id === false) {
                $message .= "Can not connecto to the target database.<br/>";
                $error = true;
            }
        } else {
            $trg_conn = $this->load->database(array("hostname" => $dbinfo["host"], "username" => $dbinfo["dbuser"], "password" => $dbinfo["dbpassword"], "database" => $trg_db_sel, "dbdriver" => "mysql"), true, true);
        }
        $this->load->library("smartyview");
        if ($error) {
            $this->smartyview->assign("optype", "synchronize");
            $this->smartyview->assign("error", true);
            $this->smartyview->assign("message", $message);
            $this->index();
        } else {
            if ($src_type == "rmt") {
                $this->smartyview->assign("src_db", $src_db);
            } else {
                $this->smartyview->assign("src_db", $src_db_sel);
            }
            $this->smartyview->assign("src_host", $src_host . !empty($src_port) ? ":" . $src_port : "");
            $this->smartyview->assign("src_type", $src_type);
            $this->smartyview->assign("src_username", $src_username);
            $this->smartyview->assign("src_pass", $src_pass);
            if ($trg_type == "rmt") {
                $this->smartyview->assign("trg_db", $trg_db);
            } else {
                $this->smartyview->assign("trg_db", $trg_db_sel);
            }
            $this->smartyview->assign("trg_host", $trg_host . !empty($trg_port) ? ":" . $trg_port : "");
            $this->smartyview->assign("trg_type", $trg_type);
            $this->smartyview->assign("trg_username", $trg_username);
            $this->smartyview->assign("trg_pass", $trg_pass);
            $src_tbls = $src_conn->list_tables();
            $trg_tbls = $trg_conn->list_tables();
            $trg_exists = array();
            foreach ($src_tbls as $src_tbl) {
                $trg_exists[] = in_array($src_tbl, $trg_tbls);
            }
            $this->smartyview->assign("trg_exists", $trg_exists);
            $this->smartyview->assign("src_tbls", $src_tbls);
            $this->smartyview->display("confirmsynchronize.tpl");
        }
    }
    public function confirmsyn()
    {
        $this->load->helper("dbface");
        $this->load->library("session");
        $dbid = $this->session->userdata("lastdbid");
        $src_db = $this->input->post("src_db");
        $src_host = $this->input->post("src_host");
        $src_pass = $this->input->post("src_pass");
        $src_type = $this->input->post("src_type");
        $src_username = $this->input->post("src_username");
        $trg_db = $this->input->post("trg_db");
        $trg_host = $this->input->post("trg_host");
        $trg_pass = $this->input->post("trg_pass");
        $trg_type = $this->input->post("trg_type");
        $trg_username = $this->input->post("trg_username");
        $onlystructure = $this->input->post("onlystructure") == "1";
        $error = false;
        $message = "";
        $this->load->database();
        $dbinfo = $this->db->query("select host, port, dbname, dbuser, dbpassword from t_db where dbid = ?", array($dbid))->row_array();
        if ($src_type == "rmt") {
            $src_conn = $this->load->database(array("hostname" => $src_host, "username" => $src_username, "password" => $src_pass, "database" => $src_db, "dbdriver" => "mysql"), true, true);
        } else {
            $src_conn = $this->load->database(array("hostname" => $dbinfo["host"], "username" => $dbinfo["dbuser"], "password" => $dbinfo["dbpassword"], "database" => $src_db, "dbdriver" => "mysql"), true, true);
        }
        if ($trg_type == "rmt") {
            $trg_conn = $this->load->database(array("hostname" => $trg_host, "username" => $trg_username, "password" => $trg_pass, "database" => $trg_db, "dbdriver" => "mysql"), true, true);
        } else {
            $trg_conn = $this->load->database(array("hostname" => $dbinfo["host"], "username" => $dbinfo["dbuser"], "password" => $dbinfo["dbpassword"], "database" => $trg_db, "dbdriver" => "mysql"), true, true);
        }
        $this->load->library("sqlexport");
        $this->sqlexport->dbname = $src_db;
        $chk_tbls = $this->input->post("chk_tbls");
        foreach ($chk_tbls as $chk_tbl) {
            $tableDef = $this->sqlexport->getTableDef($src_conn, $chk_tbl, "", "");
            $sqls = explode(";", $tableDef);
            foreach ($sqls as $sql) {
                $trg_conn->query($sql);
                if (!$onlystructure) {
                    $local_query = "SELECT * FROM " . PMA_backquote($src_db) . "." . PMA_backquote($chk_tbl);
                    $result = $src_conn->query($local_query);
                    $line_buf = get_insert_string($result, $chk_tbl);
                    foreach ($line_buf as $line) {
                        $trg_conn->query($line);
                    }
                }
            }
        }
        echo "The selected tables have synchronized!";
    }
}

?>