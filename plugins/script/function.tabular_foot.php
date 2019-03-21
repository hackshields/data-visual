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
 * Smarty {tabular_foot} function plugin
 *
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_tabular_foot($params)
{
    if (!$params || !isset($params['series'])) {
        return "";
    }
    $use_line_number = $params['use_line_number'];
    $series = $params['series'];
    $itemkey = isset($params['item']) ? $params['item'] : FALSE;
    // collect all total total_fields
    $total_fields = array();
    foreach ($series as $field => $series_option) {
        if ($series_option['total'] == '1') {
            $total_fields[$field] = $series_option;
            $total_fields[$field]['total'] = 0;
        }
    }
    if (count($total_fields) == 0) {
        return "";
    }
    $output = "<tfoot><tr>";
    $fields = $params['fields'];
    $datas = $params['datas'];
    foreach ($datas as &$data) {
        foreach ($total_fields as $field => $val) {
            if ($itemkey) {
                if (is_numeric($data[$field][$itemkey])) {
                    $total_fields[$field]['total'] += $data[$field][$itemkey];
                }
            } else {
                if (is_numeric($data[$field])) {
                    $total_fields[$field]['total'] += $data[$field];
                }
            }
        }
    }
    if ($use_line_number) {
        $output .= "<td class='text-center'>Total</td>";
    }
    foreach ($fields as $field) {
        $align = $total_fields[$field]['align'];
        if ($align == 'center') {
            $output .= "<td class='text-center'>";
        } else {
            if ($align == 'right') {
                $output .= "<td class='text-right'>";
            } else {
                $output .= "<td>";
            }
        }
        if (isset($total_fields[$field])) {
            $output .= value_format($total_fields[$field]['total'], $total_fields[$field]);
        }
        $output .= "</td>";
    }
    $output .= "</tr></tfoot>";
    return $output;
}
function value_format($val, $series_option)
{
    if (!$series_option || !isset($series_option['format']) || $series_option['format'] == 'none') {
        return $val;
    }
    $format = $series_option['format'];
    if ($format == 'number') {
        if (!is_numeric($val)) {
            return $val;
        }
        $decimal = isset($series_option['decimal']) && is_numeric($series_option['decimal']) ? $series_option['decimal'] : 0;
        return number_format($val, (int) $decimal);
    } else {
        if ($format == 'percent') {
            return $val . '%';
        } else {
            if ($format == 'currency') {
                if (!is_numeric($val)) {
                    return $val;
                }
                $decimal = isset($series_option['decimal']) && is_numeric($series_option['decimal']) ? $series_option['decimal'] : 0;
                $symbol = isset($series_option['currency']) && $series_option['currency'] != 'none' ? $series_option['currency'] : '';
                return $symbol . number_format($val, $decimal);
            }
        }
    }
    return $val;
}

?>