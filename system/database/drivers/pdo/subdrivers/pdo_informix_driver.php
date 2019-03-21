<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * PDO Informix Database Adapter Class
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
class CI_DB_pdo_informix_driver extends CI_DB_pdo_driver
{
    /**
     * Sub-driver
     *
     * @var	string
     */
    public $subdriver = "informix";
    /**
     * ORDER BY random keyword
     *
     * @var	array
     */
    protected $_random_keyword = array("ASC", "ASC");
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
        if (empty($this->dsn)) {
            $this->dsn = "informix:";
            if (empty($this->hostname) && empty($this->host) && empty($this->port) && empty($this->service)) {
                if (isset($this->DSN)) {
                    $this->dsn .= "DSN=" . $this->DSN;
                } else {
                    if (!empty($this->database)) {
                        $this->dsn .= "DSN=" . $this->database;
                    }
                }
                return NULL;
            }
            if (isset($this->host)) {
                $this->dsn .= "host=" . $this->host;
            } else {
                $this->dsn .= "host=" . (empty($this->hostname) ? "127.0.0.1" : $this->hostname);
            }
            if (isset($this->service)) {
                $this->dsn .= "; service=" . $this->service;
            } else {
                if (!empty($this->port)) {
                    $this->dsn .= "; service=" . $this->port;
                }
            }
            empty($this->database) or empty($this->server) or $this->dsn .= "; protocol=" . (isset($this->protocol) ? $this->protocol : "onsoctcp") . "; EnableScrollableCursors=1";
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
        $sql = "SELECT \"tabname\" FROM \"systables\"\r\n\t\t\tWHERE \"tabid\" > 99 AND \"tabtype\" = 'T' AND LOWER(\"owner\") = " . $this->escape(strtolower($this->username));
        if ($prefix_limit === true && $this->dbprefix !== "") {
            $sql .= " AND \"tabname\" LIKE '" . $this->escape_like_str($this->dbprefix) . "%' " . sprintf($this->_like_escape_str, $this->_like_escape_chr);
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
        if (strpos($table, ".") !== false) {
            sscanf($table, "%[^.].%s", $owner, $table);
        } else {
            $owner = $this->username;
        }
        return "SELECT \"colname\" FROM \"systables\", \"syscolumns\"\r\n\t\t\tWHERE \"systables\".\"tabid\" = \"syscolumns\".\"tabid\"\r\n\t\t\t\tAND \"systables\".\"tabtype\" = 'T'\r\n\t\t\t\tAND LOWER(\"systables\".\"owner\") = " . $this->escape(strtolower($owner)) . "\r\n\t\t\t\tAND LOWER(\"systables\".\"tabname\") = " . $this->escape(strtolower($table));
    }
    /**
     * Returns an object with field data
     *
     * @param	string	$table
     * @return	array
     */
    public function field_data($table)
    {
        $sql = "SELECT \"syscolumns\".\"colname\" AS \"name\",\r\n\t\t\t\tCASE \"syscolumns\".\"coltype\"\r\n\t\t\t\t\tWHEN 0 THEN 'CHAR'\r\n\t\t\t\t\tWHEN 1 THEN 'SMALLINT'\r\n\t\t\t\t\tWHEN 2 THEN 'INTEGER'\r\n\t\t\t\t\tWHEN 3 THEN 'FLOAT'\r\n\t\t\t\t\tWHEN 4 THEN 'SMALLFLOAT'\r\n\t\t\t\t\tWHEN 5 THEN 'DECIMAL'\r\n\t\t\t\t\tWHEN 6 THEN 'SERIAL'\r\n\t\t\t\t\tWHEN 7 THEN 'DATE'\r\n\t\t\t\t\tWHEN 8 THEN 'MONEY'\r\n\t\t\t\t\tWHEN 9 THEN 'NULL'\r\n\t\t\t\t\tWHEN 10 THEN 'DATETIME'\r\n\t\t\t\t\tWHEN 11 THEN 'BYTE'\r\n\t\t\t\t\tWHEN 12 THEN 'TEXT'\r\n\t\t\t\t\tWHEN 13 THEN 'VARCHAR'\r\n\t\t\t\t\tWHEN 14 THEN 'INTERVAL'\r\n\t\t\t\t\tWHEN 15 THEN 'NCHAR'\r\n\t\t\t\t\tWHEN 16 THEN 'NVARCHAR'\r\n\t\t\t\t\tWHEN 17 THEN 'INT8'\r\n\t\t\t\t\tWHEN 18 THEN 'SERIAL8'\r\n\t\t\t\t\tWHEN 19 THEN 'SET'\r\n\t\t\t\t\tWHEN 20 THEN 'MULTISET'\r\n\t\t\t\t\tWHEN 21 THEN 'LIST'\r\n\t\t\t\t\tWHEN 22 THEN 'Unnamed ROW'\r\n\t\t\t\t\tWHEN 40 THEN 'LVARCHAR'\r\n\t\t\t\t\tWHEN 41 THEN 'BLOB/CLOB/BOOLEAN'\r\n\t\t\t\t\tWHEN 4118 THEN 'Named ROW'\r\n\t\t\t\t\tELSE \"syscolumns\".\"coltype\"\r\n\t\t\t\tEND AS \"type\",\r\n\t\t\t\t\"syscolumns\".\"collength\" as \"max_length\",\r\n\t\t\t\tCASE \"sysdefaults\".\"type\"\r\n\t\t\t\t\tWHEN 'L' THEN \"sysdefaults\".\"default\"\r\n\t\t\t\t\tELSE NULL\r\n\t\t\t\tEND AS \"default\"\r\n\t\t\tFROM \"syscolumns\", \"systables\", \"sysdefaults\"\r\n\t\t\tWHERE \"syscolumns\".\"tabid\" = \"systables\".\"tabid\"\r\n\t\t\t\tAND \"systables\".\"tabid\" = \"sysdefaults\".\"tabid\"\r\n\t\t\t\tAND \"syscolumns\".\"colno\" = \"sysdefaults\".\"colno\"\r\n\t\t\t\tAND \"systables\".\"tabtype\" = 'T'\r\n\t\t\t\tAND LOWER(\"systables\".\"owner\") = " . $this->escape(strtolower($this->username)) . "\r\n\t\t\t\tAND LOWER(\"systables\".\"tabname\") = " . $this->escape(strtolower($table)) . "\r\n\t\t\tORDER BY \"syscolumns\".\"colno\"";
        return ($query = $this->query($sql)) !== false ? $query->result_object() : false;
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
        return "TRUNCATE TABLE ONLY " . $table;
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
     * @param	string	$sql	$SQL Query
     * @return	string
     */
    protected function _limit($sql)
    {
        $select = "SELECT " . ($this->qb_offset ? "SKIP " . $this->qb_offset : "") . "FIRST " . $this->qb_limit . " ";
        return preg_replace("/^(SELECT\\s)/i", $select, $sql, 1);
    }
}

?>