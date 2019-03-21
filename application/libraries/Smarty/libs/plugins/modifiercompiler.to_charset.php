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
 * Smarty to_charset modifier plugin
 * Type:     modifier
 * Name:     to_charset
 * Purpose:  convert character encoding from internal encoding to $charset
 *
 * @author Rodney Rehm
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_to_charset($params)
{
    if (!Smarty::$_MBSTRING) {
        return $params[0];
    }
    if (!isset($params[1])) {
        $params[1] = "\"ISO-8859-1\"";
    }
    return "mb_convert_encoding(" . $params[0] . ", " . $params[1] . ", \"" . addslashes(Smarty::$_CHARSET) . "\")";
}

?>