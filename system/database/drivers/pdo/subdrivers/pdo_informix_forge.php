<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * PDO Informix Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_pdo_informix_forge extends CI_DB_pdo_forge
{
    /**
     * RENAME TABLE statement
     *
     * @var	string
     */
    protected $_rename_table = "RENAME TABLE %s TO %s";
    /**
     * UNSIGNED support
     *
     * @var	array
     */
    protected $_unsigned = array("SMALLINT" => "INTEGER", "INT" => "BIGINT", "INTEGER" => "BIGINT", "REAL" => "DOUBLE PRECISION", "SMALLFLOAT" => "DOUBLE PRECISION");
    /**
     * DEFAULT value representation in CREATE/ALTER TABLE statements
     *
     * @var	string
     */
    protected $_default = ", ";
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
        if ($alter_type === "CHANGE") {
            $alter_type = "MODIFY";
        }
        return parent::_alter_table($alter_type, $table, $field);
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
                $attributes["TYPE"] = "SMALLINT";
                $attributes["UNSIGNED"] = false;
                return NULL;
            case "MEDIUMINT":
                $attributes["TYPE"] = "INTEGER";
                $attributes["UNSIGNED"] = false;
                return NULL;
            case "BYTE":
            case "TEXT":
            case "BLOB":
            case "CLOB":
                $attributes["UNIQUE"] = false;
                if (isset($attributes["DEFAULT"])) {
                    unset($attributes["DEFAULT"]);
                }
                return NULL;
        }
    }
    /**
     * Field attribute UNIQUE
     *
     * @param	array	&$attributes
     * @param	array	&$field
     * @return	void
     */
    protected function _attr_unique(&$attributes, &$field)
    {
        if (!empty($attributes["UNIQUE"]) && $attributes["UNIQUE"] === true) {
            $field["unique"] = " UNIQUE CONSTRAINT " . $this->db->escape_identifiers($field["name"]);
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
    }
}

?>