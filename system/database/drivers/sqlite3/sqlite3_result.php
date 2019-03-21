<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * SQLite3 Result Class
 *
 * This class extends the parent result class: CI_DB_result
 *
 * @category	Database
 * @author		Andrey Andreev
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_sqlite3_result extends CI_DB_result
{
    /**
     * Number of fields in the result set
     *
     * @return	int
     */
    public function num_fields()
    {
        return $this->result_id->numColumns();
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
        $i = 0;
        for ($c = $this->num_fields(); $i < $c; $i++) {
            $field_names[] = $this->result_id->columnName($i);
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
        static $data_types = NULL;
        $retval = array();
        $i = 0;
        for ($c = $this->num_fields(); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $this->result_id->columnName($i);
            $type = $this->result_id->columnType($i);
            $retval[$i]->type = isset($data_types[$type]) ? $data_types[$type] : $type;
            $retval[$i]->max_length = NULL;
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
        if (is_object($this->result_id)) {
            $this->result_id->finalize();
            $this->result_id = NULL;
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
        return $this->result_id->fetchArray(SQLITE3_ASSOC);
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
        if (($row = $this->result_id->fetchArray(SQLITE3_ASSOC)) === false) {
            return false;
        }
        if ($class_name === "stdClass") {
            return (object) $row;
        }
        $class_name = new $class_name();
        foreach (array_keys($row) as $key) {
            $class_name->{$key} = $row[$key];
        }
        return $class_name;
    }
    /**
     * Data Seek
     *
     * Moves the internal pointer to the desired offset. We call
     * this internally before fetching results to make sure the
     * result set starts at zero.
     *
     * @param	int	$n	(ignored)
     * @return	array
     */
    public function data_seek($n = 0)
    {
        return 0 < $n ? false : $this->result_id->reset();
    }
}

?>