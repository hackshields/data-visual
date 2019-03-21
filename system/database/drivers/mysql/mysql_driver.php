<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * MySQL Database Adapter Class
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
class CI_DB_mysql_driver extends CI_DB
{
    /**
     * Database driver
     *
     * @var	string
     */
    public $dbdriver = "mysql";
    /**
     * Compression flag
     *
     * @var	bool
     */
    public $compress = false;
    /**
     * DELETE hack flag
     *
     * Whether to use the MySQL "delete hack" which allows the number
     * of affected rows to be shown. Uses a preg_replace when enabled,
     * adding a bit more processing to all queries.
     *
     * @var	bool
     */
    public $delete_hack = true;
    /**
     * Strict ON flag
     *
     * Whether we're running in strict SQL mode.
     *
     * @var	bool
     */
    public $stricton = NULL;
    /**
     * Identifier escape character
     *
     * @var	string
     */
    protected $_escape_char = "`";
    /**
     * Class constructor
     *
     * @param	array	$params
     * @return	void
     */
    public function __construct($params)
    {
        parent::__construct($params);
        if (!empty($this->port)) {
            $this->hostname .= ":" . $this->port;
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
        $client_flags = $this->compress === false ? 0 : MYSQL_CLIENT_COMPRESS;
        if ($this->encrypt === true) {
            $client_flags = $client_flags | MYSQL_CLIENT_SSL;
        }
        $this->conn_id = $persistent === true ? mysql_pconnect($this->hostname, $this->username, $this->password, $client_flags) : mysql_connect($this->hostname, $this->username, $this->password, true, $client_flags);
        if ($this->database !== "" && !$this->db_select()) {
            log_message("error", "Unable to select database: " . $this->database);
            return $this->db_debug === true ? $this->display_error("db_unable_to_select", $this->database) : false;
        }
        if (is_resource($this->conn_id)) {
            if (!mysql_set_charset($this->char_set, $this->conn_id)) {
                log_message("error", "Database: Unable to set the configured connection charset ('" . $this->char_set . "').");
                $this->close();
                return $this->db->debug ? $this->display_error("db_unable_to_set_charset", $this->char_set) : false;
            }
            if (isset($this->stricton)) {
                if ($this->stricton) {
                    $this->simple_query("SET SESSION sql_mode = CONCAT(@@sql_mode, \",\", \"STRICT_ALL_TABLES\")");
                } else {
                    $this->simple_query("SET SESSION sql_mode =\r\n\t\t\t\t\t\tREPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(\r\n\t\t\t\t\t\t@@sql_mode,\r\n\t\t\t\t\t\t\"STRICT_ALL_TABLES,\", \"\"),\r\n\t\t\t\t\t\t\",STRICT_ALL_TABLES\", \"\"),\r\n\t\t\t\t\t\t\"STRICT_ALL_TABLES\", \"\"),\r\n\t\t\t\t\t\t\"STRICT_TRANS_TABLES,\", \"\"),\r\n\t\t\t\t\t\t\",STRICT_TRANS_TABLES\", \"\"),\r\n\t\t\t\t\t\t\"STRICT_TRANS_TABLES\", \"\")");
                }
            }
            return $this->conn_id;
        }
        return false;
    }
    /**
     * Reconnect
     *
     * Keep / reestablish the db connection if no queries have been
     * sent for a length of time exceeding the server's idle timeout
     *
     * @return	void
     */
    public function reconnect()
    {
        if (mysql_ping($this->conn_id) === false) {
            $this->conn_id = false;
        }
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
        if (mysql_select_db($database, $this->conn_id)) {
            $this->database = $database;
            $this->data_cache = array();
            return true;
        }
        return false;
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
        if (!$this->conn_id || ($version = mysql_get_server_info($this->conn_id)) === false) {
            return false;
        }
        $this->data_cache["version"] = $version;
        return $this->data_cache["version"];
    }
    /**
     * Execute the query
     *
     * @param	string	$sql	an SQL query
     * @return	mixed
     */
    protected function _execute($sql)
    {
        return mysql_query($this->_prep_query($sql), $this->conn_id);
    }
    /**
     * Prep the query
     *
     * If needed, each database adapter can prep the query string
     *
     * @param	string	$sql	an SQL query
     * @return	string
     */
    protected function _prep_query($sql)
    {
        if ($this->delete_hack === true && preg_match("/^\\s*DELETE\\s+FROM\\s+(\\S+)\\s*\$/i", $sql)) {
            return trim($sql) . " WHERE 1=1";
        }
        return $sql;
    }
    /**
     * Begin Transaction
     *
     * @return	bool
     */
    protected function _trans_begin()
    {
        $this->simple_query("SET AUTOCOMMIT=0");
        return $this->simple_query("START TRANSACTION");
    }
    /**
     * Commit Transaction
     *
     * @return	bool
     */
    protected function _trans_commit()
    {
        if ($this->simple_query("COMMIT")) {
            $this->simple_query("SET AUTOCOMMIT=1");
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
        if ($this->simple_query("ROLLBACK")) {
            $this->simple_query("SET AUTOCOMMIT=1");
            return true;
        }
        return false;
    }
    /**
     * Platform-dependant string escape
     *
     * @param	string
     * @return	string
     */
    protected function _escape_str($str)
    {
        return mysql_real_escape_string($str, $this->conn_id);
    }
    /**
     * Affected Rows
     *
     * @return	int
     */
    public function affected_rows()
    {
        return mysql_affected_rows($this->conn_id);
    }
    /**
     * Insert ID
     *
     * @return	int
     */
    public function insert_id()
    {
        return mysql_insert_id($this->conn_id);
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
        $sql = "SHOW TABLES FROM " . $this->escape_identifiers($this->database);
        if ($prefix_limit !== false && $this->dbprefix !== "") {
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
        if (($query = $this->query("SHOW COLUMNS FROM " . $this->protect_identifiers($table, true, NULL, false))) === false) {
            return false;
        }
        $query = $query->result_object();
        $retval = array();
        $i = 0;
        for ($c = count($query); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->Field;
            sscanf($query[$i]->Type, "%[a-z](%d)", $retval[$i]->type, $retval[$i]->max_length);
            $retval[$i]->default = $query[$i]->Default;
            $retval[$i]->primary_key = (int) ($query[$i]->Key === "PRI");
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
        return array("code" => mysql_errno($this->conn_id), "message" => mysql_error($this->conn_id));
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
        if (!empty($this->qb_join) && 1 < count($this->qb_from)) {
            return "(" . implode(", ", $this->qb_from) . ")";
        }
        return implode(", ", $this->qb_from);
    }
    /**
     * Close DB Connection
     *
     * @return	void
     */
    protected function _close()
    {
        @mysql_close($this->conn_id);
    }
}

?>