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
 * {create_variables name="" value="value" sql=""  func="" url=""}
 *
 * create variable
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_create_variable($params, $template)
{
    if (!is_array($params) || count($params) == 0 || !isset($params['name'])) {
        dbface_log('error', 'wrong parameters for create_variable');
        return;
    }
    $CI =& get_instance();
    $var_name = $params['name'];
    // if contains value, this is a fixed value
    if (isset($params['value'])) {
        $template->assign($var_name, $params['value']);
        return;
    }
    // if set func, invoke global function
    if (isset($params['func'])) {
        $func = $params['func'];
        if (!function_exists($func)) {
            dbface_log('error', 'create_variable, function ' . $func . ' is not exists');
            return;
        }
        $func_result = call_user_func($func);
        $template->assign($var_name, $func_result);
        return;
    }
    // if set url, try to capture the result from URL, try to encode to JSON object first, failed to text
    if (isset($params['url'])) {
        $http_method = isset($params['method']) ? $params['method'] : 'GET';
        $body = call_http_service($params['url'], array(), $http_method, TRUE);
        $try_json_result = json_decode($body, TRUE);
        if (json_last_error() == JSON_ERROR_NONE) {
            $template->assign($var_name, $try_json_result);
        } else {
            $template->assign($var_name, $body);
        }
        return;
    }
    // if set query
    if (isset($params['sql'])) {
        $connid = $CI->config->item('running_connid');
        $creatorid = $CI->session->userdata('login_creatorid');
        if (empty($connid) || empty($creatorid)) {
            dbface_log('error', 'create_variable without database connection');
            return;
        }
        $db = $CI->_get_db($creatorid, $connid);
        $query = $db->query($params['sql']);
        if ($query) {
            $num_rows = $query->num_rows();
            if ($num_rows == 1) {
                $template->assign($var_name, $query->row_array());
            } else {
                if ($num_rows > 1) {
                    $template->assign($var_name, $query->result_array());
                } else {
                    if ($num_rows == 0) {
                        $template->assign($var_name, array());
                    }
                }
            }
        } else {
            $error = $db->error();
            if ($error) {
                dbface_log('error', 'Query Failed, error: ' . $error['code'] . ', message: ' . $error['message'] . '<br/>' . $params['sql']);
            } else {
                dbface_log('error', 'Query Failed: ' . $params['sql']);
            }
        }
        return;
    }
    if (isset($params['json'])) {
        return;
    }
    dbface_log('error', 'create_variable, invalid variable source: value, func, url, sql, json');
    return '';
}

?>