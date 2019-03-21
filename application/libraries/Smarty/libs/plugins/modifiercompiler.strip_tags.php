<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
/**
 * Smarty strip_tags modifier plugin
 * Type:     modifier
 * Name:     strip_tags
 * Purpose:  strip html tags from text
 *
 * @link   http://www.smarty.net/docs/en/language.modifier.strip.tags.tpl strip_tags (Smarty online manual)
 * @author Uwe Tews
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_strip_tags($params)
{
    if (!isset($params[1]) || $params[1] === true || trim($params[1], "\"") === "true") {
        return "preg_replace('!<[^>]*?>!', ' ', " . $params[0] . ")";
    }
    return "strip_tags(" . $params[0] . ")";
}

?>