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
 * Smarty Method GetDefaultModifiers
 *
 * Smarty::getDefaultModifiers() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_GetDefaultModifiers
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;
    /**
     * Get default modifiers
     *
     * @api Smarty::getDefaultModifiers()
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     *
     * @return array list of default modifiers
     */
    public function getDefaultModifiers(Smarty_Internal_TemplateBase $obj)
    {
        $smarty = $obj->_getSmartyObj();
        return $smarty->default_modifiers;
    }
}

?>