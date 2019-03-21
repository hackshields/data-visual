<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * PDO Forge Class
 *
 * @package		CodeIgniter
 * @subpackage	Drivers
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/database/
 */
class CI_DB_pdo_forge extends CI_DB_forge
{
    /**
     * CREATE TABLE IF statement
     *
     * @var	string
     */
    protected $_create_table_if = false;
    /**
     * DROP TABLE IF statement
     *
     * @var	string
     */
    protected $_drop_table_if = false;
}

?>