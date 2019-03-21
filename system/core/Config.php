<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Config Class
 *
 * This class contains functions that enable config files to be managed
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Config
{
    /**
     * List of all loaded config values
     *
     * @var	array
     */
    public $config = array();
    /**
     * List of all loaded config files
     *
     * @var	array
     */
    public $is_loaded = array();
    /**
     * List of paths to search when trying to load a config file.
     *
     * @used-by	CI_Loader
     * @var		array
     */
    public $_config_paths = NULL;
    /**
     * Class constructor
     *
     * Sets the $config data from the primary config.php file as a class variable.
     *
     * @return	void
     */
    public function __construct()
    {
        $this->config =& get_config();
        if (empty($this->config["base_url"])) {
            if (isset($_SERVER["HTTP_HOST"]) && preg_match("/^((\\[[0-9a-f:]+\\])|(\\d{1,3}(\\.\\d{1,3}){3})|[a-z0-9\\-\\.]+)(:\\d+)?\$/i", $_SERVER["HTTP_HOST"])) {
                $base_url = (is_https() ? "https" : "http") . "://" . $_SERVER["HTTP_HOST"] . substr($_SERVER["SCRIPT_NAME"], 0, strpos($_SERVER["SCRIPT_NAME"], basename($_SERVER["SCRIPT_FILENAME"])));
            } else {
                $base_url = "http://localhost/";
            }
            $this->set_item("base_url", $base_url);
        }
        log_message("info", "Config Class Initialized");
    }
    /**
     * Load Config File
     *
     * @param	string	$file			Configuration file name
     * @param	bool	$use_sections		Whether configuration values should be loaded into their own section
     * @param	bool	$fail_gracefully	Whether to just return FALSE or display an error message
     * @return	bool	TRUE if the file was loaded correctly or FALSE on failure
     */
    public function load($file = "", $use_sections = false, $fail_gracefully = false)
    {
        $file = $file === "" ? "config" : str_replace(".php", "", $file);
        $loaded = false;
        foreach ($this->_config_paths as $path) {
            foreach (array($file, ENVIRONMENT . DIRECTORY_SEPARATOR . $file) as $location) {
                $file_path = $path . "config/" . $location . ".php";
                if (in_array($file_path, $this->is_loaded, true)) {
                    return true;
                }
                if (!file_exists($file_path)) {
                    continue;
                }
                include $file_path;
                if (!isset($config) || !is_array($config)) {
                    if ($fail_gracefully === true) {
                        return false;
                    }
                    show_error("Your " . $file_path . " file does not appear to contain a valid configuration array.");
                }
                if ($use_sections === true) {
                    $this->config[$file] = isset($this->config[$file]) ? array_merge($this->config[$file], $config) : $config;
                } else {
                    $this->config = array_merge($this->config, $config);
                }
                $this->is_loaded[] = $file_path;
                $config = NULL;
                $loaded = true;
                log_message("info", "Config file loaded: " . $file_path);
            }
        }
        if ($loaded === true) {
            return true;
        }
        if ($fail_gracefully === true) {
            return false;
        }
        show_error("The configuration file " . $file . ".php does not exist.");
    }
    /**
     * Fetch a config file item
     *
     * @param	string	$item	Config item name
     * @param	string	$index	Index name
     * @return	string|null	The configuration item or NULL if the item doesn't exist
     */
    public function item($item, $index = "")
    {
        if ($index == "") {
            return isset($this->config[$item]) ? $this->config[$item] : NULL;
        }
        return isset($this->config[$index]) && isset($this->config[$index][$item]) ? $this->config[$index][$item] : NULL;
    }
    /**
     * Fetch a config file item with slash appended (if not empty)
     *
     * @param	string		$item	Config item name
     * @return	string|null	The configuration item or NULL if the item doesn't exist
     */
    public function slash_item($item)
    {
        if (!isset($this->config[$item])) {
            return NULL;
        }
        if (trim($this->config[$item]) === "") {
            return "";
        }
        return rtrim($this->config[$item], "/") . "/";
    }
    /**
     * Site URL
     *
     * Returns base_url . index_page [. uri_string]
     *
     * @uses	CI_Config::_uri_string()
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function site_url($uri = "", $protocol = NULL)
    {
        $base_url = $this->slash_item("base_url");
        if (isset($protocol)) {
            if ($protocol === "") {
                $base_url = substr($base_url, strpos($base_url, "//"));
            } else {
                $base_url = $protocol . substr($base_url, strpos($base_url, "://"));
            }
        }
        if (empty($uri)) {
            return $base_url . $this->item("index_page");
        }
        $uri = $this->_uri_string($uri);
        if ($this->item("enable_query_strings") === false) {
            $suffix = isset($this->config["url_suffix"]) ? $this->config["url_suffix"] : "";
            if ($suffix !== "") {
                if (($offset = strpos($uri, "?")) !== false) {
                    $uri = substr($uri, 0, $offset) . $suffix . substr($uri, $offset);
                } else {
                    $uri .= $suffix;
                }
            }
            return $base_url . $this->slash_item("index_page") . $uri;
        }
        if (strpos($uri, "?") === false) {
            $uri = "?" . $uri;
        }
        return $base_url . $this->item("index_page") . $uri;
    }
    /**
     * Base URL
     *
     * Returns base_url [. uri_string]
     *
     * @uses	CI_Config::_uri_string()
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @param	string	$protocol
     * @return	string
     */
    public function base_url($uri = "", $protocol = NULL)
    {
        $base_url = $this->slash_item("base_url");
        if (isset($protocol)) {
            if ($protocol === "") {
                $base_url = substr($base_url, strpos($base_url, "//"));
            } else {
                $base_url = $protocol . substr($base_url, strpos($base_url, "://"));
            }
        }
        return $base_url . $this->_uri_string($uri);
    }
    /**
     * Build URI string
     *
     * @used-by	CI_Config::site_url()
     * @used-by	CI_Config::base_url()
     *
     * @param	string|string[]	$uri	URI string or an array of segments
     * @return	string
     */
    protected function _uri_string($uri)
    {
        if ($this->item("enable_query_strings") === false) {
            is_array($uri) and implode("/", $uri);
            return ltrim($uri, "/");
        }
        if (is_array($uri)) {
            return http_build_query($uri);
        }
        return $uri;
    }
    /**
     * Set a config file item
     *
     * @param	string	$item	Config item key
     * @param	string	$value	Config item value
     * @return	void
     */
    public function set_item($item, $value)
    {
        $this->config[$item] = $value;
    }
}

?>