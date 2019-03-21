<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CodeIgniter Memcached Caching Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Core
 * @author		EllisLab Dev Team
 * @link
 */
class CI_Cache_memcached extends CI_Driver
{
    /**
     * Holds the memcached object
     *
     * @var object
     */
    protected $_memcached = NULL;
    /**
     * Memcached configuration
     *
     * @var array
     */
    protected $_config = array("default" => array("host" => "127.0.0.1", "port" => 11211, "weight" => 1));
    /**
     * Class constructor
     *
     * Setup Memcache(d)
     *
     * @return	void
     */
    public function __construct()
    {
        $CI =& get_instance();
        $defaults = $this->_config["default"];
        if ($CI->config->load("memcached", true, true)) {
            $this->_config = $CI->config->config["memcached"];
        }
        if (class_exists("Memcached", false)) {
            $this->_memcached = new Memcached();
        } else {
            if (class_exists("Memcache", false)) {
                $this->_memcached = new Memcache();
            } else {
                log_message("error", "Cache: Failed to create Memcache(d) object; extension not loaded?");
                return NULL;
            }
        }
        foreach ($this->_config as $cache_name => $cache_server) {
            if (!isset($cache_server["hostname"])) {
                log_message("debug", "Cache: Memcache(d) configuration \"" . $cache_name . "\" doesn't include a hostname; ignoring.");
                continue;
            }
            if ($cache_server["hostname"][0] === "/") {
                $cache_server["port"] = 0;
            } else {
                if (empty($cache_server["port"])) {
                    $cache_server["port"] = $defaults["port"];
                }
            }
            isset($cache_server["weight"]) or $cache_server["weight"] = $defaults["weight"];
            if ($this->_memcached instanceof Memcache) {
                $this->_memcached->addServer($cache_server["hostname"], $cache_server["port"], true, $cache_server["weight"]);
            } else {
                if ($this->_memcached instanceof Memcached) {
                    $this->_memcached->addServer($cache_server["hostname"], $cache_server["port"], $cache_server["weight"]);
                }
            }
        }
    }
    /**
     * Fetch from cache
     *
     * @param	string	$id	Cache ID
     * @return	mixed	Data on success, FALSE on failure
     */
    public function get($id)
    {
        $data = $this->_memcached->get($id);
        return is_array($data) ? $data[0] : $data;
    }
    /**
     * Save
     *
     * @param	string	$id	Cache ID
     * @param	mixed	$data	Data being cached
     * @param	int	$ttl	Time to live
     * @param	bool	$raw	Whether to store the raw value
     * @return	bool	TRUE on success, FALSE on failure
     */
    public function save($id, $data, $ttl = 60, $raw = false)
    {
        if ($raw !== true) {
            $data = array($data, time(), $ttl);
        }
        if ($this->_memcached instanceof Memcached) {
            return $this->_memcached->set($id, $data, $ttl);
        }
        if ($this->_memcached instanceof Memcache) {
            return $this->_memcached->set($id, $data, 0, $ttl);
        }
        return false;
    }
    /**
     * Delete from Cache
     *
     * @param	mixed	$id	key to be deleted.
     * @return	bool	true on success, false on failure
     */
    public function delete($id)
    {
        return $this->_memcached->delete($id);
    }
    /**
     * Increment a raw value
     *
     * @param	string	$id	Cache ID
     * @param	int	$offset	Step/value to add
     * @return	mixed	New value on success or FALSE on failure
     */
    public function increment($id, $offset = 1)
    {
        if (($result = $this->_memcached->increment($id, $offset)) === false) {
            return $this->_memcached->add($id, $offset) ? $offset : false;
        }
        return $result;
    }
    /**
     * Decrement a raw value
     *
     * @param	string	$id	Cache ID
     * @param	int	$offset	Step/value to reduce by
     * @return	mixed	New value on success or FALSE on failure
     */
    public function decrement($id, $offset = 1)
    {
        if (($result = $this->_memcached->decrement($id, $offset)) === false) {
            return $this->_memcached->add($id, 0) ? 0 : false;
        }
        return $result;
    }
    /**
     * Clean the Cache
     *
     * @return	bool	false on failure/true on success
     */
    public function clean()
    {
        return $this->_memcached->flush();
    }
    /**
     * Cache Info
     *
     * @return	mixed	array on success, false on failure
     */
    public function cache_info()
    {
        return $this->_memcached->getStats();
    }
    /**
     * Get Cache Metadata
     *
     * @param	mixed	$id	key to get cache metadata on
     * @return	mixed	FALSE on failure, array on success.
     */
    public function get_metadata($id)
    {
        $stored = $this->_memcached->get($id);
        if (count($stored) !== 3) {
            return false;
        }
        list($data, $time, $ttl) = $stored;
        return array("expire" => $time + $ttl, "mtime" => $time, "data" => $data);
    }
    /**
     * Is supported
     *
     * Returns FALSE if memcached is not supported on the system.
     * If it is, we setup the memcached object & return TRUE
     *
     * @return	bool
     */
    public function is_supported()
    {
        return extension_loaded("memcached") || extension_loaded("memcache");
    }
    /**
     * Class destructor
     *
     * Closes the connection to Memcache(d) if present.
     *
     * @return	void
     */
    public function __destruct()
    {
        if ($this->_memcached instanceof Memcache) {
            $this->_memcached->close();
        } else {
            if ($this->_memcached instanceof Memcached && method_exists($this->_memcached, "quit")) {
                $this->_memcached->quit();
            }
        }
    }
}

?>