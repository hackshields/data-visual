<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Firebird/Interbase Database Adapter Class
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
class CI_DB_ibase_driver extends CI_DB
{
    /**
     * Database driver
     *
     * @var	string
     */
    public $dbdriver = "ibase";
    /**
     * ORDER BY random keyword
     *
     * @var	array
     */
    protected $_random_keyword = array("RAND()", "RAND()");
    /**
     * IBase Transaction status flag
     *
     * @var	resource
     */
    protected $_ibase_trans = NULL;
    /**
     * Non-persistent database connection
     *
     * @param	bool	$persistent
     * @return	resource
     */
    public function db_connect($persistent = false)
    {
        return $persistent === true ? ibase_pconnect($this->hostname . ":" . $this->database, $this->username, $this->password, $this->char_set) : ibase_connect($this->hostname . ":" . $this->database, $this->username, $this->password, $this->char_set);
    }
    /**
     * Database version number
     *
     * @return	string
     */
    public function version()
    {
        if (isset($this->data_cache["version"])) {
            return $this->data_cache["version"];
        }
        if ($service = ibase_service_attach($this->hostname, $this->username, $this->password)) {
            $this->data_cache["version"] = ibase_server_info($service, IBASE_SVC_SERVER_VERSION);
            ibase_service_detach($service);
            return $this->data_cache["version"];
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
        return ibase_query(isset($this->_ibase_trans) ? $this->_ibase_trans : $this->conn_id, $sql);
    }
    /**
     * Begin Transaction
     *
     * @return	bool
     */
    protected function _trans_begin()
    {
        if (($trans_handle = ibase_trans($this->conn_id)) === false) {
            return false;
        }
        $this->_ibase_trans = $trans_handle;
        return true;
    }
    /**
     * Commit Transaction
     *
     * @return	bool
     */
    protected function _trans_commit()
    {
        if (ibase_commit($this->_ibase_trans)) {
            $this->_ibase_trans = NULL;
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
        if (ibase_rollback($this->_ibase_trans)) {
            $this->_ibase_trans = NULL;
            return true;
        }
        return false;
    }
    /**
     * Affected Rows
     *
     * @return	int
     */
    public function affected_rows()
    {
        return ibase_affected_rows($this->conn_id);
    }
    /**
     * Insert ID
     *
     * @param	string	$generator_name
     * @param	int	$inc_by
     * @return	int
     */
    public function insert_id($generator_name, $inc_by = 0)
    {
        return ibase_gen_id("\"" . $generator_name . "\"", $inc_by);
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
        $sql = "SELECT TRIM(\"RDB\$RELATION_NAME\") AS TABLE_NAME FROM \"RDB\$RELATIONS\" WHERE \"RDB\$RELATION_NAME\" NOT LIKE 'RDB\$%' AND \"RDB\$RELATION_NAME\" NOT LIKE 'MON\$%'";
        if ($prefix_limit !== false && $this->dbprefix !== "") {
            return $sql . " AND TRIM(\"RDB\$RELATION_NAME\") AS TABLE_NAME LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_like_escape_str, $this->_like_escape_chr);
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
        return "SELECT TRIM(\"RDB\$FIELD_NAME\") AS COLUMN_NAME FROM \"RDB\$RELATION_FIELDS\" WHERE \"RDB\$RELATION_NAME\" = " . $this->escape($table);
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
        return ($query = $this->query($sql)) !== false ? $query->result_object() : false;
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
        return array("code" => ibase_errcode(), "message" => ibase_errmsg());
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
        if (stripos($this->version(), "firebird") !== false) {
            $select = "FIRST " . $this->qb_limit . ($this->qb_offset ? " SKIP " . $this->qb_offset : "");
        } else {
            $select = "ROWS " . ($this->qb_offset ? $this->qb_offset . " TO " . ($this->qb_limit + $this->qb_offset) : $this->qb_limit);
        }
        return preg_replace("`SELECT`i", "SELECT " . $select, $sql, 1);
    }
    /**
     * Close DB Connection
     *
     * @return	void
     */
    protected function _close()
    {
        ibase_close($this->conn_id);
    }
}

?>