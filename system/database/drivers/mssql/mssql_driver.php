<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * MS SQL Database Adapter Class
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
class CI_DB_mssql_driver extends CI_DB
{
    /**
     * Database driver
     *
     * @var	string
     */
    public $dbdriver = "mssql";
    /**
     * ORDER BY random keyword
     *
     * @var	array
     */
    protected $_random_keyword = array("NEWID()", "RAND(%d)");
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
     * Appends the port number to the hostname, if needed.
     *
     * @param	array	$params
     * @return	void
     */
    public function __construct($params)
    {
        parent::__construct($params);
        if (!empty($this->port)) {
            $this->hostname .= (DIRECTORY_SEPARATOR === "\\" ? "," : ":") . $this->port;
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
        ini_set("mssql.charset", $this->char_set);
        $this->conn_id = $persistent ? mssql_pconnect($this->hostname, $this->username, $this->password) : mssql_connect($this->hostname, $this->username, $this->password);
        if (!$this->conn_id) {
            return false;
        }
        if ($this->database !== "" && !$this->db_select()) {
            log_message("error", "Unable to select database: " . $this->database);
            return $this->db_debug === true ? $this->display_error("db_unable_to_select", $this->database) : false;
        }
        $query = $this->query("SELECT CASE WHEN (@@OPTIONS | 256) = @@OPTIONS THEN 1 ELSE 0 END AS qi");
        $query = $query->row_array();
        $this->_quoted_identifier = empty($query) ? false : (bool) $query["qi"];
        $this->_escape_char = $this->_quoted_identifier ? "\"" : array("[", "]");
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
        if ($database === "") {
            $database = $this->database;
        }
        if (mssql_select_db("[" . $database . "]", $this->conn_id)) {
            $this->database = $database;
            $this->data_cache = array();
            return true;
        }
        return false;
    }
    /**
     * Execute the query
     *
     * @param	string	$sql	an SQL query
     * @return	mixed	resource if rows are returned, bool otherwise
     */
    protected function _execute($sql)
    {
        return mssql_query($sql, $this->conn_id);
    }
    /**
     * Begin Transaction
     *
     * @return	bool
     */
    protected function _trans_begin()
    {
        return $this->simple_query("BEGIN TRAN");
    }
    /**
     * Commit Transaction
     *
     * @return	bool
     */
    protected function _trans_commit()
    {
        return $this->simple_query("COMMIT TRAN");
    }
    /**
     * Rollback Transaction
     *
     * @return	bool
     */
    protected function _trans_rollback()
    {
        return $this->simple_query("ROLLBACK TRAN");
    }
    /**
     * Affected Rows
     *
     * @return	int
     */
    public function affected_rows()
    {
        return mssql_rows_affected($this->conn_id);
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
        $query = version_compare($this->version(), "8", ">=") ? "SELECT SCOPE_IDENTITY() AS last_id" : "SELECT @@IDENTITY AS last_id";
        $query = $this->query($query);
        $query = $query->row();
        return $query->last_id;
    }
    /**
     * Version number query string
     *
     * @return	string
     */
    protected function _version()
    {
        return "SELECT SERVERPROPERTY('ProductVersion') AS ver";
    }
    /**
     * List table query
     *
     * Generates a platform-specific query string so that the table names can be fetched
     *
     * @param	bool	$prefix_limit
     * @return	string
     */
    protected function _list_tables($prefix_limit = false)
    {
        $sql = "SELECT " . $this->escape_identifiers("name") . " FROM " . $this->escape_identifiers("sysobjects") . " WHERE " . $this->escape_identifiers("type") . " = 'U'";
        if ($prefix_limit !== false && $this->dbprefix !== "") {
            $sql .= " AND " . $this->escape_identifiers("name") . " LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_like_escape_str, $this->_like_escape_chr);
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
        if (($query = $this->query($sql)) === false) {
            return false;
        }
        $query = $query->result_object();
        $retval = array();
        $i = 0;
        for ($c = count($query); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->COLUMN_NAME;
            $retval[$i]->type = $query[$i]->DATA_TYPE;
            $retval[$i]->max_length = 0 < $query[$i]->CHARACTER_MAXIMUM_LENGTH ? $query[$i]->CHARACTER_MAXIMUM_LENGTH : $query[$i]->NUMERIC_PRECISION;
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
        static $error = array("code" => 0, "message" => NULL);
        $message = mssql_get_last_message();
        if (!empty($message)) {
            $error["code"] = $this->query("SELECT @@ERROR AS code")->row()->code;
            $error["message"] = $message;
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
        $this->qb_orderby = array();
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
        if ($this->qb_limit) {
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
        $limit = $this->qb_offset + $this->qb_limit;
        if (version_compare($this->version(), "9", ">=") && $this->qb_offset && !empty($this->qb_orderby)) {
            $orderby = $this->_compile_order_by();
            $sql = trim(substr($sql, 0, strrpos($sql, $orderby)));
            if (count($this->qb_select) === 0) {
                $select = "*";
            } else {
                $select = array();
                $field_regexp = $this->_quoted_identifier ? "(\"[^\\\"]+\")" : "(\\[[^\\]]+\\])";
                $i = 0;
                for ($c = count($this->qb_select); $i < $c; $i++) {
                    $select[] = preg_match("/(?:\\s|\\.)" . $field_regexp . "\$/i", $this->qb_select[$i], $m) ? $m[1] : $this->qb_select[$i];
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
        if (version_compare($this->version(), "10", ">=")) {
            return parent::_insert_batch($table, $keys, $values);
        }
        return $this->db->db_debug ? $this->db->display_error("db_unsupported_feature") : false;
    }
    /**
     * Close DB Connection
     *
     * @return	void
     */
    protected function _close()
    {
        mssql_close($this->conn_id);
    }
}

?>