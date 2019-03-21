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
 * evaluate compiler parameter
 *
 * @param array   $params  parameter array as given to the compiler function
 * @param integer $index   array index of the parameter to convert
 * @param mixed   $default value to be returned if the parameter is not present
 *
 * @return mixed evaluated value of parameter or $default
 * @throws SmartyException if parameter is not a literal (but an expression, variable, …)
 * @author Rodney Rehm
 */
function smarty_literal_compiler_param($params, $index, $default = NULL)
{
    if (!isset($params[$index])) {
        return $default;
    }
    if (!preg_match("/^(['\"]?)[a-zA-Z0-9-]+(\\1)\$/", $params[$index])) {
        throw new SmartyException("\$param[" . $index . "] is not a literal and is thus not evaluatable at compile time");
    }
    $t = NULL;
    eval("\$t = " . $params[$index] . ";");
    return $t;
}

?>