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
 * Smarty Internal Plugin Compile Break Class
 *
 * @package    Smarty
 * @subpackage Compiler
 */
class Smarty_Internal_Compile_Break extends Smarty_Internal_CompileBase
{
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $optional_attributes = array("levels");
    /**
     * Attribute definition: Overwrites base class.
     *
     * @var array
     * @see Smarty_Internal_CompileBase
     */
    public $shorttag_order = array("levels");
    /**
     * Tag name may be overloaded by Smarty_Internal_Compile_Continue
     *
     * @var string
     */
    public $tag = "break";
    /**
     * Compiles code for the {break} tag
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return string compiled code
     * @throws \SmartyCompilerException
     */
    public function compile($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        list($levels, $foreachLevels) = $this->checkLevels($args, $compiler);
        $output = "<?php ";
        if (0 < $foreachLevels && $this->tag === "continue") {
            $foreachLevels--;
        }
        if (0 < $foreachLevels) {
            $foreachCompiler = $compiler->getTagCompiler("foreach");
            $output .= $foreachCompiler->compileRestore($foreachLevels);
        }
        $output .= (string) $this->tag . " " . $levels . ";?>";
        return $output;
    }
    /**
     * check attributes and return array of break and foreach levels
     *
     * @param  array                                $args     array with attributes from parser
     * @param \Smarty_Internal_TemplateCompilerBase $compiler compiler object
     *
     * @return array
     * @throws \SmartyCompilerException
     */
    public function checkLevels($args, Smarty_Internal_TemplateCompilerBase $compiler)
    {
        static $_is_loopy = array("for" => true, "foreach" => true, "while" => true, "section" => true);
        $_attr = $this->getAttributes($compiler, $args);
        if ($_attr["nocache"] === true) {
            $compiler->trigger_template_error("nocache option not allowed", NULL, true);
        }
        if (isset($_attr["levels"])) {
            if (!is_numeric($_attr["levels"])) {
                $compiler->trigger_template_error("level attribute must be a numeric constant", NULL, true);
            }
            $levels = $_attr["levels"];
        } else {
            $levels = 1;
        }
        $level_count = $levels;
        $stack_count = count($compiler->_tag_stack) - 1;
        $foreachLevels = 0;
        for ($lastTag = ""; 0 < $level_count && 0 <= $stack_count; $stack_count--) {
            if (isset($_is_loopy[$compiler->_tag_stack[$stack_count][0]])) {
                $lastTag = $compiler->_tag_stack[$stack_count][0];
                if ($level_count === 0) {
                    break;
                }
                $level_count--;
                if ($compiler->_tag_stack[$stack_count][0] === "foreach") {
                    $foreachLevels++;
                }
            }
        }
        if ($level_count !== 0) {
            $compiler->trigger_template_error("cannot " . $this->tag . " " . $levels . " level(s)", NULL, true);
        }
        if ($lastTag === "foreach" && $this->tag === "break" && 0 < $foreachLevels) {
            $foreachLevels--;
        }
        return array($levels, $foreachLevels);
    }
}

?>