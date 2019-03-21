<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Postgre Forge Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_postgre_forge extends CI_DB_forge
{
    /**
     * UNSIGNED support
     *
     * @var	array
     */
    protected $_unsigned = array("INT2" => "INTEGER", "SMALLINT" => "INTEGER", "INT" => "BIGINT", "INT4" => "BIGINT", "INTEGER" => "BIGINT", "INT8" => "NUMERIC", "BIGINT" => "NUMERIC", "REAL" => "DOUBLE PRECISION", "FLOAT" => "DOUBLE PRECISION");
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
        if (version_compare($this->db->version(), "9.0", ">")) {
            $this->create_table_if = "CREATE TABLE IF NOT EXISTS";
        }
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
        if (in_array($alter_type, array("DROP", "ADD"), true)) {
            return parent::_alter_table($alter_type, $table, $field);
        }
        $sql = "ALTER TABLE " . $this->db->escape_identifiers($table);
        $sqls = array();
        $i = 0;
        for ($c = count($field); $i < $c; $i++) {
            if ($field[$i]["_literal"] !== false) {
                return false;
            }
            if (version_compare($this->db->version(), "8", ">=") && isset($field[$i]["type"])) {
                $sqls[] = $sql . " ALTER COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . " TYPE " . $field[$i]["type"] . $field[$i]["length"];
            }
            if (!empty($field[$i]["default"])) {
                $sqls[] = $sql . " ALTER COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . " SET DEFAULT " . $field[$i]["default"];
            }
            if (isset($field[$i]["null"])) {
                $sqls[] = $sql . " ALTER COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . ($field[$i]["null"] === true ? " DROP NOT NULL" : " SET NOT NULL");
            }
            if (!empty($field[$i]["new_name"])) {
                $sqls[] = $sql . " RENAME COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . " TO " . $this->db->escape_identifiers($field[$i]["new_name"]);
            }
            if (!empty($field[$i]["comment"])) {
                $sqls[] = "COMMENT ON COLUMN " . $this->db->escape_identifiers($table) . "." . $this->db->escape_identifiers($field[$i]["name"]) . " IS " . $field[$i]["comment"];
            }
        }
        return $sqls;
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
        if (isset($attributes["CONSTRAINT"]) && stripos($attributes["TYPE"], "int") !== false) {
            $attributes["CONSTRAINT"] = NULL;
        }
        switch (strtoupper($attributes["TYPE"])) {
            case "TINYINT":
                $attributes["TYPE"] = "SMALLINT";
                $attributes["UNSIGNED"] = false;
                return NULL;
            case "MEDIUMINT":
                $attributes["TYPE"] = "INTEGER";
                $attributes["UNSIGNED"] = false;
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
        if (!empty($attributes["AUTO_INCREMENT"]) && $attributes["AUTO_INCREMENT"] === true) {
            $field["type"] = $field["type"] === "NUMERIC" ? "BIGSERIAL" : "SERIAL";
        }
    }
}

?>