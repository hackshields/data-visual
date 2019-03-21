<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("set_realpath")) {
    /**
     * Set Realpath
     *
     * @param	string
     * @param	bool	checks to see if the path exists
     * @return	string
     */
    function set_realpath($path, $check_existance = false)
    {
        if (preg_match("#^(http:\\/\\/|https:\\/\\/|www\\.|ftp|php:\\/\\/)#i", $path) || filter_var($path, FILTER_VALIDATE_IP) === $path) {
            show_error("The path you submitted must be a local server path, not a URL");
        }
        if (realpath($path) !== false) {
            $path = realpath($path);
        } else {
            if ($check_existance && !is_dir($path) && !is_file($path)) {
                show_error("Not a valid path: " . $path);
            }
        }
        return is_dir($path) ? rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR : $path;
    }
}

?>