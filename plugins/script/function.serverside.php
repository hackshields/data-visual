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
 * {serverside}
 * use serverside
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_serverside($params)
{
    $CI =& get_instance();
    $CI->config->set_item('tabular_serverside', TRUE);
}

?>