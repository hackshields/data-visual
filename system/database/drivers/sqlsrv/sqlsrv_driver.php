<?php 
defined("BASEPATH") or exit( "No direct script access allowed" );

/**
 * SQLSRV Database Adapter Class
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

class CI_DB_sqlsrv_driver extends CI_DB
{
/**
	 * Database driver
	 *
	 * @var	string
	 */
    public $dbdriver = "sqlsrv";
/**
	 * Scrollable flag
	 *
	 * Determines what cursor type to use when executing queries.
	 *
	 * FALSE or SQLSRV_CURSOR_FORWARD would increase performance,
	 * but would disable num_rows() (and possibly insert_id())
	 *
	 * @var	mixed
	 */
    public $scrollable = NULL;
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
    protected $_quoted_identifier = true;

    /**
	 * Class constructor
	 *
	 * @param	array	$params
	 * @return	void
	 */

    public function __construct($params)
    {
        parent::__construct($params);
        if( $this->scrollable === NULL ) 
        {
            $this->scrollable = (defined("SQLSRV_CURSOR_CLIENT_BUFFERED") ? SQLSRV_CURSOR_CLIENT_BUFFERED : false);
        }

    }

    /**
	 * Database connection
	 *
	 * @param	bool	$pooling
	 * @return	resource
	 */

    public function db_connect($pooling = false)
    {
        $charset = (in_array(strtolower($this->char_set), array( "utf-8", "utf8" ), true) ? "UTF-8" : SQLSRV_ENC_CHAR);
        $connection = array( "UID" => (empty($this->username) ? "" : $this->username), "PWD" => (empty($this->password) ? "" : $this->password), "Database" => $this->database, "ConnectionPooling" => ($pooling === true ? 1 : 0), "CharacterSet" => $charset, "Encrypt" => ($this->encrypt === true ? 1 : 0), "ReturnDatesAsStrings" => 1 );
        if( empty($connection["UID"]) && empty($connection["PWD"]) ) 
        {
            unset($connection["UID"]);
            unset($connection["PWD"]);
        }

        if( false !== ($this->conn_id = sqlsrv_connect($this->hostname, $connection)) ) 
        {
            $query = $this->query("SELECT CASE WHEN (@@OPTIONS | 256) = @@OPTIONS THEN 1 ELSE 0 END AS qi");
            $query = $query->row_array();
            $this->_quoted_identifier = (empty($query) ? false : (bool) $query["qi"]);
            $this->_escape_char = ($this->_quoted_identifier ? "\"" : array( "[", "]" ));
        }

        return $this->conn_id;
    }

    /**
	 * Select the database
	 *
	 * @param	string	$database
	 * @return	bool
	 */

    public function db_select($database = "")
    {
        if( $database === "" ) 
        {
            $database = $this->database;
        }

        if( $this->_execute("USE " . $this->escape_identifiers($database)) ) 
        {
            $this->database = $database;
            $this->data_cache = array(  );
            return true;
        }

        return false;
    }

    /**
	 * Execute the query
	 *
	 * @param	string	$sql	an SQL query
	 * @return	resource
	 */

    protected function _execute($sql)
    {
        return ($this->scrollable === false || $this->is_write_type($sql) ? sqlsrv_query($this->conn_id, $sql) : sqlsrv_query($this->conn_id, $sql, NULL, array( "Scrollable" => $this->scrollable )));
    }

    /**
	 * Begin Transaction
	 *
	 * @return	bool
	 */

    protected function _trans_begin()
    {
        return sqlsrv_begin_transaction($this->conn_id);
    }

    /**
	 * Commit Transaction
	 *
	 * @return	bool
	 */

    protected function _trans_commit()
    {
        return sqlsrv_commit($this->conn_id);
    }

    /**
	 * Rollback Transaction
	 *
	 * @return	bool
	 */

    protected function _trans_rollback()
    {
        return sqlsrv_rollback($this->conn_id);
    }

    /**
	 * Affected Rows
	 *
	 * @return	int
	 */

    public function affected_rows()
    {
        return sqlsrv_rows_affected($this->result_id);
    }

    /**
	 * Insert ID
	 *
	 * Returns the last id created in the Identity column.
	 *
	 * @return	string
	 */

    public function insert_id()
    {
        return $this->query("SELECT SCOPE_IDENTITY() AS insert_id")->row()->insert_id;
    }

    /**
	 * Database version number
	 *
	 * @return	string
	 */

    public function version()
    {
        if( isset($this->data_cache["version"]) ) 
        {
            return $this->data_cache["version"];
        }

        if( !$this->conn_id || ($info = sqlsrv_server_info($this->conn_id)) === false ) 
        {
            return false;
        }

        $this->data_cache["version"] = $info["SQLServerVersion"];
        return $this->data_cache["version"];
    }

    /**
	 * List table query
	 *
	 * Generates a platform-specific query string so that the table names can be fetched
	 *
	 * @param	bool
	 * @return	string	$prefix_limit
	 */

    protected function _list_tables($prefix_limit = false)
    {
        $sql = "SELECT " . $this->escape_identifiers("name") . " FROM " . $this->escape_identifiers("sysobjects") . " WHERE " . $this->escape_identifiers("type") . " = 'U'";
        if( $prefix_limit === true && $this->dbprefix !== "" ) 
        {
            $sql .= " AND " . $this->escape_identifiers("name") . " LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_escape_like_str, $this->_escape_like_chr);
        }

        return $sql . " ORDER BY " . $this->escape_identifiers("name");
    }

    /**
	 * List column query
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
	 * Error
	 *
	 * Returns an array containing code and message of the last
	 * database error that has occured.
	 *
	 * @return	array
	 */

    public function error()
    {
        $error = array( "code" => "00000", "message" => "" );
        $sqlsrv_errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
        if( !is_array($sqlsrv_errors) ) 
        {
            return $error;
        }

        $sqlsrv_error = array_shift($sqlsrv_errors);
        if( isset($sqlsrv_error["SQLSTATE"]) ) 
        {
            $error["code"] = (isset($sqlsrv_error["code"]) ? $sqlsrv_error["SQLSTATE"] . "/" . $sqlsrv_error["code"] : $sqlsrv_error["SQLSTATE"]);
        }
        else
        {
            if( isset($sqlsrv_error["code"]) ) 
            {
                $error["code"] = $sqlsrv_error["code"];
            }

        }

        if( isset($sqlsrv_error["message"]) ) 
        {
            $error["message"] = $sqlsrv_error["message"];
        }

        return $error;
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
        return "TRUNCATE TABLE " . $table;
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

        return ($this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false);
    }

    /**
	 * Close DB Connection
	 *
	 * @return	void
	 */

    protected function _close()
    {
        sqlsrv_close($this->conn_id);
    }

}


