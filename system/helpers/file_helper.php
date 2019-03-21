<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("read_file")) {
    /**
     * Read File
     *
     * Opens the file specfied in the path and returns it as a string.
     *
     * @todo	Remove in version 3.1+.
     * @deprecated	3.0.0	It is now just an alias for PHP's native file_get_contents().
     * @param	string	$file	Path to file
     * @return	string	File contents
     */
    function read_file($file)
    {
        return @file_get_contents($file);
    }
}
if (!function_exists("write_file")) {
    /**
     * Write File
     *
     * Writes data to the file specified in the path.
     * Creates a new file if non-existent.
     *
     * @param	string	$path	File path
     * @param	string	$data	Data to write
     * @param	string	$mode	fopen() mode (default: 'wb')
     * @return	bool
     */
    function write_file($path, $data, $mode = "wb")
    {
        if (!($fp = @fopen($path, $mode))) {
            return false;
        }
        flock($fp, LOCK_EX);
        $result = $written = 0;
        $length = strlen($data);
        while ($written < $length) {
            if (($result = fwrite($fp, substr($data, $written))) === false) {
                break;
            }
            $written += $result;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        return is_int($result);
    }
}
if (!function_exists("delete_files")) {
    /**
     * Delete Files
     *
     * Deletes all files contained in the supplied directory path.
     * Files must be writable or owned by the system in order to be deleted.
     * If the second parameter is set to TRUE, any directories contained
     * within the supplied base directory will be nuked as well.
     *
     * @param	string	$path		File path
     * @param	bool	$del_dir	Whether to delete any directories found in the path
     * @param	bool	$htdocs		Whether to skip deleting .htaccess and index page files
     * @param	int	$_level		Current directory depth level (default: 0; internal use only)
     * @return	bool
     */
    function delete_files($path, $del_dir = false, $htdocs = false, $_level = 0)
    {
        $path = rtrim($path, "/\\");
        if (!($current_dir = @opendir($path))) {
            return false;
        }
        while (false !== ($filename = @readdir($current_dir))) {
            if ($filename !== "." && $filename !== "..") {
                $filepath = $path . DIRECTORY_SEPARATOR . $filename;
                if (is_dir($filepath) && $filename[0] !== "." && !is_link($filepath)) {
                    delete_files($filepath, $del_dir, $htdocs, $_level + 1);
                } else {
                    if ($htdocs !== true || !preg_match("/^(\\.htaccess|index\\.(html|htm|php)|web\\.config)\$/i", $filename)) {
                        @unlink($filepath);
                    }
                }
            }
        }
        closedir($current_dir);
        return $del_dir === true && 0 < $_level ? @rmdir($path) : true;
    }
}
if (!function_exists("get_filenames")) {
    /**
     * Get Filenames
     *
     * Reads the specified directory and builds an array containing the filenames.
     * Any sub-folders contained within the specified path are read as well.
     *
     * @param	string	path to source
     * @param	bool	whether to include the path as part of the filename
     * @param	bool	internal variable to determine recursion status - do not use in calls
     * @return	array
     */
    function get_filenames($source_dir, $include_path = false, $_recursion = false)
    {
        static $_filedata = array();
        if ($fp = @opendir($source_dir)) {
            if ($_recursion === false) {
                $_filedata = array();
                $source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }
            while (false !== ($file = readdir($fp))) {
                if (is_dir($source_dir . $file) && $file[0] !== ".") {
                    get_filenames($source_dir . $file . DIRECTORY_SEPARATOR, $include_path, true);
                } else {
                    if ($file[0] !== ".") {
                        $_filedata[] = $include_path === true ? $source_dir . $file : $file;
                    }
                }
            }
            closedir($fp);
            return $_filedata;
        }
        return false;
    }
}
if (!function_exists("get_dir_file_info")) {
    /**
     * Get Directory File Information
     *
     * Reads the specified directory and builds an array containing the filenames,
     * filesize, dates, and permissions
     *
     * Any sub-folders contained within the specified path are read as well.
     *
     * @param	string	path to source
     * @param	bool	Look only at the top level directory specified?
     * @param	bool	internal variable to determine recursion status - do not use in calls
     * @return	array
     */
    function get_dir_file_info($source_dir, $top_level_only = true, $_recursion = false)
    {
        static $_filedata = array();
        $relative_path = $source_dir;
        if ($fp = @opendir($source_dir)) {
            if ($_recursion === false) {
                $_filedata = array();
                $source_dir = rtrim(realpath($source_dir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            }
            while (false !== ($file = readdir($fp))) {
                if (is_dir($source_dir . $file) && $file[0] !== "." && $top_level_only === false) {
                    get_dir_file_info($source_dir . $file . DIRECTORY_SEPARATOR, $top_level_only, true);
                } else {
                    if ($file[0] !== ".") {
                        $_filedata[$file] = get_file_info($source_dir . $file);
                        $_filedata[$file]["relative_path"] = $relative_path;
                    }
                }
            }
            closedir($fp);
            return $_filedata;
        }
        return false;
    }
}
if (!function_exists("get_file_info")) {
    /**
     * Get File Info
     *
     * Given a file and path, returns the name, path, size, date modified
     * Second parameter allows you to explicitly declare what information you want returned
     * Options are: name, server_path, size, date, readable, writable, executable, fileperms
     * Returns FALSE if the file cannot be found.
     *
     * @param	string	path to file
     * @param	mixed	array or comma separated string of information returned
     * @return	array
     */
    function get_file_info($file, $returned_values = array())
    {
        if (!file_exists($file)) {
            return false;
        }
        if (is_string($returned_values)) {
            $returned_values = explode(",", $returned_values);
        }
        foreach ($returned_values as $key) {
            switch ($key) {
                case "name":
                    $fileinfo["name"] = basename($file);
                    break;
                case "server_path":
                    $fileinfo["server_path"] = $file;
                    break;
                case "size":
                    $fileinfo["size"] = filesize($file);
                    break;
                case "date":
                    $fileinfo["date"] = filemtime($file);
                    break;
                case "readable":
                    $fileinfo["readable"] = is_readable($file);
                    break;
                case "writable":
                    $fileinfo["writable"] = is_really_writable($file);
                    break;
                case "executable":
                    $fileinfo["executable"] = is_executable($file);
                    break;
                case "fileperms":
                    $fileinfo["fileperms"] = fileperms($file);
                    break;
            }
        }
        return $fileinfo;
    }
}
if (!function_exists("get_mime_by_extension")) {
    /**
     * Get Mime by Extension
     *
     * Translates a file extension into a mime type based on config/mimes.php.
     * Returns FALSE if it can't determine the type, or open the mime config file
     *
     * Note: this is NOT an accurate way of determining file mime types, and is here strictly as a convenience
     * It should NOT be trusted, and should certainly NOT be used for security
     *
     * @param	string	$filename	File name
     * @return	string
     */
    function get_mime_by_extension($filename)
    {
        static $mimes = NULL;
        if (!is_array($mimes)) {
            $mimes = get_mimes();
            if (empty($mimes)) {
                return false;
            }
        }
        $extension = strtolower(substr(strrchr($filename, "."), 1));
        if (isset($mimes[$extension])) {
            return is_array($mimes[$extension]) ? current($mimes[$extension]) : $mimes[$extension];
        }
        return false;
    }
}
if (!function_exists("symbolic_permissions")) {
    /**
     * Symbolic Permissions
     *
     * Takes a numeric value representing a file's permissions and returns
     * standard symbolic notation representing that value
     *
     * @param	int	$perms	Permissions
     * @return	string
     */
    function symbolic_permissions($perms)
    {
        if (($perms & 49152) === 49152) {
            $symbolic = "s";
        } else {
            if (($perms & 40960) === 40960) {
                $symbolic = "l";
            } else {
                if (($perms & 32768) === 32768) {
                    $symbolic = "-";
                } else {
                    if (($perms & 24576) === 24576) {
                        $symbolic = "b";
                    } else {
                        if (($perms & 16384) === 16384) {
                            $symbolic = "d";
                        } else {
                            if (($perms & 8192) === 8192) {
                                $symbolic = "c";
                            } else {
                                if (($perms & 4096) === 4096) {
                                    $symbolic = "p";
                                } else {
                                    $symbolic = "u";
                                }
                            }
                        }
                    }
                }
            }
        }
        $symbolic .= ($perms & 256 ? "r" : "-") . ($perms & 128 ? "w" : "-") . ($perms & 64 ? $perms & 2048 ? "s" : "x" : ($perms & 2048 ? "S" : "-"));
        $symbolic .= ($perms & 32 ? "r" : "-") . ($perms & 16 ? "w" : "-") . ($perms & 8 ? $perms & 1024 ? "s" : "x" : ($perms & 1024 ? "S" : "-"));
        $symbolic .= ($perms & 4 ? "r" : "-") . ($perms & 2 ? "w" : "-") . ($perms & 1 ? $perms & 512 ? "t" : "x" : ($perms & 512 ? "T" : "-"));
        return $symbolic;
    }
}
if (!function_exists("octal_permissions")) {
    /**
     * Octal Permissions
     *
     * Takes a numeric value representing a file's permissions and returns
     * a three character string representing the file's octal permissions
     *
     * @param	int	$perms	Permissions
     * @return	string
     */
    function octal_permissions($perms)
    {
        return substr(sprintf("%o", $perms), -3);
    }
}

?>