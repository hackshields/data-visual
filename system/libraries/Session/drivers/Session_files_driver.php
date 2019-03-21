<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CodeIgniter Session Files Driver
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/user_guide/libraries/sessions.html
 */
class CI_Session_files_driver extends CI_Session_driver implements SessionHandlerInterface
{
    /**
     * Save path
     *
     * @var	string
     */
    protected $_save_path = NULL;
    /**
     * File handle
     *
     * @var	resource
     */
    protected $_file_handle = NULL;
    /**
     * File name
     *
     * @var	resource
     */
    protected $_file_path = NULL;
    /**
     * File new flag
     *
     * @var	bool
     */
    protected $_file_new = NULL;
    /**
     * Validate SID regular expression
     *
     * @var	string
     */
    protected $_sid_regexp = NULL;
    /**
     * mbstring.func_overload flag
     *
     * @var	bool
     */
    protected static $func_overload = NULL;
    /**
     * Class constructor
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    public function __construct(&$params)
    {
        parent::__construct($params);
        if (isset($this->_config["save_path"])) {
            $this->_config["save_path"] = rtrim($this->_config["save_path"], "/\\");
            ini_set("session.save_path", $this->_config["save_path"]);
        } else {
            log_message("debug", "Session: \"sess_save_path\" is empty; using \"session.save_path\" value from php.ini.");
            $this->_config["save_path"] = rtrim(ini_get("session.save_path"), "/\\");
        }
        $this->_sid_regexp = $this->_config["_sid_regexp"];
        isset($func_overload) or ini_get("mbstring.func_overload");
    }
    /**
     * Open
     *
     * Sanitizes the save_path directory.
     *
     * @param	string	$save_path	Path to session files' directory
     * @param	string	$name		Session cookie name
     * @return	bool
     */
    public function open($save_path, $name)
    {
        if (!is_dir($save_path)) {
            if (!mkdir($save_path, 448, true)) {
                throw new Exception("Session: Configured save path '" . $this->_config["save_path"] . "' is not a directory, doesn't exist or cannot be created.");
            }
        } else {
            if (!is_writable($save_path)) {
                throw new Exception("Session: Configured save path '" . $this->_config["save_path"] . "' is not writable by the PHP process.");
            }
        }
        $this->_config["save_path"] = $save_path;
        $this->_file_path = $this->_config["save_path"] . DIRECTORY_SEPARATOR . $name . ($this->_config["match_ip"] ? md5($_SERVER["REMOTE_ADDR"]) : "");
        return $this->_success;
    }
    /**
     * Read
     *
     * Reads session data and acquires a lock
     *
     * @param	string	$session_id	Session ID
     * @return	string	Serialized session data
     */
    public function read($session_id)
    {
        if ($this->_file_handle === NULL) {
            $this->_file_new = !file_exists($this->_file_path . $session_id);
            if (($this->_file_handle = fopen($this->_file_path . $session_id, "c+b")) === false) {
                log_message("error", "Session: Unable to open file '" . $this->_file_path . $session_id . "'.");
                return $this->_failure;
            }
            $this->_session_id = $session_id;
            if ($this->_file_new) {
                chmod($this->_file_path . $session_id, 384);
                $this->_fingerprint = md5("");
                return "";
            }
        } else {
            if ($this->_file_handle === false) {
                return $this->_failure;
            }
            rewind($this->_file_handle);
        }
        $session_data = "";
        $read = 0;
        $length = filesize($this->_file_path . $session_id);
        while ($read < $length) {
            if (($buffer = fread($this->_file_handle, $length - $read)) === false) {
                break;
            }
            $session_data .= $buffer;
            $read += self::strlen($buffer);
        }
        $this->_fingerprint = md5($session_data);
        return $session_data;
    }
    /**
     * Write
     *
     * Writes (create / update) session data
     *
     * @param	string	$session_id	Session ID
     * @param	string	$session_data	Serialized session data
     * @return	bool
     */
    public function write($session_id, $session_data)
    {
        if ($session_id !== $this->_session_id && ($this->close() === $this->_failure || $this->read($session_id) === $this->_failure)) {
            return $this->_failure;
        }
        if (!is_resource($this->_file_handle)) {
            return $this->_failure;
        }
        if ($this->_fingerprint === md5($session_data)) {
            return !$this->_file_new && !touch($this->_file_path . $session_id) ? $this->_failure : $this->_success;
        }
        if (!$this->_file_new) {
            ftruncate($this->_file_handle, 0);
            rewind($this->_file_handle);
        }
        if (0 < ($length = strlen($session_data))) {
            $written = 0;
            while ($written < $length) {
                if (($result = fwrite($this->_file_handle, substr($session_data, $written))) === false) {
                    break;
                }
                $written += $result;
            }
            if (!is_int($result)) {
                $this->_fingerprint = md5(substr($session_data, 0, $written));
                log_message("error", "Session: Unable to write data.");
                return $this->_failure;
            }
        }
        $this->_fingerprint = md5($session_data);
        return $this->_success;
    }
    /**
     * Close
     *
     * Releases locks and closes file descriptor.
     *
     * @return	bool
     */
    public function close()
    {
        if (is_resource($this->_file_handle)) {
            flock($this->_file_handle, LOCK_UN);
            fclose($this->_file_handle);
            $this->_file_handle = $this->_file_new = $this->_session_id = NULL;
        }
        return $this->_success;
    }
    /**
     * Destroy
     *
     * Destroys the current session.
     *
     * @param	string	$session_id	Session ID
     * @return	bool
     */
    public function destroy($session_id)
    {
        if ($this->close() === $this->_success) {
            if (file_exists($this->_file_path . $session_id)) {
                $this->_cookie_destroy();
                return unlink($this->_file_path . $session_id) ? $this->_success : $this->_failure;
            }
            return $this->_success;
        }
        if ($this->_file_path !== NULL) {
            clearstatcache();
            if (file_exists($this->_file_path . $session_id)) {
                $this->_cookie_destroy();
                return unlink($this->_file_path . $session_id) ? $this->_success : $this->_failure;
            }
            return $this->_success;
        }
        return $this->_failure;
    }
    /**
     * Garbage Collector
     *
     * Deletes expired sessions
     *
     * @param	int 	$maxlifetime	Maximum lifetime of sessions
     * @return	bool
     */
    public function gc($maxlifetime)
    {
        if (!is_dir($this->_config["save_path"]) || ($directory = opendir($this->_config["save_path"])) === false) {
            log_message("debug", "Session: Garbage collector couldn't list files under directory '" . $this->_config["save_path"] . "'.");
            return $this->_failure;
        }
        $ts = time() - $maxlifetime;
        $pattern = $this->_config["match_ip"] === true ? "[0-9a-f]{32}" : "";
        $pattern = sprintf("#\\A%s" . $pattern . $this->_sid_regexp . "\\z#", preg_quote($this->_config["cookie_name"]));
        while (($file = readdir($directory)) !== false) {
            if (!preg_match($pattern, $file) || !is_file($this->_config["save_path"] . DIRECTORY_SEPARATOR . $file) || ($mtime = filemtime($this->_config["save_path"] . DIRECTORY_SEPARATOR . $file)) === false || $ts < $mtime) {
                continue;
            }
            unlink($this->_config["save_path"] . DIRECTORY_SEPARATOR . $file);
        }
        closedir($directory);
        return $this->_success;
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
}

?>