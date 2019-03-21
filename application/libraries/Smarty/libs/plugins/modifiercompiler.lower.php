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
 * Smarty lower modifier plugin
 * Type:     modifier
 * Name:     lower
 * Purpose:  convert string to lowercase
 *
 * @link   http://www.smarty.net/manual/en/language.modifier.lower.php lower (Smarty online manual)
 * @author Monte Ohrt <monte at ohrt dot com>
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_lower($params)
{
    if (Smarty::$_MBSTRING) {
        return "mb_strtolower(" . $params[0] . ", '" . addslashes(Smarty::$_CHARSET) . "')";
    }
    return "strtolower(" . $params[0] . ")";
}

?>