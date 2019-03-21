<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

function smarty_modifier_join($value)
{
    if (is_array($value)) {
        $CI =& get_instance();
        $newvalue = array();
        foreach ($value as $item) {
            $newvalue[] = $CI->db->escape($item);
        }
        return implode(", ", $newvalue);
    }
    return $value;
}

?>