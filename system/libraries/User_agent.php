<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * User Agent Class
 *
 * Identifies the platform, browser, robot, or mobile device of the browsing agent
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	User Agent
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/user_agent.html
 */
class CI_User_agent
{
    /**
     * Current user-agent
     *
     * @var string
     */
    public $agent = NULL;
    /**
     * Flag for if the user-agent belongs to a browser
     *
     * @var bool
     */
    public $is_browser = false;
    /**
     * Flag for if the user-agent is a robot
     *
     * @var bool
     */
    public $is_robot = false;
    /**
     * Flag for if the user-agent is a mobile browser
     *
     * @var bool
     */
    public $is_mobile = false;
    /**
     * Languages accepted by the current user agent
     *
     * @var array
     */
    public $languages = array();
    /**
     * Character sets accepted by the current user agent
     *
     * @var array
     */
    public $charsets = array();
    /**
     * List of platforms to compare against current user agent
     *
     * @var array
     */
    public $platforms = array();
    /**
     * List of browsers to compare against current user agent
     *
     * @var array
     */
    public $browsers = array();
    /**
     * List of mobile browsers to compare against current user agent
     *
     * @var array
     */
    public $mobiles = array();
    /**
     * List of robots to compare against current user agent
     *
     * @var array
     */
    public $robots = array();
    /**
     * Current user-agent platform
     *
     * @var string
     */
    public $platform = "";
    /**
     * Current user-agent browser
     *
     * @var string
     */
    public $browser = "";
    /**
     * Current user-agent version
     *
     * @var string
     */
    public $version = "";
    /**
     * Current user-agent mobile name
     *
     * @var string
     */
    public $mobile = "";
    /**
     * Current user-agent robot name
     *
     * @var string
     */
    public $robot = "";
    /**
     * HTTP Referer
     *
     * @var	mixed
     */
    public $referer = NULL;
    /**
     * Constructor
     *
     * Sets the User Agent and runs the compilation routine
     *
     * @return	void
     */
    public function __construct()
    {
        $this->_load_agent_file();
        if (isset($_SERVER["HTTP_USER_AGENT"])) {
            $this->agent = trim($_SERVER["HTTP_USER_AGENT"]);
            $this->_compile_data();
        }
        log_message("info", "User Agent Class Initialized");
    }
    /**
     * Compile the User Agent Data
     *
     * @return	bool
     */
    protected function _load_agent_file()
    {
        if ($found = file_exists(APPPATH . "config/user_agents.php")) {
            include APPPATH . "config/user_agents.php";
        }
        if (file_exists(APPPATH . "config/" . ENVIRONMENT . "/user_agents.php")) {
            include APPPATH . "config/" . ENVIRONMENT . "/user_agents.php";
            $found = true;
        }
        if ($found !== true) {
            return false;
        }
        $return = false;
        if (isset($platforms)) {
            $this->platforms = $platforms;
            unset($platforms);
            $return = true;
        }
        if (isset($browsers)) {
            $this->browsers = $browsers;
            unset($browsers);
            $return = true;
        }
        if (isset($mobiles)) {
            $this->mobiles = $mobiles;
            unset($mobiles);
            $return = true;
        }
        if (isset($robots)) {
            $this->robots = $robots;
            unset($robots);
            $return = true;
        }
        return $return;
    }
    /**
     * Compile the User Agent Data
     *
     * @return	bool
     */
    protected function _compile_data()
    {
        $this->_set_platform();
        foreach (array("_set_robot", "_set_browser", "_set_mobile") as $function) {
            if ($this->{$function}() === true) {
                break;
            }
        }
    }
    /**
     * Set the Platform
     *
     * @return	bool
     */
    protected function _set_platform()
    {
        if (is_array($this->platforms) && 0 < count($this->platforms)) {
            foreach ($this->platforms as $key => $val) {
                if (preg_match("|" . preg_quote($key) . "|i", $this->agent)) {
                    $this->platform = $val;
                    return true;
                }
            }
        }
        $this->platform = "Unknown Platform";
        return false;
    }
    /**
     * Set the Browser
     *
     * @return	bool
     */
    protected function _set_browser()
    {
        if (is_array($this->browsers) && 0 < count($this->browsers)) {
            foreach ($this->browsers as $key => $val) {
                if (preg_match("|" . $key . ".*?([0-9\\.]+)|i", $this->agent, $match)) {
                    $this->is_browser = true;
                    $this->version = $match[1];
                    $this->browser = $val;
                    $this->_set_mobile();
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Set the Robot
     *
     * @return	bool
     */
    protected function _set_robot()
    {
        if (is_array($this->robots) && 0 < count($this->robots)) {
            foreach ($this->robots as $key => $val) {
                if (preg_match("|" . preg_quote($key) . "|i", $this->agent)) {
                    $this->is_robot = true;
                    $this->robot = $val;
                    $this->_set_mobile();
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Set the Mobile Device
     *
     * @return	bool
     */
    protected function _set_mobile()
    {
        if (is_array($this->mobiles) && 0 < count($this->mobiles)) {
            foreach ($this->mobiles as $key => $val) {
                if (false !== stripos($this->agent, $key)) {
                    $this->is_mobile = true;
                    $this->mobile = $val;
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Set the accepted languages
     *
     * @return	void
     */
    protected function _set_languages()
    {
        if (count($this->languages) === 0 && !empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
            $this->languages = explode(",", preg_replace("/(;\\s?q=[0-9\\.]+)|\\s/i", "", strtolower(trim($_SERVER["HTTP_ACCEPT_LANGUAGE"]))));
        }
        if (count($this->languages) === 0) {
            $this->languages = array("Undefined");
        }
    }
    /**
     * Set the accepted character sets
     *
     * @return	void
     */
    protected function _set_charsets()
    {
        if (count($this->charsets) === 0 && !empty($_SERVER["HTTP_ACCEPT_CHARSET"])) {
            $this->charsets = explode(",", preg_replace("/(;\\s?q=.+)|\\s/i", "", strtolower(trim($_SERVER["HTTP_ACCEPT_CHARSET"]))));
        }
        if (count($this->charsets) === 0) {
            $this->charsets = array("Undefined");
        }
    }
    /**
     * Is Browser
     *
     * @param	string	$key
     * @return	bool
     */
    public function is_browser($key = NULL)
    {
        if (!$this->is_browser) {
            return false;
        }
        if ($key === NULL) {
            return true;
        }
        return isset($this->browsers[$key]) && $this->browser === $this->browsers[$key];
    }
    /**
     * Is Robot
     *
     * @param	string	$key
     * @return	bool
     */
    public function is_robot($key = NULL)
    {
        if (!$this->is_robot) {
            return false;
        }
        if ($key === NULL) {
            return true;
        }
        return isset($this->robots[$key]) && $this->robot === $this->robots[$key];
    }
    /**
     * Is Mobile
     *
     * @param	string	$key
     * @return	bool
     */
    public function is_mobile($key = NULL)
    {
        if (!$this->is_mobile) {
            return false;
        }
        if ($key === NULL) {
            return true;
        }
        return isset($this->mobiles[$key]) && $this->mobile === $this->mobiles[$key];
    }
    /**
     * Is this a referral from another site?
     *
     * @return	bool
     */
    public function is_referral()
    {
        if (!isset($this->referer)) {
            if (empty($_SERVER["HTTP_REFERER"])) {
                $this->referer = false;
            } else {
                $referer_host = @parse_url($_SERVER["HTTP_REFERER"], PHP_URL_HOST);
                $own_host = parse_url(config_item("base_url"), PHP_URL_HOST);
                $this->referer = $referer_host && $referer_host !== $own_host;
            }
        }
        return $this->referer;
    }
    /**
     * Agent String
     *
     * @return	string
     */
    public function agent_string()
    {
        return $this->agent;
    }
    /**
     * Get Platform
     *
     * @return	string
     */
    public function platform()
    {
        return $this->platform;
    }
    /**
     * Get Browser Name
     *
     * @return	string
     */
    public function browser()
    {
        return $this->browser;
    }
    /**
     * Get the Browser Version
     *
     * @return	string
     */
    public function version()
    {
        return $this->version;
    }
    /**
     * Get The Robot Name
     *
     * @return	string
     */
    public function robot()
    {
        return $this->robot;
    }
    /**
     * Get the Mobile Device
     *
     * @return	string
     */
    public function mobile()
    {
        return $this->mobile;
    }
    /**
     * Get the referrer
     *
     * @return	bool
     */
    public function referrer()
    {
        return empty($_SERVER["HTTP_REFERER"]) ? "" : trim($_SERVER["HTTP_REFERER"]);
    }
    /**
     * Get the accepted languages
     *
     * @return	array
     */
    public function languages()
    {
        if (count($this->languages) === 0) {
            $this->_set_languages();
        }
        return $this->languages;
    }
    /**
     * Get the accepted Character Sets
     *
     * @return	array
     */
    public function charsets()
    {
        if (count($this->charsets) === 0) {
            $this->_set_charsets();
        }
        return $this->charsets;
    }
    /**
     * Test for a particular language
     *
     * @param	string	$lang
     * @return	bool
     */
    public function accept_lang($lang = "en")
    {
        return in_array(strtolower($lang), $this->languages(), true);
    }
    /**
     * Test for a particular character set
     *
     * @param	string	$charset
     * @return	bool
     */
    public function accept_charset($charset = "utf-8")
    {
        return in_array(strtolower($charset), $this->charsets(), true);
    }
    /**
     * Parse a custom user-agent string
     *
     * @param	string	$string
     * @return	void
     */
    public function parse($string)
    {
        $this->is_browser = false;
        $this->is_robot = false;
        $this->is_mobile = false;
        $this->browser = "";
        $this->version = "";
        $this->mobile = "";
        $this->robot = "";
        $this->agent = $string;
        if (!empty($string)) {
            $this->_compile_data();
        }
    }
}

?>