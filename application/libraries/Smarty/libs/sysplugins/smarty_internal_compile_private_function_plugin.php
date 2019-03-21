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
 * Smarty Internal Plugin Compile Function Plugin Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Private_Function_Plugin extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array();
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array("_any");
    /**
     * Compiles code for the execution of function plugin
     *
     * @param  array                                $args      array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler  compiler object
     * @param  array                                $parameter array with compilation parameter
     * @param  string                               $tag       name of function plugin
     * @param  string                               $function  PHP function name
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler, $parameter, $tag, $function)
    {
        $_attr = $this->getAttributes($compiler, $args);
        unset($_attr["nocache"]);
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            if (is_int($_key)) {
                $_paramsArray[] = (string) $_key . "=>" . $_value;
            } else {
                $_paramsArray[] = "'" . $_key . "'=>" . $_value;
            }
        }
        $_params = "array(" . implode(",", $_paramsArray) . ")";
        $output = (string) $function . "(" . $_params . ",\$_smarty_tpl)";
        if (!empty($parameter["modifierlist"])) {
            $output = $compiler->compileTag("private_modifier", array(), array("modifierlist" => $parameter["modifierlist"], "value" => $output));
        }
        $output = "<?php echo " . $output . ";?>\n";
        return $output;
    }
}

?>