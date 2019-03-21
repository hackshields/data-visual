<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("force_download")) {
    /**
     * Force Download
     *
     * Generates headers that force a download to happen
     *
     * @param	mixed	filename (or an array of local file path => destination filename)
     * @param	mixed	the data to be downloaded
     * @param	bool	whether to try and send the actual file MIME type
     * @return	void
     */
    function force_download($filename = "", $data = "", $set_mime = false)
    {
        if ($filename === "" || $data === "") {
            return NULL;
        }
        if ($data === NULL) {
            if (is_array($filename)) {
                if (count($filename) !== 1) {
                    return NULL;
                }
                reset($filename);
                $filepath = key($filename);
                $filename = current($filename);
                if (is_int($filepath)) {
                    return NULL;
                }
            } else {
                $filepath = $filename;
                $filename = explode("/", str_replace(DIRECTORY_SEPARATOR, "/", $filename));
                $filename = end($filename);
            }
            if (!@is_file($filepath) || ($filesize = @filesize($filepath)) === false) {
                return NULL;
            }
        } else {
            $filesize = strlen($data);
        }
        $mime = "application/octet-stream";
        $x = explode(".", $filename);
        $extension = end($x);
        if ($set_mime === true) {
            if (count($x) === 1 || $extension === "") {
                return NULL;
            }
            $mimes =& get_mimes();
            if (isset($mimes[$extension])) {
                $mime = is_array($mimes[$extension]) ? $mimes[$extension][0] : $mimes[$extension];
            }
        }
        if (count($x) !== 1 && isset($_SERVER["HTTP_USER_AGENT"]) && preg_match("/Android\\s(1|2\\.[01])/", $_SERVER["HTTP_USER_AGENT"])) {
            $x[count($x) - 1] = strtoupper($extension);
            $filename = implode(".", $x);
        }
        if (ob_get_level() !== 0 && @ob_end_clean() === false) {
            @ob_clean();
        }
        $charset = strtoupper(config_item("charset"));
        $utf8_filename = $charset !== "UTF-8" ? get_instance()->utf8->convert_to_utf8($filename, $charset) : $filename;
        isset($utf8_filename[0]) and rawurlencode($utf8_filename);
        header("Content-Type: " . $mime);
        header("Content-Disposition: attachment; filename=\"" . $filename . "\";" . $utf8_filename);
        header("Expires: 0");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $filesize);
        header("Cache-Control: private, no-transform, no-store, must-revalidate");
        if ($data !== NULL) {
            exit($data);
        }
        if (@readfile($filepath) === false) {
            return NULL;
        }
        exit;
    }
}

?>