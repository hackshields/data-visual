<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("byte_format")) {
    /**
     * Formats a numbers as bytes, based on size, and adds the appropriate suffix
     *
     * @param	mixed	will be cast as int
     * @param	int
     * @return	string
     */
    function byte_format($num, $precision = 1)
    {
        $CI =& get_instance();
        $CI->lang->load("number");
        if (1000000000000.0 <= $num) {
            $num = round($num / 1099511627776.0, $precision);
            $unit = $CI->lang->line("terabyte_abbr");
        } else {
            if (1000000000 <= $num) {
                $num = round($num / 1073741824, $precision);
                $unit = $CI->lang->line("gigabyte_abbr");
            } else {
                if (1000000 <= $num) {
                    $num = round($num / 1048576, $precision);
                    $unit = $CI->lang->line("megabyte_abbr");
                } else {
                    if (1000 <= $num) {
                        $num = round($num / 1024, $precision);
                        $unit = $CI->lang->line("kilobyte_abbr");
                    } else {
                        $unit = $CI->lang->line("bytes");
                        return number_format($num) . " " . $unit;
                    }
                }
            }
        }
        return number_format($num, $precision) . " " . $unit;
    }
}

?>