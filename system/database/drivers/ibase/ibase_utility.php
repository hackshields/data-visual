<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Interbase/Firebird Utility Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_ibase_utility extends CI_DB_utility
{
    /**
     * Export
     *
     * @param	string	$filename
     * @return	mixed
     */
    protected function _backup($filename)
    {
        if ($service = ibase_service_attach($this->db->hostname, $this->db->username, $this->db->password)) {
            $res = ibase_backup($service, $this->db->database, $filename . ".fbk");
            ibase_service_detach($service);
            return $res;
        }
        return false;
    }
}

?>