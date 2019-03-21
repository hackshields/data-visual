<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */
/**
 * {filter field='fieldname' value=$value}
 * generate right SQL query snipplets depends the value
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_filter($params)
{
    if (!is_array($params) || count($params) == 0) {
        return "";
    }
    $field = key($params);
    if (empty($field)) {
        return "";
    }
    $value = isset($params[$field]) ? $params[$field] : FALSE;
    if ($value == FALSE || $value == null) {
        $output = $field . ' IS NULL';
    } else {
        if (is_numeric($value) && !empty($value)) {
            $output = $field . '=' . $value;
        } else {
            if (is_string($value)) {
                $output = $field . '="' . $value . '"';
            } else {
                if (is_array($value)) {
                    $where_in = array();
                    foreach ($value as $item) {
                        $where_in[] = '"' . $item . '"';
                    }
                    $output = $field . ' IN(' . implode(', ', $where_in) . ')';
                }
            }
        }
    }
    if (empty($output)) {
        $output = '1=1';
    }
    return $output;
}

?>