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
 * Smarty Method RegisterObject
 *
 * Smarty::registerObject() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_RegisterObject
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;
    /**
     * Registers object to be used in templates
     *
     * @api  Smarty::registerObject()
     * @link http://www.smarty.net/docs/en/api.register.object.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param  string                                                         $object_name
     * @param  object                                                         $object                     the
     *                                                                                                    referenced
     *                                                                                                    PHP object to
     *                                                                                                    register
     * @param  array                                                          $allowed_methods_properties list of
     *                                                                                                    allowed
     *                                                                                                    methods
     *                                                                                                    (empty = all)
     * @param  bool                                                           $format                     smarty
     *                                                                                                    argument
     *                                                                                                    format, else
     *                                                                                                    traditional
     * @param  array                                                          $block_methods              list of
     *                                                                                                    block-methods
     *
     * @return \Smarty|\Smarty_Internal_Template
     * @throws \SmartyException
     */
    public function registerObject(Smarty_Internal_TemplateBase $obj, $object_name, $object, $allowed_methods_properties = array(), $format = true, $block_methods = array())
    {
        $smarty = $obj->_getSmartyObj();
        if (!empty($allowed_methods_properties)) {
            foreach ((array) $allowed_methods_properties as $method) {
                if (!is_callable(array($object, $method)) && !property_exists($object, $method)) {
                    throw new SmartyException("Undefined method or property '" . $method . "' in registered object");
                }
            }
        }
        if (!empty($block_methods)) {
            foreach ((array) $block_methods as $method) {
                if (!is_callable(array($object, $method))) {
                    throw new SmartyException("Undefined method '" . $method . "' in registered object");
                }
            }
        }
        $smarty->registered_objects[$object_name] = array($object, (array) $allowed_methods_properties, (bool) $format, (array) $block_methods);
        return $obj;
    }
}

?>