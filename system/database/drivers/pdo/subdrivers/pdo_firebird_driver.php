<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * PDO Firebird Database Adapter Class
 *
 * Note: _DB is an extender class that the app controller
 * creates dynamically based on whether the query builder
 * class is being used or not.
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */

class CI_DB_pdo_firebird_driver extends CI_DB_pdo_driver
{
/**
	 * Sub-driver
	 *
	 * @var	string
	 */
    public $subdriver = "firebird";
/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
    protected $_random_keyword = array( "RAND()", "RAND()" );

    /**
	 * Class constructor
	 *
	 * Builds the DSN if not already set.
	 *
	 * @param	array	$params
	 * @return	void
	 */

    public function __construct($params)
    {
        parent::__construct($params);
        if( empty($this->dsn) ) 
        {
            $this->dsn = "firebird:";
            if( !empty($this->database) ) 
            {
                $this->dsn .= "dbname=" . $this->database;
            }
            else
            {
                if( !empty($this->hostname) ) 
                {
                    $this->dsn .= "dbname=" . $this->hostname;
                }

            }

            empty($this->char_set) or empty($this->role) or         }
        else
        {
            if( !empty($this->char_set) && strpos($this->dsn, "charset=", 9) === false ) 
            {
                $this->dsn .= ";charset=" . $this->char_set;
            }

        }

    }

    /**
	 * Show table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool	$prefix_limit
	 * @return	string
	 */

    protected function _list_tables($prefix_limit = false)
    {
        $sql = "SELECT \"RDB\$RELATION_NAME\" FROM \"RDB\$RELATIONS\" WHERE \"RDB\$RELATION_NAME\" NOT LIKE 'RDB\$%' AND \"RDB\$RELATION_NAME\" NOT LIKE 'MON\$%'";
        if( $prefix_limit === true && $this->dbprefix !== "" ) 
        {
            return $sql . " AND \"RDB\$RELATION_NAME\" LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_like_escape_str, $this->_like_escape_chr);
        }

        return $sql;
    }

    /**
	 * Show column query
	 *
	 * Generates a platform-specific query string so that the column names can be fetched
	 *
	 * @param	string	$table
	 * @return	string
	 */

    protected function _list_columns($table = "")
    {
        return "SELECT \"RDB\$FIELD_NAME\" FROM \"RDB\$RELATION_FIELDS\" WHERE \"RDB\$RELATION_NAME\" = " . $this->escape($table);
    }

    /**
	 * Returns an object with field data
	 *
	 * @param	string	$table
	 * @return	array
	 */

    public function field_data($table)
    {
        $sql = "SELECT \"rfields\".\"RDB\$FIELD_NAME\" AS \"name\",\r\n\t\t\t\tCASE \"fields\".\"RDB\$FIELD_TYPE\"\r\n\t\t\t\t\tWHEN 7 THEN 'SMALLINT'\r\n\t\t\t\t\tWHEN 8 THEN 'INTEGER'\r\n\t\t\t\t\tWHEN 9 THEN 'QUAD'\r\n\t\t\t\t\tWHEN 10 THEN 'FLOAT'\r\n\t\t\t\t\tWHEN 11 THEN 'DFLOAT'\r\n\t\t\t\t\tWHEN 12 THEN 'DATE'\r\n\t\t\t\t\tWHEN 13 THEN 'TIME'\r\n\t\t\t\t\tWHEN 14 THEN 'CHAR'\r\n\t\t\t\t\tWHEN 16 THEN 'INT64'\r\n\t\t\t\t\tWHEN 27 THEN 'DOUBLE'\r\n\t\t\t\t\tWHEN 35 THEN 'TIMESTAMP'\r\n\t\t\t\t\tWHEN 37 THEN 'VARCHAR'\r\n\t\t\t\t\tWHEN 40 THEN 'CSTRING'\r\n\t\t\t\t\tWHEN 261 THEN 'BLOB'\r\n\t\t\t\t\tELSE NULL\r\n\t\t\t\tEND AS \"type\",\r\n\t\t\t\t\"fields\".\"RDB\$FIELD_LENGTH\" AS \"max_length\",\r\n\t\t\t\t\"rfields\".\"RDB\$DEFAULT_VALUE\" AS \"default\"\r\n\t\t\tFROM \"RDB\$RELATION_FIELDS\" \"rfields\"\r\n\t\t\t\tJOIN \"RDB\$FIELDS\" \"fields\" ON \"rfields\".\"RDB\$FIELD_SOURCE\" = \"fields\".\"RDB\$FIELD_NAME\"\r\n\t\t\tWHERE \"rfields\".\"RDB\$RELATION_NAME\" = " . $this->escape($table) . "\r\n\t\t\tORDER BY \"rfields\".\"RDB\$FIELD_POSITION\"";
        return (($query = $this->query($sql)) !== false ? $query->result_object() : false);
    }

    /**
	 * Update statement
	 *
	 * Generates a platform-specific update string from the supplied data
	 *
	 * @param	string	$table
	 * @param	array	$values
	 * @return	string
	 */

    protected function _update($table, $values)
    {
        $this->qb_limit = false;
        return parent::_update($table, $values);
    }

    /**
	 * Truncate statement
	 *
	 * Generates a platform-specific truncate string from the supplied data
	 *
	 * If the database does not support the TRUNCATE statement,
	 * then this method maps to 'DELETE FROM table'
	 *
	 * @param	string	$table
	 * @return	string
	 */

    protected function _truncate($table)
    {
        return "DELETE FROM " . $table;
    }

    /**
	 * Delete statement
	 *
	 * Generates a platform-specific delete string from the supplied data
	 *
	 * @param	string	$table
	 * @return	string
	 */

    protected function _delete($table)
    {
        $this->qb_limit = false;
        return parent::_delete($table);
    }

    /**
	 * LIMIT
	 *
	 * Generates a platform-specific LIMIT clause
	 *
	 * @param	string	$sql	SQL Query
	 * @return	string
	 */

    protected function _limit($sql)
    {
        if( stripos($this->version(), "firebird") !== false ) 
        {
            $select = "FIRST " . $this->qb_limit . ((0 < $this->qb_offset ? " SKIP " . $this->qb_offset : ""));
        }
        else
        {
            $select = "ROWS " . ((0 < $this->qb_offset ? $this->qb_offset . " TO " . ($this->qb_limit + $this->qb_offset) : $this->qb_limit));
        }

        return preg_replace("`SELECT`i", "SELECT " . $select, $sql);
    }

}


