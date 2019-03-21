<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CI_DB
 *
 * Acts as an alias for both CI_DB_driver and CI_DB_query_builder.
 *
 * @see	CI_DB_query_builder
 * @see	CI_DB_driver
 */
class CI_DB
{
}
/**
 * @ignore
 */
class CI_DB
{
}
/**
 * Initialize the database
 *
 * @category	Database
 * @author	EllisLab Dev Team
 * @link	https://codeigniter.com/user_guide/database/
 *
 * @param 	string|string[]	$params
 * @param 	bool		$query_builder_override
 *				Determines if query builder should be used or not
 */
function &DB($params = "", $query_builder_override = NULL)
{
    if (is_string($params) && strpos($params, "://") === false) {
        if (!file_exists($file_path = APPPATH . "config/" . ENVIRONMENT . "/database.php") && !file_exists($file_path = APPPATH . "config/database.php")) {
            show_error("The configuration file database.php does not exist.");
        }
        include $file_path;
        if (class_exists("CI_Controller", false)) {
            foreach (get_instance()->load->get_package_paths() as $path) {
                if ($path !== APPPATH) {
                    if (file_exists($file_path = $path . "config/" . ENVIRONMENT . "/database.php")) {
                        include $file_path;
                    } else {
                        if (file_exists($file_path = $path . "config/database.php")) {
                            include $file_path;
                        }
                    }
                }
            }
        }
        if (empty($db)) {
            show_error("No database connection settings were found in the database config file.");
        }
        if ($params !== "") {
            $active_group = $params;
        }
        if (!isset($active_group)) {
            show_error("You have not specified a database connection group via \$active_group in your config/database.php file.");
        } else {
            if (!isset($db[$active_group])) {
                show_error("You have specified an invalid database connection group (" . $active_group . ") in your config/database.php file.");
            }
        }
        $params = $db[$active_group];
    } else {
        if (is_string($params)) {
            if (($dsn = @parse_url($params)) === false) {
                show_error("Invalid DB Connection String");
            }
            $params = array("dbdriver" => $dsn["scheme"], "hostname" => isset($dsn["host"]) ? rawurldecode($dsn["host"]) : "", "port" => isset($dsn["port"]) ? rawurldecode($dsn["port"]) : "", "username" => isset($dsn["user"]) ? rawurldecode($dsn["user"]) : "", "password" => isset($dsn["pass"]) ? rawurldecode($dsn["pass"]) : "", "database" => isset($dsn["path"]) ? rawurldecode(substr($dsn["path"], 1)) : "");
            if (isset($dsn["query"])) {
                parse_str($dsn["query"], $extra);
                foreach ($extra as $key => $val) {
                    if (is_string($val) && in_array(strtoupper($val), array("TRUE", "FALSE", "NULL"))) {
                        $val = var_export($val, true);
                    }
                    $params[$key] = $val;
                }
            }
        }
    }
    if (empty($params["dbdriver"])) {
        show_error("You have not selected a database type to connect to.");
    }
    if ($query_builder_override !== NULL) {
        $query_builder = $query_builder_override;
    } else {
        if (!isset($query_builder) && isset($active_record)) {
            $query_builder = $active_record;
        }
    }
    require_once BASEPATH . "database/DB_driver.php";
    if (!isset($query_builder) || $query_builder === true) {
        require_once BASEPATH . "database/DB_query_builder.php";
        if (!class_exists("CI_DB", false)) {
            /**
             * CI_DB
             *
             * Acts as an alias for both CI_DB_driver and CI_DB_query_builder.
             *
             * @see	CI_DB_query_builder
             * @see	CI_DB_driver
             */
            class CI_DB extends CI_DB_query_builder
            {
            }
        }
    } else {
        if (!class_exists("CI_DB", false)) {
            /**
             * @ignore
             */
            class CI_DB extends CI_DB_driver
            {
            }
        }
    }
    $driver_file = BASEPATH . "database/drivers/" . $params["dbdriver"] . "/" . $params["dbdriver"] . "_driver.php";
    file_exists($driver_file) or show_error("Invalid DB driver");
    require_once $driver_file;
    require_once BASEPATH . "database/DB_result.php";
    require_once BASEPATH . "database/DB_query_cache.php";
    require_once BASEPATH . "database/drivers/" . $params["dbdriver"] . "/" . $params["dbdriver"] . "_result.php";
    $driver = "CI_DB_" . $params["dbdriver"] . "_driver";
    $DB = new $driver($params);
    if (!empty($DB->subdriver)) {
        $driver_file = BASEPATH . "database/drivers/" . $DB->dbdriver . "/subdrivers/" . $DB->dbdriver . "_" . $DB->subdriver . "_driver.php";
        if (file_exists($driver_file)) {
            require_once $driver_file;
            $driver = "CI_DB_" . $DB->dbdriver . "_" . $DB->subdriver . "_driver";
            $DB = new $driver($params);
        }
    }
    $DB->initialize();
    return $DB;
}

?>