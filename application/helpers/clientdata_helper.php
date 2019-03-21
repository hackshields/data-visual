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
if (!function_exists("save_data")) {
    /**
     * Set cookie
     *
     * Accepts six parameter, or you can submit an associative
     * array in the first parameter containing all the values.
     *
     * @access	public
     * @param	mixed
     * @param	string	the value of the cookie
     * @param	string	the number of seconds until expiration
     * @param	string	the cookie domain.  Usually:  .yourdomain.com
     * @param	string	the cookie path
     * @param	string	the cookie prefix
     * @return	void
     */
    function save_data($name, $data)
    {
        if ($name == "" || !is_array($data)) {
            return NULL;
        }
        $arr = array();
        foreach ($data as $key => $value) {
            array_push($arr, $key . "=" . $value);
        }
        set_cookie($name, implode("|", $arr), 60 * 60 * 24 * 365);
    }
}
if (!function_exists("get_data")) {
    /**
     * Fetch an item from the COOKIE array
     *
     * @access	public
     * @param	string
     * @param	bool
     * @return	mixed
     */
    function get_data($name)
    {
        $data = get_cookie($name);
        if (!$data) {
            return array();
        }
        $a1 = explode("|", $data);
        $ret = array();
        foreach ($a1 as $a2) {
            $a3 = explode("=", $a2);
            if (count($a3) == 2) {
                $ret[$a3[0]] = $a3[1];
            }
        }
        return $ret;
    }
}
if (!function_exists("delete_data")) {
    function delete_data($name)
    {
        delete_cookie($name);
    }
}

?>