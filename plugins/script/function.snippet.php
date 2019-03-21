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
 * {snippet name=value}
 *
 * call cloud function
 *
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_snippet($params, $template)
{
    if (empty($params) || !is_array($params) || !isset($params['name'])) {
        return "Invalid snippet plugin parameters";
    }
    $name = $params['name'];
    $CI =& get_instance();
    $db = $CI->db;
    $creatorid = $CI->session->userdata('login_creatorid');
    if (empty($name) || empty($creatorid)) {
        return "Invalid snippet name: " . $name;
    }
    $query = $db->select("key, value")->where(array('creatorid' => $creatorid, 'appid' => 0, 'type' => 'tagged_sql', 'key' => $name))->get('dc_app_options');
    if ($query->num_rows() > 0) {
        return $query->row()->value;
    }
    return $name;
}

?>