<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
if (!class_exists("Services_JSON") && !function_exists("json_encode")) {
    require_once "json/JSON.php";
    $json = new Services_JSON();
}
if (!function_exists("json_encode")) {
    /**
     * json_encode
     *
     * Encodes php to JSON code.  Parameter is the data to be encoded.
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function json_encode($data = NULL)
    {
        if ($data == NULL) {
            return false;
        }
        return $json->encode($data);
    }
}
if (!function_exists("json_decode")) {
    /**
     * json_decode
     *
     * Decodes JSON code to php.  Parameter is the data to be decoded.
     *
     * @access	public
     * @param	string
     * @return	string
     */
    function json_decode($data = NULL)
    {
        if ($data == NULL) {
            return false;
        }
        return $json->decode($data);
    }
}

?>