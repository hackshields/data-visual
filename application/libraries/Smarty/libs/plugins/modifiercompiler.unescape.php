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
 * Smarty unescape modifier plugin
 * Type:     modifier
 * Name:     unescape
 * Purpose:  unescape html entities
 *
 * @author Rodney Rehm
 *
 * @param array $params parameters
 *
 * @return string with compiled code
 */
function smarty_modifiercompiler_unescape($params)
{
    if (!isset($params[1])) {
        $params[1] = "html";
    }
    if (!isset($params[2])) {
        $params[2] = "'" . addslashes(Smarty::$_CHARSET) . "'";
    } else {
        $params[2] = "'" . $params[2] . "'";
    }
    switch (trim($params[1], "\"'")) {
        case "entity":
        case "htmlall":
            if (Smarty::$_MBSTRING) {
                return "mb_convert_encoding(" . $params[0] . ", " . $params[2] . ", 'HTML-ENTITIES')";
            }
            return "html_entity_decode(" . $params[0] . ", ENT_NOQUOTES, " . $params[2] . ")";
        case "html":
            return "htmlspecialchars_decode(" . $params[0] . ", ENT_QUOTES)";
        case "url":
            return "rawurldecode(" . $params[0] . ")";
    }
    return $params[0];
}

?>