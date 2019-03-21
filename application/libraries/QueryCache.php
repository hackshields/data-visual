<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
defined("BASEPATH") or exit("No direct script access allowed");
class QueryCache
{
    public $db = NULL;
    public $result_array = array();
    public $result_object = array();
    public $list_fields = array();
    public $field_data = array();
    public $num_rows = NULL;
    public $num_fields = NULL;
    public function __construct(&$db)
    {
        $this->db = $db;
    }
    public function cache(&$query)
    {
        $this->db = false;
        if (!$query) {
            return false;
        }
        $this->result_object = $query->result_object();
        $this->result_array = $query->result_array();
        $this->num_rows = $query->num_rows();
        $this->num_fields = $query->num_fields();
        $this->field_data = $query->field_data();
        $this->list_fields = $query->list_fields();
        return true;
    }
    public function num_rows()
    {
        return $this->num_rows;
    }
    public function result($type = "object")
    {
        if ($type == "object") {
            return $this->result_object();
        }
        return $this->result_array();
    }
    public function custom_result_object($class_name)
    {
        return $this->result_object;
    }
    public function result_array()
    {
        return $this->result_array;
    }
    public function row($n = 0, $type = "object")
    {
    }
    public function set_row($key, $value = NULL)
    {
    }
    public function custom_row_object($n, $type)
    {
    }
    public function row_object($n = 0)
    {
    }
    public function row_array($n = 0)
    {
    }
    public function first_row($type = "object")
    {
    }
    public function last_row($type = "object")
    {
    }
    public function next_row($type = "object")
    {
    }
    public function previous_row($type = "object")
    {
    }
    public function unbuffered_row($type = "object")
    {
    }
    public function num_fields()
    {
        return $this->num_fields;
    }
    public function list_fields()
    {
        return $this->list_fields;
    }
    public function field_data()
    {
        return $this->field_data;
    }
    public function data_seek($n = 0)
    {
        return false;
    }
}

?>