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
 * {source func=value}
 *
 * call cloud function
 *
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_source($params, $template)
{
    if (empty($params) || !is_array($params) || !isset($params['func'])) {
        return "Invalid source plugin parameters";
    }
    $func = $params['func'];
    unset($params['func']);
    if (function_exists($func)) {
        return call_user_func($func, $params);
    }
    $CI =& get_instance();
    $creatorid = $CI->session->userdata('login_creatorid');
    if (empty($creatorid)) {
        return "";
    }
    $result_funcs = explode('#', $func);
    if (count($result_funcs) == 2) {
        $file = $result_funcs[0];
        $func = $result_funcs[1];
        $real_path = USERPATH . 'files' . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $file;
        if (file_exists($real_path)) {
            try {
                define('__CLOUD_CODE__', TRUE);
                @(include $real_path);
                if (function_exists($func)) {
                    return call_user_func($func, $params);
                }
            } catch (Exception $e) {
                dbface_log('error', 'execute source cloud code failed: ' . $e->getMessage());
            }
        }
    } else {
        $detetch_file = USERPATH . 'files' . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $func;
        if (file_exists($detetch_file)) {
            try {
                define('__CLOUD_CODE__', TRUE);
                return @(include $detetch_file);
            } catch (Exception $e) {
                dbface_log('error', 'execute source cloud code failed: ' . $e->getMessage());
            }
        }
        return "";
    }
}

?>