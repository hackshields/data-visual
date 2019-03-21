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
 * Smarty {textformat}{/textformat} block plugin
 * Type:     block function
 * Name:     textformat
 * Purpose:  format text a certain way with preset styles
 *           or custom wrap/indent settings
 * Params:
 *
 * - style         - string (email)
 * - indent        - integer (0)
 * - wrap          - integer (80)
 * - wrap_char     - string ("\n")
 * - indent_char   - string (" ")
 * - wrap_boundary - boolean (true)
 *
 *
 * @link   http://www.smarty.net/manual/en/language.function.textformat.php {textformat}
 *         (Smarty online manual)
 *
 * @param array                    $params   parameters
 * @param string                   $content  contents of the block
 * @param Smarty_Internal_Template $template template object
 * @param boolean                  &$repeat  repeat flag
 *
 * @return string content re-formatted
 * @author Monte Ohrt <monte at ohrt dot com>
 * @throws \SmartyException
 */
function smarty_block_textformat($params, $content, Smarty_Internal_Template $template, &$repeat)
{
    if (is_null($content)) {
        return NULL;
    }
    if (Smarty::$_MBSTRING) {
        $template->_checkPlugins(array(array("function" => "smarty_modifier_mb_wordwrap", "file" => SMARTY_PLUGINS_DIR . "modifier.mb_wordwrap.php")));
    }
    $style = NULL;
    $indent = 0;
    $indent_first = 0;
    $indent_char = " ";
    $wrap = 80;
    $wrap_char = "\n";
    $wrap_cut = false;
    $assign = NULL;
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case "style":
            case "indent_char":
            case "wrap_char":
            case "assign":
                ${$_key} = (string) $_val;
                break;
            case "indent":
            case "indent_first":
            case "wrap":
                ${$_key} = (int) $_val;
                break;
            case "wrap_cut":
                ${$_key} = (bool) $_val;
                break;
            default:
                trigger_error("textformat: unknown attribute '" . $_key . "'");
        }
    }
    if ($style === "email") {
        $wrap = 72;
    }
    $_paragraphs = preg_split("![\\r\\n]{2}!", $content);
    foreach ($_paragraphs as &$_paragraph) {
        if (!$_paragraph) {
            continue;
        }
        $_paragraph = preg_replace(array("!\\s+!" . Smarty::$_UTF8_MODIFIER, "!(^\\s+)|(\\s+\$)!" . Smarty::$_UTF8_MODIFIER), array(" ", ""), $_paragraph);
        if (0 < $indent_first) {
            $_paragraph = str_repeat($indent_char, $indent_first) . $_paragraph;
        }
        if (Smarty::$_MBSTRING) {
            $_paragraph = smarty_modifier_mb_wordwrap($_paragraph, $wrap - $indent, $wrap_char, $wrap_cut);
        } else {
            $_paragraph = wordwrap($_paragraph, $wrap - $indent, $wrap_char, $wrap_cut);
        }
        if (0 < $indent) {
            $_paragraph = preg_replace("!^!m", str_repeat($indent_char, $indent), $_paragraph);
        }
    }
    $_output = implode($wrap_char . $wrap_char, $_paragraphs);
    if ($assign) {
        $template->assign($assign, $_output);
    } else {
        return $_output;
    }
}

?>