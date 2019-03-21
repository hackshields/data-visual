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
 * Smarty Method SetDebugTemplate
 *
 * Smarty::setDebugTemplate() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_SetDebugTemplate
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;
    /**
     * set the debug template
     *
     * @api Smarty::setDebugTemplate()
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $tpl_name
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws SmartyException if file is not readable
     */
    public function setDebugTemplate(Smarty_Internal_TemplateBase $obj, $tpl_name)
    {
        $smarty = $obj->_getSmartyObj();
        if (!is_readable($tpl_name)) {
            throw new SmartyException("Unknown file '" . $tpl_name . "'");
        }
        $smarty->debug_tpl = $tpl_name;
        return $obj;
    }
}

?>