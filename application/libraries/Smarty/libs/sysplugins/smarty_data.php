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
 * class for the Smarty data object
 * The Smarty data object will hold Smarty variables in the current scope
 *
 * @package    Smarty
 * @subpackage Template
 */
class Smarty_Data extends Smarty_Internal_Data
{
    /**
     * Counter
     *
     * @var int
     */
    public static $count = 0;
    /**
     * Data block name
     *
     * @var string
     */
    public $dataObjectName = "";
    /**
     * Smarty object
     *
     * @var Smarty
     */
    public $smarty = NULL;
    /**
     * create Smarty data object
     *
     * @param Smarty|array                    $_parent parent template
     * @param Smarty|Smarty_Internal_Template $smarty  global smarty instance
     * @param string                          $name    optional data block name
     *
     * @throws SmartyException
     */
    public function __construct($_parent = NULL, $smarty = NULL, $name = NULL)
    {
        parent::__construct();
        self::$count++;
        $this->dataObjectName = "Data_object " . (isset($name) ? "'" . $name . "'" : self::$count);
        $this->smarty = $smarty;
        if (is_object($_parent)) {
            $this->parent = $_parent;
        } else {
            if (is_array($_parent)) {
                foreach ($_parent as $_key => $_val) {
                    $this->tpl_vars[$_key] = new Smarty_Variable($_val);
                }
            } else {
                if ($_parent !== NULL) {
                    throw new SmartyException("Wrong type for template variables");
                }
            }
        }
    }
}

?>