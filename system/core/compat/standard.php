<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (is_php("5.5")) {
    return NULL;
}
if (!function_exists("array_column")) {
    /**
     * array_column()
     *
     * @link	http://php.net/array_column
     * @param	array	$array
     * @param	mixed	$column_key
     * @param	mixed	$index_key
     * @return	array
     */
    function array_column(array $array, $column_key, $index_key = NULL)
    {
        if (!in_array($type = gettype($column_key), array("integer", "string", "NULL"), true)) {
            if ($type === "double") {
                $column_key = (int) $column_key;
            } else {
                if ($type === "object" && method_exists($column_key, "__toString")) {
                    $column_key = (string) $column_key;
                } else {
                    trigger_error("array_column(): The column key should be either a string or an integer", 512);
                    return false;
                }
            }
        }
        if (!in_array($type = gettype($index_key), array("integer", "string", "NULL"), true)) {
            if ($type === "double") {
                $index_key = (int) $index_key;
            } else {
                if ($type === "object" && method_exists($index_key, "__toString")) {
                    $index_key = (string) $index_key;
                } else {
                    trigger_error("array_column(): The index key should be either a string or an integer", 512);
                    return false;
                }
            }
        }
        $result = array();
        foreach ($array as &$a) {
            if ($column_key === NULL) {
                $value = $a;
            } else {
                if (is_array($a) && array_key_exists($column_key, $a)) {
                    $value = $a[$column_key];
                } else {
                    continue;
                }
            }
            if ($index_key === NULL || !array_key_exists($index_key, $a)) {
                $result[] = $value;
            } else {
                $result[$a[$index_key]] = $value;
            }
        }
        return $result;
    }
}

?>