<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * {config name=value}
 *
 * display template from name
 *
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_config($params, Smarty_Internal_Template $template)
{
    $CI =& get_instance();
    if (is_array($params) && !empty($params)) {
        foreach ($params as $key => $value) {
            if ($key == 'file') {
                $creatorid = $CI->session->userdata('login_creatorid');
                $abs_file_path = USERPATH . 'files' . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . $value;
                if (file_exists($abs_file_path)) {
                    $ini_result = parse_ini_file($abs_file_path);
                    if (is_array($ini_result)) {
                        foreach ($ini_result as $ik => $iv) {
                            $CI->config->set_item($ik, $iv);
                            $template->assign($ik, $iv);
                        }
                    }
                    continue;
                }
            }
            $CI->config->set_item($key, $value);
            $template->assign($key, $value);
        }
    }
}

?>