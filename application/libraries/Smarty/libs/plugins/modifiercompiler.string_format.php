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
 * Smarty string_format modifier plugin
 * Type:     modifier
 * Name:     string_format
 * Purpose:  format strings via sprintf
 *
 * @link   http://www.smarty.net/manual/en/language.modifier.string.format.php string_format (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_string_format($params)
{
    return "sprintf(" . $params[1] . "," . $params[0] . ")";
}

?>