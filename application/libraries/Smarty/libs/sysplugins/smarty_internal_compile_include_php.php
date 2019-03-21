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
class Smarty_Internal_Compile_Include_Php extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array("file");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array("file");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array("once", "assign");
    /**
     * Compiles code for the {include_php} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        if (!$compiler->smarty instanceof SmartyBC) {
            throw new SmartyException("{include_php} is deprecated, use SmartyBC class to enable");
        }
        $_attr = $this->getAttributes($compiler, $args);
        $_smarty_tpl = $compiler->template;
        $_filepath = false;
        $_file = NULL;
        eval("\$_file = @" . $_attr["file"] . ";");
        if (!isset($compiler->smarty->security_policy) && file_exists($_file)) {
            $_filepath = $compiler->smarty->_realpath($_file, true);
        } else {
            if (isset($compiler->smarty->security_policy)) {
                $_dir = $compiler->smarty->security_policy->trusted_dir;
            } else {
                $_dir = $compiler->smarty->trusted_dir;
            }
            if (!empty($_dir)) {
                foreach ((array) $_dir as $_script_dir) {
                    $_path = $compiler->smarty->_realpath($_script_dir . DIRECTORY_SEPARATOR . $_file, true);
                    if (file_exists($_path)) {
                        $_filepath = $_path;
                        break;
                    }
                }
            }
        }
        if ($_filepath === false) {
            $compiler->trigger_template_error("{include_php} file '" . $_file . "' is not readable", NULL, true);
        }
        if (isset($compiler->smarty->security_policy)) {
            $compiler->smarty->security_policy->isTrustedPHPDir($_filepath);
        }
        if (isset($_attr["assign"])) {
            $_assign = $_attr["assign"];
        }
        $_once = "_once";
        if (isset($_attr["once"]) && $_attr["once"] === "false") {
            $_once = "";
        }
        if (isset($_assign)) {
            return "<?php ob_start();\ninclude" . $_once . " ('" . $_filepath . "');\n\$_smarty_tpl->assign(" . $_assign . ",ob_get_clean());\n?>";
        }
        return "<?php include" . $_once . " ('" . $_filepath . "');?>\n";
    }
}

?>