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
 * Smarty Internal Plugin Compile While Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_While extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {while} tag
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
        $compiler->loopNesting++;
        $_attr = $this->getAttributes($compiler, $args);
        $this->openTag($compiler, "while", $compiler->nocache);
        if (!array_key_exists("if condition", $parameter)) {
            $compiler->trigger_template_error("missing while condition", NULL, true);
        }
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        if (is_array($parameter["if condition"])) {
            if ($compiler->nocache) {
                if (is_array($parameter["if condition"]["var"])) {
                    $var = $parameter["if condition"]["var"]["var"];
                } else {
                    $var = $parameter["if condition"]["var"];
                }
                $compiler->setNocacheInVariable($var);
            }
            $prefixVar = $compiler->getNewPrefixVariable();
            $assignCompiler = new Smarty_Internal_Compile_Assign();
            $assignAttr = array();
            $assignAttr[]["value"] = $prefixVar;
            if (is_array($parameter["if condition"]["var"])) {
                $assignAttr[]["var"] = $parameter["if condition"]["var"]["var"];
                $_output = "<?php while (" . $prefixVar . " = " . $parameter["if condition"]["value"] . ") {?>";
                $_output .= $assignCompiler->compile($assignAttr, $compiler, array("smarty_internal_index" => $parameter["if condition"]["var"]["smarty_internal_index"]));
            } else {
                $assignAttr[]["var"] = $parameter["if condition"]["var"];
                $_output = "<?php while (" . $prefixVar . " = " . $parameter["if condition"]["value"] . ") {?>";
                $_output .= $assignCompiler->compile($assignAttr, $compiler, array());
            }
            return $_output;
        }
        return "<?php\n while (" . $parameter["if condition"] . ") {?>";
    }
}
/**
 * Smarty Internal Plugin Compile Whileclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Whileclose extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {/while} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $compiler->loopNesting--;
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        $compiler->nocache = $this->closeTag($compiler, array("while"));
        return "<?php }?>\n";
    }
}

?>