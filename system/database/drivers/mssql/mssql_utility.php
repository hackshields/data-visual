<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * MS SQL Utility Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_mssql_utility extends CI_DB_utility
{
    /**
     * List databases statement
     *
     * @var	string
     */
    protected $_list_databases = "EXEC sp_helpdb";
    /**
     * OPTIMIZE TABLE statement
     *
     * @var	string
     */
    protected $_optimize_table = "ALTER INDEX all ON %s REORGANIZE";
    /**
     * Export
     *
     * @param	array	$params	Preferences
     * @return	bool
     */
    protected function _backup($params = array())
    {
        return $this->db->display_error("db_unsupported_feature");
    }
}

?>