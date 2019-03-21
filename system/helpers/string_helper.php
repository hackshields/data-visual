<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("strip_slashes")) {
    /**
     * Strip Slashes
     *
     * Removes slashes contained in a string or in an array
     *
     * @param	mixed	string or array
     * @return	mixed	string or array
     */
    function strip_slashes($str)
    {
        if (!is_array($str)) {
            return stripslashes($str);
        }
        foreach ($str as $key => $val) {
            $str[$key] = strip_slashes($val);
        }
        return $str;
    }
}
if (!function_exists("strip_quotes")) {
    /**
     * Strip Quotes
     *
     * Removes single and double quotes from a string
     *
     * @param	string
     * @return	string
     */
    function strip_quotes($str)
    {
        return str_replace(array("\"", "'"), "", $str);
    }
}
if (!function_exists("quotes_to_entities")) {
    /**
     * Quotes to Entities
     *
     * Converts single and double quotes to entities
     *
     * @param	string
     * @return	string
     */
    function quotes_to_entities($str)
    {
        return str_replace(array("\\'", "\"", "'", "\""), array("&#39;", "&quot;", "&#39;", "&quot;"), $str);
    }
}
if (!function_exists("reduce_double_slashes")) {
    /**
     * Reduce Double Slashes
     *
     * Converts double slashes in a string to a single slash,
     * except those found in http://
     *
     * http://www.some-site.com//index.php
     *
     * becomes:
     *
     * http://www.some-site.com/index.php
     *
     * @param	string
     * @return	string
     */
    function reduce_double_slashes($str)
    {
        return preg_replace("#(^|[^:])//+#", "\\1/", $str);
    }
}
if (!function_exists("reduce_multiples")) {
    /**
     * Reduce Multiples
     *
     * Reduces multiple instances of a particular character.  Example:
     *
     * Fred, Bill,, Joe, Jimmy
     *
     * becomes:
     *
     * Fred, Bill, Joe, Jimmy
     *
     * @param	string
     * @param	string	the character you wish to reduce
     * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
     * @return	string
     */
    function reduce_multiples($str, $character = ",", $trim = false)
    {
        $str = preg_replace("#" . preg_quote($character, "#") . "{2,}#", $character, $str);
        return $trim === true ? trim($str, $character) : $str;
    }
}
if (!function_exists("random_string")) {
    /**
     * Create a "Random" String
     *
     * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
     * @param	int	number of characters
     * @return	string
     */
    function random_string($type = "alnum", $len = 8)
    {
        switch ($type) {
            case "basic":
                return mt_rand();
            case "alnum":
            case "numeric":
            case "nozero":
            case "alpha":
                $pool = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                break;
            case "unique":
            case "md5":
                return md5(uniqid(mt_rand()));
            case "encrypt":
            case "sha1":
                return sha1(uniqid(mt_rand(), true));
            case "alnum":
                $pool = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                break;
            case "numeric":
                $pool = "0123456789";
                break;
            case "nozero":
                $pool = "123456789";
                break;
        }
        return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
    }
}
if (!function_exists("increment_string")) {
    /**
     * Add's _1 to a string or increment the ending number to allow _2, _3, etc
     *
     * @param	string	required
     * @param	string	What should the duplicate number be appended with
     * @param	string	Which number should be used for the first dupe increment
     * @return	string
     */
    function increment_string($str, $separator = "_", $first = 1)
    {
        preg_match("/(.+)" . preg_quote($separator, "/") . "([0-9]+)\$/", $str, $match);
        return isset($match[2]) ? $match[1] . $separator . ($match[2] + 1) : $str . $separator . $first;
    }
}
if (!function_exists("alternator")) {
    /**
     * Alternator
     *
     * Allows strings to be alternated. See docs...
     *
     * @param	string (as many parameters as needed)
     * @return	string
     */
    function alternator()
    {
        static $i = NULL;
        if (func_num_args() === 0) {
            $i = 0;
            return "";
        }
        $args = func_get_args();
        return $args[$i++ % count($args)];
    }
}

?>