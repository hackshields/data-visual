<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log
{
    /**
     * Path to save log files
     *
     * @var string
     */
    protected $_log_path = NULL;
    /**
     * File permissions
     *
     * @var	int
     */
    protected $_file_permissions = 420;
    /**
     * Level of logging
     *
     * @var int
     */
    protected $_threshold = 1;
    /**
     * Array of threshold levels to log
     *
     * @var array
     */
    protected $_threshold_array = array();
    /**
     * Format of timestamp for log files
     *
     * @var string
     */
    protected $_date_fmt = "Y-m-d H:i:s";
    /**
     * Filename extension
     *
     * @var	string
     */
    protected $_file_ext = NULL;
    /**
     * Whether or not the logger can write to the log files
     *
     * @var bool
     */
    protected $_enabled = true;
    /**
     * Predefined logging levels
     *
     * @var array
     */
    protected $_levels = array("ERROR" => 1, "DEBUG" => 2, "INFO" => 3, "ALL" => 4);
    /**
     * mbstring.func_overload flag
     *
     * @var	bool
     */
    protected static $func_overload = NULL;
    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct()
    {
        $config =& get_config();
        isset($func_overload) or ini_get("mbstring.func_overload");
        $this->_log_path = $config["log_path"] !== "" ? rtrim($config["log_path"], "/\\") . DIRECTORY_SEPARATOR : APPPATH . "logs" . DIRECTORY_SEPARATOR;
        $this->_file_ext = isset($config["log_file_extension"]) && $config["log_file_extension"] !== "" ? ltrim($config["log_file_extension"], ".") : "php";
        file_exists($this->_log_path) or mkdir($this->_log_path, 493, true);
        if (!is_dir($this->_log_path) || !is_really_writable($this->_log_path)) {
            $this->_enabled = false;
        }
        if (is_numeric($config["log_threshold"])) {
            $this->_threshold = (int) $config["log_threshold"];
        } else {
            if (is_array($config["log_threshold"])) {
                $this->_threshold = 0;
                $this->_threshold_array = array_flip($config["log_threshold"]);
            }
        }
        if (!empty($config["log_date_format"])) {
            $this->_date_fmt = $config["log_date_format"];
        }
        if (!empty($config["log_file_permissions"]) && is_int($config["log_file_permissions"])) {
            $this->_file_permissions = $config["log_file_permissions"];
        }
    }
    /**
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @param	string	$level 	The error level: 'error', 'debug' or 'info'
     * @param	string	$msg 	The error message
     * @return	bool
     */
    public function write_log($level, $msg)
    {
        if ($this->_enabled === false) {
            return false;
        }
        $level = strtoupper($level);
        if ((!isset($this->_levels[$level]) || $this->_threshold < $this->_levels[$level]) && !isset($this->_threshold_array[$this->_levels[$level]])) {
            return false;
        }
        $filepath = $this->_log_path . "log-" . date("Y-m-d") . "." . $this->_file_ext;
        $message = "";
        if (!file_exists($filepath)) {
            $newfile = true;
            if ($this->_file_ext === "php") {
                $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
            }
        }
        if (!($fp = @fopen($filepath, "ab"))) {
            return false;
        }
        flock($fp, LOCK_EX);
        if (strpos($this->_date_fmt, "u") !== false) {
            $microtime_full = microtime(true);
            $microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
            $date = new DateTime(date("Y-m-d H:i:s." . $microtime_short, $microtime_full));
            $date = $date->format($this->_date_fmt);
        } else {
            $date = date($this->_date_fmt);
        }
        $message .= $this->_format_line($level, $date, $msg);
        $written = 0;
        $length = self::strlen($message);
        while ($written < $length) {
            if (($result = fwrite($fp, self::substr($message, $written))) === false) {
                break;
            }
            $written += $result;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        if (isset($newfile) && $newfile === true) {
            chmod($filepath, $this->_file_permissions);
        }
        return is_int($result);
    }
    /**
     * Format the log line.
     *
     * This is for extensibility of log formatting
     * If you want to change the log format, extend the CI_Log class and override this method
     *
     * @param	string	$level 	The error level
     * @param	string	$date 	Formatted date string
     * @param	string	$message 	The log message
     * @return	string	Formatted log line with a new line character '\n' at the end
     */
    protected function _format_line($level, $date, $message)
    {
        return $level . " - " . $date . " --> " . $message . "\n";
    }
    /**
     * Byte-safe strlen()
     *
     * @param	string	$str
     * @return	int
     */
    protected static function strlen($str)
    {
        return self::$func_overload ? mb_strlen($str, "8bit") : strlen($str);
    }
    /**
     * Byte-safe substr()
     *
     * @param	string	$str
     * @param	int	$start
     * @param	int	$length
     * @return	string
     */
    protected static function substr($str, $start, $length = NULL)
    {
        if (self::$func_overload) {
            return mb_substr($str, $start, $length, "8bit");
        }
        return isset($length) ? substr($str, $start, $length) : substr($str, $start);
    }
}

?>