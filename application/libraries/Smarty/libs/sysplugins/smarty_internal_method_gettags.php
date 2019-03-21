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
 * Smarty Method GetTags
 *
 * Smarty::getTags() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_GetTags
{
    /**
     * Valid for Smarty and template object
     *
     * @var int
     */
    public $objMap = 3;
    /**
     * Return array of tag/attributes of all tags used by an template
     *
     * @api  Smarty::getTags()
     * @link http://www.smarty.net/docs/en/api.get.tags.tpl
     *
     * @param \Smarty_Internal_TemplateBase|\Smarty_Internal_Template|\Smarty $obj
     * @param null|string|Smarty_Internal_Template                            $template
     *
     * @return array of tag/attributes
     * @throws \Exception
     * @throws \SmartyException
     */
    public function getTags(Smarty_Internal_TemplateBase $obj, $template = NULL)
    {
        $smarty = $obj->_getSmartyObj();
        if ($obj->_isTplObj() && !isset($template)) {
            $tpl = clone $obj;
        } else {
            if (isset($template) && $template->_isTplObj()) {
                $tpl = clone $template;
            } else {
                if (isset($template) && is_string($template)) {
                    $tpl = new $smarty->template_class($template, $smarty);
                    if (!$tpl->source->exists) {
                        throw new SmartyException("Unable to load template " . $tpl->source->type . " '" . $tpl->source->name . "'");
                    }
                }
            }
        }
        if (isset($tpl)) {
            $tpl->smarty = clone $tpl->smarty;
            $tpl->smarty->_cache["get_used_tags"] = true;
            $tpl->_cache["used_tags"] = array();
            $tpl->smarty->merge_compiled_includes = false;
            $tpl->smarty->disableSecurity();
            $tpl->caching = Smarty::CACHING_OFF;
            $tpl->loadCompiler();
            $tpl->compiler->compileTemplate($tpl);
            return $tpl->_cache["used_tags"];
        }
        throw new SmartyException("Missing template specification");
    }
}

?>