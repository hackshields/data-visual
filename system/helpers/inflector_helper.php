<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("singular")) {
    /**
     * Singular
     *
     * Takes a plural word and makes it singular
     *
     * @param	string	$str	Input string
     * @return	string
     */
    function singular($str)
    {
        $result = strval($str);
        if (!is_countable($result)) {
            return $result;
        }
        $singular_rules = array("/(matr)ices\$/" => "\\1ix", "/(vert|ind)ices\$/" => "\\1ex", "/^(ox)en/" => "\\1", "/(alias)es\$/" => "\\1", "/([octop|vir])i\$/" => "\\1us", "/(cris|ax|test)es\$/" => "\\1is", "/(shoe)s\$/" => "\\1", "/(o)es\$/" => "\\1", "/(bus|campus)es\$/" => "\\1", "/([m|l])ice\$/" => "\\1ouse", "/(x|ch|ss|sh)es\$/" => "\\1", "/(m)ovies\$/" => "\\1\\2ovie", "/(s)eries\$/" => "\\1\\2eries", "/([^aeiouy]|qu)ies\$/" => "\\1y", "/([lr])ves\$/" => "\\1f", "/(tive)s\$/" => "\\1", "/(hive)s\$/" => "\\1", "/([^f])ves\$/" => "\\1fe", "/(^analy)ses\$/" => "\\1sis", "/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses\$/" => "\\1\\2sis", "/([ti])a\$/" => "\\1um", "/(p)eople\$/" => "\\1\\2erson", "/(m)en\$/" => "\\1an", "/(s)tatuses\$/" => "\\1\\2tatus", "/(c)hildren\$/" => "\\1\\2hild", "/(n)ews\$/" => "\\1\\2ews", "/(quiz)zes\$/" => "\\1", "/([^us])s\$/" => "\\1");
        foreach ($singular_rules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }
        return $result;
    }
}
if (!function_exists("plural")) {
    /**
     * Plural
     *
     * Takes a singular word and makes it plural
     *
     * @param	string	$str	Input string
     * @return	string
     */
    function plural($str)
    {
        $result = strval($str);
        if (!is_countable($result)) {
            return $result;
        }
        $plural_rules = array("/(quiz)\$/" => "\\1zes", "/^(ox)\$/" => "\\1\\2en", "/([m|l])ouse\$/" => "\\1ice", "/(matr|vert|ind)ix|ex\$/" => "\\1ices", "/(x|ch|ss|sh)\$/" => "\\1es", "/([^aeiouy]|qu)y\$/" => "\\1ies", "/(hive)\$/" => "\\1s", "/(?:([^f])fe|([lr])f)\$/" => "\\1\\2ves", "/sis\$/" => "ses", "/([ti])um\$/" => "\\1a", "/(p)erson\$/" => "\\1eople", "/(m)an\$/" => "\\1en", "/(c)hild\$/" => "\\1hildren", "/(buffal|tomat)o\$/" => "\\1\\2oes", "/(bu|campu)s\$/" => "\\1\\2ses", "/(alias|status|virus)\$/" => "\\1es", "/(octop)us\$/" => "\\1i", "/(ax|cris|test)is\$/" => "\\1es", "/s\$/" => "s", "/\$/" => "s");
        foreach ($plural_rules as $rule => $replacement) {
            if (preg_match($rule, $result)) {
                $result = preg_replace($rule, $replacement, $result);
                break;
            }
        }
        return $result;
    }
}
if (!function_exists("camelize")) {
    /**
     * Camelize
     *
     * Takes multiple words separated by spaces or underscores and camelizes them
     *
     * @param	string	$str	Input string
     * @return	string
     */
    function camelize($str)
    {
        return strtolower($str[0]) . substr(str_replace(" ", "", ucwords(preg_replace("/[\\s_]+/", " ", $str))), 1);
    }
}
if (!function_exists("underscore")) {
    /**
     * Underscore
     *
     * Takes multiple words separated by spaces and underscores them
     *
     * @param	string	$str	Input string
     * @return	string
     */
    function underscore($str)
    {
        return preg_replace("/[\\s]+/", "_", trim(MB_ENABLED ? mb_strtolower($str) : strtolower($str)));
    }
}
if (!function_exists("humanize")) {
    /**
     * Humanize
     *
     * Takes multiple words separated by the separator and changes them to spaces
     *
     * @param	string	$str		Input string
     * @param 	string	$separator	Input separator
     * @return	string
     */
    function humanize($str, $separator = "_")
    {
        return ucwords(preg_replace("/[" . preg_quote($separator) . "]+/", " ", trim(MB_ENABLED ? mb_strtolower($str) : strtolower($str))));
    }
}
if (!function_exists("is_countable")) {
    /**
     * Checks if the given word has a plural version.
     *
     * @param	string	$word	Word to check
     * @return	bool
     */
    function is_countable($word)
    {
        return !in_array(strtolower($word), array("audio", "bison", "chassis", "compensation", "coreopsis", "data", "deer", "education", "emoji", "equipment", "fish", "furniture", "gold", "information", "knowledge", "love", "rain", "money", "moose", "nutrition", "offspring", "plankton", "pokemon", "police", "rice", "series", "sheep", "species", "swine", "traffic", "wheat"));
    }
}
if (!function_exists("ordinal_format")) {
    /**
     * Returns the English ordinal numeral for a given number
     *
     * @param  int    $number
     * @return string
     */
    function ordinal_format($number)
    {
        if (!ctype_digit((string) $number) || $number < 1) {
            return $number;
        }
        $last_digit = array("th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th");
        if (11 <= $number % 100 && $number % 100 <= 13) {
            return $number . "th";
        }
        return $number . $last_digit[$number % 10];
    }
}

?>