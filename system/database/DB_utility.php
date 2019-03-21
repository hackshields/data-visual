<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Database Utility Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
abstract class CI_DB_utility
{
    /**
     * Database object
     *
     * @var	object
     */
    protected $db = NULL;
    /**
     * List databases statement
     *
     * @var	string
     */
    protected $_list_databases = false;
    /**
     * OPTIMIZE TABLE statement
     *
     * @var	string
     */
    protected $_optimize_table = false;
    /**
     * REPAIR TABLE statement
     *
     * @var	string
     */
    protected $_repair_table = false;
    /**
     * Class constructor
     *
     * @param	object	&$db	Database object
     * @return	void
     */
    public function __construct(&$db)
    {
        $this->db =& $db;
        log_message("info", "Database Utility Class Initialized");
    }
    /**
     * List databases
     *
     * @return	array
     */
    public function list_databases()
    {
        if (isset($this->db->data_cache["db_names"])) {
            return $this->db->data_cache["db_names"];
        }
        if ($this->_list_databases === false) {
            return $this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false;
        }
        $this->db->data_cache["db_names"] = array();
        $query = $this->db->query($this->_list_databases);
        if ($query === false) {
            return $this->db->data_cache["db_names"];
        }
        $i = 0;
        $query = $query->result_array();
        for ($c = count($query); $i < $c; $i++) {
            $this->db->data_cache["db_names"][] = current($query[$i]);
        }
        return $this->db->data_cache["db_names"];
    }
    /**
     * Determine if a particular database exists
     *
     * @param	string	$database_name
     * @return	bool
     */
    public function database_exists($database_name)
    {
        return in_array($database_name, $this->list_databases());
    }
    /**
     * Optimize Table
     *
     * @param	string	$table_name
     * @return	mixed
     */
    public function optimize_table($table_name)
    {
        if ($this->_optimize_table === false) {
            return $this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false;
        }
        $query = $this->db->query(sprintf($this->_optimize_table, $this->db->escape_identifiers($table_name)));
        if ($query !== false) {
            $query = $query->result_array();
            return current($query);
        }
        return false;
    }
    /**
     * Optimize Database
     *
     * @return	mixed
     */
    public function optimize_database()
    {
        if ($this->_optimize_table === false) {
            return $this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false;
        }
        $result = array();
        foreach ($this->db->list_tables() as $table_name) {
            $res = $this->db->query(sprintf($this->_optimize_table, $this->db->escape_identifiers($table_name)));
            if (is_bool($res)) {
                return $res;
            }
            $res = $res->result_array();
            $res = current($res);
            $key = str_replace($this->db->database . ".", "", current($res));
            $keys = array_keys($res);
            unset($res[$keys[0]]);
            $result[$key] = $res;
        }
        return $result;
    }
    /**
     * Repair Table
     *
     * @param	string	$table_name
     * @return	mixed
     */
    public function repair_table($table_name)
    {
        if ($this->_repair_table === false) {
            return $this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false;
        }
        $query = $this->db->query(sprintf($this->_repair_table, $this->db->escape_identifiers($table_name)));
        if (is_bool($query)) {
            return $query;
        }
        $query = $query->result_array();
        return current($query);
    }
    /**
     * Generate CSV from a query result object
     *
     * @param	object	$query		Query result object
     * @param	string	$delim		Delimiter (default: ,)
     * @param	string	$newline	Newline character (default: \n)
     * @param	string	$enclosure	Enclosure (default: ")
     * @return	string
     */
    public function csv_from_result(CI_DB_result $query, $delim = ",", $newline = "\n", $enclosure = "\"")
    {
        $out = "";
        foreach ($query->list_fields() as $name) {
            $out .= $enclosure . str_replace($enclosure, $enclosure . $enclosure, $name) . $enclosure . $delim;
        }
        $out = substr($out, 0, 0 - strlen($delim)) . $newline;
        while ($row = $query->unbuffered_row("array")) {
            $line = array();
            foreach ($row as $item) {
                $line[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $item) . $enclosure;
            }
            $out .= implode($delim, $line) . $newline;
        }
        return $out;
    }
    /**
     * Generate XML data from a query result object
     *
     * @param	object	$query	Query result object
     * @param	array	$params	Any preferences
     * @return	string
     */
    public function xml_from_result(CI_DB_result $query, $params = array())
    {
        foreach (array("root" => "root", "element" => "element", "newline" => "\n", "tab" => "\t") as $key => $val) {
            if (!isset($params[$key])) {
                $params[$key] = $val;
            }
        }
        extract($params);
        get_instance()->load->helper("xml");
        $xml = "<" . $root . ">" . $newline;
        while ($row = $query->unbuffered_row()) {
            $xml .= $tab . "<" . $element . ">" . $newline;
            foreach ($row as $key => $val) {
                $xml .= $tab . $tab . "<" . $key . ">" . xml_convert($val) . "</" . $key . ">" . $newline;
            }
            $xml .= $tab . "</" . $element . ">" . $newline;
        }
        return $xml . "</" . $root . ">" . $newline;
    }
    /**
     * Database Backup
     *
     * @param	array	$params
     * @return	string
     */
    public function backup($params = array())
    {
        if (is_string($params)) {
            $params = array("tables" => $params);
        }
        $prefs = array("tables" => array(), "ignore" => array(), "filename" => "", "format" => "gzip", "add_drop" => true, "add_insert" => true, "newline" => "\n", "foreign_key_checks" => true);
        if (0 < count($params)) {
            foreach ($prefs as $key => $val) {
                if (isset($params[$key])) {
                    $prefs[$key] = $params[$key];
                }
            }
        }
        if (count($prefs["tables"]) === 0) {
            $prefs["tables"] = $this->db->list_tables();
        }
        if (!in_array($prefs["format"], array("gzip", "zip", "txt"), true)) {
            $prefs["format"] = "txt";
        }
        if ($prefs["format"] === "gzip" && !function_exists("gzencode") || $prefs["format"] === "zip" && !function_exists("gzcompress")) {
            if ($this->db->db_debug) {
                return $this->db->display_error("db_unsupported_compression");
            }
            $prefs["format"] = "txt";
        }
        if ($prefs["format"] === "zip") {
            if ($prefs["filename"] === "") {
                $prefs["filename"] = (count($prefs["tables"]) === 1 ? $prefs["tables"] : $this->db->database) . date("Y-m-d_H-i", time()) . ".sql";
            } else {
                if (preg_match("|.+?\\.zip\$|", $prefs["filename"])) {
                    $prefs["filename"] = str_replace(".zip", "", $prefs["filename"]);
                }
                if (!preg_match("|.+?\\.sql\$|", $prefs["filename"])) {
                    $prefs["filename"] .= ".sql";
                }
            }
            $CI =& get_instance();
            $CI->load->library("zip");
            $CI->zip->add_data($prefs["filename"], $this->_backup($prefs));
            return $CI->zip->get_zip();
        }
        if ($prefs["format"] === "txt") {
            return $this->_backup($prefs);
        }
        if ($prefs["format"] === "gzip") {
            return gzencode($this->_backup($prefs));
        }
    }
}

?>