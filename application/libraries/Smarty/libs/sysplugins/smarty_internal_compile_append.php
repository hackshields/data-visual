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
 * Smarty Internal Plugin Compile Append Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Append extends Smarty_Internal_Compile_Assign
{
    /**
     * Compiles code for the {append} tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $this->required_attributes = array("var", "value");
        $this->shorttag_order = array("var", "value");
        $this->optional_attributes = array("scope", "index");
        $this->mapCache = array();
        $_attr = $this->getAttributes($compiler, $args);
        if (isset($_attr["index"])) {
            $_params["smarty_internal_index"] = "[" . $_attr["index"] . "]";
            unset($_attr["index"]);
        } else {
            $_params["smarty_internal_index"] = "[]";
        }
        $_new_attr = array();
        foreach ($_attr as $key => $value) {
            $_new_attr[] = array($key => $value);
        }
        return parent::compile($_new_attr, $compiler, $_params);
    }
}

?>