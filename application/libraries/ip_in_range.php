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
function decbin32($dec)
{
    return str_pad(decbin($dec), 32, "0", STR_PAD_LEFT);
}
function ip_in_range($ip, $range)
{
    if (strpos($range, "/") !== false) {
        list($range, $netmask) = explode("/", $range, 2);
        if (strpos($netmask, ".") !== false) {
            $netmask = str_replace("*", "0", $netmask);
            $netmask_dec = ip2long($netmask);
            return (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec);
        }
        $x = explode(".", $range);
        while (count($x) < 4) {
            $x[] = "0";
        }
        list($a, $b, $c, $d) = $x;
        $range = sprintf("%u.%u.%u.%u", empty($a) ? "0" : $a, empty($b) ? "0" : $b, empty($c) ? "0" : $c, empty($d) ? "0" : $d);
        $range_dec = ip2long($range);
        $ip_dec = ip2long($ip);
        $wildcard_dec = pow(2, 32 - $netmask) - 1;
        $netmask_dec = ~$wildcard_dec;
        return ($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec);
    }
    if (strpos($range, "*") !== false) {
        $lower = str_replace("*", "0", $range);
        $upper = str_replace("*", "255", $range);
        $range = (string) $lower . "-" . $upper;
    }
    if (strpos($range, "-") !== false) {
        list($lower, $upper) = explode("-", $range, 2);
        $lower_dec = (double) sprintf("%u", ip2long($lower));
        $upper_dec = (double) sprintf("%u", ip2long($upper));
        $ip_dec = (double) sprintf("%u", ip2long($ip));
        return $lower_dec <= $ip_dec && $ip_dec <= $upper_dec;
    }
    return false;
}

?>