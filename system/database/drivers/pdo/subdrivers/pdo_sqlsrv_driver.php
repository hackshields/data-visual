<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * PDO SQLSRV Database Adapter Class
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

class CI_DB_pdo_sqlsrv_driver extends CI_DB_pdo_driver
{
/**
	 * Sub-driver
	 *
	 * @var	string
	 */
    public $subdriver = "sqlsrv";
/**
	 * ORDER BY random keyword
	 *
	 * @var	array
	 */
    protected $_random_keyword = array( "NEWID()", "RAND(%d)" );
/**
	 * Quoted identifier flag
	 *
	 * Whether to use SQL-92 standard quoted identifier
	 * (double quotes) or brackets for identifier escaping.
	 *
	 * @var	bool
	 */
    protected $_quoted_identifier = NULL;

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
            $this->dsn = "sqlsrv:Server=" . ((empty($this->hostname) ? "127.0.0.1" : $this->hostname));
            empty($this->port) or empty($this->database) or if( isset($this->QuotedId) ) 
{
    $this->dsn .= ";QuotedId=" . $this->QuotedId;
    $this->_quoted_identifier = (bool) $this->QuotedId;
}

            if( isset($this->ConnectionPooling) ) 
            {
                $this->dsn .= ";ConnectionPooling=" . $this->ConnectionPooling;
            }

            if( $this->encrypt === true ) 
            {
                $this->dsn .= ";Encrypt=1";
            }

            if( isset($this->TraceOn) ) 
            {
                $this->dsn .= ";TraceOn=" . $this->TraceOn;
            }

            if( isset($this->TrustServerCertificate) ) 
            {
                $this->dsn .= ";TrustServerCertificate=" . $this->TrustServerCertificate;
            }

            empty($this->APP) or empty($this->Failover_Partner) or empty($this->LoginTimeout) or empty($this->MultipleActiveResultSets) or empty($this->TraceFile) or empty($this->WSID) or         }
        else
        {
            if( preg_match("/QuotedId=(0|1)/", $this->dsn, $match) ) 
            {
                $this->_quoted_identifier = (bool) $match[1];
            }

        }

    }

    /**
	 * Database connection
	 *
	 * @param	bool	$persistent
	 * @return	object
	 */

    public function db_connect($persistent = false)
    {
        if( !empty($this->char_set) && preg_match("/utf[^8]*8/i", $this->char_set) ) 
        {
            $this->options[PDO::SQLSRV_ENCODING_UTF8] = 1;
        }

        $this->conn_id = parent::db_connect($persistent);
        if( !is_object($this->conn_id) || is_bool($this->_quoted_identifier) ) 
        {
            return $this->conn_id;
        }

        $query = $this->query("SELECT CASE WHEN (@@OPTIONS | 256) = @@OPTIONS THEN 1 ELSE 0 END AS qi");
        $query = $query->row_array();
        $this->_quoted_identifier = (empty($query) ? false : (bool) $query["qi"]);
        $this->_escape_char = ($this->_quoted_identifier ? "\"" : array( "[", "]" ));
        return $this->conn_id;
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
        $sql = "SELECT " . $this->escape_identifiers("name") . " FROM " . $this->escape_identifiers("sysobjects") . " WHERE " . $this->escape_identifiers("type") . " = 'U'";
        if( $prefix_limit === true && $this->dbprefix !== "" ) 
        {
            $sql .= " AND " . $this->escape_identifiers("name") . " LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_like_escape_str, $this->_like_escape_chr);
        }

        return $sql . " ORDER BY " . $this->escape_identifiers("name");
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
        return "SELECT COLUMN_NAME\r\n\t\t\tFROM INFORMATION_SCHEMA.Columns\r\n\t\t\tWHERE UPPER(TABLE_NAME) = " . $this->escape(strtoupper($table));
    }

    /**
	 * Returns an object with field data
	 *
	 * @param	string	$table
	 * @return	array
	 */

    public function field_data($table)
    {
        $sql = "SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, COLUMN_DEFAULT\r\n\t\t\tFROM INFORMATION_SCHEMA.Columns\r\n\t\t\tWHERE UPPER(TABLE_NAME) = " . $this->escape(strtoupper($table));
        if( ($query = $this->query($sql)) === false ) 
        {
            return false;
        }

        $query = $query->result_object();
        $retval = array(  );
        $i = 0;
        for( $c = count($query); $i < $c; $i++ ) 
        {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->COLUMN_NAME;
            $retval[$i]->type = $query[$i]->DATA_TYPE;
            $retval[$i]->max_length = (0 < $query[$i]->CHARACTER_MAXIMUM_LENGTH ? $query[$i]->CHARACTER_MAXIMUM_LENGTH : $query[$i]->NUMERIC_PRECISION);
            $retval[$i]->default = $query[$i]->COLUMN_DEFAULT;
        }
        return $retval;
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
        $this->qb_orderby = array(  );
        return parent::_update($table, $values);
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
        if( $this->qb_limit ) 
        {
            return "WITH ci_delete AS (SELECT TOP " . $this->qb_limit . " * FROM " . $table . $this->_compile_wh("qb_where") . ") DELETE FROM ci_delete";
        }

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
        if( version_compare($this->version(), "11", ">=") ) 
        {
            empty($this->qb_orderby) and return $sql . " OFFSET " . (int) $this->qb_offset . " ROWS FETCH NEXT " . $this->qb_limit . " ROWS ONLY";
        }

        $limit = $this->qb_offset + $this->qb_limit;
        if( $this->qb_offset && !empty($this->qb_orderby) ) 
        {
            $orderby = $this->_compile_order_by();
            $sql = trim(substr($sql, 0, strrpos($sql, $orderby)));
            if( count($this->qb_select) === 0 ) 
            {
                $select = "*";
            }
            else
            {
                $select = array(  );
                $field_regexp = ($this->_quoted_identifier ? "(\"[^\\\"]+\")" : "(\\[[^\\]]+\\])");
                $i = 0;
                for( $c = count($this->qb_select); $i < $c; $i++ ) 
                {
                    $select[] = (preg_match("/(?:\\s|\\.)" . $field_regexp . "\$/i", $this->qb_select[$i], $m) ? $m[1] : $this->qb_select[$i]);
                }
                $select = implode(", ", $select);
            }

            return "SELECT " . $select . " FROM (\n\n" . preg_replace("/^(SELECT( DISTINCT)?)/i", "\\1 ROW_NUMBER() OVER(" . trim($orderby) . ") AS " . $this->escape_identifiers("CI_rownum") . ", ", $sql) . "\n\n) " . $this->escape_identifiers("CI_subquery") . "\nWHERE " . $this->escape_identifiers("CI_rownum") . " BETWEEN " . ($this->qb_offset + 1) . " AND " . $limit;
        }

        return preg_replace("/(^\\SELECT (DISTINCT)?)/i", "\\1 TOP " . $limit . " ", $sql);
    }

    /**
	 * Insert batch statement
	 *
	 * Generates a platform-specific insert string from the supplied data.
	 *
	 * @param	string	$table	Table name
	 * @param	array	$keys	INSERT keys
	 * @param	array	$values	INSERT values
	 * @return	string|bool
	 */

    protected function _insert_batch($table, $keys, $values)
    {
        if( version_compare($this->version(), "10", ">=") ) 
        {
            return parent::_insert_batch($table, $keys, $values);
        }

        return ($this->db_debug ? $this->display_error("db_unsupported_feature") : false);
    }

}


