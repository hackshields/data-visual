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
 * Smarty escape modifier plugin
 * Type:     modifier
 * Name:     escape
 * Purpose:  escape string for output
 *
 * @link   http://www.smarty.net/docsv2/en/language.modifier.escape count_characters (Smarty online manual)
 * @author Rodney Rehm
 *
 * @param array                                 $params parameters
 * @param  Smarty_Internal_TemplateCompilerBase $compiler
 *
 * @return string with compiled code
 * @throws \SmartyException
 */
function smarty_modifiercompiler_escape($params, Smarty_Internal_TemplateCompilerBase $compiler)
{
    static $_double_encode = NULL;
    static $is_loaded = false;
    $compiler->template->_checkPlugins(array(array("function" => "smarty_literal_compiler_param", "file" => SMARTY_PLUGINS_DIR . "shared.literal_compiler_param.php")));
    if ($_double_encode === NULL) {
        $_double_encode = version_compare(PHP_VERSION, "5.2.3", ">=");
    }
    try {
        $esc_type = smarty_literal_compiler_param($params, 1, "html");
        $char_set = smarty_literal_compiler_param($params, 2, Smarty::$_CHARSET);
        $double_encode = smarty_literal_compiler_param($params, 3, true);
        if (!$char_set) {
            $char_set = Smarty::$_CHARSET;
        }
        switch ($esc_type) {
            case "html":
                if ($_double_encode) {
                    return "htmlspecialchars(" . $params[0] . ", ENT_QUOTES, " . var_export($char_set, true) . ", " . var_export($double_encode, true) . ")";
                }
                if ($double_encode) {
                    return "htmlspecialchars(" . $params[0] . ", ENT_QUOTES, " . var_export($char_set, true) . ")";
                }
            case "htmlall":
                if (Smarty::$_MBSTRING) {
                    if ($_double_encode) {
                        return "mb_convert_encoding(htmlspecialchars(" . $params[0] . ", ENT_QUOTES, " . var_export($char_set, true) . ", " . var_export($double_encode, true) . "), \"HTML-ENTITIES\", " . var_export($char_set, true) . ")";
                    }
                    if ($double_encode) {
                        return "mb_convert_encoding(htmlspecialchars(" . $params[0] . ", ENT_QUOTES, " . var_export($char_set, true) . "), \"HTML-ENTITIES\", " . var_export($char_set, true) . ")";
                    }
                }
                if ($_double_encode) {
                    return "htmlentities(" . $params[0] . ", ENT_QUOTES, " . var_export($char_set, true) . ", " . var_export($double_encode, true) . ")";
                }
                if ($double_encode) {
                    return "htmlentities(" . $params[0] . ", ENT_QUOTES, " . var_export($char_set, true) . ")";
                }
            case "url":
                return "rawurlencode(" . $params[0] . ")";
            case "urlpathinfo":
                return "str_replace(\"%2F\", \"/\", rawurlencode(" . $params[0] . "))";
            case "quotes":
                return "preg_replace(\"%(?<!\\\\\\\\)'%\", \"\\'\"," . $params[0] . ")";
            case "javascript":
                return "strtr(" . $params[0] . ", array(\"\\\\\" => \"\\\\\\\\\", \"'\" => \"\\\\'\", \"\\\"\" => \"\\\\\\\"\", \"\\r\" => \"\\\\r\", \"\\n\" => \"\\\\n\", \"</\" => \"<\\/\" ))";
        }
    } catch (SmartyException $e) {
    }
    if ($compiler->template->caching && $compiler->tag_nocache | $compiler->nocache) {
        $compiler->required_plugins["nocache"]["escape"]["modifier"]["file"] = SMARTY_PLUGINS_DIR . "modifier.escape.php";
        $compiler->required_plugins["nocache"]["escape"]["modifier"]["function"] = "smarty_modifier_escape";
    } else {
        $compiler->required_plugins["compiled"]["escape"]["modifier"]["file"] = SMARTY_PLUGINS_DIR . "modifier.escape.php";
        $compiler->required_plugins["compiled"]["escape"]["modifier"]["function"] = "smarty_modifier_escape";
    }
    return "smarty_modifier_escape(" . join(", ", $params) . ")";
}

?>