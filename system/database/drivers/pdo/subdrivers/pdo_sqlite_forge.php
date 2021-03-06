<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * PDO SQLite Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_sqlite_forge extends CI_DB_pdo_forge
{
    /**
     * CREATE TABLE IF statement
     *
     * @var	string
     */
    protected $_create_table_if = "CREATE TABLE IF NOT EXISTS";
    /**
     * DROP TABLE IF statement
     *
     * @var	string
     */
    protected $_drop_table_if = "DROP TABLE IF EXISTS";
    /**
     * UNSIGNED support
     *
     * @var	bool|array
     */
    protected $_unsigned = false;
    /**
     * NULL value representation in CREATE/ALTER TABLE statements
     *
     * @var	string
     */
    protected $_null = "NULL";
    /**
     * Class constructor
     *
     * @param	object	&$db	Database object
     * @return	void
     */
    public function __construct(&$db)
    {
        parent::__construct($db);
        if (version_compare($this->db->version(), "3.3", "<")) {
            $this->_create_table_if = false;
            $this->_drop_table_if = false;
        }
    }
    /**
     * Create database
     *
     * @param	string	$db_name	(ignored)
     * @return	bool
     */
    public function create_database($db_name = "")
    {
        return true;
    }
    /**
     * Drop database
     *
     * @param	string	$db_name	(ignored)
     * @return	bool
     */
    public function drop_database($db_name = "")
    {
        if (file_exists($this->db->database)) {
            $this->db->close();
            if (!@unlink($this->db->database)) {
                return $this->db->db_debug ? $this->db->display_error("db_unable_to_drop") : false;
            }
            if (!empty($this->db->data_cache["db_names"])) {
                $key = array_search(strtolower($this->db->database), array_map("strtolower", $this->db->data_cache["db_names"]), true);
                if ($key !== false) {
                    unset($this->db->data_cache["db_names"][$key]);
                }
            }
            return true;
        }
        return $this->db->db_debug ? $this->db->display_error("db_unable_to_drop") : false;
    }
    /**
     * ALTER TABLE
     *
     * @param	string	$alter_type	ALTER type
     * @param	string	$table		Table name
     * @param	mixed	$field		Column definition
     * @return	string|string[]
     */
    protected function _alter_table($alter_type, $table, $field)
    {
        if ($alter_type === "DROP" || $alter_type === "CHANGE") {
            return false;
        }
        return parent::_alter_table($alter_type, $table, $field);
    }
    /**
     * Process column
     *
     * @param	array	$field
     * @return	string
     */
    protected function _process_column($field)
    {
        return $this->db->escape_identifiers($field["name"]) . " " . $field["type"] . $field["auto_increment"] . $field["null"] . $field["unique"] . $field["default"];
    }
    /**
     * Field attribute TYPE
     *
     * Performs a data type mapping between different databases.
     *
     * @param	array	&$attributes
     * @return	void
     */
    protected function _attr_type(&$attributes)
    {
        switch (strtoupper($attributes["TYPE"])) {
            case "ENUM":
            case "SET":
                $attributes["TYPE"] = "TEXT";
                return NULL;
        }
    }
    /**
     * Field attribute AUTO_INCREMENT
     *
     * @param	array	&$attributes
     * @param	array	&$field
     * @return	void
     */
    protected function _attr_auto_increment(&$attributes, &$field)
    {
        if (!empty($attributes["AUTO_INCREMENT"]) && $attributes["AUTO_INCREMENT"] === true && stripos($field["type"], "int") !== false) {
            $field["type"] = "INTEGER PRIMARY KEY";
            $field["default"] = "";
            $field["null"] = "";
            $field["unique"] = "";
            $field["auto_increment"] = " AUTOINCREMENT";
            $this->primary_keys = array();
        }
    }
}

?>