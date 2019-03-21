<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * {template name=value}
 *
 * display template from name
 *
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_template($params, Smarty_Internal_Template $template)
{
    if (empty($params['var'])) {
        trigger_error("assign: missing 'var' parameter");
        return;
    }
    return "";
}

?>