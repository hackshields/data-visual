<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Database Result Class
 *
 * This is the platform-independent result class.
 * This class will not be called directly. Rather, the adapter
 * class for the specific database will extend and instantiate it.
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_result
{
    /**
     * Connection ID
     *
     * @var	resource|object
     */
    public $conn_id = NULL;
    /**
     * Result ID
     *
     * @var	resource|object
     */
    public $result_id = NULL;
    /**
     * Result Array
     *
     * @var	array[]
     */
    public $result_array = array();
    /**
     * Result Object
     *
     * @var	object[]
     */
    public $result_object = array();
    /**
     * Custom Result Object
     *
     * @var	object[]
     */
    public $custom_result_object = array();
    /**
     * Current Row index
     *
     * @var	int
     */
    public $current_row = 0;
    /**
     * Number of rows
     *
     * @var	int
     */
    public $num_rows = NULL;
    /**
     * Row data
     *
     * @var	array
     */
    public $row_data = NULL;
    /**
     * Constructor
     *
     * @param	object	$driver_object
     * @return	void
     */
    public function __construct(&$driver_object)
    {
        $this->conn_id = $driver_object->conn_id;
        $this->result_id = $driver_object->result_id;
    }
    /**
     * Number of rows in the result set
     *
     * @return	int
     */
    public function num_rows()
    {
        if (is_int($this->num_rows)) {
            return $this->num_rows;
        }
        if (0 < count($this->result_array)) {
            return $this->num_rows = count($this->result_array);
        }
        if (0 < count($this->result_object)) {
            return $this->num_rows = count($this->result_object);
        }
        return $this->num_rows = count($this->result_array());
    }
    /**
     * Query result. Acts as a wrapper function for the following functions.
     *
     * @param	string	$type	'object', 'array' or a custom class name
     * @return	array
     */
    public function result($type = "object")
    {
        if ($type === "array") {
            return $this->result_array();
        }
        if ($type === "object") {
            return $this->result_object();
        }
        return $this->custom_result_object($type);
    }
    /**
     * Custom query result.
     *
     * @param	string	$class_name
     * @return	array
     */
    public function custom_result_object($class_name)
    {
        if (isset($this->custom_result_object[$class_name])) {
            return $this->custom_result_object[$class_name];
        }
        if (!$this->result_id || $this->num_rows === 0) {
            return array();
        }
        $_data = NULL;
        if (0 < ($c = count($this->result_array))) {
            $_data = "result_array";
        } else {
            if (0 < ($c = count($this->result_object))) {
                $_data = "result_object";
            }
        }
        if ($_data !== NULL) {
            for ($i = 0; $i < $c; $i++) {
                $this->custom_result_object[$class_name][$i] = new $class_name();
                foreach ($this->{$_data}[$i] as $key => $value) {
                    $this->custom_result_object[$class_name][$i]->{$key} = $value;
                }
            }
            return $this->custom_result_object[$class_name];
        }
        is_null($this->row_data) or $this->data_seek(0);
        $this->custom_result_object[$class_name] = array();
        while ($row = $this->_fetch_object($class_name)) {
            $this->custom_result_object[$class_name][] = $row;
        }
        return $this->custom_result_object[$class_name];
    }
    /**
     * Query result. "object" version.
     *
     * @return	array
     */
    public function result_object()
    {
        if (0 < count($this->result_object)) {
            return $this->result_object;
        }
        if (!$this->result_id || $this->num_rows === 0) {
            return array();
        }
        if (0 < ($c = count($this->result_array))) {
            for ($i = 0; $i < $c; $i++) {
                $this->result_object[$i] = (object) $this->result_array[$i];
            }
            return $this->result_object;
        }
        is_null($this->row_data) or $this->data_seek(0);
        while ($row = $this->_fetch_object()) {
            $this->result_object[] = $row;
        }
        return $this->result_object;
    }
    /**
     * Query result. "array" version.
     *
     * @return	array
     */
    public function result_array()
    {
        if (0 < count($this->result_array)) {
            return $this->result_array;
        }
        if (!$this->result_id || $this->num_rows === 0) {
            return array();
        }
        if (0 < ($c = count($this->result_object))) {
            for ($i = 0; $i < $c; $i++) {
                $this->result_array[$i] = (array) $this->result_object[$i];
            }
            return $this->result_array;
        }
        is_null($this->row_data) or $this->data_seek(0);
        while ($row = $this->_fetch_assoc()) {
            $this->result_array[] = $row;
        }
        return $this->result_array;
    }
    /**
     * Row
     *
     * A wrapper method.
     *
     * @param	mixed	$n
     * @param	string	$type	'object' or 'array'
     * @return	mixed
     */
    public function row($n = 0, $type = "object")
    {
        if (!is_numeric($n)) {
            is_array($this->row_data) or $this->row_array(0);
            if (empty($this->row_data) || !array_key_exists($n, $this->row_data)) {
                return NULL;
            }
            return $this->row_data[$n];
        }
        if ($type === "object") {
            return $this->row_object($n);
        }
        if ($type === "array") {
            return $this->row_array($n);
        }
        return $this->custom_row_object($n, $type);
    }
    /**
     * Assigns an item into a particular column slot
     *
     * @param	mixed	$key
     * @param	mixed	$value
     * @return	void
     */
    public function set_row($key, $value = NULL)
    {
        if (!is_array($this->row_data)) {
            $this->row_data = $this->row_array(0);
        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->row_data[$k] = $v;
            }
            return NULL;
        } else {
            if ($key !== "" && $value !== NULL) {
                $this->row_data[$key] = $value;
            }
        }
    }
    /**
     * Returns a single result row - custom object version
     *
     * @param	int	$n
     * @param	string	$type
     * @return	object
     */
    public function custom_row_object($n, $type)
    {
        isset($this->custom_result_object[$type]) or $this->custom_result_object($type);
        if (count($this->custom_result_object[$type]) === 0) {
            return NULL;
        }
        if ($n !== $this->current_row && isset($this->custom_result_object[$type][$n])) {
            $this->current_row = $n;
        }
        return $this->custom_result_object[$type][$this->current_row];
    }
    /**
     * Returns a single result row - object version
     *
     * @param	int	$n
     * @return	object
     */
    public function row_object($n = 0)
    {
        $result = $this->result_object();
        if (count($result) === 0) {
            return NULL;
        }
        if ($n !== $this->current_row && isset($result[$n])) {
            $this->current_row = $n;
        }
        return $result[$this->current_row];
    }
    /**
     * Returns a single result row - array version
     *
     * @param	int	$n
     * @return	array
     */
    public function row_array($n = 0)
    {
        $result = $this->result_array();
        if (count($result) === 0) {
            return NULL;
        }
        if ($n !== $this->current_row && isset($result[$n])) {
            $this->current_row = $n;
        }
        return $result[$this->current_row];
    }
    /**
     * Returns the "first" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function first_row($type = "object")
    {
        $result = $this->result($type);
        return count($result) === 0 ? NULL : $result[0];
    }
    /**
     * Returns the "last" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function last_row($type = "object")
    {
        $result = $this->result($type);
        return count($result) === 0 ? NULL : $result[count($result) - 1];
    }
    /**
     * Returns the "next" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function next_row($type = "object")
    {
        $result = $this->result($type);
        if (count($result) === 0) {
            return NULL;
        }
        return isset($result[$this->current_row + 1]) ? $result[++$this->current_row] : NULL;
    }
    /**
     * Returns the "previous" row
     *
     * @param	string	$type
     * @return	mixed
     */
    public function previous_row($type = "object")
    {
        $result = $this->result($type);
        if (count($result) === 0) {
            return NULL;
        }
        if (isset($result[$this->current_row - 1])) {
            $this->current_row--;
        }
        return $result[$this->current_row];
    }
    /**
     * Returns an unbuffered row and move pointer to next row
     *
     * @param	string	$type	'array', 'object' or a custom class name
     * @return	mixed
     */
    public function unbuffered_row($type = "object")
    {
        if ($type === "array") {
            return $this->_fetch_assoc();
        }
        if ($type === "object") {
            return $this->_fetch_object();
        }
        return $this->_fetch_object($type);
    }
    /**
     * Number of fields in the result set
     *
     * Overridden by driver result classes.
     *
     * @return	int
     */
    public function num_fields()
    {
        return 0;
    }
    /**
     * Fetch Field Names
     *
     * Generates an array of column names.
     *
     * Overridden by driver result classes.
     *
     * @return	array
     */
    public function list_fields()
    {
        return array();
    }
    /**
     * Field data
     *
     * Generates an array of objects containing field meta-data.
     *
     * Overridden by driver result classes.
     *
     * @return	array
     */
    public function field_data()
    {
        return array();
    }
    /**
     * Free the result
     *
     * Overridden by driver result classes.
     *
     * @return	void
     */
    public function free_result()
    {
        $this->result_id = false;
    }
    /**
     * Data Seek
     *
     * Moves the internal pointer to the desired offset. We call
     * this internally before fetching results to make sure the
     * result set starts at zero.
     *
     * Overridden by driver result classes.
     *
     * @param	int	$n
     * @return	bool
     */
    public function data_seek($n = 0)
    {
        return false;
    }
    /**
     * Result - associative array
     *
     * Returns the result set as an array.
     *
     * Overridden by driver result classes.
     *
     * @return	array
     */
    protected function _fetch_assoc()
    {
        return array();
    }
    /**
     * Result - object
     *
     * Returns the result set as an object.
     *
     * Overridden by driver result classes.
     *
     * @param	string	$class_name
     * @return	object
     */
    protected function _fetch_object($class_name = "stdClass")
    {
        return new $class_name();
    }
}

?>