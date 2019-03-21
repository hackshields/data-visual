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
/**
 * Smarty regex_replace modifier plugin
 * Type:     modifier
 * Name:     regex_replace
 * Purpose:  regular expression search/replace
 *
 * @link    http://smarty.php.net/manual/en/language.modifier.regex.replace.php
 *          regex_replace (Smarty online manual)
 * @author  Monte Ohrt <monte at ohrt dot com>
 *
 * @param string       $string  input string
 * @param string|array $search  regular expression(s) to search for
 * @param string|array $replace string(s) that should be replaced
 * @param int          $limit   the maximum number of replacements
 *
 * @return string
 */
function smarty_modifier_regex_replace($string, $search, $replace, $limit = -1)
{
    if (is_array($search)) {
        foreach ($search as $idx => $s) {
            $search[$idx] = _smarty_regex_replace_check($s);
        }
    } else {
        $search = _smarty_regex_replace_check($search);
    }
    return preg_replace($search, $replace, $string, $limit);
}
/**
 * @param  string $search string(s) that should be replaced
 *
 * @return string
 * @ignore
 */
function _smarty_regex_replace_check($search)
{
    if (($pos = strpos($search, "")) !== false) {
        $search = substr($search, 0, $pos);
    }
    if (preg_match("!([a-zA-Z\\s]+)\$!s", $search, $match) && strpos($match[1], "e") !== false) {
        $search = substr($search, 0, 0 - strlen($match[1])) . preg_replace("![e\\s]+!", "", $match[1]);
    }
    return $search;
}

?>