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
 * Translates in the currently selected language the specified translation $stringToken
 * Translations strings are located either in /lang/xx.php or within the plugin lang directory.
 *
 * Usage:
 *  {'General_Unknown'|translate} will be translated as 'Unknown' (see the entry in /lang/en.php)
 *
 * @param $stringToken
 * @return string The translated string, with optional substrings parameters replaced
 */
function smarty_modifier_translate($stringToken)
{
    if (func_num_args() <= 1) {
        $aValues = array();
    } else {
        $aValues = func_get_args();
        array_shift($aValues);
    }
    $CI =& get_instance();
    $language = $CI->config->item("language");
    $CI->lang->load("message", $language);
    $v = $CI->lang->line($stringToken, false);
    if ($v) {
        return vsprintf($v, $aValues);
    }
    $CI->lang->load("message", "english");
    $v = $CI->lang->line($stringToken, false);
    if ($v) {
        return vsprintf($v, $aValues);
    }
    return $stringToken;
}

?>