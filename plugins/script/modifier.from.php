<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

function smarty_modifier_from($value)
{
    if (is_string($value)) {
        $arr = explode('-', $value);
        if (count($arr) == 2) {
            return trim($arr[0]);
        }
    }
    return $value;
}

?>