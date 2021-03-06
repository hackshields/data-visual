<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * PDO CUBRID Database Adapter Class
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

class CI_DB_pdo_cubrid_driver extends CI_DB_pdo_driver
{
/**
	 * Sub-driver
	 *
	 * @var	string
	 */
    public $subdriver = "cubrid";
/**
	 * Identifier escape character
	 *
	 * @var	string
	 */
    protected $_escape_char = "`";
/**
	 * ORDER BY random keyword
	 *
	 * @var array
	 */
    protected $_random_keyword = array( "RANDOM()", "RANDOM(%d)" );

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
            $this->dsn = "cubrid:host=" . ((empty($this->hostname) ? "127.0.0.1" : $this->hostname));
            empty($this->port) or empty($this->database) or empty($this->char_set) or         }

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
        $sql = "SHOW TABLES";
        if( $prefix_limit === true && $this->dbprefix !== "" ) 
        {
            return $sql . " LIKE '" . $this->escape_like_str($this->dbprefix) . "%'";
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
        return "SHOW COLUMNS FROM " . $this->protect_identifiers($table, true, NULL, false);
    }

    /**
	 * Returns an object with field data
	 *
	 * @param	string	$table
	 * @return	array
	 */

    public function field_data($table)
    {
        if( ($query = $this->query("SHOW COLUMNS FROM " . $this->protect_identifiers($table, true, NULL, false))) === false ) 
        {
            return false;
        }

        $query = $query->result_object();
        $retval = array(  );
        $i = 0;
        for( $c = count($query); $i < $c; $i++ ) 
        {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->Field;
            sscanf($query[$i]->Type, "%[a-z](%d)", $retval[$i]->type, $retval[$i]->max_length);
            $retval[$i]->default = $query[$i]->Default;
            $retval[$i]->primary_key = (int) ($query[$i]->Key === "PRI");
        }
        return $retval;
    }

    /**
	 * Update_Batch statement
	 *
	 * Generates a platform-specific batch update string from the supplied data
	 *
	 * @param	string	$table	Table name
	 * @param	array	$values	Update data
	 * @param	string	$index	WHERE key
	 * @return	string
	 */

    protected function _update_batch($table, $values, $index)
    {
        $ids = array(  );
        foreach( $values as $key => $val ) 
        {
            $ids[] = $val[$index];
            foreach( array_keys($val) as $field ) 
            {
                if( $field !== $index ) 
                {
                    $final[$field][] = "WHEN " . $index . " = " . $val[$index] . " THEN " . $val[$field];
                }

            }
        }
        $cases = "";
        foreach( $final as $k => $v ) 
        {
            $cases .= $k . " = CASE \n" . implode("\n", $v) . "\n" . "ELSE " . $k . " END), ";
        }
        $this->where($index . " IN(" . implode(",", $ids) . ")", NULL, false);
        return "UPDATE " . $table . " SET " . substr($cases, 0, -2) . $this->_compile_wh("qb_where");
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
        return "TRUNCATE " . $table;
    }

    /**
	 * FROM tables
	 *
	 * Groups tables in FROM clauses if needed, so there is no confusion
	 * about operator precedence.
	 *
	 * @return	string
	 */

    protected function _from_tables()
    {
        if( !empty($this->qb_join) && 1 < count($this->qb_from) ) 
        {
            return "(" . implode(", ", $this->qb_from) . ")";
        }

        return implode(", ", $this->qb_from);
    }

}


