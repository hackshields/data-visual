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
if (!function_exists("directory_copy")) {
    /**
     * Copy a whole Directory
     *
     * Copy a directory recrusively ( all file and directories inside it )
     *
     * @access    public
     * @param    string    path to source dir
     * @param    string    path to destination dir
     * @return    array
     */
    function directory_copy($srcdir, $dstdir)
    {
        $srcdir = rtrim($srcdir, "/");
        $dstdir = rtrim($dstdir, "/");
        if (!is_dir($dstdir)) {
            mkdir($dstdir, 511, true);
        }
        $dir_map = directory_map($srcdir);
        foreach ($dir_map as $object_key => $object_value) {
            if (is_numeric($object_key)) {
                copy($srcdir . "/" . $object_value, $dstdir . "/" . $object_value);
            } else {
                directory_copy($srcdir . "/" . $object_key, $dstdir . "/" . $object_key);
            }
        }
    }
}
function get_file_type(&$data)
{
    $strInfo = @unpack("c2chars", $data);
    $typeCode = intval($strInfo["chars1"] . $strInfo["chars2"]);
    $fileType = "";
    switch ($typeCode) {
        case 7790:
            $fileType = "exe";
            break;
        case 7784:
            $fileType = "midi";
            break;
        case 8297:
            $fileType = "rar";
            break;
        case 255216:
            $fileType = "jpg";
            break;
        case 7173:
            $fileType = "gif";
            break;
        case 6677:
            $fileType = "bmp";
            break;
        case 13780:
            $fileType = "png";
            break;
        default:
            $fileType = "txt";
    }
    return $fileType;
}
function get_array_value($arr, $idx, $default_v = false)
{
    if ($arr && isset($arr[$idx]) && $arr[$idx] != "null" && !empty($arr[$idx])) {
        return $arr[$idx];
    }
    return $default_v;
}
function is_supported_chart($type)
{
    return $type == "linechart" || $type == "scatterplot" || $type == "areachart" || $type == "barchart" || $type == "columnchart" || $type == "combinedbarlinechart" || $type == "piechart" || $type == "funnel" || $type == "gauges" || $type == "bulletchart" || $type == "treemap" || $type == "wordcloud" || $type == "chartjsonapp" || $type == "radar" || $type == "googlemap";
}
function convert_delimiter($val)
{
    if ($val == 0) {
        return ";";
    }
    if ($val == 1) {
        return ",";
    }
    if ($val == 2) {
        return "\t";
    }
    return ";";
}
function convert_linedelimiter($val)
{
    return "\n";
}
function convert_enclosure($val)
{
    if ($val == 0) {
        return "\"";
    }
    if ($val == 1) {
        return "'";
    }
    if ($val == 2) {
        return "";
    }
    return "\"";
}
function get_table_lookup_query($db, $default)
{
    if (function_exists("api_get_table_lookup_query")) {
        $result = call_user_func_array("api_get_table_lookup_query", array($db));
        if ($result && is_string($result)) {
            return $result;
        }
    }
    return $default;
}
function _sort_result_tables(&$tables)
{
    if (!is_array($tables)) {
        return $tables;
    }
    if (function_exists("sort_tables")) {
        return call_user_func("sort_tables", $tables);
    }
    return $tables;
}
function list_attached_db_tables($db)
{
    $remote_type = $db->remote_type;
    $clz = $remote_type . "_db";
    if (!file_exists(APPPATH . "/libraries/" . $clz . ".php")) {
        dbface_log("error", "remote type not supported");
        return array();
    }
    require_once APPPATH . "/libraries/" . $clz . ".php";
    try {
        $remoted_db = new $clz($db->attached_config);
        $collections = $remoted_db->list_tables();
        return $collections;
    } catch (Exception $e) {
        dbface_log("error", "list_remote_tables failed: " . $e->getMessage(), $e->getTrace());
        return array();
    }
}
function list_dynamodb_collections($dynamodb_config)
{
    require_once APPPATH . "/libraries/DynamoDb.php";
    try {
        $dynamodb = new DynamoDb($dynamodb_config);
        $collections = $dynamodb->listCollections();
        return $collections;
    } catch (Exception $e) {
        dbface_log("error", "list_dynamodb_collections failed: " . $e->getMessage(), $e->getTrace());
        return array();
    }
}
function list_mongodb_collections($mongo_config)
{
    require_once APPPATH . "/libraries/Mongo_db.php";
    try {
        $mongo_db = new Mongo_db($mongo_config);
        $collections = $mongo_db->listCollections();
        return $collections;
    } catch (Exception $e) {
        dbface_log("error", "list_mongodb_collections failed: " . $e->getMessage(), $e->getTrace());
        return array();
    }
}
function list_tables($db, $only_sql = false)
{
    if (!$db) {
        dbface_log("error", "list_tables on invalid database connection");
        return array();
    }
    if (function_exists("api_table_lookup")) {
        $result = call_user_func("api_table_lookup", $db->dbdriver, $db->subdriver);
        if ($result && is_string($result)) {
            $query = $db->query($result);
            $retval = array();
            if ($query && 0 < $query->num_rows()) {
                $result = $query->result_array();
                foreach ($result as $row) {
                    $row = array_change_key_case($row, CASE_UPPER);
                    $retval[] = $row["TABLE_SCHEMA"] . "." . $row["TABLE_NAME"];
                }
            }
            return _sort_result_tables($retval);
        }
    }
    $retval = array();
    if (isset($db->mongodb_config) && !$only_sql) {
        $retval = list_mongodb_collections($db->mongodb_config);
    }
    if (isset($db->dynamodb_config) && !$only_sql) {
        $retval = list_dynamodb_collections($db->dynamodb_config);
    }
    if (isset($db->attached_config) && !$only_sql) {
        $retval = list_attached_db_tables($db);
    }
    if ($db->dbdriver == "pdo" && $db->subdriver == "sqlsrv") {
        $sql = get_table_lookup_query($db, "SELECT [TABLE_CATALOG],[TABLE_SCHEMA],[TABLE_NAME],[TABLE_TYPE] FROM [INFORMATION_SCHEMA].[TABLES] WHERE TABLE_TYPE='BASE TABLE' OR TABLE_TYPE='VIEW' ORDER BY TABLE_SCHEMA");
        $query = $db->query($sql);
        if ($query && 0 < $query->num_rows()) {
            $result = $query->result_array();
            foreach ($result as $row) {
                $retval[] = $row["TABLE_SCHEMA"] . "." . $row["TABLE_NAME"];
            }
        }
        return _sort_result_tables($retval);
    } else {
        if ($db->dbdriver == "oci" || $db->dbdriver == "oci8") {
            $query = $db->query("select TABLE_NAME from all_tables where owner = ?", array(strtoupper($db->username)));
            if ($query && 0 < $query->num_rows()) {
                $result = $query->result_array();
                foreach ($result as $row) {
                    $retval[] = $row["TABLE_NAME"];
                }
            }
            return _sort_result_tables($retval);
        } else {
            if ($db->dbdriver == "odbc") {
                $result = odbc_tables($db->conn_id);
                while (odbc_fetch_row($result)) {
                    if (odbc_result($result, "TABLE_TYPE") == "TABLE") {
                        $retval[] = odbc_result($result, "TABLE_NAME");
                    }
                }
                return _sort_result_tables($retval);
            }
            try {
                $tables = @$db->list_tables();
                $tables = array_merge($retval, $tables);
                return _sort_result_tables($tables);
            } catch (Exception $e) {
                return array();
            }
        }
    }
}
function _set_primary_keys_sqlsrv($db, $tablename, $schema, &$field_data)
{
    if ($db->dbdriver == "pdo" && $db->subdriver == "sqlsrv") {
        $query = $db->query("EXEC sp_pkeys @table_name = ?, @table_owner= ?", array($tablename, $schema));
        $result = $query->result_array();
        $keys = array();
        foreach ($result as $row) {
            $keys[] = $row["COLUMN_NAME"];
        }
        foreach ($field_data as &$field) {
            if (in_array($field->name, $keys)) {
                $field->primary_key = 1;
            }
        }
    }
}
function _set_primary_keys_odbc($db, $tablename, &$field_data)
{
    if ($db->dbdriver == "odbc") {
        $rs = odbc_primarykeys($db->conn_id, "", "", $tablename);
        if ($rs) {
            $result = odbc_result_all($rs);
            if ($result) {
                $keys = array();
                while (odbc_fetch_row($result)) {
                    $keys[] = odbc_result($result, "COLUMN_NAME");
                }
                foreach ($field_data as &$field) {
                    if (in_array($field->name, $keys)) {
                        $field->primary_key = 1;
                    }
                }
            }
        }
    }
}
function _set_primary_keys_firebird($db, $tablename, &$field_data)
{
    if ($db->dbdriver == "pdo" && $db->subdriver == "firebird") {
        $query = $db->query("Select s.rdb\$field_name as COLUMN_NAME From rdb\$index_segments s LEFT JOIN rdb\$relation_constraints rc ON (rc.rdb\$index_name = s.rdb\$index_name) Where rc.rdb\$relation_name = ? and rc.rdb\$constraint_type = ?", array($tablename, "PRIMARY KEY"));
        $result = $query->result_array();
        $keys = array();
        foreach ($result as $row) {
            $keys[] = $row["COLUMN_NAME"];
        }
        foreach ($field_data as &$field) {
            if (in_array($field->name, $keys)) {
                $field->primary_key = 1;
            }
        }
    }
}
function _set_primary_keys_oracle($db, $tablename, &$field_data)
{
    if ($db->dbdriver == "oci" || $db->dbdriver == "oci8") {
        $query = $db->query("SELECT cols.table_name, cols.column_name, cols.position, cons.status, cons.owner FROM all_constraints cons, all_cons_columns cols WHERE cols.table_name = ? AND cons.constraint_type = 'P' AND cons.constraint_name = cols.constraint_name AND cons.owner = cols.owner ORDER BY cols.table_name, cols.position", array($tablename));
        $result = $query->result_array();
        $keys = array();
        foreach ($result as $row) {
            $keys[] = $row["COLUMN_NAME"];
        }
        foreach ($field_data as &$field) {
            if (in_array($field->name, $keys)) {
                $field->primary_key = 1;
            }
        }
    }
}
function _set_primary_keys_pgsql($db, $table, &$field_data)
{
    $query = $db->query("SELECT pg_attribute.attname, format_type(pg_attribute.atttypid, pg_attribute.atttypmod) FROM pg_index, pg_class, pg_attribute, pg_namespace WHERE pg_class.oid = '" . $table . "'::regclass AND indrelid = pg_class.oid AND nspname = 'public' AND pg_class.relnamespace = pg_namespace.oid AND pg_attribute.attrelid = pg_class.oid AND pg_attribute.attnum = any(pg_index.indkey) AND indisprimary");
    $result = $query->result_array();
    $keys = array();
    foreach ($result as $row) {
        $keys[] = $row["attname"];
    }
    foreach ($field_data as &$field) {
        if (in_array($field->name, $keys)) {
            $field->primary_key = 1;
        }
    }
}
function field_data($db, $table)
{
    $field_data = false;
    if ($db->dbdriver == "pdo" && $db->subdriver == "sqlsrv") {
        list($schema, $tablename) = explode(".", $table);
        $field_data = $db->field_data($tablename);
        _set_primary_keys_sqlsrv($db, $tablename, $schema, $field_data);
    } else {
        if ($db->dbdriver == "pdo" && $db->subdriver == "pgsql") {
            $field_data = $db->field_data($table);
            _set_primary_keys_pgsql($db, $table, $field_data);
        } else {
            if ($db->dbdriver == "oci" || $db->dbdriver == "oci8") {
                $field_data = $db->field_data($table);
                _set_primary_keys_oracle($db, $table, $field_data);
            } else {
                if ($db->dbdriver == "pdo" && $db->subdriver == "firebird") {
                    $field_data = $db->field_data($table);
                    _set_primary_keys_firebird($db, $table, $field_data);
                } else {
                    if ($db->dbdriver == "odbc") {
                        $field_data = $db->field_data($table);
                        _set_primary_keys_odbc($db, $table, $field_data);
                    } else {
                        $field_data = $db->field_data($table);
                    }
                }
            }
        }
    }
    return $field_data;
}
function table_exists($db, $table)
{
    return $db->table_exists($table);
}
function list_fields($db, $table)
{
    if (!$db) {
        return array();
    }
    if (isset($db->mongodb_config)) {
        require_once APPPATH . "/libraries/Mongo_db.php";
        try {
            $mongo_db = new Mongo_db($db->mongodb_config);
            return $mongo_db->get_sample_fields($table);
        } catch (Exception $e) {
            dbface_log("error", "mongodb#get_sample_fields fail:" . $e->getMessage());
        }
        return array();
    }
    if ($db->dbdriver == "sqlite3") {
        $sql = "SELECT * FROM " . $table . " LIMIT 1";
        $query = $db->query($sql);
        $retval = array();
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $keys = array_keys($row);
            foreach ($keys as $val) {
                if (!is_int($val)) {
                    $retval[] = $val;
                }
            }
        }
        return $retval;
    } else {
        if ($db->dbdriver == "odbc") {
            $retval = array();
            if ($result = odbc_exec($db->conn_id, "select * from " . $table)) {
                for ($i = 1; $i <= odbc_num_fields($result); $i++) {
                    $retval[] = odbc_field_name($result, $i);
                }
                odbc_free_result($result);
            }
            return $retval;
        }
        return $db->list_fields($table);
    }
}
function dp_gi()
{
    return gi();
}
function convert_sql_type($sqltype)
{
    if ($sqltype == 253) {
        return "string";
    }
    if ($sqltype == 246 || $sqltype == 3 || $sqltype == 8) {
        return "integer";
    }
    return $sqltype;
}
function get_preferred_language()
{
    $langs = array();
    if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
        preg_match_all("/([a-z]{1,8}(-[a-z]{1,8})?)\\s*(;\\s*q\\s*=\\s*(1|0\\.[0-9]+))?/i", $_SERVER["HTTP_ACCEPT_LANGUAGE"], $lang_parse);
        if (count($lang_parse[1])) {
            $langs = array_combine($lang_parse[1], $lang_parse[4]);
            foreach ($langs as $lang => $val) {
                if ($val === "") {
                    $langs[$lang] = 1;
                }
            }
            arsort($langs, SORT_NUMERIC);
        }
    }
    foreach ($langs as $lang => $val) {
        if (stristr($lang, "-")) {
            $tmp = explode("-", $lang);
            $lang = $tmp[0];
        }
        if ($lang == "zh" || $lang == "zh-cn") {
            return "zh-CN";
        }
        return "english";
    }
    return "english";
}
function get_db_datalength($db, $dbname)
{
    $result = $db->query("select sum(`DATA_LENGTH`) as datalength from information_schema.`TABLES` where `TABLE_SCHEMA`='" . $dbname . "'")->row_array();
    if ($result) {
        return $result["datalength"] ? $result["datalength"] : 0;
    }
    return "0";
}
function get_mysql_engines($db, $support = "YES")
{
    $engines = $db->query("show engines")->result_array();
    return $engines;
}
/**
 * returns array of all tables in given db or dbs
 * this function expects unquoted names:
 * RIGHT: my_database
 * WRONG: `my_database`
 * WRONG: my\_database
 * if $tbl_is_group is true, $table is used as filter for table names
 * if $tbl_is_group is 'comment, $table is used as filter for table comments
 *
 * <code>
 * PMA_DBI_get_tables_full('my_database');
 * PMA_DBI_get_tables_full('my_database', 'my_table'));
 * PMA_DBI_get_tables_full('my_database', 'my_tables_', true));
 * PMA_DBI_get_tables_full('my_database', 'my_tables_', 'comment'));
 * </code>
 *
 * @todo    move into PMA_Table
 * @uses    PMA_DBI_fetch_result()
 * @uses    PMA_escape_mysql_wildcards()
 * @uses    PMA_backquote()
 * @uses    is_array()
 * @uses    addslashes()
 * @uses    strpos()
 * @uses    strtoupper()
 * @param   string          $databases      database
 * @param   string          $table          table
 * @param   boolean|string  $tbl_is_group   $table is a table group
 * @param   resource        $link           mysql link
 * @param   integer         $limit_offset   zero-based offset for the count
 * @param   boolean|integer $limit_count    number of tables to return
 * @param   string          $sort_by        table attribute to sort by
 * @param   string          $sort_order     direction to sort (ASC or DESC)
 * @return  array           list of tables in given db(s)
 */
function get_tables_full($database, $table = false, $tbl_is_group = false, $link = NULL, $sort_by = "Name", $sort_order = "ASC")
{
    $tables = array();
    if (!$link) {
        return $tables;
    }
    if (true === $tbl_is_group) {
        $sql = "SHOW TABLE STATUS FROM " . PMA_backquote($database) . " LIKE '" . PMA_escape_mysql_wildcards(addslashes($table)) . "%'";
    } else {
        $sql = "SHOW TABLE STATUS FROM " . PMA_backquote($database);
    }
    $query = $link->query($sql);
    if (!$query) {
        return $tables;
    }
    $each_tables = $query->result_array();
    $sort_keys = array();
    foreach ($each_tables as $each_table) {
        $table_name = $each_table["Name"];
        $table = array();
        if (!isset($each_table["Type"]) && isset($each_table["Engine"])) {
            $table["Type"] = $each_table["Engine"];
        } else {
            if (!isset($each_table["Engine"]) && isset($each_table["Type"])) {
                $table["Engine"] = $each_table["Type"];
            }
        }
        $table["TABLE_SCHEMA"] = $database;
        $table["TABLE_NAME"] = $each_table["Name"];
        $table["ENGINE"] = $each_table["Engine"];
        $table["VERSION"] = $each_table["Version"];
        $table["ROW_FORMAT"] = $each_table["Row_format"];
        $table["TABLE_ROWS"] = $each_table["Rows"];
        $table["AVG_ROW_LENGTH"] = $each_table["Avg_row_length"];
        $table["DATA_LENGTH"] = formatBytes($each_table["Data_length"]);
        $table["MAX_DATA_LENGTH"] = $each_table["Max_data_length"];
        $table["INDEX_LENGTH"] = $each_table["Index_length"];
        $table["DATA_FREE"] = $each_table["Data_free"];
        $table["AUTO_INCREMENT"] = $each_table["Auto_increment"];
        $table["CREATE_TIME"] = $each_table["Create_time"];
        $table["UPDATE_TIME"] = $each_table["Update_time"];
        $table["CHECK_TIME"] = $each_table["Check_time"];
        $table["TABLE_COLLATION"] = $each_table["Collation"];
        $table["CHECKSUM"] = $each_table["Checksum"];
        $table["CREATE_OPTIONS"] = $each_table["Create_options"];
        $table["TABLE_COMMENT"] = $each_table["Comment"];
        if (strtoupper($each_table["Comment"]) === "VIEW" && $each_table["Engine"] == NULL) {
            $table["TABLE_TYPE"] = "VIEW";
        } else {
            $table["TABLE_TYPE"] = "BASE TABLE";
        }
        array_push($tables, $table);
    }
    return $tables;
}
function PMA_backquote($a_name, $do_it = true)
{
    if (!$do_it) {
        return $a_name;
    }
    if (is_array($a_name)) {
        $result = array();
        foreach ($a_name as $key => $val) {
            $result[$key] = PMA_backquote($val);
        }
        return $result;
    } else {
        if (strlen($a_name) && $a_name !== "*") {
            return "`" . str_replace("`", "``", $a_name) . "`";
        }
        return $a_name;
    }
}
function PMA_escape_mysql_wildcards($name)
{
    $name = str_replace("_", "\\_", $name);
    $name = str_replace("%", "\\%", $name);
    return $name;
}
function get_fields($db, $table)
{
    $fields = $db->query("SHOW FULL FIELDS FROM " . pma_backquote($table))->result_array();
    return $fields;
}
function get_field($db, $table, $fieldname)
{
    $fields = $db->query("SHOW FULL FIELDS FROM " . pma_backquote($table) . " like '" . $fieldname . "'")->row_array();
    return $fields;
}
function get_index($db, $table, $index = false)
{
    $_raw_index = $db->query("SHOW INDEX FROM " . pma_backquote($table))->result_array();
    if ($index) {
        $retIndex = array();
        foreach ($_raw_index as $aindex) {
            if ($aindex["Key_name"] == $index) {
                $retIndex["Key_name"] = $aindex["Key_name"];
                $retIndex["Non_unique"] = $aindex["Non_unique"];
                $retIndex["Packed"] = $aindex["Packed"];
                $retIndex["Comment"] = $aindex["Comment"];
                $retIndex["Index_type"] = $aindex["Index_type"];
                if (!isset($retIndex["Fields"])) {
                    $retIndex["Fields"] = array();
                }
                $fieldinfo = array();
                $fieldinfo["Column_name"] = $aindex["Column_name"];
                $fieldinfo["Collation"] = $aindex["Collation"];
                $fieldinfo["Cardinality"] = $aindex["Cardinality"];
                $fieldinfo["Sub_part"] = $aindex["Sub_part"];
                $fieldinfo["Null"] = $aindex["Null"];
                $retIndex["Fields"][] = $fieldinfo;
            }
        }
        return $retIndex;
    } else {
        $allindex = array();
        foreach ($_raw_index as $aindex) {
            $key_name = $aindex["Key_name"];
            if (!isset($allindex[$key_name])) {
                $allindex[$key_name] = array();
            }
            $allindex[$key_name]["Key_name"] = $aindex["Key_name"];
            $allindex[$key_name]["Non_unique"] = $aindex["Non_unique"];
            $allindex[$key_name]["Packed"] = $aindex["Packed"];
            $allindex[$key_name]["Comment"] = $aindex["Comment"];
            $allindex[$key_name]["Index_type"] = $aindex["Index_type"];
            if (!isset($allindex[$key_name]["Fields"])) {
                $allindex[$key_name]["Fields"] = array();
            }
            $fieldinfo = array();
            $fieldinfo["Column_name"] = $aindex["Column_name"];
            $fieldinfo["Collation"] = $aindex["Collation"];
            $fieldinfo["Cardinality"] = $aindex["Cardinality"];
            $fieldinfo["Sub_part"] = $aindex["Sub_part"];
            $fieldinfo["Null"] = $aindex["Null"];
            $allindex[$key_name]["Fields"][] = $fieldinfo;
        }
        $t = array();
        foreach ($allindex as $key => $value) {
            $a = $value;
            $a["fieldnum"] = count($value["Fields"]);
            $t[] = $a;
        }
        return $t;
    }
}
function get_primary($db, $table)
{
    $_raw_index = get_index($db, $table);
    foreach ($_raw_index as $_each_index) {
        if ($_each_index["Key_name"] == "PRIMARY") {
            return $_each_index;
        }
    }
    return false;
}
/**
 * @see PMA_Table::generateFieldSpec()
 */
function generateAlter($oldcol, $newcol, $type, $length, $attribute, $collation, $null, $default_type, $default_value, $extra, $comment = "", &$field_primary, $index, $default_orig)
{
    return pma_backquote($oldcol) . " " . generateFieldSpec($newcol, $type, $length, $attribute, $collation, $null, $default_type, $default_value, $extra, $comment, $field_primary, $index, $default_orig);
}
/**
 * generates column/field specification for ALTER or CREATE TABLE syntax
 *
 * @todo    move into class PMA_Column
 * @todo on the interface, some js to clear the default value when the default
 * current_timestamp is checked
 * @static
 * @param   string  $name       name
 * @param   string  $type       type ('INT', 'VARCHAR', 'BIT', ...)
 * @param   string  $length     length ('2', '5,2', '', ...)
 * @param   string  $attribute
 * @param   string  $collation
 * @param   string  $null       with 'NULL' or 'NOT NULL'
 * @param   string  $default_type   whether default is CURRENT_TIMESTAMP,
 *                                  NULL, NONE, USER_DEFINED
 * @param   boolean $default_value  default value for USER_DEFINED default type
 * @param   string  $extra      'AUTO_INCREMENT'
 * @param   string  $comment    field comment
 * @param   array   &$field_primary list of fields for PRIMARY KEY
 * @param   string  $index
 * @return  string  field specification
 */
function generateFieldSpec($name, $type, $length = "", $attribute = "", $collation = "", $null = false, $default_type = "USER_DEFINED", $default_value = "", $extra = "", $comment = "", &$field_primary, $index, $default_orig = false)
{
    $is_timestamp = strpos(" " . strtoupper($type), "TIMESTAMP") == 1;
    $query = pma_backquote($name) . " " . $type;
    if ($length != "" && !preg_match("@^(DATE|DATETIME|TIME|TINYBLOB|TINYTEXT|BLOB|TEXT|MEDIUMBLOB|MEDIUMTEXT|LONGBLOB|LONGTEXT)\$@i", $type)) {
        $query .= "(" . $length . ")";
    }
    if ($attribute != "") {
        $query .= " " . $attribute;
    }
    if (!empty($collation) && $collation != "NULL" && preg_match("@^(TINYTEXT|TEXT|MEDIUMTEXT|LONGTEXT|VARCHAR|CHAR|ENUM|SET)\$@i", $type)) {
        $query .= PMA_generateCharsetQueryPart($collation);
    }
    if ($null !== false) {
        if ($null == "NULL") {
            $query .= " NULL";
        } else {
            $query .= " NOT NULL";
        }
    }
    switch ($default_type) {
        case "USER_DEFINED":
            if ($is_timestamp && $default_value === "0") {
                $query .= " DEFAULT 0";
            } else {
                if ($type == "BIT") {
                    $query .= " DEFAULT b'" . preg_replace("/[^01]/", "0", $default_value) . "'";
                } else {
                    $query .= " DEFAULT '" . PMA_sqlAddslashes($default_value) . "'";
                }
            }
            break;
        case "NULL":
        case "CURRENT_TIMESTAMP":
            $query .= " DEFAULT " . $default_type;
            break;
        case "NONE":
        default:
            break;
    }
    if (!empty($extra)) {
        $query .= " " . $extra;
        if ($extra == "AUTO_INCREMENT") {
            $primary_cnt = count($field_primary);
            if (1 == $primary_cnt) {
                for ($j = 0; $j < $primary_cnt && $field_primary[$j] != $index; $j++) {
                }
                if (isset($field_primary[$j]) && $field_primary[$j] == $index) {
                    $query .= " PRIMARY KEY";
                    unset($field_primary[$j]);
                }
            } else {
                $found_in_pk = false;
                for ($j = 0; $j < $primary_cnt; $j++) {
                    if ($field_primary[$j] == $index) {
                        $found_in_pk = true;
                        break;
                    }
                }
                if (!$found_in_pk) {
                    $field_primary[] = $index;
                }
            }
        }
    }
    if (!empty($comment)) {
        $query .= " COMMENT '" . PMA_sqlAddslashes($comment) . "'";
    }
    return $query;
}
function PMA_generateCharsetQueryPart($collation)
{
    list($charset) = explode("_", $collation);
    return " CHARACTER SET " . $charset . ($charset == $collation ? "" : " COLLATE " . $collation);
}
/**
 * Add slashes before "'" and "\" characters so a value containing them can
 * be used in a sql comparison.
 *
 * @uses    str_replace()
 * @param   string   the string to slash
 * @param   boolean  whether the string will be used in a 'LIKE' clause
 *                   (it then requires two more escaped sequences) or not
 * @param   boolean  whether to treat cr/lfs as escape-worthy entities
 *                   (converts \n to \\n, \r to \\r)
 *
 * @param   boolean  whether this function is used as part of the
 *                   "Create PHP code" dialog
 *
 * @return  string   the slashed string
 *
 * @access  public
 */
function PMA_sqlAddslashes($a_string = "", $is_like = false, $crlf = false, $php_code = false)
{
    if ($is_like) {
        $a_string = str_replace("\\", "\\\\\\\\", $a_string);
    } else {
        $a_string = str_replace("\\", "\\\\", $a_string);
    }
    if ($crlf) {
        $a_string = str_replace("\n", "\\n", $a_string);
        $a_string = str_replace("\r", "\\r", $a_string);
        $a_string = str_replace("\t", "\\t", $a_string);
    }
    if ($php_code) {
        $a_string = str_replace("'", "\\'", $a_string);
    } else {
        $a_string = str_replace("'", "''", $a_string);
    }
    return $a_string;
}
/**
 * Extracts the various parts from a field type spec
 *
 * @uses    strpos()
 * @uses    chop()
 * @uses    substr()
 * @param   string $fieldspec
 * @return  array associative array containing type, spec_in_brackets
 *          and possibly enum_set_values (another array)
 * @author  Marc Delisle
 * @author  Joshua Hogendorn
 */
function DF_extractFieldSpec($fieldspec)
{
    $first_bracket_pos = strpos($fieldspec, "(");
    if ($first_bracket_pos) {
        $spec_in_brackets = rtrim(substr($fieldspec, $first_bracket_pos + 1, strrpos($fieldspec, ")") - $first_bracket_pos - 1));
        $type = strtolower(rtrim(substr($fieldspec, 0, $first_bracket_pos)));
    } else {
        $type = $fieldspec;
        $spec_in_brackets = "";
    }
    if ("enum" == $type || "set" == $type) {
        $enum_set_values = array();
        $working = "";
        $in_string = false;
        for ($index = 0; isset($fieldspec[$index]); $index++) {
            $char = $fieldspec[$index];
            if ($char == "'") {
                if (!$in_string) {
                    $in_string = true;
                    $working = "";
                } else {
                    $has_next = isset($fieldspec[$index + 1]);
                    $next = $has_next ? $fieldspec[$index + 1] : NULL;
                    if (!$has_next || $next != "'") {
                        $enum_set_values[] = $working;
                        $in_string = false;
                    } else {
                        if ($next == "'") {
                            $working .= "'";
                            $index++;
                        }
                    }
                }
            } else {
                if ("\\" == $char && isset($fieldspec[$index + 1]) && "'" == $fieldspec[$index + 1]) {
                    $working .= "'";
                    $index++;
                } else {
                    $working .= $char;
                }
            }
        }
    } else {
        $enum_set_values = array();
    }
    return array("type" => $type, "spec_in_brackets" => $spec_in_brackets, "enum_set_values" => $enum_set_values);
}
function get_mysql_collation($db)
{
    $collations = $db->query("show collation")->result_array();
    $mysql_collations = array();
    foreach ($collations as $row) {
        if (!isset($mysql_collations[$row["Charset"]]) || !is_array($mysql_collations[$row["Charset"]])) {
            $mysql_collations[$row["Charset"]] = array($row["Collation"]);
        } else {
            $mysql_collations[$row["Charset"]][] = $row["Collation"];
        }
    }
    unset($collations);
    unset($row);
    return $mysql_collations;
}
function get_data_types($db)
{
    if ($db->dbdriver == "sqlite3" || $db->dbdriver == "pdo" && $db->subdriver == "sqlite") {
        return array("INTEGER", "REAL", "TEXT", "BLOB", "NUMERIC", "BOOLEAN", "DATETIME");
    }
    return array();
}
function get_url_base()
{
    $PHP_SELF = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["ORIG_PATH_INFO"]);
    $PHP_SELF = str_replace("index.php", "", $PHP_SELF);
    $PHP_SELF = str_replace("//", "/", $PHP_SELF);
    $PHP_DOMAIN = $_SERVER["SERVER_NAME"];
    $PHP_REFERER = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";
    $PHP_SCHEME = $_SERVER["SERVER_PORT"] == "443" ? "https://" : "http://";
    $PHP_PORT = $_SERVER["SERVER_PORT"] == "80" ? "" : ":" . $_SERVER["SERVER_PORT"];
    $CUR_PHP_URL = $PHP_SCHEME . $PHP_DOMAIN . $PHP_PORT . $PHP_SELF;
    return $CUR_PHP_URL;
}
function insert_batch($db, $table, &$insert_array)
{
    if ($db->dbdriver == "mysqli") {
        return $db->insert_batch($table, $insert_array);
    }
    $db->trans_begin();
    foreach ($insert_array as $row) {
        $db->insert($table, $row);
    }
    return $db->trans_complete();
}
function parse_json_data($string)
{
    if (empty($string)) {
        return $string;
    }
    $array = json_decode($string, true);
    if (json_last_error() == JSON_ERROR_NONE) {
        return $array;
    }
    return $string;
}
function colorname_to_rgb($colorname, $default = "#333")
{
    $CI =& get_instance();
    $CI->config->load("colormap", true);
    $color_map = $CI->config->item("colormap");
    $colors = $color_map["colormap"];
    return $colorname;
}
function name_to_fontfamily($name, $bold, $italic)
{
    if (name == "Anton") {
    }
    return $name;
}
function encrypt_text($plaintext, $type)
{
    $CI =& get_instance();
    $settings = $CI->config->item($type);
    $CI->load->library("encryption", $settings);
    $ciphertext = $CI->encryption->encrypt($plaintext);
    return $ciphertext;
}
function decrypt_text($ciphertext, $type)
{
    $CI =& get_instance();
    $settings = $CI->config->item($type);
    $CI->load->library("encryption", $settings);
    $ori_text = $CI->encryption->decrypt($ciphertext);
    return $ori_text;
}
function DF_escape_for_db($name)
{
    $name = str_replace(".", "_", $name);
    $name = str_replace("%", "_", $name);
    $name = str_replace(" ", "_", $name);
    return $name;
}
function quick_check_json($str)
{
    if (preg_match("/^\\d+\$/", trim($str))) {
        return true;
    }
    return false;
}
function get_color_skin($skin)
{
    $colorskin = "blue";
    if ($skin == "skin-colortic-light-blue") {
        $colorskin = "blue";
    } else {
        if ($skin == "skin-colortic-pink") {
            $colorskin = "pink";
        } else {
            if ($skin == "skin-colortic-green") {
                $colorskin = "green";
            } else {
                if ($skin == "skin-colortic-light-green") {
                    $colorskin = "green";
                } else {
                    if ($skin == "skin-colortic-blue") {
                        $colorskin = "blue";
                    } else {
                        if ($skin == "skin-colortic-yellow") {
                            $colorskin = "yellow";
                        } else {
                            if ($skin == "skin-colortic-red") {
                                $colorskin = "red";
                            } else {
                                if ($skin == "skin-colortic-purple") {
                                    $colorskin = "purple";
                                } else {
                                    if ($skin == "skin-colortic-dark-green") {
                                        $colorskin = "green";
                                    } else {
                                        if ($skin == "skin-colortic-gray") {
                                            $colorskin = "black";
                                        } else {
                                            if ($skin == "skin-colortic-orange") {
                                                $colorskin = "orange";
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
    return $colorskin;
}
function string_to_boolean($val)
{
    $val = strtolower($val);
    if ($val == "true" || $val == "1") {
        return true;
    }
    return false;
}
function sanitized_filename($filename)
{
    $sanitized = preg_replace("/[^a-zA-Z0-9\\-\\._]/", "", $filename);
    return $sanitized;
}
function apache_module_exists($module)
{
    if (function_exists("apache_get_modules")) {
        return in_array($module, apache_get_modules());
    }
    return true;
}
function module_rewrite_enabled()
{
    return function_exists("apache_module_exists") && apache_module_exists("mod_rewrite");
}
function tag_in_array($tag, $available_tags, $def = false)
{
    if (empty($tag) || empty($available_tags)) {
        return $def;
    }
    return in_array($tag, explode(",", $available_tags));
}
function save_base64_image($base64_data, $save_path)
{
    $data = base64_decode(preg_replace("#^data:image/\\w+;base64,#i", "", $base64_data));
    $CI =& get_instance();
    $CI->load->helper("file");
    write_file($save_path, $data);
}
function remove_dot_segments($input)
{
    $output = "";
    while (strpos($input, "./") !== false || strpos($input, "/.") !== false || $input === "." || $input === "..") {
        if (strpos($input, "../") === 0) {
            $input = substr($input, 3);
        } else {
            if (strpos($input, "./") === 0) {
                $input = substr($input, 2);
            } else {
                if (strpos($input, "/./") === 0) {
                    $input = substr($input, 2);
                } else {
                    if ($input === "/.") {
                        $input = "/";
                    } else {
                        if (strpos($input, "/../") === 0) {
                            $input = substr($input, 3);
                            $output = substr_replace($output, "", strrpos($output, "/"));
                        } else {
                            if ($input === "/..") {
                                $input = "/";
                                $output = substr_replace($output, "", strrpos($output, "/"));
                            } else {
                                if ($input === "." || $input === "..") {
                                    $input = "";
                                } else {
                                    if (($pos = strpos($input, "/", 1)) !== false) {
                                        $output .= substr($input, 0, $pos);
                                        $input = substr_replace($input, "", 0, $pos);
                                    } else {
                                        $output .= $input;
                                        $input = "";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $output . $input;
}
function run_cloud_code($file)
{
    $file = remove_dot_segments($file);
    $CI =& get_instance();
    $creatorid = $CI->session->userdata("login_creatorid");
    if (empty($creatorid)) {
        echo "Permission Denied!";
    } else {
        if (substr($file, 0 - strlen(".php") !== ".php")) {
            $file = $file . ".php";
        }
        $file_path = "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $file;
        if (file_exists($file_path)) {
            require $file_path;
        }
    }
}
function get_mongodb_client($param)
{
    require_once APPPATH . "third_party/mongo-php-library/vendor/autoload.php";
    $hostname = $param["hostname"];
    $port = $param["port"];
    $dns = "mongodb://" . $hostname . ":" . $port;
    if (isset($param["database"])) {
        $database = $param["database"];
        $dns .= "/" . $database;
    }
    if (isset($param["no_auth"]) && $param["no_auth"] == true) {
        $options = array();
    } else {
        $options = array("username" => $param["username"], "password" => $param["password"]);
    }
    $db = new MongoDB\Client($dns, $options);
    return $db;
}
function call_http_service($url, $params = array(), $method = "POST", $ignore_error = false)
{
    dbface_log("info", "call http service invoked: " . $url);
    require_once APPPATH . "third_party/guzzle/autoloader.php";
    try {
        $client = new GuzzleHttp\Client();
        if (isset($params["json"]) || isset($params["body"])) {
            $response = $client->request($method, $url, $params);
        } else {
            $response = $client->request($method, $url, array("form_params" => $params));
        }
        $code = $response->getStatusCode();
        $body = $response->getBody();
        dbface_log("info", "http service result: code: " . $code);
        return $body;
    } catch (GuzzleHttp\Exception\RequestException $e) {
        dbface_log("error", "Call Http URL Request Exception: " . $url . ", target server response: " . $e->getResponse());
        if ($ignore_error) {
        } else {
            if ($e->hasResponse()) {
                echo $e->getResponse();
            }
        }
    }
}
function parse_url_parameter($db, $url, $cached, $last_update, $creatorid, $name)
{
    parse_str($url, $arr);
    $ttl = 3600;
    if (isset($arr["ttl"])) {
        $ttl = $arr["ttl"];
    } else {
        $CI =& get_instance();
        $ttl = $CI->config->item("default_parameter_ttl");
    }
    if (!empty($cached) && ($ttl == 0 || time() - $last_update < $ttl)) {
        return $cached;
    }
    require_once APPPATH . "third_party/guzzle/autoloader.php";
    try {
        $client = new GuzzleHttp\Client();
        $response = $client->request("GET", $url, array("verify" => false));
        $code = $response->getStatusCode();
        if ($code == 200) {
            $cached = $response->getBody();
            $db->update("dc_parameter", array("lastupdate" => time(), "cached" => $cached), array("connid" => "0", "creatorid" => $creatorid, "name" => $name));
        }
    } catch (GuzzleHttp\Exception\RequestException $e) {
    }
    return $cached;
}
function PMA_isView()
{
    return false;
}
function __($str)
{
    return $str;
}
/**
 * Writes localised date
 *
 * @param string $timestamp the current timestamp
 * @param string $format    format
 *
 * @return  string   the formatted date
 *
 * @access  public
 */
function PMA_localisedDate($timestamp = -1, $format = "")
{
    $month = array(__("Jan"), __("Feb"), __("Mar"), __("Apr"), _pgettext("Short month name", "May"), __("Jun"), __("Jul"), __("Aug"), __("Sep"), __("Oct"), __("Nov"), __("Dec"));
    $day_of_week = array(_pgettext("Short week day name", "Sun"), __("Mon"), __("Tue"), __("Wed"), __("Thu"), __("Fri"), __("Sat"));
    if ($format == "") {
        $format = __("%B %d, %Y at %I:%M %p");
    }
    if ($timestamp == -1) {
        $timestamp = time();
    }
    $date = preg_replace("@%[aA]@", $day_of_week[(int) strftime("%w", $timestamp)], $format);
    $date = preg_replace("@%[bB]@", $month[(int) strftime("%m", $timestamp) - 1], $date);
    return strftime($date, $timestamp);
}
function _authcode($string, $operation = "DECODE", $key = "", $expiry = 0)
{
    $ckey_length = 4;
    $key = md5($key ? $key : AUTH_CODE_INTERNAL);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? $operation == "DECODE" ? substr($string, 0, $ckey_length) : substr(md5(microtime()), 0 - $ckey_length) : "";
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    $string = $operation == "DECODE" ? base64_decode(substr($string, $ckey_length)) : sprintf("%010d", $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = "";
    $box = range(0, 255);
    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ $box[($box[$a] + $box[$j]) % 256]);
    }
    if ($operation == "DECODE") {
        if ((substr($result, 0, 10) == 0 || 0 < substr($result, 0, 10) - time()) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        }
        return "";
    }
    return $keyc . str_replace("=", "", base64_encode($result));
}
function _get_mapped_datatype_mysqli_to_sqlite3($from_datatype)
{
    switch ($from_datatype) {
        case MYSQLI_TYPE_TINY:
        case MYSQLI_TYPE_SHORT:
        case MYSQLI_TYPE_LONG:
        case MYSQLI_TYPE_INT24:
        case MYSQLI_TYPE_LONGLONG:
        case MYSQLI_TYPE_TIMESTAMP:
            return "INTEGER";
        case MYSQLI_TYPE_FLOAT:
        case MYSQLI_TYPE_DOUBLE:
            return "REAL";
        case MYSQLI_TYPE_VAR_STRING:
        case MYSQLI_TYPE_STRING:
        case MYSQLI_TYPE_CHAR:
            return "TEXT";
        case MYSQLI_TYPE_TINY_BLOB:
        case MYSQLI_TYPE_BLOB:
        case MYSQLI_TYPE_LONG_BLOB:
            return "BLOG";
    }
    return "TEXT";
}
function get_mapped_datatype($fromdriver, $from_datatype, $todriver)
{
    dbface_log("info", "from driver: " . $fromdriver . ", to driver: " . $todriver . ", datatype: " . $from_datatype);
    $func = "_get_mapped_datatype_" . $fromdriver . "_to_" . $todriver;
    if (function_exists($func)) {
        return call_user_func($func, $from_datatype);
    }
    return "TEXT";
}
function save_parse_object($clsName, $params = array())
{
    try {
        $CI =& get_instance();
        $enable_remote_log = $CI->config->item("enable_remote_log");
        if ($enable_remote_log == true) {
            $CI->load->library("DbFaceParseLog");
            return $CI->dbfaceparselog->save_object($clsName, $params);
        }
    } catch (Exception $e) {
    }
    return false;
}
function get_db_default_port($dbdriver)
{
    if ($dbdriver == "mysqli") {
        return 3306;
    }
    if ($dbdriver == "mongodb") {
        return 27017;
    }
    if ($dbdriver == "pgsql") {
        return 5432;
    }
    if ($dbdriver == "sqlsrv") {
        return 1433;
    }
    return 0;
}
function get_one_parse_object($clsName, $params = array())
{
    try {
        $CI =& get_instance();
        $enable_remote_log = $CI->config->item("enable_remote_log");
        if ($enable_remote_log == true) {
            $CI->load->library("DbFaceParseLog");
            return $CI->dbfaceparselog->get_one_object($clsName, $params);
        }
    } catch (Exception $e) {
    }
    return false;
}
function mongo_object_to_string($v)
{
    if (is_string($v)) {
        return (string) $v;
    }
    if (is_array($v)) {
        return json_encode($v);
    }
    if (is_object($v)) {
        $clsName = get_class($v);
        if ($clsName == "MongoDB\\Model\\BSONDocument" || $clsName == "MongoDB\\Model\\BSONArray") {
            $size = call_user_func(array($v, "count"));
            return "{ " . $size . " fields }";
        }
        if ($clsName == "MongoDB\\BSON\\Binary") {
            $data = $v->serialize();
            $file_type = get_file_type($data);
            return "{ " . $file_type . " }";
        }
        if (method_exists($v, "__toString")) {
            return (string) $v;
        }
        return serialize($v);
    }
    return $v;
}
function html_template_container($name)
{
    $base_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "templates";
    $dir = $base_dir . DIRECTORY_SEPARATOR . $name;
    if (!file_exists($dir) || !is_dir($dir)) {
        return false;
    }
    $config_json_file = $dir . DIRECTORY_SEPARATOR . "config.json";
    if (!file_exists($config_json_file)) {
        return false;
    }
    $json = json_decode(file_get_contents($config_json_file), true);
    return isset($json["container"]) ? $json["container"] : false;
}
function formatBytes($bytes)
{
    $bytes = (int) $bytes;
    if (1024 * 1024 < $bytes) {
        return round($bytes / 1024 / 1024, 2) . " MB";
    }
    if (1024 < $bytes) {
        return round($bytes / 1024, 2) . " KB";
    }
    return $bytes . " B";
}
function str_endsWith($haystack, $needle, $case = true)
{
    $expectedPosition = strlen($haystack) - strlen($needle);
    if ($case) {
        return strrpos($haystack, $needle, 0) === $expectedPosition;
    }
    return strripos($haystack, $needle, 0) === $expectedPosition;
}
function count_table_rows($db, $table)
{
    $CI =& get_instance();
    $estimate_table_rows_for_innodb = $CI->config->item("estimate_table_rows_for_innodb");
    if ($estimate_table_rows_for_innodb && $db->dbdriver == "mysqli") {
        $database = $db->database;
        $sql = "SHOW TABLE STATUS FROM " . pma_backquote($database) . " where Name = ?";
        $query = $db->query($sql, $table);
        if (0 < $query->num_rows()) {
            $table_info = $query->row_array();
            if ($table_info["Engine"] == "InnoDB") {
                return $table_info["Rows"];
            }
        }
    }
    return $db->count_all($table);
}
function check_ds_plugin($plugin_url)
{
    $info = parse_url($plugin_url);
    if (!$info || !isset($info["scheme"])) {
        return false;
    }
    $schema = $info["scheme"];
    $plugin_dir = FC_PATH . "plugins" . DIRECTORY_SEPARATOR . "datasources" . DIRECTORY_SEPARATOR . $schema;
    if (file_exists($plugin_dir) && is_dir($plugin_dir)) {
        return true;
    }
    return false;
}
/**
 * execute sync task from job define entry.
 * target: saved into tablename, from: from settings
 *
 * @param $job
 * @param $db
 * @return
 */
function execute_plugin_job($job, $db)
{
    $job_target = $job["target"];
    $job_from = $job["from"];
    if (empty($job_target) || empty($job_from) || !is_array($job_from) || empty($db)) {
        return false;
    }
    $url = isset($job_from["url"]) ? $job_from["url"] : false;
    $func = isset($job_from["func"]) ? $job_from["func"] : false;
    if (empty($url) && empty($func)) {
        return false;
    }
    $result_data = array();
    if (!empty($url)) {
        require_once APPPATH . "third_party/guzzle/autoloader.php";
        $method = isset($job_from["method"]) ? $job_from["method"] : "GET";
        $headers = isset($job_from["headers"]) ? $job_from["headers"] : array();
        $client = new GuzzleHttp\Client();
        $res = $client->request($method, $url, array("headers" => $headers));
        $code = $res->getStatusCode();
        if ($code != 200) {
            $reason = $res->getReasonPhrase();
            dbface_log("error", "execute_plugin_job failed: " . $url . ", reason: " . $reason);
            return false;
        }
        $body = $res->getBody();
        $result_data = json_decode($body, true);
    } else {
        if (!empty($func)) {
            if (!function_exists($func)) {
                dbface_log("error", "execute_plugin_job failed: " . $func . " not found");
                return false;
            }
            $params = isset($job_from["params"]) ? $job_from["params"] : NULL;
            $result_data = call_user_func($func, $params);
        }
    }
    if (empty($result_data) || !is_array($result_data)) {
        return false;
    }
    $pushtype = isset($job["type"]) && $job["type"] == "append" ? "append" : "rewrite";
    $db->trans_start();
    if ($pushtype == "rewrite") {
        $db->delete($job_target);
    }
    foreach ($result_data as $row) {
        $db->insert($job_target, $row);
    }
    $db->trans_complete();
    return true;
}
/**
 * import all variables into current scope.
 *
 * @param $names: FALSE, import all variables or just the specific variable
 *
 * @return FALSE, wrong, no variable found
 */
function import_var($names = false)
{
    $CI =& get_instance();
    $creatorid = $CI->session->userdata("login_creatorid");
    if (empty($creatorid)) {
        return false;
    }
    $result = array();
    if ($names == false) {
        $query = $CI->db->select("name, cached")->where("creatorid", $creatorid)->get("dc_parameter");
    } else {
        $query = $CI->db->select("name, cached")->where("creatorid", $creatorid)->where_in("name", $names)->get("dc_parameter");
    }
    foreach ($query->result_array() as $row) {
        $result[$row["name"]] = $row["cached"];
    }
    return $result;
}
/**
 * import specific db connection into current scope.
 *
 * @param $id_or_name
 * @return $db the db connection object
 */
function import_db($id_or_name)
{
    $CI =& get_instance();
    $creatorid = $CI->session->userdata("login_creatorid");
    if (is_string($id_or_name)) {
        $query = $CI->db->select("connid")->where(array("creatorid" => $creatorid, "name" => $id_or_name))->get("dc_conn");
        if ($query->num_rows() == 0) {
            return false;
        }
        $id_or_name = $query->row()->connid;
    }
    $db = $CI->_get_db($creatorid, $id_or_name);
    return $db;
}
/**
 * check the ip address is in whitelist, TRUE: in whitelist FALSE; not in whitelist
 *
 * @param $ip_address the checked ip address
 * @param $whitelist whitelist string, split by ,
 * @return TRUE: in whitelist FALSE not in whitelist
 */
function check_ip_in_whitelist($ip_address, $whitelist)
{
    if (empty($whitelist)) {
        return true;
    }
    $ip_ranges = explode(",", $whitelist);
    if (empty($ip_ranges) || count($ip_ranges) == 0) {
        return true;
    }
    if ($whitelist == $ip_address) {
        return true;
    }
    require_once APPPATH . "libraries" . DIRECTORY_SEPARATOR . "ip_in_range.php";
    foreach ($ip_ranges as $range) {
        if (ip_in_range($ip_address, $range)) {
            return true;
        }
    }
    return false;
}

?>