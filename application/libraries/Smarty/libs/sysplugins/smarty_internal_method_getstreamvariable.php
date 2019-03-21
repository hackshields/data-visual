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
 * Smarty Method GetStreamVariable
 *
 * Smarty::getStreamVariable() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_GetStreamVariable
{
    /**
     * Valid for all objects
     *
     * @var int
     */
    public $objMap = 7;
    /**
     * gets  a stream variable
     *
     * @api Smarty::getStreamVariable()
     *
     * @param \Smarty_Internal_Data|\Smarty_Internal_Template|\Smarty $data
     * @param  string                                                 $variable the stream of the variable
     *
     * @return mixed
     * @throws \SmartyException
     */
    public function getStreamVariable(Smarty_Internal_Data $data, $variable)
    {
        $_result = "";
        $fp = fopen($variable, "r+");
        if ($fp) {
            while (!feof($fp) && ($current_line = fgets($fp)) !== false) {
                $_result .= $current_line;
            }
            fclose($fp);
            return $_result;
        }
        $smarty = isset($data->smarty) ? $data->smarty : $data;
        if ($smarty->error_unassigned) {
            throw new SmartyException("Undefined stream variable \"" . $variable . "\"");
        }
        return NULL;
    }
}

?>