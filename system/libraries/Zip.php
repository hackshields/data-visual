<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Zip Compression Class
 *
 * This class is based on a library I found at Zend:
 * http://www.zend.com/codex.php?id=696&single=1
 *
 * The original library is a little rough around the edges so I
 * refactored it and added several additional methods -- Rick Ellis
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Encryption
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/zip.html
 */
class CI_Zip
{
    /**
     * Zip data in string form
     *
     * @var string
     */
    public $zipdata = "";
    /**
     * Zip data for a directory in string form
     *
     * @var string
     */
    public $directory = "";
    /**
     * Number of files/folder in zip file
     *
     * @var int
     */
    public $entries = 0;
    /**
     * Number of files in zip
     *
     * @var int
     */
    public $file_num = 0;
    /**
     * relative offset of local header
     *
     * @var int
     */
    public $offset = 0;
    /**
     * Reference to time at init
     *
     * @var int
     */
    public $now = NULL;
    /**
     * The level of compression
     *
     * Ranges from 0 to 9, with 9 being the highest level.
     *
     * @var	int
     */
    public $compression_level = 2;
    /**
     * mbstring.func_overload flag
     *
     * @var	bool
     */
    protected static $func_overload = NULL;
    /**
     * Initialize zip compression class
     *
     * @return	void
     */
    public function __construct()
    {
        isset($func_overload) or ini_get("mbstring.func_overload");
        $this->now = time();
        log_message("info", "Zip Compression Class Initialized");
    }
    /**
     * Add Directory
     *
     * Lets you add a virtual directory into which you can place files.
     *
     * @param	mixed	$directory	the directory name. Can be string or array
     * @return	void
     */
    public function add_dir($directory)
    {
        foreach ((array) $directory as $dir) {
            if (!preg_match("|.+/\$|", $dir)) {
                $dir .= "/";
            }
            $dir_time = $this->_get_mod_time($dir);
            $this->_add_dir($dir, $dir_time["file_mtime"], $dir_time["file_mdate"]);
        }
    }
    /**
     * Get file/directory modification time
     *
     * If this is a newly created file/dir, we will set the time to 'now'
     *
     * @param	string	$dir	path to file
     * @return	array	filemtime/filemdate
     */
    protected function _get_mod_time($dir)
    {
        $date = file_exists($dir) ? getdate(filemtime($dir)) : getdate($this->now);
        return array("file_mtime" => ($date["hours"] << 11) + ($date["minutes"] << 5) + $date["seconds"] / 2, "file_mdate" => ($date["year"] - 1980 << 9) + ($date["mon"] << 5) + $date["mday"]);
    }
    /**
     * Add Directory
     *
     * @param	string	$dir	the directory name
     * @param	int	$file_mtime
     * @param	int	$file_mdate
     * @return	void
     */
    protected function _add_dir($dir, $file_mtime, $file_mdate)
    {
        $dir = str_replace("\\", "/", $dir);
        $this->zipdata .= "PK\3\4\n" . pack("v", $file_mtime) . pack("v", $file_mdate) . pack("V", 0) . pack("V", 0) . pack("V", 0) . pack("v", self::strlen($dir)) . pack("v", 0) . $dir . pack("V", 0) . pack("V", 0) . pack("V", 0);
        $this->directory .= "PK\1\2" . pack("v", $file_mtime) . pack("v", $file_mdate) . pack("V", 0) . pack("V", 0) . pack("V", 0) . pack("v", self::strlen($dir)) . pack("v", 0) . pack("v", 0) . pack("v", 0) . pack("v", 0) . pack("V", 16) . pack("V", $this->offset) . $dir;
        $this->offset = self::strlen($this->zipdata);
        $this->entries++;
    }
    /**
     * Add Data to Zip
     *
     * Lets you add files to the archive. If the path is included
     * in the filename it will be placed within a directory. Make
     * sure you use add_dir() first to create the folder.
     *
     * @param	mixed	$filepath	A single filepath or an array of file => data pairs
     * @param	string	$data		Single file contents
     * @return	void
     */
    public function add_data($filepath, $data = NULL)
    {
        if (is_array($filepath)) {
            foreach ($filepath as $path => $data) {
                $file_data = $this->_get_mod_time($path);
                $this->_add_data($path, $data, $file_data["file_mtime"], $file_data["file_mdate"]);
            }
        } else {
            $file_data = $this->_get_mod_time($filepath);
            $this->_add_data($filepath, $data, $file_data["file_mtime"], $file_data["file_mdate"]);
        }
    }
    /**
     * Add Data to Zip
     *
     * @param	string	$filepath	the file name/path
     * @param	string	$data	the data to be encoded
     * @param	int	$file_mtime
     * @param	int	$file_mdate
     * @return	void
     */
    protected function _add_data($filepath, $data, $file_mtime, $file_mdate)
    {
        $filepath = str_replace("\\", "/", $filepath);
        $uncompressed_size = self::strlen($data);
        $crc32 = crc32($data);
        $gzdata = self::substr(gzcompress($data, $this->compression_level), 2, -4);
        $compressed_size = self::strlen($gzdata);
        $this->zipdata .= "PK\3\4\24" . pack("v", $file_mtime) . pack("v", $file_mdate) . pack("V", $crc32) . pack("V", $compressed_size) . pack("V", $uncompressed_size) . pack("v", self::strlen($filepath)) . pack("v", 0) . $filepath . $gzdata;
        $this->directory .= "PK\1\2" . pack("v", $file_mtime) . pack("v", $file_mdate) . pack("V", $crc32) . pack("V", $compressed_size) . pack("V", $uncompressed_size) . pack("v", self::strlen($filepath)) . pack("v", 0) . pack("v", 0) . pack("v", 0) . pack("v", 0) . pack("V", 32) . pack("V", $this->offset) . $filepath;
        $this->offset = self::strlen($this->zipdata);
        $this->entries++;
        $this->file_num++;
    }
    /**
     * Read the contents of a file and add it to the zip
     *
     * @param	string	$path
     * @param	bool	$archive_filepath
     * @return	bool
     */
    public function read_file($path, $archive_filepath = false)
    {
        if (file_exists($path) && false !== ($data = file_get_contents($path))) {
            if (is_string($archive_filepath)) {
                $name = str_replace("\\", "/", $archive_filepath);
            } else {
                $name = str_replace("\\", "/", $path);
                if ($archive_filepath === false) {
                    $name = preg_replace("|.*/(.+)|", "\\1", $name);
                }
            }
            $this->add_data($name, $data);
            return true;
        }
        return false;
    }
    /**
     * Read a directory and add it to the zip.
     *
     * This function recursively reads a folder and everything it contains (including
     * sub-folders) and creates a zip based on it. Whatever directory structure
     * is in the original file path will be recreated in the zip file.
     *
     * @param	string	$path	path to source directory
     * @param	bool	$preserve_filepath
     * @param	string	$root_path
     * @return	bool
     */
    public function read_dir($path, $preserve_filepath = true, $root_path = NULL)
    {
        $path = rtrim($path, "/\\") . DIRECTORY_SEPARATOR;
        if (!($fp = @opendir($path))) {
            return false;
        }
        if ($root_path === NULL) {
            $root_path = str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, dirname($path)) . DIRECTORY_SEPARATOR;
        }
        while (false !== ($file = readdir($fp))) {
            if ($file[0] === ".") {
                continue;
            }
            if (is_dir($path . $file)) {
                $this->read_dir($path . $file . DIRECTORY_SEPARATOR, $preserve_filepath, $root_path);
            } else {
                if (false !== ($data = file_get_contents($path . $file))) {
                    $name = str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, $path);
                    if ($preserve_filepath === false) {
                        $name = str_replace($root_path, "", $name);
                    }
                    $this->add_data($name . $file, $data);
                }
            }
        }
        closedir($fp);
        return true;
    }
    /**
     * Get the Zip file
     *
     * @return	string	(binary encoded)
     */
    public function get_zip()
    {
        if ($this->entries === 0) {
            return false;
        }
        return $this->zipdata . $this->directory . "PK\5\6" . pack("v", $this->entries) . pack("v", $this->entries) . pack("V", self::strlen($this->directory)) . pack("V", self::strlen($this->zipdata)) . "";
    }
    /**
     * Write File to the specified directory
     *
     * Lets you write a file
     *
     * @param	string	$filepath	the file name
     * @return	bool
     */
    public function archive($filepath)
    {
        if (!($fp = @fopen($filepath, "w+b"))) {
            return false;
        }
        flock($fp, LOCK_EX);
        $result = $written = 0;
        $data = $this->get_zip();
        $length = self::strlen($data);
        while ($written < $length) {
            if (($result = fwrite($fp, self::substr($data, $written))) === false) {
                break;
            }
            $written += $result;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return is_int($result);
    }
    /**
     * Download
     *
     * @param	string	$filename	the file name
     * @return	void
     */
    public function download($filename = "backup.zip")
    {
        if (!preg_match("|.+?\\.zip\$|", $filename)) {
            $filename .= ".zip";
        }
        get_instance()->load->helper("download");
        $get_zip = $this->get_zip();
        $zip_content =& $get_zip;
        force_download($filename, $zip_content);
    }
    /**
     * Initialize Data
     *
     * Lets you clear current zip data. Useful if you need to create
     * multiple zips with different data.
     *
     * @return	CI_Zip
     */
    public function clear_data()
    {
        $this->zipdata = "";
        $this->directory = "";
        $this->entries = 0;
        $this->file_num = 0;
        $this->offset = 0;
        return $this;
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