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
 * Function: smarty_make_timestamp
 * Purpose:  used by other smarty functions to make a timestamp from a string.
 *
 * @author   Monte Ohrt <monte at ohrt dot com>
 *
 * @param DateTime|int|string $string date object, timestamp or string that can be converted using strtotime()
 *
 * @return int
 */
function smarty_make_timestamp($string)
{
    if (empty($string)) {
        return time();
    }
    if ($string instanceof DateTime || interface_exists("DateTimeInterface", false) && $string instanceof DateTimeInterface) {
        return (int) $string->format("U");
    }
    if (strlen($string) === 14 && ctype_digit($string)) {
        return mktime(substr($string, 8, 2), substr($string, 10, 2), substr($string, 12, 2), substr($string, 4, 2), substr($string, 6, 2), substr($string, 0, 4));
    }
    if (is_numeric($string)) {
        return (int) $string;
    }
    $time = strtotime($string);
    if ($time === -1 || $time === false) {
        return time();
    }
    return $time;
}

?>