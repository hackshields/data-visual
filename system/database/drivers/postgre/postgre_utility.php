<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Postgre Utility Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_postgre_utility extends CI_DB_utility
{
    /**
     * List databases statement
     *
     * @var	string
     */
    protected $_list_databases = "SELECT datname FROM pg_database";
    /**
     * OPTIMIZE TABLE statement
     *
     * @var	string
     */
    protected $_optimize_table = "REINDEX TABLE %s";
    /**
     * Export
     *
     * @param	array	$params	Preferences
     * @return	mixed
     */
    protected function _backup($params = array())
    {
        return $this->db->display_error("db_unsupported_feature");
    }
}

?>