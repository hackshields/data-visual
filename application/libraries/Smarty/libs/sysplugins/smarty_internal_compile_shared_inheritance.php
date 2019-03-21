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
 * Smarty Internal Plugin Compile Shared Inheritance Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Shared_Inheritance extends Smarty_Internal_CompileBase
{
    /**
     * Compile inheritance initialization code as prefix
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param bool|false                            $initChildSequence if true force child template
     */
    public static function postCompile(Smarty_Internal_TemplateCompilerBase $compiler, $initChildSequence = false)
    {
        $compiler->prefixCompiledCode .= "<?php \$_smarty_tpl->_loadInheritance();\n\$_smarty_tpl->inheritance->init(\$_smarty_tpl, " . var_export($initChildSequence, true) . ");\n?>\n";
    }
    /**
     * Register post compile callback to compile inheritance initialization code
     *
     * @param \Smarty_Internal_TemplateCompilerBase $compiler
     * @param bool|false                            $initChildSequence if true force child template
     */
    public function registerInit(Smarty_Internal_TemplateCompilerBase $compiler, $initChildSequence = false)
    {
        if ($initChildSequence || !isset($compiler->_cache["inheritanceInit"])) {
            $compiler->registerPostCompileCallback(array("Smarty_Internal_Compile_Shared_Inheritance", "postCompile"), array($initChildSequence), "inheritanceInit", $initChildSequence);
            $compiler->_cache["inheritanceInit"] = true;
        }
    }
}

?>