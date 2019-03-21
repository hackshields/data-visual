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
 * Smarty Internal Plugin Compile Object Block Function Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_Object_Block_Function extends Smarty_Internal_Compile_Private_Block_Plugin
{
    /**
     * Setup callback and parameter array
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param  array                                $_attr attributes
     * @param  string                               $tag
     * @param  string                               $method
     *
     * @return array
     */
    public function setup(Smarty_Internal_TemplateCompilerBase $compiler, $_attr, $tag, $method)
    {
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = (string) $_key . "=>" . $_value;
            } else {
                $_paramsArray[] = "'" . $_key . "'=>" . $_value;
            }
        }
        $callback = array("\$_smarty_tpl->smarty->registered_objects['" . $tag . "'][0]", "->" . $method);
        return array($callback, $_paramsArray, "array(\$_block_plugin" . $this->nesting . ", '" . $method . "')");
    }
}

?>