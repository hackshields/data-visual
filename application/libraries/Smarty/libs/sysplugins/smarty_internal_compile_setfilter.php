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
 * Smarty Internal Plugin Compile Setfilter Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Setfilter extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for setfilter tag
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter)
    {
        $compiler->variable_filter_stack[] = $compiler->variable_filters;
        $compiler->variable_filters = $parameter["modifier_list"];
        $compiler->has_code = false;
        return true;
    }
}
/**
 * Smarty Internal Plugin Compile Setfilterclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Setfilterclose extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {/setfilter} tag
     * This tag does not generate compiled output. It resets variable filter.
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        if (count($compiler->variable_filter_stack)) {
            $compiler->variable_filters = array_pop($compiler->variable_filter_stack);
        } else {
            $compiler->variable_filters = array();
        }
        $compiler->has_code = false;
        return true;
    }
}

?>