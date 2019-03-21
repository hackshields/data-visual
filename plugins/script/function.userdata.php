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
 * {userdata var=value}
 * generate right SQL query snipplets depends the value
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_userdata($params)
{
    if (!is_array($params) || count($params) == 0) {
        return;
    }
    $CI =& get_instance();
    $data = $CI->config->item('predefined_variables');
    if (!$data || empty($data) || !is_array($data)) {
        $data = array();
    }
    $data = array_merge($data, $params);
    $CI->config->set_item('predefined_variables', $data);
}

?>