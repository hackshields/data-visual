<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CodeIgniter Driver Library Class
 *
 * This class enables you to create "Driver" libraries that add runtime ability
 * to extend the capabilities of a class via additional driver objects
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Driver_Library
{
    /**
     * Array of drivers that are available to use with the driver class
     *
     * @var array
     */
    protected $valid_drivers = array();
    /**
     * Name of the current class - usually the driver class
     *
     * @var string
     */
    protected $lib_name = NULL;
    /**
     * Get magic method
     *
     * The first time a child is used it won't exist, so we instantiate it
     * subsequents calls will go straight to the proper child.
     *
     * @param	string	Child class name
     * @return	object	Child class
     */
    public function __get($child)
    {
        return $this->load_driver($child);
    }
    /**
     * Load driver
     *
     * Separate load_driver call to support explicit driver load by library or user
     *
     * @param	string	Driver name (w/o parent prefix)
     * @return	object	Child class
     */
    public function load_driver($child)
    {
        $prefix = config_item("subclass_prefix");
        if (!isset($this->lib_name)) {
            $this->lib_name = str_replace(array("CI_", $prefix), "", get_class($this));
        }
        $child_name = $this->lib_name . "_" . $child;
        if (!in_array($child, $this->valid_drivers)) {
            $msg = "Invalid driver requested: " . $child_name;
            log_message("error", $msg);
            show_error($msg);
        }
        $CI = get_instance();
        $paths = $CI->load->get_package_paths(true);
        $class_name = $prefix . $child_name;
        $found = class_exists($class_name, false);
        if (!$found) {
            foreach ($paths as $path) {
                $file = $path . "libraries/" . $this->lib_name . "/drivers/" . $prefix . $child_name . ".php";
                if (file_exists($file)) {
                    $basepath = BASEPATH . "libraries/" . $this->lib_name . "/drivers/" . $child_name . ".php";
                    if (!file_exists($basepath)) {
                        $msg = "Unable to load the requested class: CI_" . $child_name;
                        log_message("error", $msg);
                        show_error($msg);
                    }
                    include_once $basepath;
                    include_once $file;
                    $found = true;
                    break;
                }
            }
        }
        if (!$found) {
            $class_name = "CI_" . $child_name;
            if (!class_exists($class_name, false)) {
                foreach ($paths as $path) {
                    $file = $path . "libraries/" . $this->lib_name . "/drivers/" . $child_name . ".php";
                    if (file_exists($file)) {
                        include_once $file;
                        break;
                    }
                }
            }
        }
        if (!class_exists($class_name, false)) {
            if (class_exists($child_name, false)) {
                $class_name = $child_name;
            } else {
                $msg = "Unable to load the requested driver: " . $class_name;
                log_message("error", $msg);
                show_error($msg);
            }
        }
        $obj = new $class_name();
        $obj->decorate($this);
        $this->{$child} = $obj;
        return $this->{$child};
    }
}
/**
 * CodeIgniter Driver Class
 *
 * This class enables you to create drivers for a Library based on the Driver Library.
 * It handles the drivers' access to the parent library
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Driver
{
    /**
     * Instance of the parent class
     *
     * @var object
     */
    protected $_parent = NULL;
    /**
     * List of methods in the parent class
     *
     * @var array
     */
    protected $_methods = array();
    /**
     * List of properties in the parent class
     *
     * @var array
     */
    protected $_properties = array();
    /**
     * Array of methods and properties for the parent class(es)
     *
     * @static
     * @var	array
     */
    protected static $_reflections = array();
    /**
     * Decorate
     *
     * Decorates the child with the parent driver lib's methods and properties
     *
     * @param	object
     * @return	void
     */
    public function decorate($parent)
    {
        $this->_parent = $parent;
        $class_name = get_class($parent);
        if (!isset(self::$_reflections[$class_name])) {
            $r = new ReflectionObject($parent);
            foreach ($r->getMethods() as $method) {
                if ($method->isPublic()) {
                    $this->_methods[] = $method->getName();
                }
            }
            foreach ($r->getProperties() as $prop) {
                if ($prop->isPublic()) {
                    $this->_properties[] = $prop->getName();
                }
            }
            self::$_reflections[$class_name] = array($this->_methods, $this->_properties);
        } else {
            list($this->_methods, $this->_properties) = self::$_reflections[$class_name];
        }
    }
    /**
     * __call magic method
     *
     * Handles access to the parent driver library's methods
     *
     * @param	string
     * @param	array
     * @return	mixed
     */
    public function __call($method, $args = array())
    {
        if (in_array($method, $this->_methods)) {
            return call_user_func_array(array($this->_parent, $method), $args);
        }
        throw new BadMethodCallException("No such method: " . $method . "()");
    }
    /**
     * __get magic method
     *
     * Handles reading of the parent driver library's properties
     *
     * @param	string
     * @return	mixed
     */
    public function __get($var)
    {
        if (in_array($var, $this->_properties)) {
            return $this->_parent->{$var};
        }
    }
    /**
     * __set magic method
     *
     * Handles writing to the parent driver library's properties
     *
     * @param	string
     * @param	array
     * @return	mixed
     */
    public function __set($var, $val)
    {
        if (in_array($var, $this->_properties)) {
            $this->_parent->{$var} = $val;
        }
    }
}

?>