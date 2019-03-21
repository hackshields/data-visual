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
 * Smarty Resource Plugin
 * Base implementation for resource plugins that don't compile cache
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
abstract class Smarty_Resource_Recompiled extends Smarty_Resource
{
    /**
     * Flag that it's an recompiled resource
     *
     * @var bool
     */
    public $recompiled = true;
    /**
     * Resource does implement populateCompiledFilepath() method
     *
     * @var bool
     */
    public $hasCompiledHandler = true;
    /**
     * compile template from source
     *
     * @param Smarty_Internal_Template $_smarty_tpl do not change variable name, is used by compiled template
     *
     * @throws Exception
     */
    public function process(Smarty_Internal_Template $_smarty_tpl)
    {
        $compiled =& $_smarty_tpl->compiled;
        $compiled->file_dependency = array();
        $compiled->includes = array();
        $compiled->nocache_hash = NULL;
        $compiled->unifunc = NULL;
        $level = ob_get_level();
        ob_start();
        $_smarty_tpl->loadCompiler();
        try {
            eval("?>" . $_smarty_tpl->compiler->compileTemplate($_smarty_tpl));
        } catch (Exception $e) {
            unset($_smarty_tpl->compiler);
            while ($level < ob_get_level()) {
                ob_end_clean();
            }
            throw $e;
        }
        unset($_smarty_tpl->compiler);
        ob_get_clean();
        $compiled->timestamp = time();
        $compiled->exists = true;
    }
    /**
     * populate Compiled Object with compiled filepath
     *
     * @param  Smarty_Template_Compiled $compiled  compiled object
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return void
     */
    public function populateCompiledFilepath(Smarty_Template_Compiled $compiled, Smarty_Internal_Template $_template)
    {
        $compiled->filepath = false;
        $compiled->timestamp = false;
        $compiled->exists = false;
    }
    /**
     * @return bool
     */
    public function checkTimestamps()
    {
        return false;
    }
}

?>