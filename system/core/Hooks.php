<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Hooks Class
 *
 * Provides a mechanism to extend the base system without hacking.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/hooks.html
 */
class CI_Hooks
{
    /**
     * Determines whether hooks are enabled
     *
     * @var	bool
     */
    public $enabled = false;
    /**
     * List of all hooks set in config/hooks.php
     *
     * @var	array
     */
    public $hooks = array();
    /**
     * Array with class objects to use hooks methods
     *
     * @var array
     */
    protected $_objects = array();
    /**
     * In progress flag
     *
     * Determines whether hook is in progress, used to prevent infinte loops
     *
     * @var	bool
     */
    protected $_in_progress = false;
    /**
     * Class constructor
     *
     * @param	CI_Config	$config
     * @return	void
     */
    public function __construct(CI_Config $config)
    {
        log_message("info", "Hooks Class Initialized");
        if ($config->item("enable_hooks") === false) {
            return NULL;
        }
        if (file_exists(APPPATH . "config/hooks.php")) {
            include APPPATH . "config/hooks.php";
        }
        if (file_exists(APPPATH . "config/" . ENVIRONMENT . "/hooks.php")) {
            include APPPATH . "config/" . ENVIRONMENT . "/hooks.php";
        }
        if (!isset($hook) || !is_array($hook)) {
            return NULL;
        }
        $this->hooks =& $hook;
        $this->enabled = true;
    }
    /**
     * Call Hook
     *
     * Calls a particular hook. Called by CodeIgniter.php.
     *
     * @uses	CI_Hooks::_run_hook()
     *
     * @param	string	$which	Hook name
     * @return	bool	TRUE on success or FALSE on failure
     */
    public function call_hook($which = "")
    {
        if (!$this->enabled || !isset($this->hooks[$which])) {
            return false;
        }
        if (is_array($this->hooks[$which]) && !isset($this->hooks[$which]["function"])) {
            foreach ($this->hooks[$which] as $val) {
                $this->_run_hook($val);
            }
        } else {
            $this->_run_hook($this->hooks[$which]);
        }
        return true;
    }
    /**
     * Run Hook
     *
     * Runs a particular hook
     *
     * @param	array	$data	Hook details
     * @return	bool	TRUE on success or FALSE on failure
     */
    protected function _run_hook($data)
    {
        if (is_callable($data)) {
            is_array($data);
            is_array($data) ? $data[0]->{$data}[1]() : $data();
            return true;
        }
        if (!is_array($data)) {
            return false;
        }
        if ($this->_in_progress === true) {
            return NULL;
        }
        if (!(isset($data["filepath"]) && isset($data["filename"]))) {
            return false;
        }
        $filepath = APPPATH . $data["filepath"] . "/" . $data["filename"];
        if (!file_exists($filepath)) {
            return false;
        }
        $class = empty($data["class"]) ? false : $data["class"];
        $function = empty($data["function"]) ? false : $data["function"];
        $params = isset($data["params"]) ? $data["params"] : "";
        if (empty($function)) {
            return false;
        }
        $this->_in_progress = true;
        if ($class !== false) {
            if (isset($this->_objects[$class])) {
                if (method_exists($this->_objects[$class], $function)) {
                    $this->_objects[$class]->{$function}($params);
                } else {
                    return $this->_in_progress = false;
                }
            } else {
                class_exists($class, false) or (require_once $filepath);
                if (!class_exists($class, false) || !method_exists($class, $function)) {
                    return $this->_in_progress = false;
                }
                $this->_objects[$class] = new $class();
                $this->_objects[$class]->{$function}($params);
            }
        } else {
            function_exists($function) or (require_once $filepath);
            if (!function_exists($function)) {
                return $this->_in_progress = false;
            }
            $function($params);
        }
        $this->_in_progress = false;
        return true;
    }
}

?>