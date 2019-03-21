<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Oracle Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_oci8_forge extends CI_DB_forge
{
    /**
     * CREATE DATABASE statement
     *
     * @var	string
     */
    protected $_create_database = false;
    /**
     * CREATE TABLE IF statement
     *
     * @var	string
     */
    protected $_create_table_if = false;
    /**
     * DROP DATABASE statement
     *
     * @var	string
     */
    protected $_drop_database = false;
    /**
     * DROP TABLE IF statement
     *
     * @var	string
     */
    protected $_drop_table_if = false;
    /**
     * UNSIGNED support
     *
     * @var	bool|array
     */
    protected $_unsigned = false;
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
        if ($alter_type === "DROP") {
            return parent::_alter_table($alter_type, $table, $field);
        }
        if ($alter_type === "CHANGE") {
            $alter_type = "MODIFY";
        }
        $sql = "ALTER TABLE " . $this->db->escape_identifiers($table);
        $sqls = array();
        $i = 0;
        for ($c = count($field); $i < $c; $i++) {
            if ($field[$i]["_literal"] !== false) {
                $field[$i] = "\n\t" . $field[$i]["_literal"];
            } else {
                $field[$i]["_literal"] = "\n\t" . $this->_process_column($field[$i]);
                if (!empty($field[$i]["comment"])) {
                    $sqls[] = "COMMENT ON COLUMN " . $this->db->escape_identifiers($table) . "." . $this->db->escape_identifiers($field[$i]["name"]) . " IS " . $field[$i]["comment"];
                }
                if ($alter_type === "MODIFY" && !empty($field[$i]["new_name"])) {
                    $sqls[] = $sql . " RENAME COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . " " . $this->db->escape_identifiers($field[$i]["new_name"]);
                }
            }
        }
        $sql .= " " . $alter_type . " ";
        $sql .= count($field) === 1 ? $field[0] : "(" . implode(",", $field) . ")";
        array_unshift($sqls, $sql);
        return $sql;
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
            case "TINYINT":
                $attributes["TYPE"] = "NUMBER";
                return NULL;
            case "MEDIUMINT":
                $attributes["TYPE"] = "NUMBER";
                return NULL;
            case "INT":
                $attributes["TYPE"] = "NUMBER";
                return NULL;
            case "BIGINT":
                $attributes["TYPE"] = "NUMBER";
                return NULL;
        }
    }
}

?>