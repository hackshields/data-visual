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
 * Smarty Method ClearCache
 *
 * Smarty::clearCache() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_ClearCache
{
    /**
     * Valid for Smarty object
     *
     * @var int
     */
    public $objMap = 1;
    /**
     * Empty cache for a specific template
     *
     * @api  Smarty::clearCache()
     * @link http://www.smarty.net/docs/en/api.clear.cache.tpl
     *
     * @param \Smarty  $smarty
     * @param  string  $template_name template name
     * @param  string  $cache_id      cache id
     * @param  string  $compile_id    compile id
     * @param  integer $exp_time      expiration time
     * @param  string  $type          resource type
     *
     * @return int number of cache files deleted
     * @throws \SmartyException
     */
    public function clearCache(Smarty $smarty, $template_name, $cache_id = NULL, $compile_id = NULL, $exp_time = NULL, $type = NULL)
    {
        $smarty->_clearTemplateCache();
        $_cache_resource = Smarty_CacheResource::load($smarty, $type);
        return $_cache_resource->clear($smarty, $template_name, $cache_id, $compile_id, $exp_time);
    }
}

?>