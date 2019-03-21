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
class Smarty_Internal_Compile_Insert extends Smarty_Internal_CompileBase
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
     * Compiles code for the {insert} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        $nocacheParam = $compiler->template->caching && ($compiler->tag_nocache || $compiler->nocache);
        if (!$nocacheParam) {
            $compiler->suppressNocacheProcessing = true;
        }
        $compiler->tag_nocache = true;
        $_smarty_tpl = $compiler->template;
        $_name = NULL;
        $_script = NULL;
        $_output = "<?php ";
        eval("\$_name = @" . $_attr["name"] . ";");
        if (isset($_attr["assign"])) {
            $_assign = $_attr["assign"];
            $var = trim($_attr["assign"], "'");
            if (isset($compiler->template->tpl_vars[$var])) {
                $compiler->template->tpl_vars[$var]->nocache = true;
            } else {
                $compiler->template->tpl_vars[$var] = new Smarty_Variable(NULL, true);
            }
        }
        if (isset($_attr["script"])) {
            $_function = "smarty_insert_" . $_name;
            $_smarty_tpl = $compiler->template;
            $_filepath = false;
            eval("\$_script = @" . $_attr["script"] . ";");
            if (!isset($compiler->smarty->security_policy) && file_exists($_script)) {
                $_filepath = $_script;
            } else {
                if (isset($compiler->smarty->security_policy)) {
                    $_dir = $compiler->smarty->security_policy->trusted_dir;
                } else {
                    $_dir = $compiler->smarty instanceof SmartyBC ? $compiler->smarty->trusted_dir : NULL;
                }
                if (!empty($_dir)) {
                    foreach ((array) $_dir as $_script_dir) {
                        $_script_dir = rtrim($_script_dir, "/\\") . DIRECTORY_SEPARATOR;
                        if (file_exists($_script_dir . $_script)) {
                            $_filepath = $_script_dir . $_script;
                            break;
                        }
                    }
                }
            }
            if ($_filepath === false) {
                $compiler->trigger_template_error("{insert} missing script file '" . $_script . "'", NULL, true);
            }
            $_output .= "require_once '" . $_filepath . "' ;";
            require_once $_filepath;
            if (!is_callable($_function)) {
                $compiler->trigger_template_error(" {insert} function '" . $_function . "' is not callable in script file '" . $_script . "'", NULL, true);
            }
        } else {
            $_filepath = "null";
            $_function = "insert_" . $_name;
            if (!is_callable($_function) && !($_function = $compiler->getPlugin($_name, "insert"))) {
                $compiler->trigger_template_error("{insert} no function or plugin found for '" . $_name . "'", NULL, true);
            }
        }
        unset($_attr["name"]);
        unset($_attr["assign"]);
        unset($_attr["script"]);
        unset($_attr["nocache"]);
        $_paramsArray = array();
        foreach ($_attr as $_key => $_value) {
            $_paramsArray[] = "'" . $_key . "' => " . $_value;
        }
        $_params = "array(" . implode(", ", $_paramsArray) . ")";
        if (isset($_assign)) {
            if ($_smarty_tpl->caching && !$nocacheParam) {
                $_output .= "echo Smarty_Internal_Nocache_Insert::compile ('" . $_function . "'," . $_params . ", \$_smarty_tpl, '" . $_filepath . "'," . $_assign . ");?>";
            } else {
                $_output .= "\$_smarty_tpl->assign(" . $_assign . " , " . $_function . " (" . $_params . ",\$_smarty_tpl), true);?>";
            }
        } else {
            if ($_smarty_tpl->caching && !$nocacheParam) {
                $_output .= "echo Smarty_Internal_Nocache_Insert::compile ('" . $_function . "'," . $_params . ", \$_smarty_tpl, '" . $_filepath . "');?>";
            } else {
                $_output .= "echo " . $_function . "(" . $_params . ",\$_smarty_tpl);?>";
            }
        }
        return $_output;
    }
}

?>