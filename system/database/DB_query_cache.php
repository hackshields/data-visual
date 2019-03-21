<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
class CI_DB_Query_Cache
{
    public $db = NULL;
    public $result_array = array();
    public $result_object = array();
    public $list_fields = array();
    public $field_data = array();
    public $num_rows = NULL;
    public $num_fields = NULL;
    public $seek_index = 0;
    public function __construct(&$db)
    {
        $this->db = $db;
    }
    public function cache(&$query)
    {
        $this->db = false;
        $this->result_object = $query->result_object();
        $this->result_array = $query->result_array();
        $this->num_rows = $query->num_rows();
        $this->num_fields = $query->num_fields();
        $this->field_data = $query->field_data();
        $this->list_fields = $query->list_fields();
    }
    public function cacheDirect(&$fields, &$field_data, &$datas)
    {
        $this->list_fields = $fields;
        $this->result_array = $datas;
        $this->num_fields = count($fields);
        $this->num_rows = count($datas);
        $this->field_data = $field_data;
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
    public function result_object()
    {
        $this->seek_index = 0;
        return $this->result_object;
    }
    public function custom_result_object($class_name)
    {
        $this->seek_index = 0;
        return $this->result_object;
    }
    public function result_array()
    {
        $this->seek_index = 0;
        return $this->result_array;
    }
    public function row($n = 0, $type = "object")
    {
        if ($type == "object") {
            return $this->row_object($n);
        }
        return $this->row_array($n);
    }
    public function set_row($key, $value = NULL)
    {
    }
    public function custom_row_object($n, $type)
    {
    }
    public function row_object($n = 0)
    {
        if ($n < count($this->result_object)) {
            return $this->result_object[$n];
        }
        return false;
    }
    public function row_array($n = 0)
    {
        if ($n < count($this->result_array)) {
            return $this->result_array[$n];
        }
        return false;
    }
    public function first_row($type = "object")
    {
        $this->seek_index = 0;
        if ($type == "object") {
            return $this->row_object(0);
        }
        return $this->row_array(0);
    }
    public function last_row($type = "object")
    {
        $this->seek_index = count($this->result_array) - 1;
        if ($type == "object") {
            return $this->row_object($this->seek_index);
        }
        return $this->row_array($this->seek_index);
    }
    public function next_row($type = "object")
    {
        if ($type == "object") {
            return $this->row_object($this->seek_index++);
        }
        return $this->row_array($this->seek_index++);
    }
    public function previous_row($type = "object")
    {
        if ($type == "object") {
            return $this->row_object($this->seek_index--);
        }
        return $this->row_array($this->seek_index--);
    }
    public function unbuffered_row($type = "object")
    {
        return $this->result_array[$this->seek_index++];
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
        $this->seek_index = $n;
        return true;
    }
    public function free_result()
    {
        unset($this->result_array);
        unset($this->result_object);
        unset($this->list_fields);
        unset($this->field_data);
    }
}

?>