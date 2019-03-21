<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * Interbase/Firebird Forge Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */

class CI_DB_ibase_forge extends CI_DB_forge
{
/**
	 * CREATE TABLE IF statement
	 *
	 * @var	string
	 */
    protected $_create_table_if = false;
/**
	 * RENAME TABLE statement
	 *
	 * @var	string
	 */
    protected $_rename_table = false;
/**
	 * DROP TABLE IF statement
	 *
	 * @var	string
	 */
    protected $_drop_table_if = false;
/**
	 * UNSIGNED support
	 *
	 * @var	array
	 */
    protected $_unsigned = array( "SMALLINT" => "INTEGER", "INTEGER" => "INT64", "FLOAT" => "DOUBLE PRECISION" );
/**
	 * NULL value representation in CREATE/ALTER TABLE statements
	 *
	 * @var	string
	 */
    protected $_null = "NULL";

    /**
	 * Create database
	 *
	 * @param	string	$db_name
	 * @return	string
	 */

    public function create_database($db_name)
    {
        empty($this->db->hostname) or return parent::create_database("\"" . $db_name . "\"");
    }

    /**
	 * Drop database
	 *
	 * @param	string	$db_name	(ignored)
	 * @return	bool
	 */

    public function drop_database($db_name = "")
    {
        if( !ibase_drop_db($this->conn_id) ) 
        {
            return ($this->db->db_debug ? $this->db->display_error("db_unable_to_drop") : false);
        }

        if( !empty($this->db->data_cache["db_names"]) ) 
        {
            $key = array_search(strtolower($this->db->database), array_map("strtolower", $this->db->data_cache["db_names"]), true);
            if( $key !== false ) 
            {
                unset($this->db->data_cache["db_names"][$key]);
            }

        }

        return true;
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
        if( in_array($alter_type, array( "DROP", "ADD" ), true) ) 
        {
            return parent::_alter_table($alter_type, $table, $field);
        }

        $sql = "ALTER TABLE " . $this->db->escape_identifiers($table);
        $sqls = array(  );
        $i = 0;
        for( $c = count($field); $i < $c; $i++ ) 
        {
            if( $field[$i]["_literal"] !== false ) 
            {
                return false;
            }

            if( isset($field[$i]["type"]) ) 
            {
                $sqls[] = $sql . " ALTER COLUMN " . $this->db->escape_identififers($field[$i]["name"]) . " TYPE " . $field[$i]["type"] . $field[$i]["length"];
            }

            if( !empty($field[$i]["default"]) ) 
            {
                $sqls[] = $sql . " ALTER COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . " SET DEFAULT " . $field[$i]["default"];
            }

            if( isset($field[$i]["null"]) ) 
            {
                $sqls[] = "UPDATE \"RDB\$RELATION_FIELDS\" SET \"RDB\$NULL_FLAG\" = " . (($field[$i]["null"] === true ? "NULL" : "1")) . " WHERE \"RDB\$FIELD_NAME\" = " . $this->db->escape($field[$i]["name"]) . " AND \"RDB\$RELATION_NAME\" = " . $this->db->escape($table);
            }

            if( !empty($field[$i]["new_name"]) ) 
            {
                $sqls[] = $sql . " ALTER COLUMN " . $this->db->escape_identifiers($field[$i]["name"]) . " TO " . $this->db->escape_identifiers($field[$i]["new_name"]);
            }

        }
        return $sqls;
    }

    /**
	 * Process column
	 *
	 * @param	array	$field
	 * @return	string
	 */

    protected function _process_column($field)
    {
        return $this->db->escape_identifiers($field["name"]) . " " . $field["type"] . $field["length"] . $field["null"] . $field["unique"] . $field["default"];
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
        switch( strtoupper($attributes["TYPE"]) ) 
        {
            case "TINYINT":
                $attributes["TYPE"] = "SMALLINT";
                $attributes["UNSIGNED"] = false;
                return NULL;
            case "MEDIUMINT":
                $attributes["TYPE"] = "INTEGER";
                $attributes["UNSIGNED"] = false;
                return NULL;
            case "INT":
                $attributes["TYPE"] = "INTEGER";
                return NULL;
            case "BIGINT":
                $attributes["TYPE"] = "INT64";
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
    }

}


