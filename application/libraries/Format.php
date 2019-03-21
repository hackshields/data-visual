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
/**
 * Format class
 * Help convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author    Phil Sturgeon, Chris Kacerguis, @softwarespot
 * @license   http://www.dbad-license.org/
 */
class Format
{
    /**
     * CodeIgniter instance
     *
     * @var object
     */
    private $_CI = NULL;
    /**
     * Data to parse
     *
     * @var mixed
     */
    protected $_data = array();
    /**
     * Type to convert from
     *
     * @var string
     */
    protected $_from_type = NULL;
    const ARRAY_FORMAT = "array";
    const CSV_FORMAT = "csv";
    const JSON_FORMAT = "json";
    const HTML_FORMAT = "html";
    const PHP_FORMAT = "php";
    const SERIALIZED_FORMAT = "serialized";
    const XML_FORMAT = "xml";
    const DEFAULT_FORMAT = self::JSON_FORMAT;
    /**
     * DO NOT CALL THIS DIRECTLY, USE factory()
     *
     * @param NULL $data
     * @param NULL $from_type
     * @throws Exception
     */
    public function __construct($data = NULL, $from_type = NULL)
    {
        $this->_CI =& get_instance();
        $this->_CI->load->helper("inflector");
        if ($from_type !== NULL) {
            if (method_exists($this, "_from_" . $from_type)) {
                $data = call_user_func(array($this, "_from_" . $from_type), $data);
            } else {
                throw new Exception("Format class does not support conversion from \"" . $from_type . "\".");
            }
        }
        $this->_data = $data;
    }
    /**
     * Create an instance of the format class
     * e.g: echo $this->format->factory(['foo' => 'bar'])->to_csv();
     *
     * @param mixed $data Data to convert/parse
     * @param string $from_type Type to convert from e.g. json, csv, html
     *
     * @return object Instance of the format class
     */
    public function factory($data, $from_type = NULL)
    {
        return new static($data, $from_type);
    }
    /**
     * Format data as an array
     *
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @return array Data parsed as an array; otherwise, an empty array
     */
    public function to_array($data = NULL)
    {
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        if (is_array($data) === false) {
            $data = (array) $data;
        }
        $array = array();
        foreach ((array) $data as $key => $value) {
            if (is_object($value) === true || is_array($value) === true) {
                $array[$key] = $this->to_array($value);
            } else {
                $array[$key] = $value;
            }
        }
        return $array;
    }
    /**
     * Format data as XML
     *
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @param NULL $structure
     * @param string $basenode
     * @return mixed
     */
    public function to_xml($data = NULL, $structure = NULL, $basenode = "xml")
    {
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        if ($structure === NULL) {
            $structure = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><" . $basenode . " />");
        }
        if (is_array($data) === false && is_object($data) === false) {
            $data = (array) $data;
        }
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = (int) $value;
            }
            if (is_numeric($key)) {
                $key = singular($basenode) != $basenode ? singular($basenode) : "item";
            }
            $key = preg_replace("/[^a-z_\\-0-9]/i", "", $key);
            if ($key === "_attributes" && (is_array($value) || is_object($value))) {
                $attributes = $value;
                if (is_object($attributes)) {
                    $attributes = get_object_vars($attributes);
                }
                foreach ($attributes as $attribute_name => $attribute_value) {
                    $structure->addAttribute($attribute_name, $attribute_value);
                }
            } else {
                if (is_array($value) || is_object($value)) {
                    $node = $structure->addChild($key);
                    $this->to_xml($value, $node, $key);
                } else {
                    $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, "UTF-8"), ENT_QUOTES, "UTF-8");
                    $structure->addChild($key, $value);
                }
            }
        }
        return $structure->asXML();
    }
    /**
     * Format data as HTML
     *
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @return mixed
     */
    public function to_html($data = NULL)
    {
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        if (is_array($data) === false) {
            $data = (array) $data;
        }
        if (isset($data[0]) && count($data) !== count($data, COUNT_RECURSIVE)) {
            $headings = array_keys($data[0]);
        } else {
            $headings = array_keys($data);
            $data = array($data);
        }
        $this->_CI->load->library("table");
        $this->_CI->table->set_heading($headings);
        foreach ($data as $row) {
            $row = @array_map("strval", $row);
            $this->_CI->table->add_row($row);
        }
        return $this->_CI->table->generate();
    }
    /**
     * @link http://www.metashock.de/2014/02/create-csv-file-in-memory-php/
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @param string $delimiter The optional delimiter parameter sets the field
     * delimiter (one character only). NULL will use the default value (,)
     * @param string $enclosure The optional enclosure parameter sets the field
     * enclosure (one character only). NULL will use the default value (")
     * @return string A csv string
     */
    public function to_csv($data = NULL, $delimiter = ",", $enclosure = "\"")
    {
        $handle = fopen("php://temp/maxmemory:1048576", "w");
        if ($handle === false) {
            return NULL;
        }
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        if ($delimiter === NULL) {
            $delimiter = ",";
        }
        if ($enclosure === NULL) {
            $enclosure = "\"";
        }
        if (is_array($data) === false) {
            $data = (array) $data;
        }
        if (isset($data[0]) && count($data) !== count($data, COUNT_RECURSIVE)) {
            $headings = array_keys($data[0]);
        } else {
            $headings = array_keys($data);
            $data = array($data);
        }
        fputcsv($handle, $headings, $delimiter, $enclosure);
        foreach ($data as $record) {
            if (is_array($record) === false) {
                break;
            }
            $record = @array_map("strval", $record);
            fputcsv($handle, $record, $delimiter, $enclosure);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return $csv;
    }
    /**
     * Encode data as json
     *
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @return string Json representation of a value
     */
    public function to_json($data = NULL)
    {
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        $callback = $this->_CI->input->get("callback");
        if (empty($callback) === true) {
            return json_encode($data);
        }
        if (preg_match("/^[a-z_\\\$][a-z0-9\\\$_]*(\\.[a-z_\\\$][a-z0-9\\\$_]*)*\$/i", $callback)) {
            return $callback . "(" . json_encode($data) . ");";
        }
        $data["warning"] = "INVALID JSONP CALLBACK: " . $callback;
        return json_encode($data);
    }
    /**
     * Encode data as a serialized array
     *
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @return string Serialized data
     */
    public function to_serialized($data = NULL)
    {
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        return serialize($data);
    }
    /**
     * Format data using a PHP structure
     *
     * @param mixed|NULL $data Optional data to pass, so as to override the data passed
     * to the constructor
     * @return mixed String representation of a variable
     */
    public function to_php($data = NULL)
    {
        if ($data === NULL && func_num_args() === 0) {
            $data = $this->_data;
        }
        return var_export($data, true);
    }
    /**
     * @param string $data XML string
     * @return array XML element object; otherwise, empty array
     */
    protected function _from_xml($data)
    {
        return $data ? (array) simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA) : array();
    }
    /**
     * @param string $data CSV string
     * @param string $delimiter The optional delimiter parameter sets the field
     * delimiter (one character only). NULL will use the default value (,)
     * @param string $enclosure The optional enclosure parameter sets the field
     * enclosure (one character only). NULL will use the default value (")
     * @return array A multi-dimensional array with the outer array being the number of rows
     * and the inner arrays the individual fields
     */
    protected function _from_csv($data, $delimiter = ",", $enclosure = "\"")
    {
        if ($delimiter === NULL) {
            $delimiter = ",";
        }
        if ($enclosure === NULL) {
            $enclosure = "\"";
        }
        return str_getcsv($data, $delimiter, $enclosure);
    }
    /**
     * @param string $data Encoded json string
     * @return mixed Decoded json string with leading and trailing whitespace removed
     */
    protected function _from_json($data)
    {
        return json_decode(trim($data));
    }
    /**
     * @param string $data Data to unserialize
     * @return mixed Unserialized data
     */
    protected function _from_serialize($data)
    {
        return unserialize(trim($data));
    }
    /**
     * @param string $data Data to trim leading and trailing whitespace
     * @return string Data with leading and trailing whitespace removed
     */
    protected function _from_php($data)
    {
        return trim($data);
    }
}

?>