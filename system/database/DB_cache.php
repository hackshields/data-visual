<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Database Cache Class
 *
 * @category	Database
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/database/
 */
class CI_DB_Cache
{
    /**
     * CI Singleton
     *
     * @var	object
     */
    public $CI = NULL;
    /**
     * Database object
     *
     * Allows passing of DB object so that multiple database connections
     * and returned DB objects can be supported.
     *
     * @var	object
     */
    public $db = NULL;
    /**
     * Constructor
     *
     * @param	object	&$db
     * @return	void
     */
    public function __construct(&$db)
    {
        $this->CI =& get_instance();
        $this->db =& $db;
        $this->CI->load->helper("file");
        $this->check_path();
    }
    /**
     * Set Cache Directory Path
     *
     * @param	string	$path	Path to the cache directory
     * @return	bool
     */
    public function check_path($path = "")
    {
        if ($path === "") {
            if ($this->db->cachedir === "") {
                return $this->db->cache_off();
            }
            $path = $this->db->cachedir;
        }
        $path = realpath($path) ? rtrim(realpath($path), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : rtrim($path, "/") . "/";
        if (!is_dir($path)) {
            log_message("debug", "DB cache path error: " . $path);
            return $this->db->cache_off();
        }
        if (!is_really_writable($path)) {
            log_message("debug", "DB cache dir not writable: " . $path);
            return $this->db->cache_off();
        }
        $this->db->cachedir = $path;
        return true;
    }
    /**
     * Retrieve a cached query
     *
     * The URI being requested will become the name of the cache sub-folder.
     * An MD5 hash of the SQL statement will become the cache file name.
     *
     * @param	string	$sql
     * @return	string
     */
    public function read($sql)
    {
        $filepath = $this->db->cachedir . md5($sql);
        if (!file_exists($filepath)) {
            return false;
        }
        $data = unserialize(@file_get_contents($filepath));
        if (0 < $data["ttl"] && $data["time"] + $data["ttl"] < time()) {
            unlink($filepath);
            return false;
        }
        if (false === ($cachedata = $data["data"])) {
            return false;
        }
        return $cachedata;
    }
    /**
     * Write a query to a cache file
     *
     * @param	string	$sql
     * @param	object	$object
     * @return	bool
     */
    public function write($sql, $object)
    {
        $dir_path = $this->db->cachedir;
        $filename = md5($sql);
        if (!is_dir($dir_path) && !@mkdir($dir_path, 488)) {
            return false;
        }
        $contents = array("time" => time(), "ttl" => $this->db->cache_ttl, "data" => $object);
        if (write_file($dir_path . $filename, serialize($contents)) === false) {
            return false;
        }
        chmod($dir_path . $filename, 416);
        return true;
    }
    /**
     * Delete cache files within a particular directory
     *
     * @param	string	$segment_one
     * @param	string	$segment_two
     * @return	void
     */
    public function delete($segment_one = "", $segment_two = "")
    {
        if ($segment_one === "") {
            $segment_one = $this->CI->uri->segment(1) == false ? "default" : $this->CI->uri->segment(1);
        }
        if ($segment_two === "") {
            $segment_two = $this->CI->uri->segment(2) == false ? "index" : $this->CI->uri->segment(2);
        }
        $dir_path = $this->db->cachedir . $segment_one . "+" . $segment_two . "/";
        delete_files($dir_path, true);
    }
    /**
     * Delete all existing cache files
     *
     * @return	void
     */
    public function delete_all()
    {
        delete_files($this->db->cachedir, true, true);
    }
}

?>