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
 * Smarty Internal Undefined
 *
 * Class to handle undefined method calls or calls to obsolete runtime extensions
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Undefined
{
    /**
     * Name of undefined extension class
     *
     * @var string|null
     */
    public $class = NULL;
    /**
     * Smarty_Internal_Undefined constructor.
     *
     * @param null|string $class name of undefined extension class
     */
    public function __construct($class = NULL)
    {
        $this->class = $class;
    }
    /**
     * Wrapper for obsolete class Smarty_Internal_Runtime_ValidateCompiled
     *
     * @param  \Smarty_Internal_Template $tpl
     * @param  array                     $properties special template properties
     * @param  bool                      $cache      flag if called from cache file
     *
     * @return bool false
     */
    public function decodeProperties(Smarty_Internal_Template $tpl, $properties, $cache = false)
    {
        if ($cache) {
            $tpl->cached->valid = false;
        } else {
            $tpl->mustCompile = true;
        }
        return false;
    }
    /**
     * Call error handler for undefined method
     *
     * @param string $name unknown method-name
     * @param array  $args argument array
     *
     * @return mixed
     * @throws SmartyException
     */
    public function __call($name, $args)
    {
        if (isset($this->class)) {
            throw new SmartyException("undefined extension class '" . $this->class . "'");
        }
        throw new SmartyException(get_class($args[0]) . "->" . $name . "() undefined method");
    }
}

?>