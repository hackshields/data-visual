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
 * Smarty Internal Plugin Compile Insert Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Nocache_Insert
{
    /**
     * Compiles code for the {insert} tag into cache file
     *
     * @param  string                   $_function insert function name
     * @param  array                    $_attr     array with parameter
     * @param  Smarty_Internal_Template $_template template object
     * @param  string                   $_script   script name to load or 'null'
     * @param  string                   $_assign   optional variable name
     *
     * @return string                   compiled code
     */
    public static function compile($_function, $_attr, $_template, $_script, $_assign = NULL)
    {
        $_output = "<?php ";
        if ($_script !== "null") {
            $_output .= "require_once '" . $_script . "';";
        }
        if (isset($_assign)) {
            $_output .= "\$_smarty_tpl->assign('" . $_assign . "' , " . $_function . " (" . var_export($_attr, true) . ",\\\$_smarty_tpl), true);?>";
        } else {
            $_output .= "echo " . $_function . "(" . var_export($_attr, true) . ",\$_smarty_tpl);?>";
        }
        $_tpl = $_template;
        while ($_tpl->_isSubTpl()) {
            $_tpl = $_tpl->parent;
        }
        return "/*%%SmartyNocache:" . $_tpl->compiled->nocache_hash . "%%*/" . $_output . "/*/%%SmartyNocache:" . $_tpl->compiled->nocache_hash . "%%*/";
    }
}

?>