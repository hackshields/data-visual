<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("set_cookie")) {
    /**
     * Set cookie
     *
     * Accepts seven parameters, or you can submit an associative
     * array in the first parameter containing all the values.
     *
     * @param	mixed
     * @param	string	the value of the cookie
     * @param	int	the number of seconds until expiration
     * @param	string	the cookie domain.  Usually:  .yourdomain.com
     * @param	string	the cookie path
     * @param	string	the cookie prefix
     * @param	bool	true makes the cookie secure
     * @param	bool	true makes the cookie accessible via http(s) only (no javascript)
     * @return	void
     */
    function set_cookie($name, $value = "", $expire = 0, $domain = "", $path = "/", $prefix = "", $secure = NULL, $httponly = NULL)
    {
        get_instance()->input->set_cookie($name, $value, $expire, $domain, $path, $prefix, $secure, $httponly);
    }
}
if (!function_exists("get_cookie")) {
    /**
     * Fetch an item from the COOKIE array
     *
     * @param	string
     * @param	bool
     * @return	mixed
     */
    function get_cookie($index, $xss_clean = false)
    {
        $prefix = isset($_COOKIE[$index]) ? "" : config_item("cookie_prefix");
        return get_instance()->input->cookie($prefix . $index, $xss_clean);
    }
}
if (!function_exists("delete_cookie")) {
    /**
     * Delete a COOKIE
     *
     * @param	mixed
     * @param	string	the cookie domain. Usually: .yourdomain.com
     * @param	string	the cookie path
     * @param	string	the cookie prefix
     * @return	void
     */
    function delete_cookie($name, $domain = "", $path = "/", $prefix = "")
    {
        set_cookie($name, "", "", $domain, $path, $prefix);
    }
}

?>