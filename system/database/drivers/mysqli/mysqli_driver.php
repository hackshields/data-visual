<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * MySQLi Database Adapter Class
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
class CI_DB_mysqli_driver extends CI_DB
{
    /**
     * Database driver
     *
     * @var	string
     */
    public $dbdriver = "mysqli";
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
     * MySQLi object
     *
     * Has to be preserved without being assigned to $conn_id.
     *
     * @var	MySQLi
     */
    protected $_mysqli = NULL;
    /**
     * Database connection
     *
     * @param	bool	$persistent
     * @return	object
     */
    public function db_connect($persistent = false)
    {
        if ($this->hostname[0] === "/") {
            $hostname = NULL;
            $port = NULL;
            $socket = $this->hostname;
        } else {
            $hostname = $persistent === true ? "p:" . $this->hostname : $this->hostname;
            $port = empty($this->port) ? NULL : $this->port;
            $socket = NULL;
        }
        $client_flags = $this->compress === true ? MYSQLI_CLIENT_COMPRESS : 0;
        $this->_mysqli = mysqli_init();
        $this->_mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        if (isset($this->stricton)) {
            if ($this->stricton) {
                $this->_mysqli->options(MYSQLI_INIT_COMMAND, "SET SESSION sql_mode = CONCAT(@@sql_mode, \",\", \"STRICT_ALL_TABLES\")");
            } else {
                $this->_mysqli->options(MYSQLI_INIT_COMMAND, "SET SESSION sql_mode =\r\n\t\t\t\t\tREPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(\r\n\t\t\t\t\t@@sql_mode,\r\n\t\t\t\t\t\"STRICT_ALL_TABLES,\", \"\"),\r\n\t\t\t\t\t\",STRICT_ALL_TABLES\", \"\"),\r\n\t\t\t\t\t\"STRICT_ALL_TABLES\", \"\"),\r\n\t\t\t\t\t\"STRICT_TRANS_TABLES,\", \"\"),\r\n\t\t\t\t\t\",STRICT_TRANS_TABLES\", \"\"),\r\n\t\t\t\t\t\"STRICT_TRANS_TABLES\", \"\")");
            }
        }
        if (is_array($this->encrypt)) {
            $ssl = array();
            empty($this->encrypt["ssl_key"]) or $ssl["key"] = $this->encrypt["ssl_key"];
            empty($this->encrypt["ssl_cert"]) or $ssl["cert"] = $this->encrypt["ssl_cert"];
            empty($this->encrypt["ssl_ca"]) or $ssl["ca"] = $this->encrypt["ssl_ca"];
            empty($this->encrypt["ssl_capath"]) or $ssl["capath"] = $this->encrypt["ssl_capath"];
            empty($this->encrypt["ssl_cipher"]) or $ssl["cipher"] = $this->encrypt["ssl_cipher"];
            if (!empty($ssl)) {
                if (isset($this->encrypt["ssl_verify"])) {
                    if ($this->encrypt["ssl_verify"]) {
                        defined("MYSQLI_OPT_SSL_VERIFY_SERVER_CERT") and $this->_mysqli->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
                    } else {
                        if (defined("MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT")) {
                            $client_flags |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
                        }
                    }
                }
                $client_flags |= MYSQLI_CLIENT_SSL;
                $this->_mysqli->ssl_set(isset($ssl["key"]) ? $ssl["key"] : NULL, isset($ssl["cert"]) ? $ssl["cert"] : NULL, isset($ssl["ca"]) ? $ssl["ca"] : NULL, isset($ssl["capath"]) ? $ssl["capath"] : NULL, isset($ssl["cipher"]) ? $ssl["cipher"] : NULL);
            }
        }
        if ($this->_mysqli->real_connect($hostname, $this->username, $this->password, $this->database, $port, $socket, $client_flags)) {
            if ($client_flags & MYSQLI_CLIENT_SSL && version_compare($this->_mysqli->client_info, "5.7.3", "<=") && empty($this->_mysqli->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)) {
                $this->_mysqli->close();
                $message = "MySQLi was configured for an SSL connection, but got an unencrypted connection instead!";
                log_message("error", $message);
                return $this->db->db_debug ? $this->db->display_error($message, "", true) : false;
            }
            if (!$this->_mysqli->set_charset($this->char_set)) {
                log_message("error", "Database: Unable to set the configured connection charset ('" . $this->char_set . "').");
                $this->_mysqli->close();
                return $this->db->db_debug ? $this->display_error("db_unable_to_set_charset", $this->char_set) : false;
            }
            return $this->_mysqli;
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
        if ($this->conn_id !== false && $this->conn_id->ping() === false) {
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
        if ($this->conn_id->select_db($database)) {
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
        $this->data_cache["version"] = $this->conn_id->server_info;
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
        return $this->conn_id->query($this->_prep_query($sql));
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
        $this->conn_id->autocommit(false);
        return is_php("5.5") ? $this->conn_id->begin_transaction() : $this->simple_query("START TRANSACTION");
    }
    /**
     * Commit Transaction
     *
     * @return	bool
     */
    protected function _trans_commit()
    {
        if ($this->conn_id->commit()) {
            $this->conn_id->autocommit(true);
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
        if ($this->conn_id->rollback()) {
            $this->conn_id->autocommit(true);
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
        return $this->conn_id->real_escape_string($str);
    }
    /**
     * Affected Rows
     *
     * @return	int
     */
    public function affected_rows()
    {
        return $this->conn_id->affected_rows;
    }
    /**
     * Insert ID
     *
     * @return	int
     */
    public function insert_id()
    {
        return $this->conn_id->insert_id;
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
     * database error that has occurred.
     *
     * @return	array
     */
    public function error()
    {
        if (!empty($this->_mysqli->connect_errno)) {
            return array("code" => $this->_mysqli->connect_errno, "message" => $this->_mysqli->connect_error);
        }
        return array("code" => $this->conn_id->errno, "message" => $this->conn_id->error);
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
        $this->conn_id->close();
    }
}

?>