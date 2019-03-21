<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * PDO Utility Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/database/
 */
class CI_DB_pdo_utility extends CI_DB_utility
{
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