<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * ODBC Database Adapter Class
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
class CI_DB_odbc_driver extends CI_DB
{
    /**
     * Database driver
     *
     * @var	string
     */
    public $dbdriver = "odbc";
    /**
     * Database schema
     *
     * @var	string
     */
    public $schema = "public";
    /**
     * Identifier escape character
     *
     * Must be empty for ODBC.
     *
     * @var	string
     */
    protected $_escape_char = "";
    /**
     * ESCAPE statement string
     *
     * @var	string
     */
    protected $_like_escape_str = " {escape '%s'} ";
    /**
     * ORDER BY random keyword
     *
     * @var	array
     */
    protected $_random_keyword = array("RND()", "RND(%d)");
    /**
     * ODBC result ID resource returned from odbc_prepare()
     *
     * @var	resource
     */
    private $odbc_result = NULL;
    /**
     * Values to use with odbc_execute() for prepared statements
     *
     * @var	array
     */
    private $binds = array();
    /**
     * Class constructor
     *
     * @param	array	$params
     * @return	void
     */
    public function __construct($params)
    {
        parent::__construct($params);
        if (empty($this->dsn)) {
            $this->dsn = $this->hostname;
        }
    }
    /**
     * Non-persistent database connection
     *
     * @param	bool	$persistent
     * @return	resource
     */
    public function db_connect($persistent = false)
    {
        return $persistent === true ? odbc_pconnect($this->dsn, $this->username, $this->password) : odbc_connect($this->dsn, $this->username, $this->password);
    }
    /**
     * Compile Bindings
     *
     * @param	string	$sql	SQL statement
     * @param	array	$binds	An array of values to bind
     * @return	string
     */
    public function compile_binds($sql, $binds)
    {
        if (empty($binds) || empty($this->bind_marker) || strpos($sql, $this->bind_marker) === false) {
            return $sql;
        }
        if (!is_array($binds)) {
            $binds = array($binds);
            $bind_count = 1;
        } else {
            $binds = array_values($binds);
            $bind_count = count($binds);
        }
        $ml = strlen($this->bind_marker);
        if ($c = preg_match_all("/'[^']*'/i", $sql, $matches)) {
            $c = preg_match_all("/" . preg_quote($this->bind_marker, "/") . "/i", str_replace($matches[0], str_replace($this->bind_marker, str_repeat(" ", $ml), $matches[0]), $sql, $c), $matches, PREG_OFFSET_CAPTURE);
            if ($bind_count !== $c) {
                return $sql;
            }
        } else {
            if (($c = preg_match_all("/" . preg_quote($this->bind_marker, "/") . "/i", $sql, $matches, PREG_OFFSET_CAPTURE)) !== $bind_count) {
                return $sql;
            }
        }
        if ($this->bind_marker !== "?") {
            do {
                $c--;
                $sql = substr_replace($sql, "?", $matches[0][$c][1], $ml);
            } while ($c !== 0);
        }
        if (false !== ($this->odbc_result = odbc_prepare($this->conn_id, $sql))) {
            $this->binds = array_values($binds);
        }
        return $sql;
    }
    /**
     * Execute the query
     *
     * @param	string	$sql	an SQL query
     * @return	resource
     */
    protected function _execute($sql)
    {
        if (!isset($this->odbc_result)) {
            return odbc_exec($this->conn_id, $sql);
        }
        if ($this->odbc_result === false) {
            return false;
        }
        if (true === ($success = odbc_execute($this->odbc_result, $this->binds))) {
            $this->is_write_type($sql) or $this->binds = array();
            return $success;
        }
        $this->odbc_result = NULL;
    }
    /**
     * Begin Transaction
     *
     * @return	bool
     */
    protected function _trans_begin()
    {
        return odbc_autocommit($this->conn_id, false);
    }
    /**
     * Commit Transaction
     *
     * @return	bool
     */
    protected function _trans_commit()
    {
        if (odbc_commit($this->conn_id)) {
            odbc_autocommit($this->conn_id, true);
            return true;
        }
        return false;
    }
    /**
     * Rollback Transaction
     *
     * @return	bool
     */
    protected function _trans_rollback()
    {
        if (odbc_rollback($this->conn_id)) {
            odbc_autocommit($this->conn_id, true);
            return true;
        }
        return false;
    }
    /**
     * Determines if a query is a "write" type.
     *
     * @param	string	An SQL query string
     * @return	bool
     */
    public function is_write_type($sql)
    {
        if (preg_match("#^(INSERT|UPDATE).*RETURNING\\s.+(\\,\\s?.+)*\$#i", $sql)) {
            return false;
        }
        return parent::is_write_type($sql);
    }
    /**
     * Platform-dependant string escape
     *
     * @param	string
     * @return	string
     */
    protected function _escape_str($str)
    {
        $this->db->display_error("db_unsupported_feature");
    }
    /**
     * Affected Rows
     *
     * @return	int
     */
    public function affected_rows()
    {
        return odbc_num_rows($this->result_id);
    }
    /**
     * Insert ID
     *
     * @return	bool
     */
    public function insert_id()
    {
        return $this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false;
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
        $sql = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $this->schema . "'";
        if ($prefix_limit !== false && $this->dbprefix !== "") {
            return $sql . " AND table_name LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_like_escape_str, $this->_like_escape_chr);
        }
        return $sql;
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
        return "SHOW COLUMNS FROM " . $table;
    }
    /**
     * Field data query
     *
     * Generates a platform-specific query so that the column data can be retrieved
     *
     * @param	string	$table
     * @return	string
     */
    protected function _field_data($table)
    {
        return "SELECT TOP 1 * FROM " . $table;
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
        return array("code" => odbc_error($this->conn_id), "message" => odbc_errormsg($this->conn_id));
    }
    /**
     * Close DB Connection
     *
     * @return	void
     */
    protected function _close()
    {
        odbc_close($this->conn_id);
    }
}

?>