<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CodeIgniter Session Driver Class
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Sessions
 * @author	Andrey Andreev
 * @link	https://codeigniter.com/user_guide/libraries/sessions.html
 */
abstract class CI_Session_driver implements SessionHandlerInterface
{
    protected $_config = NULL;
    /**
     * Data fingerprint
     *
     * @var	bool
     */
    protected $_fingerprint = NULL;
    /**
     * Lock placeholder
     *
     * @var	mixed
     */
    protected $_lock = false;
    /**
     * Read session ID
     *
     * Used to detect session_regenerate_id() calls because PHP only calls
     * write() after regenerating the ID.
     *
     * @var	string
     */
    protected $_session_id = NULL;
    /**
     * Success and failure return values
     *
     * Necessary due to a bug in all PHP 5 versions where return values
     * from userspace handlers are not handled properly. PHP 7 fixes the
     * bug, so we need to return different values depending on the version.
     *
     * @see	https://wiki.php.net/rfc/session.user.return-value
     * @var	mixed
     */
    protected $_success = NULL;
    protected $_failure = NULL;
    /**
     * Class constructor
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    public function __construct(&$params)
    {
        $this->_config =& $params;
        if (is_php("7")) {
            $this->_success = true;
            $this->_failure = false;
        } else {
            $this->_success = 0;
            $this->_failure = -1;
        }
    }
    /**
     * Cookie destroy
     *
     * Internal method to force removal of a cookie by the client
     * when session_destroy() is called.
     *
     * @return	bool
     */
    protected function _cookie_destroy()
    {
        return setcookie($this->_config["cookie_name"], NULL, 1, $this->_config["cookie_path"], $this->_config["cookie_domain"], $this->_config["cookie_secure"], true);
    }
    /**
     * Get lock
     *
     * A dummy method allowing drivers with no locking functionality
     * (databases other than PostgreSQL and MySQL) to act as if they
     * do acquire a lock.
     *
     * @param	string	$session_id
     * @return	bool
     */
    protected function _get_lock($session_id)
    {
        $this->_lock = true;
        return true;
    }
    /**
     * Release lock
     *
     * @return	bool
     */
    protected function _release_lock()
    {
        if ($this->_lock) {
            $this->_lock = false;
        }
        return true;
    }
    /**
     * Fail
     *
     * Drivers other than the 'files' one don't (need to) use the
     * session.save_path INI setting, but that leads to confusing
     * error messages emitted by PHP when open() or write() fail,
     * as the message contains session.save_path ...
     * To work around the problem, the drivers will call this method
     * so that the INI is set just in time for the error message to
     * be properly generated.
     *
     * @return	mixed
     */
    protected function _fail()
    {
        ini_set("session.save_path", config_item("sess_save_path"));
        return $this->_failure;
    }
}

?>