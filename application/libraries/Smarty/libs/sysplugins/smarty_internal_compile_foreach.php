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
 * Smarty Internal Plugin Compile Foreach Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreach extends Smarty_Internal_Compile_Private_ForeachSection
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $required_attributes = array("from", "item");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array("name", "key", "properties");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array("from", "item", "key", "name");
    /**
     * counter
     *
     * @var int
     */
    public $counter = 0;
    /**
     * Name of this tag
     *
     * @var string
     */
    public $tagName = "foreach";
    /**
     * Valid properties of $smarty.foreach.name.xxx variable
     *
     * @var array
     */
    public $nameProperties = array("first", "last", "index", "iteration", "show", "total");
    /**
     * Valid properties of $item@xxx variable
     *
     * @var array
     */
    public $itemProperties = array("first", "last", "index", "iteration", "show", "total", "key");
    /**
     * Flag if tag had name attribute
     *
     * @var bool
     */
    public $isNamed = false;
    /**
     * Compiles code for the {foreach} tag
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
        $compiler->loopNesting++;
        $this->isNamed = false;
        $_attr = $this->getAttributes($compiler, $args);
        $from = $_attr["from"];
        $item = $compiler->getId($_attr["item"]);
        if ($item === false) {
            $item = $compiler->getVariableName($_attr["item"]);
        }
        $key = $name = NULL;
        $attributes = array("item" => $item);
        if (isset($_attr["key"])) {
            $key = $compiler->getId($_attr["key"]);
            if ($key === false) {
                $key = $compiler->getVariableName($_attr["key"]);
            }
            $attributes["key"] = $key;
        }
        if (isset($_attr["name"])) {
            $this->isNamed = true;
            $attributes["name"] = $compiler->getId($_attr["name"]);
            $name = $attributes["name"];
        }
        foreach ($attributes as $a => $v) {
            if ($v === false) {
                $compiler->trigger_template_error("'" . $a . "' attribute/variable has illegal value", NULL, true);
            }
        }
        $fromName = $compiler->getVariableName($_attr["from"]);
        if ($fromName) {
            foreach (array("item", "key") as $a) {
                if (isset($attributes[$a]) && $attributes[$a] === $fromName) {
                    $compiler->trigger_template_error("'" . $a . "' and 'from' may not have same variable name '" . $fromName . "'", NULL, true);
                }
            }
        }
        $itemVar = "\$_smarty_tpl->tpl_vars['" . $item . "']";
        $local = "\$__foreach_" . $attributes["item"] . "_" . $this->counter++ . "_";
        $itemAttr = array();
        $namedAttr = array();
        $this->scanForProperties($attributes, $compiler);
        if (!empty($this->matchResults["item"])) {
            $itemAttr = $this->matchResults["item"];
        }
        if (!empty($this->matchResults["named"])) {
            $namedAttr = $this->matchResults["named"];
        }
        if (isset($_attr["properties"]) && preg_match_all("/['](.*?)[']/", $_attr["properties"], $match)) {
            foreach ($match[1] as $prop) {
                if (in_array($prop, $this->itemProperties)) {
                    $itemAttr[$prop] = true;
                } else {
                    $compiler->trigger_template_error("Invalid property '" . $prop . "'", NULL, true);
                }
            }
            if ($this->isNamed) {
                foreach ($match[1] as $prop) {
                    if (in_array($prop, $this->nameProperties)) {
                        $nameAttr[$prop] = true;
                    } else {
                        $compiler->trigger_template_error("Invalid property '" . $prop . "'", NULL, true);
                    }
                }
            }
        }
        if (isset($itemAttr["first"])) {
            $itemAttr["index"] = true;
        }
        if (isset($namedAttr["first"])) {
            $namedAttr["index"] = true;
        }
        if (isset($namedAttr["last"])) {
            $namedAttr["iteration"] = true;
            $namedAttr["total"] = true;
        }
        if (isset($itemAttr["last"])) {
            $itemAttr["iteration"] = true;
            $itemAttr["total"] = true;
        }
        if (isset($namedAttr["show"])) {
            $namedAttr["total"] = true;
        }
        if (isset($itemAttr["show"])) {
            $itemAttr["total"] = true;
        }
        $keyTerm = "";
        if (isset($attributes["key"])) {
            $keyTerm = "\$_smarty_tpl->tpl_vars['" . $key . "']->value => ";
        }
        if (isset($itemAttr["key"])) {
            $keyTerm = (string) $itemVar . "->key => ";
        }
        if ($this->isNamed) {
            $foreachVar = "\$_smarty_tpl->tpl_vars['__smarty_foreach_" . $attributes["name"] . "']";
        }
        $needTotal = isset($itemAttr["total"]);
        $this->openTag($compiler, "foreach", array("foreach", $compiler->nocache, $local, $itemVar, empty($itemAttr) ? 1 : 2));
        $compiler->nocache = $compiler->nocache | $compiler->tag_nocache;
        $output = "<?php\n";
        $output .= "\$_from = \$_smarty_tpl->smarty->ext->_foreach->init(\$_smarty_tpl, " . $from . ", " . var_export($item, true);
        if ($name || $needTotal || $key) {
            $output .= ", " . var_export($needTotal, true);
        }
        if ($name || $key) {
            $output .= ", " . var_export($key, true);
        }
        if ($name) {
            $output .= ", " . var_export($name, true) . ", " . var_export($namedAttr, true);
        }
        $output .= ");\n";
        if (isset($itemAttr["show"])) {
            $output .= (string) $itemVar . "->show = (" . $itemVar . "->total > 0);\n";
        }
        if (isset($itemAttr["iteration"])) {
            $output .= (string) $itemVar . "->iteration = 0;\n";
        }
        if (isset($itemAttr["index"])) {
            $output .= (string) $itemVar . "->index = -1;\n";
        }
        $output .= "if (\$_from !== null) {\n";
        $output .= "foreach (\$_from as " . $keyTerm . $itemVar . "->value) {\n";
        if (isset($attributes["key"]) && isset($itemAttr["key"])) {
            $output .= "\$_smarty_tpl->tpl_vars['" . $key . "']->value = " . $itemVar . "->key;\n";
        }
        if (isset($itemAttr["iteration"])) {
            $output .= (string) $itemVar . "->iteration++;\n";
        }
        if (isset($itemAttr["index"])) {
            $output .= (string) $itemVar . "->index++;\n";
        }
        if (isset($itemAttr["first"])) {
            $output .= (string) $itemVar . "->first = !" . $itemVar . "->index;\n";
        }
        if (isset($itemAttr["last"])) {
            $output .= (string) $itemVar . "->last = " . $itemVar . "->iteration === " . $itemVar . "->total;\n";
        }
        if (isset($foreachVar)) {
            if (isset($namedAttr["iteration"])) {
                $output .= (string) $foreachVar . "->value['iteration']++;\n";
            }
            if (isset($namedAttr["index"])) {
                $output .= (string) $foreachVar . "->value['index']++;\n";
            }
            if (isset($namedAttr["first"])) {
                $output .= (string) $foreachVar . "->value['first'] = !" . $foreachVar . "->value['index'];\n";
            }
            if (isset($namedAttr["last"])) {
                $output .= (string) $foreachVar . "->value['last'] = " . $foreachVar . "->value['iteration'] === " . $foreachVar . "->value['total'];\n";
            }
        }
        if (!empty($itemAttr)) {
            $output .= (string) $local . "saved = " . $itemVar . ";\n";
        }
        $output .= "?>";
        return $output;
    }
    /**
     * Compiles code for to restore saved template variables
     *
     * @param int $levels number of levels to restore
     *
     * @return string compiled code
     */
    public function compileRestore($levels)
    {
        return "\$_smarty_tpl->smarty->ext->_foreach->restore(\$_smarty_tpl, " . $levels . ");";
    }
}
/**
 * Smarty Internal Plugin Compile Foreachelse Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreachelse extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {foreachelse} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $_attr = $this->getAttributes($compiler, $args);
        list($openTag, $nocache, $local, $itemVar, $restore) = $this->closeTag($compiler, array("foreach"));
        $this->openTag($compiler, "foreachelse", array("foreachelse", $nocache, $local, $itemVar, 0));
        $output = "<?php\n";
        if ($restore === 2) {
            $output .= (string) $itemVar . " = " . $local . "saved;\n";
        }
        $output .= "}\n} else {\n?>";
        return $output;
    }
}
/**
 * Smarty Internal Plugin Compile Foreachclose Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Foreachclose extends Smarty_Internal_CompileBase
{
    /**
     * Compiles code for the {/foreach} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        $compiler->loopNesting--;
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }
        list($openTag, $compiler->nocache, $local, $itemVar, $restore) = $this->closeTag($compiler, array("foreach", "foreachelse"));
        $output = "<?php\n";
        if ($restore === 2) {
            $output .= (string) $itemVar . " = " . $local . "saved;\n";
        }
        if (0 < $restore) {
            $output .= "}\n";
        }
        $output .= "}\n";
        $foreachCompiler = $compiler->getTagCompiler("foreach");
        $output .= $foreachCompiler->compileRestore(1);
        $output .= "?>";
        return $output;
    }
}

?>