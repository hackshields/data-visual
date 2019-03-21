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
 * Smarty from_charset modifier plugin
 * Type:     modifier
 * Name:     from_charset
 * Purpose:  convert character encoding from $charset to internal encoding
 *
 * @author Rodney Rehm
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_from_charset($params)
{
    if (!Smarty::$_MBSTRING) {
        return $params[0];
    }
    if (!isset($params[1])) {
        $params[1] = "\"ISO-8859-1\"";
    }
    return "mb_convert_encoding(" . $params[0] . ", \"" . addslashes(Smarty::$_CHARSET) . "\", " . $params[1] . ")";
}

?>