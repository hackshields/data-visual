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
 * {nocache}
 * disable cache
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_nocache($params)
{
    $CI =& get_instance();
    $CI->config->set_item('cache_app', 0);
    $CI->config->set_item('cache_sql_query', 0);
    $CI->config->set_item('_session_disable_cache_', TRUE);
}

?>