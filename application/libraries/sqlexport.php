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
class Sqlexport
{
    public $sql_relation = false;
    public $sql_comments = false;
    public $sql_mime = false;
    public $sql_data = true;
    public $sql_dates = true;
    public $sql_backquotes = true;
    public $sql_header_comment = true;
    public $sql_use_transaction = false;
    public $sql_disable_fk = false;
    public $time_start = NULL;
    public $dump_buffer = NULL;
    public $dump_buffer_len = NULL;
    public $save_filename = NULL;
    public $buffer_needed = true;
    public $onfly_compression = true;
    public $memory_limit = 102400;
    public $compression = NULL;
    public $save_on_server = true;
    public $filename = NULL;
    public $filepath = NULL;
    public $file_handle = NULL;
    public $asfile = NULL;
    public $output_charset_conversion = NULL;
    public $charset = NULL;
    public $charset_of_file = NULL;
    public $tables = NULL;
    public $views = NULL;
    public $sql_structure = true;
    public $version = "";
    public $crlf = "\n";
    public $strHost = "";
    public $strGenTime = "";
    public $sql_compatibility = "NONE";
    public $dbname = "classicmodels";
    public $err_url = "";
    public $export_type = "sql";
    public $sql_auto_increment = true;
    public $sql_type = "INSERT";
    public $sql_delayed = false;
    public $sql_ignore = false;
    public $sql_columns = true;
    public $sql_drop_table = true;
    public $cfgRelation = NULL;
    public $sql_constraints = NULL;
    public $sql_constraints_query = NULL;
    public $sql_if_not_exists = true;
    public $sql_extended = true;
    public $sql_max_query_size = 20;
    public $sql_procedure_function = true;
    public function __construct()
    {
        $this->dump_buffer = "";
        $this->dump_buffer_len = 0;
    }
    public function doExport($db)
    {
        while (!$this->_exportHeader()) {
            break;
        }
        $do_relation = $this->sql_relation;
        $do_comments = $this->sql_comments;
        $do_mime = $this->sql_mime;
        $do_dates = $this->sql_dates;
        if (!$this->_exportDBHeader($this->dbname)) {
            break;
        }
        $i = 0;
        $views = array();
        foreach ($this->tables as $table) {
            $is_view = PMA_isView($db, $this->dbname, $table);
            if ($is_view) {
                $views[] = $table;
            }
            if (!$this->sql_structure || !$this->_exportStructure($db, $table, $this->crlf, $this->err_url, $do_relation, $do_comments, $do_mime, $do_dates, $is_view ? "stand_in" : "create_table", $this->export_type)) {
            }
            if ($this->sql_data && !$is_view) {
                $local_query = "SELECT * FROM " . PMA_backquote($this->dbname) . "." . PMA_backquote($table);
                if (!$this->_exportData($db, $table, $this->crlf, $this->err_url, $local_query)) {
                }
            }
            if ($this->sql_structure && !$this->_exportStructure($db, $table, $this->crlf, $this->err_url, $do_relation, $do_comments, $do_mime, $do_dates, "triggers", $this->export_type)) {
            }
        }
        foreach ($views as $view) {
            if ($this->sql_structure && !$this->_exportStructure($db, $view, $this->crlf, $this->err_url, $do_relation, $do_comments, $do_mime, $do_dates, "create_view", $export_type)) {
            }
        }
        if (!$this->_exportDBFooter($db)) {
            break;
        }
        if (!$this->_exportFooter()) {
            break;
        }
        if (!false) {
            if ($this->compression == "zip" && function_exists("gzcompress")) {
                @fclose($this->file_handle);
                $this->file_handle = fopen($this->filepath, "r");
                $contents = fread($this->file_handle, filesize($this->filepath));
                fclose($this->file_handle);
                unlink($this->filepath);
                $CI =& get_instance();
                $CI->load->library("Zip");
                $CI->zip->add_data(substr($this->filename, 0, -4), $contents);
                $CI->zip->archive($this->filepath);
            }
        }
    }
    /**
     * Outputs database header
     *
     * @param   string      Database name
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function _exportDBHeader($db)
    {
        $head = $this->_exportComment() . $this->_exportComment("Database: " . ($this->sql_backquotes ? PMA_backquote($db) : "'" . $db . "'")) . $this->_exportComment();
        return $this->_exportOutputHandler($head);
    }
    /**
     * Dispatches between the versions of 'getTableContent' to use depending
     * on the php version
     *
     * @param   string      the database name
     * @param   string      the table name
     * @param   string      the end of line sequence
     * @param   string      the url to go back in case of error
     * @param   string      SQL query for obtaining data
     *
     * @return  bool        Whether it suceeded
     *
     * @global  boolean  whether to use backquotes to allow the use of special
     *                   characters in database, table and fields names or not
     * @global  integer  the number of records
     * @global  integer  the current record position
     *
     * @access  public
     *
     * @see     PMA_getTableContentFast(), PMA_getTableContentOld()
     *
     * @author  staybyte
     */
    public function _exportData($db, $table, $crlf, $error_url, $sql_query)
    {
        $formatted_table_name = $this->sql_backquotes ? PMA_backquote($table) : "'" . $table . "'";
        $head = $this->_possibleCRLF() . $this->_exportComment() . $this->_exportComment("Dumping data for table" . " " . $formatted_table_name) . $this->_exportComment();
        if (!$this->_exportOutputHandler($head)) {
            return false;
        }
        $buffer = "";
        $result = $db->query($sql_query);
        $error = $db->error();
        $tmp_error = $error["message"];
        if ($tmp_error) {
            return $this->_exportOutputHandler($this->_exportComment("in use" . " (" . $tmp_error . ")"));
        }
        if ($result != false) {
            if (!$this->_exportOutputHandler($crlf)) {
                return false;
            }
            $fields_cnt = $result->num_fields();
            $fields_meta = $result->field_data();
            $field_flags = array();
            for ($j = 0; $j < $fields_cnt; $j++) {
            }
            for ($j = 0; $j < $fields_cnt; $j++) {
                $field_set[$j] = PMA_backquote($fields_meta[$j]->name, $this->sql_backquotes);
            }
            if ($this->sql_type == "REPLACE") {
                $sql_command = "REPLACE";
            } else {
                $sql_command = "INSERT";
            }
            if ($this->sql_delayed) {
                $insert_delayed = " DELAYED";
            } else {
                $insert_delayed = "";
            }
            if ($this->sql_type == "INSERT" && $this->sql_ignore) {
                $insert_delayed .= " IGNORE";
            }
            if ($this->sql_columns) {
                $fields = implode(", ", $field_set);
                $schema_insert = $sql_command . $insert_delayed . " INTO " . PMA_backquote($table, $this->sql_backquotes) . " (" . $fields . ") VALUES";
            } else {
                $schema_insert = $sql_command . $insert_delayed . " INTO " . PMA_backquote($table, $this->sql_backquotes) . " VALUES";
            }
            $search = array("", "\n", "\r", "\32");
            $replace = array("\\0", "\\n", "\\r", "\\Z");
            $current_row = 0;
            $query_size = 0;
            if ($this->sql_extended && $this->sql_type != "UPDATE") {
                $separator = ",";
                $schema_insert .= $crlf;
            } else {
                $separator = ";";
            }
            $data = $result->result_array();
            foreach ($data as $row) {
                $current_row++;
                for ($j = 0; $j < $fields_cnt; $j++) {
                    if (!isset($row[$fields_meta[$j]->name]) || is_null($row[$fields_meta[$j]->name])) {
                        $values[] = "NULL";
                    } else {
                        $values[] = "'" . str_replace($search, $replace, PMA_sqlAddslashes($row[$fields_meta[$j]->name])) . "'";
                    }
                }
                if ($this->sql_extended) {
                    if ($current_row == 1) {
                        $insert_line = $schema_insert . "(" . implode(", ", $values) . ")";
                    } else {
                        $insert_line = "(" . implode(", ", $values) . ")";
                        if (0 < $this->sql_max_query_size && $this->sql_max_query_size < $query_size + strlen($insert_line)) {
                            if (!$this->_exportOutputHandler(";" . $crlf)) {
                                return false;
                            }
                            $query_size = 0;
                            $current_row = 1;
                            $insert_line = $schema_insert . $insert_line;
                        }
                    }
                    $query_size += strlen($insert_line);
                } else {
                    $insert_line = $schema_insert . "(" . implode(", ", $values) . ")";
                }
                unset($values);
                if (!$this->_exportOutputHandler(($current_row == 1 ? "" : $separator . $crlf) . $insert_line)) {
                    return false;
                }
            }
            if (0 < $current_row && !$this->_exportOutputHandler(";" . $crlf)) {
                return false;
            }
        }
        $result->free_result();
        return true;
    }
    /**
     * Output handler for all exports, if needed buffering, it stores data into
     * $dump_buffer, otherwise it prints thems out.
     *
     * @param   string  the insert statement
     *
     * @return  bool    Whether output suceeded
     */
    public function _exportOutputHandler($line)
    {
        if ($this->buffer_needed) {
            $this->dump_buffer .= $line;
            if ($this->onfly_compression) {
                $this->dump_buffer_len += strlen($line);
                if ($this->output_charset_conversion) {
                    $dump_buffer = PMA_convert_string($this->charset, $this->charset_of_file, $dump_buffer);
                }
                if ($this->compression == "bzip" && @function_exists("bzcompress")) {
                    $this->dump_buffer = bzcompress($this->dump_buffer);
                } else {
                    if ($this->compression == "gzip" && @function_exists("gzencode")) {
                        $this->dump_buffer = gzencode($this->dump_buffer);
                    }
                }
                if ($this->save_on_server) {
                    $write_result = @fwrite($this->file_handle, $this->dump_buffer);
                    if (!$write_result || $write_result != strlen($this->dump_buffer)) {
                        return false;
                    }
                } else {
                    echo $this->dump_buffer;
                }
                $this->dump_buffer = "";
                $this->dump_buffer_len = 0;
            } else {
                $time_now = time();
                if ($time_now + 30 <= $time_start) {
                    $time_start = $time_now;
                    header("X-pmaPing: Pong");
                }
            }
        }
        return true;
    }
    /**
     * Possibly outputs comment
     *
     * @param   string      Text of comment
     *
     * @return  string      The formatted comment
     */
    public function _exportComment($text = "")
    {
        return "--" . (empty($text) ? "" : " ") . $text . $this->crlf;
    }
    /**
     * Possibly outputs CRLF
     *
     * @return  string  $crlf or nothing 
     */
    public function _possibleCRLF()
    {
        return $this->crlf;
    }
    /**
     * Outputs export footer
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function _exportFooter()
    {
        $foot = "";
        if ($this->sql_disable_fk) {
            $foot .= "SET FOREIGN_KEY_CHECKS=1;" . $this->crlf;
        }
        if ($this->sql_use_transaction) {
            $foot .= "COMMIT;" . $this->crlf;
        }
        $charset_of_file = isset($this->charset_of_file) ? $this->charset_of_file : "";
        if (!empty($this->asfile)) {
            $foot .= $this->crlf . "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;" . $this->crlf . "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;" . $this->crlf . "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;" . $this->crlf;
        }
        return $this->_exportOutputHandler($foot);
    }
    /**
     * Outputs export header
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function _exportHeader()
    {
        global $cfg;
        global $mysql_charset_map;
        $head = $this->_exportComment("DbFace SQL Dump") . $this->_exportComment("version " . $this->version) . $this->_exportComment("https://www.dbface.com/") . $this->_exportComment();
        $head .= $this->_exportComment($this->strHost);
        $head .= $this->_exportComment($this->strGenTime) . $this->_possibleCRLF();
        if ($this->sql_header_comment) {
            $lines = explode("\\n", $this->sql_header_comment);
            $head .= $this->_exportComment();
            foreach ($lines as $one_line) {
                $head .= $this->_exportComment($one_line);
            }
            $head .= $this->_exportComment();
        }
        if ($this->sql_disable_fk) {
            $head .= "SET FOREIGN_KEY_CHECKS=0;" . $this->crlf;
        }
        if ($this->sql_compatibility == "NONE") {
            $head .= "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";" . $this->crlf;
        }
        if ($this->sql_use_transaction) {
            $head .= "SET AUTOCOMMIT=0;" . $this->crlf . "START TRANSACTION;" . $this->crlf;
        }
        $head .= $this->_possibleCRLF();
        if (!$this->asfile) {
            $set_names = $this->charset;
            $head .= $this->crlf . "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;" . $this->crlf . "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;" . $this->crlf . "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;" . $this->crlf . "/*!40101 SET NAMES " . $set_names . " */;" . $this->crlf . $this->crlf;
        }
        return $this->_exportOutputHandler($head);
    }
    /**
     * Outputs CREATE DATABASE database
     *
     * @param   string      Database name
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function _exportDBCreate($db)
    {
        global $crlf;
        if (isset($GLOBALS["sql_drop_database"]) && !$this->_exportOutputHandler("DROP DATABASE " . (isset($GLOBALS["sql_backquotes"]) ? PMA_backquote($db) : $db) . ";" . $crlf)) {
            return false;
        }
        $create_query = "CREATE DATABASE " . (isset($GLOBALS["sql_backquotes"]) ? PMA_backquote($db) : $db);
        $collation = PMA_getDbCollation($db);
        if (strpos($collation, "_")) {
            $create_query .= " DEFAULT CHARACTER SET " . substr($collation, 0, strpos($collation, "_")) . " COLLATE " . $collation;
        } else {
            $create_query .= " DEFAULT CHARACTER SET " . $collation;
        }
        $create_query .= ";" . $crlf;
        if (!$this->_exportOutputHandler($create_query)) {
            return false;
        }
        if (isset($GLOBALS["sql_backquotes"]) && isset($GLOBALS["sql_compatibility"]) && $GLOBALS["sql_compatibility"] == "NONE") {
            return $this->_exportOutputHandler("USE " . PMA_backquote($db) . ";" . $crlf);
        }
        return $this->_exportOutputHandler("USE " . $db . ";" . $crlf);
    }
    /**
     * Outputs database footer
     *
     * @param   string      Database name
     *
     * @return  bool        Whether it suceeded
     *
     * @access  public
     */
    public function _exportDBFooter($db)
    {
        $result = true;
        if ($this->sql_constraints) {
            $result = $this->_exportOutputHandler($this->sql_constraints);
            unset($this->sql_constraints);
        }
        return $result;
    }
    /**
     * Returns a stand-in CREATE definition to resolve view dependencies
     *
     * @param   string   the database name
     * @param   string   the vew name
     * @param   string   the end of line sequence
     *
     * @return  string   resulting definition
     *
     * @access  public
     */
    public function _getTableDefStandIn($db, $view, $crlf)
    {
        $create_query = "";
        if (!empty($GLOBALS["sql_drop_table"])) {
            $create_query .= "DROP VIEW IF EXISTS " . PMA_backquote($view) . ";" . $crlf;
        }
        $create_query .= "CREATE TABLE ";
        if ($this->sql_if_not_exists) {
            $create_query .= "IF NOT EXISTS ";
        }
        $create_query .= PMA_backquote($view) . " (" . $crlf;
        $tmp = array();
        $columns = PMA_DBI_get_columns_full($db, $view);
        foreach ($columns as $column_name => $definition) {
            $tmp[] = PMA_backquote($column_name) . " " . $definition["Type"] . $crlf;
        }
        $create_query .= implode(",", $tmp) . ");";
        return $create_query;
    }
    /**
     * Returns $table's CREATE definition
     *
     * @param   string   the database name
     * @param   string   the table name
     * @param   string   the end of line sequence
     * @param   string   the url to go back in case of error
     * @param   boolean  whether to include creation/update/check dates
     * @param   boolean  whether to add semicolon and end-of-line at the end
     * @param   boolean  whether we're handling view
     *
     * @return  string   resulting schema
     *
     * @global  boolean  whether to add 'drop' statements or not
     * @global  boolean  whether to use backquotes to allow the use of special
     *                   characters in database, table and fields names or not
     *
     * @access  public
     */
    public function getTableDef($db, $table, $crlf, $error_url, $show_dates = false, $add_semicolon = true, $view = false)
    {
        $schema_create = "";
        $auto_increment = "";
        $new_crlf = $crlf;
        $result = $db->query("SHOW TABLE STATUS FROM " . PMA_backquote($this->dbname) . " LIKE '" . PMA_sqlAddslashes($table) . "'");
        if (0 < $result->num_rows()) {
            $tmpres = $result->row_array();
            if ($this->sql_auto_increment && !empty($tmpres["Auto_increment"])) {
                $auto_increment .= " AUTO_INCREMENT=" . $tmpres["Auto_increment"] . " ";
            }
            $result->free_result();
        }
        $schema_create .= $new_crlf;
        if (!empty($this->sql_drop_table) && !PMA_isView($db, $this->dbname, $table)) {
            $schema_create .= "DROP TABLE IF EXISTS " . PMA_backquote($table, $this->sql_backquotes) . ";" . $crlf;
        }
        if ($this->sql_backquotes) {
            $db->query("SET SQL_QUOTE_SHOW_CREATE = 1");
        } else {
            $db->query("SET SQL_QUOTE_SHOW_CREATE = 0");
        }
        $result = $db->query("SHOW CREATE TABLE " . PMA_backquote($this->dbname) . "." . PMA_backquote($table));
        $error = $db->error();
        $tmp_error = $error["message"];
        if ($tmp_error) {
            return $this->_exportComment("in use" . "(" . $tmp_error . ")");
        }
        if ($result != false && ($row = $result->row_array())) {
            $create_query = $row["Create Table"];
            unset($row);
            if (strpos($create_query, "(\r\n ")) {
                $create_query = str_replace("\r\n", $crlf, $create_query);
            } else {
                if (strpos($create_query, "(\n ")) {
                    $create_query = str_replace("\n", $crlf, $create_query);
                } else {
                    if (strpos($create_query, "(\r ")) {
                        $create_query = str_replace("\r", $crlf, $create_query);
                    }
                }
            }
            if ($view) {
                $create_query = preg_replace("/" . PMA_backquote($this->dbname) . "\\./", "", $create_query);
            }
            if (isset($GLOBALS["sql_if_not_exists"])) {
                $create_query = preg_replace("/^CREATE TABLE/", "CREATE TABLE IF NOT EXISTS", $create_query);
            }
            if (preg_match("@CONSTRAINT|FOREIGN[\\s]+KEY@", $create_query)) {
                $sql_lines = explode($crlf, $create_query);
                $sql_count = count($sql_lines);
                for ($i = 0; $i < $sql_count; $i++) {
                    if (preg_match("@^[\\s]*(CONSTRAINT|FOREIGN[\\s]+KEY)@", $sql_lines[$i])) {
                        break;
                    }
                }
                if ($i != $sql_count) {
                    $sql_lines[$i - 1] = preg_replace("@,\$@", "", $sql_lines[$i - 1]);
                    if (!isset($sql_constraints)) {
                        if (isset($GLOBALS["no_constraints_comments"])) {
                            $sql_constraints = "";
                        } else {
                            $sql_constraints = $crlf . $this->_exportComment() . $this->_exportComment("Constraints for dumped tables") . $this->_exportComment();
                        }
                    }
                    if (!isset($GLOBALS["no_constraints_comments"])) {
                        $sql_constraints .= $crlf . $this->_exportComment() . $this->_exportComment("Constraints for table" . " " . PMA_backquote($table)) . $this->_exportComment();
                    }
                    $sql_constraints_query .= "ALTER TABLE " . PMA_backquote($table) . $crlf;
                    $sql_constraints .= "ALTER TABLE " . PMA_backquote($table) . $crlf;
                    $first = true;
                    $j = $i;
                    while ($j < $sql_count) {
                        if (preg_match("@CONSTRAINT|FOREIGN[\\s]+KEY@", $sql_lines[$j])) {
                            if (!$first) {
                                $sql_constraints .= $crlf;
                            }
                            if (strpos($sql_lines[$j], "CONSTRAINT") === false) {
                                $str_tmp = preg_replace("/(FOREIGN[\\s]+KEY)/", "ADD \\1", $sql_lines[$j]);
                                $sql_constraints_query .= $str_tmp;
                                $sql_constraints .= $str_tmp;
                            } else {
                                $str_tmp = preg_replace("/(CONSTRAINT)/", "ADD \\1", $sql_lines[$j]);
                                $sql_constraints_query .= $str_tmp;
                                $sql_constraints .= $str_tmp;
                            }
                            $first = false;
                            $j++;
                        } else {
                            break;
                        }
                    }
                    $sql_constraints .= ";" . $crlf;
                    $sql_constraints_query .= ";";
                    $create_query = implode($crlf, array_slice($sql_lines, 0, $i)) . $crlf . implode($crlf, array_slice($sql_lines, $j, $sql_count - 1));
                    unset($sql_lines);
                }
            }
            $schema_create .= $create_query;
        }
        $schema_create = preg_replace("/AUTO_INCREMENT\\s*=\\s*([0-9])+/", "", $schema_create);
        $schema_create .= $auto_increment;
        $result->free_result();
        return $schema_create . ($add_semicolon ? ";" . $crlf : "");
    }
    /**
     * Returns $table's comments, relations etc.
     *
     * @param   string   the database name
     * @param   string   the table name
     * @param   string   the end of line sequence
     * @param   boolean  whether to include relation comments
     * @param   boolean  whether to include mime comments
     *
     * @return  string   resulting comments
     *
     * @access  public
     */
    public function _getTableComments($db, $table, $crlf, $do_relation = false, $do_mime = false)
    {
        global $cfgRelation;
        global $sql_backquotes;
        global $sql_constraints;
        $schema_create = "";
        if ($do_relation && !empty($cfgRelation["relation"])) {
            $res_rel = PMA_getForeigners($db, $table);
            if ($res_rel && 0 < count($res_rel)) {
                $have_rel = true;
            } else {
                $have_rel = false;
            }
        } else {
            $have_rel = false;
        }
        if ($do_mime && $cfgRelation["mimework"] && !($mime_map = PMA_getMIME($db, $table, true))) {
            unset($mime_map);
        }
        if (isset($mime_map) && 0 < count($mime_map)) {
            $schema_create .= $this->_possibleCRLF() . $this->_exportComment() . $this->_exportComment("MIME TYPES FOR TABLE" . " " . PMA_backquote($table, $sql_backquotes) . ":");
            @reset($mime_map);
            foreach ($mime_map as $mime_field => $mime) {
                $schema_create .= $this->_exportComment("  " . PMA_backquote($mime_field, $sql_backquotes)) . $this->_exportComment("      " . PMA_backquote($mime["mimetype"], $sql_backquotes));
            }
            $schema_create .= $this->_exportComment();
        }
        if ($have_rel) {
            $schema_create .= $this->_possibleCRLF() . $this->_exportComment() . $this->_exportComment($GLOBALS["strRelationsForTable"] . " " . PMA_backquote($table, $sql_backquotes) . ":");
            foreach ($res_rel as $rel_field => $rel) {
                $schema_create .= $this->_exportComment("  " . PMA_backquote($rel_field, $sql_backquotes)) . $this->_exportComment("      " . PMA_backquote($rel["foreign_table"], $sql_backquotes) . " -> " . PMA_backquote($rel["foreign_field"], $sql_backquotes));
            }
            $schema_create .= $this->_exportComment();
        }
        return $schema_create;
    }
    /**
     * Outputs table's structure
     *
     * @param   string   the database name
     * @param   string   the table name
     * @param   string   the end of line sequence
     * @param   string   the url to go back in case of error
     * @param   boolean  whether to include relation comments
     * @param   boolean  whether to include the pmadb-style column comments
     *                   as comments in the structure; this is deprecated
     *                   but the parameter is left here because export.php
     *                   calls PMA_exportStructure() also for other export
     *                   types which use this parameter
     * @param   boolean  whether to include mime comments
     * @param   string   'stand_in', 'create_table', 'create_view'
     * @param   string   'server', 'database', 'table'
     *
     * @return  bool     Whether it suceeded
     *
     * @access  public
     */
    public function _exportStructure($db, $table, $crlf, $error_url, $relation = false, $comments = false, $mime = false, $dates = false, $export_mode, $export_type)
    {
        $formatted_table_name = $this->sql_backquotes ? PMA_backquote($table) : "'" . $table . "'";
        $dump = $this->_possibleCRLF() . $this->_exportComment(str_repeat("-", 56)) . $this->_possibleCRLF() . $this->_exportComment();
        switch ($export_mode) {
            case "create_table":
                $dump .= $this->_exportComment("Table structure for table" . " " . $formatted_table_name) . $this->_exportComment();
                $dump .= $this->getTableDef($db, $table, $crlf, $error_url, $dates);
                $dump .= $this->_getTableComments($db, $table, $crlf, $relation, $mime);
                break;
            case "create_view":
                $dump .= $this->_exportComment("Structure for view" . " " . $formatted_table_name) . $this->_exportComment();
                if ($export_type != "table") {
                    $dump .= "DROP TABLE IF EXISTS " . PMA_backquote($table) . ";" . $crlf;
                }
                $dump .= $this->getTableDef($db, $table, $crlf, $error_url, $dates, true, true);
                break;
            case "stand_in":
                $dump .= $this->_exportComment("Stand-in structure for view" . " " . $formatted_table_name) . $this->_exportComment();
                $dump .= $this->_getTableDefStandIn($db, $table, $crlf);
        }
        unset($this->sql_constraints_query);
        return $this->_exportOutputHandler($dump);
    }
}

?>