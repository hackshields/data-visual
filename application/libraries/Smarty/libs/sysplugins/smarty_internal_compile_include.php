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
 * Smarty Internal Plugin Compile Include Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Include extends Smarty_Internal_CompileBase
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
    public $option_flags = array("nocache", "inline", "caching");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array("_any");
    /**
     * Valid scope names
     *
     * @var array
     */
    public $valid_scopes = NULL;
    const CACHING_NOCACHE_CODE = 9999;
    /**
     * Compiles code for the {include} tag
     *
     * @param  array                                  $args     array with attributes from parser
     * @param  Smarty_Internal_SmartyTemplateCompiler $compiler compiler object
     *
     * @return string
     * @throws \Exception
     * @throws \SmartyCompilerException
     * @throws \SmartyException
     */
    public function compile($args, Smarty_Internal_SmartyTemplateCompiler $compiler)
    {
        $uid = $t_hash = NULL;
        $_attr = $this->getAttributes($compiler, $args);
        $fullResourceName = $source_resource = $_attr["file"];
        $variable_template = false;
        $cache_tpl = false;
        if (preg_match("/^(['\"])(([A-Za-z0-9_\\-]{2,})[:])?(([^\$()]+)|(.+))\\1\$/", $source_resource, $match)) {
            $type = !empty($match[3]) ? $match[3] : $compiler->template->smarty->default_resource_type;
            $name = !empty($match[5]) ? $match[5] : $match[6];
            $handler = Smarty_Resource::load($compiler->smarty, $type);
            if ($handler->recompiled || $handler->uncompiled) {
                $variable_template = true;
            }
            if (!$variable_template && $type !== "string") {
                $fullResourceName = (string) $type . ":" . $name;
                $compiled = $compiler->parent_compiler->template->compiled;
                if (isset($compiled->includes[$fullResourceName])) {
                    $compiled->includes[$fullResourceName]++;
                    $cache_tpl = true;
                } else {
                    if ((string) $compiler->template->source->type . ":" . $compiler->template->source->name == $fullResourceName) {
                        $compiled->includes[$fullResourceName] = 2;
                        $cache_tpl = true;
                    } else {
                        $compiled->includes[$fullResourceName] = 1;
                    }
                }
                $fullResourceName = $match[1] . $fullResourceName . $match[1];
            }
            if (empty($match[5])) {
                $variable_template = true;
            }
        } else {
            $variable_template = true;
        }
        $_scope = $compiler->convertScope($_attr, $this->valid_scopes);
        if ($cache_tpl || $variable_template || 0 < $compiler->loopNesting) {
            $_cache_tpl = "true";
        } else {
            $_cache_tpl = "false";
        }
        $_caching = Smarty::CACHING_OFF;
        $call_nocache = $compiler->tag_nocache || $compiler->nocache;
        if ($compiler->template->caching && !$compiler->nocache && !$compiler->tag_nocache) {
            $_caching = self::CACHING_NOCACHE_CODE;
        }
        $merge_compiled_includes = ($compiler->smarty->merge_compiled_includes || $_attr["inline"] === true) && !$compiler->template->source->handler->recompiled;
        if ($merge_compiled_includes) {
            if ($variable_template) {
                $merge_compiled_includes = false;
            }
            if (isset($_attr["compile_id"]) && $compiler->isVariable($_attr["compile_id"])) {
                $merge_compiled_includes = false;
            }
        }
        if ($_attr["nocache"] !== true && $_attr["caching"]) {
            $_caching = $_new_caching = (int) $_attr["caching"];
            $call_nocache = true;
        } else {
            $_new_caching = Smarty::CACHING_LIFETIME_CURRENT;
        }
        if (isset($_attr["cache_lifetime"])) {
            $_cache_lifetime = $_attr["cache_lifetime"];
            $call_nocache = true;
            $_caching = $_new_caching;
        } else {
            $_cache_lifetime = "\$_smarty_tpl->cache_lifetime";
        }
        if (isset($_attr["cache_id"])) {
            $_cache_id = $_attr["cache_id"];
            $call_nocache = true;
            $_caching = $_new_caching;
        } else {
            $_cache_id = "\$_smarty_tpl->cache_id";
        }
        if (isset($_attr["compile_id"])) {
            $_compile_id = $_attr["compile_id"];
        } else {
            $_compile_id = "\$_smarty_tpl->compile_id";
        }
        if ($compiler->template->caching && $call_nocache) {
            $merge_compiled_includes = false;
        }
        if (isset($_attr["assign"])) {
            if ($_assign = $compiler->getId($_attr["assign"])) {
                $_assign = "'" . $_assign . "'";
                if ($compiler->tag_nocache || $compiler->nocache || $call_nocache) {
                    $compiler->setNocacheInVariable($_attr["assign"]);
                }
            } else {
                $_assign = $_attr["assign"];
            }
        }
        $has_compiled_template = false;
        if ($merge_compiled_includes) {
            $c_id = isset($_attr["compile_id"]) ? $_attr["compile_id"] : $compiler->template->compile_id;
            $t_hash = sha1($c_id . ($_caching ? "--caching" : "--nocaching"));
            $compiler->smarty->allow_ambiguous_resources = true;
            $tpl = new $compiler->smarty->template_class(trim($fullResourceName, "\"'"), $compiler->smarty, $compiler->template, $compiler->template->cache_id, $c_id, $_caching);
            $uid = $tpl->source->type . $tpl->source->uid;
            if (!isset($compiler->parent_compiler->mergedSubTemplatesData[$uid][$t_hash])) {
                $has_compiled_template = $this->compileInlineTemplate($compiler, $tpl, $t_hash);
            } else {
                $has_compiled_template = true;
            }
            unset($tpl);
        }
        unset($_attr["file"]);
        unset($_attr["assign"]);
        unset($_attr["cache_id"]);
        unset($_attr["compile_id"]);
        unset($_attr["cache_lifetime"]);
        unset($_attr["nocache"]);
        unset($_attr["caching"]);
        unset($_attr["scope"]);
        unset($_attr["inline"]);
        $_vars = "array()";
        if (!empty($_attr)) {
            $_pairs = array();
            foreach ($_attr as $key => $value) {
                $_pairs[] = "'" . $key . "'=>" . $value;
            }
            $_vars = "array(" . join(",", $_pairs) . ")";
        }
        $update_compile_id = $compiler->template->caching && !$compiler->tag_nocache && !$compiler->nocache && $_compile_id !== "\$_smarty_tpl->compile_id";
        if ($has_compiled_template && !$call_nocache) {
            $_output = "<?php\n";
            if ($update_compile_id) {
                $_output .= $compiler->makeNocacheCode("\$_compile_id_save[] = \$_smarty_tpl->compile_id;\n\$_smarty_tpl->compile_id = " . $_compile_id . ";\n");
            }
            if (!empty($_attr) && $_caching === 9999 && $compiler->template->caching) {
                $_vars_nc = "foreach (" . $_vars . " as \$ik => \$iv) {\n";
                $_vars_nc .= "\$_smarty_tpl->tpl_vars[\$ik] =  new Smarty_Variable(\$iv);\n";
                $_vars_nc .= "}\n";
                $_output .= substr($compiler->processNocacheCode("<?php " . $_vars_nc . "?>\n", true), 6, -3);
            }
            if (isset($_assign)) {
                $_output .= "ob_start();\n";
            }
            $_output .= "\$_smarty_tpl->_subTemplateRender(" . $fullResourceName . ", " . $_cache_id . ", " . $_compile_id . ", " . $_caching . ", " . $_cache_lifetime . ", " . $_vars . ", " . $_scope . ", " . $_cache_tpl . ", '" . $compiler->parent_compiler->mergedSubTemplatesData[$uid][$t_hash]["uid"] . "', '" . $compiler->parent_compiler->mergedSubTemplatesData[$uid][$t_hash]["func"] . "');\n";
            if (isset($_assign)) {
                $_output .= "\$_smarty_tpl->assign(" . $_assign . ", ob_get_clean());\n";
            }
            if ($update_compile_id) {
                $_output .= $compiler->makeNocacheCode("\$_smarty_tpl->compile_id = array_pop(\$_compile_id_save);\n");
            }
            $_output .= "?>";
            return $_output;
        }
        if ($call_nocache) {
            $compiler->tag_nocache = true;
        }
        $_output = "<?php ";
        if ($update_compile_id) {
            $_output .= "\$_compile_id_save[] = \$_smarty_tpl->compile_id;\n\$_smarty_tpl->compile_id = " . $_compile_id . ";\n";
        }
        if (isset($_assign)) {
            $_output .= "ob_start();\n";
        }
        $_output .= "\$_smarty_tpl->_subTemplateRender(" . $fullResourceName . ", " . $_cache_id . ", " . $_compile_id . ", " . $_caching . ", " . $_cache_lifetime . ", " . $_vars . ", " . $_scope . ", " . $_cache_tpl . ");\n";
        if (isset($_assign)) {
            $_output .= "\$_smarty_tpl->assign(" . $_assign . ", ob_get_clean());\n";
        }
        if ($update_compile_id) {
            $_output .= "\$_smarty_tpl->compile_id = array_pop(\$_compile_id_save);\n";
        }
        $_output .= "?>";
        return $_output;
    }
    /**
     * Compile inline sub template
     *
     * @param \Smarty_Internal_SmartyTemplateCompiler $compiler
     * @param \Smarty_Internal_Template               $tpl
     * @param  string                                 $t_hash
     *
     * @return bool
     * @throws \Exception
     * @throws \SmartyException
     */
    public function compileInlineTemplate(Smarty_Internal_SmartyTemplateCompiler $compiler, Smarty_Internal_Template $tpl, $t_hash)
    {
        $uid = $tpl->source->type . $tpl->source->uid;
        if (!$tpl->source->handler->uncompiled && $tpl->source->exists) {
            $compiler->parent_compiler->mergedSubTemplatesData[$uid][$t_hash]["uid"] = $tpl->source->uid;
            if (isset($compiler->template->inheritance)) {
                $tpl->inheritance = clone $compiler->template->inheritance;
            }
            $tpl->compiled = new Smarty_Template_Compiled();
            $tpl->compiled->nocache_hash = $compiler->parent_compiler->template->compiled->nocache_hash;
            $tpl->loadCompiler();
            $compiler->parent_compiler->mergedSubTemplatesData[$uid][$t_hash]["func"] = $tpl->compiled->unifunc = "content_" . str_replace(array(".", ","), "_", uniqid("", true));
            $tpl->mustCompile = true;
            $compiler->parent_compiler->mergedSubTemplatesData[$uid][$t_hash]["nocache_hash"] = $tpl->compiled->nocache_hash;
            if ($tpl->source->type === "file") {
                $sourceInfo = $tpl->source->filepath;
            } else {
                $basename = $tpl->source->handler->getBasename($tpl->source);
                $sourceInfo = $tpl->source->type . ":" . ($basename ? $basename : $tpl->source->name);
            }
            $compiled_code = "<?php\n\n";
            $compiled_code .= "/* Start inline template \"" . $sourceInfo . "\" =============================*/\n";
            $compiled_code .= "function " . $tpl->compiled->unifunc . " (Smarty_Internal_Template \$_smarty_tpl) {\n";
            $compiled_code .= "?>\n" . $tpl->compiler->compileTemplateSource($tpl, NULL, $compiler->parent_compiler);
            $compiled_code .= "<?php\n";
            $compiled_code .= "}\n?>\n";
            $compiled_code .= $tpl->compiler->postFilter($tpl->compiler->blockOrFunctionCode);
            $compiled_code .= "<?php\n\n";
            $compiled_code .= "/* End inline template \"" . $sourceInfo . "\" =============================*/\n";
            $compiled_code .= "?>";
            unset($tpl->compiler);
            if ($tpl->compiled->has_nocache_code) {
                $compiled_code = str_replace((string) $tpl->compiled->nocache_hash, $compiler->template->compiled->nocache_hash, $compiled_code);
                $compiler->template->compiled->has_nocache_code = true;
            }
            $compiler->parent_compiler->mergedSubTemplatesCode[$tpl->compiled->unifunc] = $compiled_code;
            return true;
        }
        return false;
    }
}

?>