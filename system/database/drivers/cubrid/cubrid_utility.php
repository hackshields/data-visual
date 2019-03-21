<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CUBRID Utility Class
 *
 * @category	Database
 * @author		Esen Sagynov
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_cubrid_utility extends CI_DB_utility
{
    /**
     * List databases
     *
     * @return	array
     */
    public function list_databases()
    {
        if (isset($this->db->data_cache["db_names"])) {
            return $this->db->data_cache["db_names"];
        }
        $this->db->data_cache["db_names"] = cubrid_list_dbs($this->db->conn_id);
        return $this->db->data_cache["db_names"];
    }
    /**
     * CUBRID Export
     *
     * @param	array	Preferences
     * @return	mixed
     */
    protected function _backup($params = array())
    {
        return $this->db->display_error("db_unsupported_feature");
    }
}

?>