<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * oci8 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_oci8_result extends CI_DB_result
{
    /**
     * Statement ID
     *
     * @var	resource
     */
    public $stmt_id = NULL;
    /**
     * Cursor ID
     *
     * @var	resource
     */
    public $curs_id = NULL;
    /**
     * Limit used flag
     *
     * @var	bool
     */
    public $limit_used = NULL;
    /**
     * Commit mode flag
     *
     * @var	int
     */
    public $commit_mode = NULL;
    /**
     * Class constructor
     *
     * @param	object	&$driver_object
     * @return	void
     */
    public function __construct(&$driver_object)
    {
        parent::__construct($driver_object);
        $this->stmt_id = $driver_object->stmt_id;
        $this->curs_id = $driver_object->curs_id;
        $this->limit_used = $driver_object->limit_used;
        $this->commit_mode =& $driver_object->commit_mode;
        $driver_object->stmt_id = false;
    }
    /**
     * Number of fields in the result set
     *
     * @return	int
     */
    public function num_fields()
    {
        $count = oci_num_fields($this->stmt_id);
        return $this->limit_used ? $count - 1 : $count;
    }
    /**
     * Fetch Field Names
     *
     * Generates an array of column names
     *
     * @return	array
     */
    public function list_fields()
    {
        $field_names = array();
        $c = 1;
        for ($fieldCount = $this->num_fields(); $c <= $fieldCount; $c++) {
            $field_names[] = oci_field_name($this->stmt_id, $c);
        }
        return $field_names;
    }
    /**
     * Field data
     *
     * Generates an array of objects containing field meta-data
     *
     * @return	array
     */
    public function field_data()
    {
        $retval = array();
        $c = 1;
        for ($fieldCount = $this->num_fields(); $c <= $fieldCount; $c++) {
            $F = new stdClass();
            $F->name = oci_field_name($this->stmt_id, $c);
            $F->type = oci_field_type($this->stmt_id, $c);
            $F->max_length = oci_field_size($this->stmt_id, $c);
            $retval[] = $F;
        }
        return $retval;
    }
    /**
     * Free the result
     *
     * @return	void
     */
    public function free_result()
    {
        if (is_resource($this->result_id)) {
            oci_free_statement($this->result_id);
            $this->result_id = false;
        }
        if (is_resource($this->stmt_id)) {
            oci_free_statement($this->stmt_id);
        }
        if (is_resource($this->curs_id)) {
            oci_cancel($this->curs_id);
            $this->curs_id = NULL;
        }
    }
    /**
     * Result - associative array
     *
     * Returns the result set as an array
     *
     * @return	array
     */
    protected function _fetch_assoc()
    {
        $id = $this->curs_id ? $this->curs_id : $this->stmt_id;
        return oci_fetch_assoc($id);
    }
    /**
     * Result - object
     *
     * Returns the result set as an object
     *
     * @param	string	$class_name
     * @return	object
     */
    protected function _fetch_object($class_name = "stdClass")
    {
        $row = $this->curs_id ? oci_fetch_object($this->curs_id) : oci_fetch_object($this->stmt_id);
        if ($class_name === "stdClass" || !$row) {
            return $row;
        }
        $class_name = new $class_name();
        foreach ($row as $key => $value) {
            $class_name->{$key} = $value;
        }
        return $class_name;
    }
}

?>