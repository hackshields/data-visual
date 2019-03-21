<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

function smarty_modifier_usercolumn($params)
{
    $str = $params;
    // replace _ to " "
    $str = str_replace('_', ' ', $str);
    $str = str_replace('-', ' ', $str);
    return $str;
}

?>