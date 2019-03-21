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
 * Smarty Internal Plugin Compile Function_Call Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Call extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array("name");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array("name");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array("_any");
    /**
     * Compiles the calls of user defined tags defined by {function}
     *
     * @param  array  $args     array with attributes from parser
     * @param  object $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        if (isset($_attr["assign"])) {
            $_assign = $_attr["assign"];
        }
        $_name = $_attr["name"];
        unset($_attr["name"]);
        unset($_attr["assign"]);
        unset($_attr["nocache"]);
        if (!$compiler->template->caching || $compiler->nocache || $compiler->tag_nocache) {
            $_nocache = "true";
        } else {
            $_nocache = "false";
        }
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = (string) $_key . "=>" . $_value;
            } else {
                $_paramsArray[] = "'" . $_key . "'=>" . $_value;
            }
        }
        $_params = "array(" . implode(",", $_paramsArray) . ")";
        if (isset($_assign)) {
            $_output = "<?php ob_start();\n\$_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction(\$_smarty_tpl, " . $_name . ", " . $_params . ", " . $_nocache . ");\n\$_smarty_tpl->assign(" . $_assign . ", ob_get_clean());?>\n";
        } else {
            $_output = "<?php \$_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction(\$_smarty_tpl, " . $_name . ", " . $_params . ", " . $_nocache . ");?>\n";
        }
        return $_output;
    }
}

?>