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
 * Cache Handler API
 *
 * @package    Smarty
 * @subpackage Cacher
 * @author     Rodney Rehm
 */
abstract class Smarty_CacheResource
{
    /**
     * resource types provided by the core
     *
     * @var array
     */
    protected static $sysplugins = array("file" => "smarty_internal_cacheresource_file.php");
    /**
     * populate Cached Object with meta data from Resource
     *
     * @param Smarty_Template_Cached   $cached    cached object
     * @param Smarty_Internal_Template $_template template object
     *
     * @return void
     */
    public abstract function populate(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template);
    /**
     * populate Cached Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Cached $cached
     *
     * @return void
     */
    public abstract function populateTimestamp(Smarty_Template_Cached $cached);
    /**
     * Read the cached template and process header
     *
     * @param Smarty_Internal_Template $_template template object
     * @param Smarty_Template_Cached   $cached    cached object
     * @param boolean                  $update    flag if called because cache update
     *
     * @return boolean true or false if the cached content does not exist
     */
    public abstract function process(Smarty_Internal_Template $_template, Smarty_Template_Cached $cached, $update);
    /**
     * Write the rendered template output to cache
     *
     * @param Smarty_Internal_Template $_template template object
     * @param string                   $content   content to cache
     *
     * @return boolean success
     */
    public abstract function writeCachedContent(Smarty_Internal_Template $_template, $content);
    /**
     * Read cached template from cache
     *
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return string  content
     */
    public abstract function readCachedContent(Smarty_Internal_Template $_template);
    /**
     * Return cached content
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return null|string
     */
    public function getCachedContent(Smarty_Internal_Template $_template)
    {
        if ($_template->cached->handler->process($_template)) {
            ob_start();
            $unifunc = $_template->cached->unifunc;
            $unifunc($_template);
            return ob_get_clean();
        }
    }
    /**
     * Empty cache
     *
     * @param Smarty  $smarty   Smarty object
     * @param integer $exp_time expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    public abstract function clearAll(Smarty $smarty, $exp_time);
    /**
     * Empty cache for a specific template
     *
     * @param Smarty  $smarty        Smarty object
     * @param string  $resource_name template name
     * @param string  $cache_id      cache id
     * @param string  $compile_id    compile id
     * @param integer $exp_time      expiration time (number of seconds, not timestamp)
     *
     * @return integer number of cache files deleted
     */
    public abstract function clear(Smarty $smarty, $resource_name, $cache_id, $compile_id, $exp_time);
    /**
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool|null
     */
    public function locked(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        $start = microtime(true);
        $hadLock = NULL;
        while ($this->hasLock($smarty, $cached)) {
            $hadLock = true;
            if ($smarty->locking_timeout < microtime(true) - $start) {
                return false;
            }
            sleep(1);
        }
        return $hadLock;
    }
    /**
     * Check is cache is locked for this template
     *
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool
     */
    public function hasLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        return false;
    }
    /**
     * Lock cache for this template
     *
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool
     */
    public function acquireLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        return true;
    }
    /**
     * Unlock cache for this template
     *
     * @param Smarty                 $smarty
     * @param Smarty_Template_Cached $cached
     *
     * @return bool
     */
    public function releaseLock(Smarty $smarty, Smarty_Template_Cached $cached)
    {
        return true;
    }
    /**
     * Load Cache Resource Handler
     *
     * @param Smarty $smarty Smarty object
     * @param string $type   name of the cache resource
     *
     * @throws SmartyException
     * @return Smarty_CacheResource Cache Resource Handler
     */
    public static function load(Smarty $smarty, $type = NULL)
    {
        if (!isset($type)) {
            $type = $smarty->caching_type;
        }
        if (isset($smarty->_cache["cacheresource_handlers"][$type])) {
            return $smarty->_cache["cacheresource_handlers"][$type];
        }
        if (isset($smarty->registered_cache_resources[$type])) {
            $smarty->_cache["cacheresource_handlers"][$type] = $smarty->registered_cache_resources[$type];
            return $smarty->_cache["cacheresource_handlers"][$type];
        }
        if (isset(self::$sysplugins[$type])) {
            $cache_resource_class = "Smarty_Internal_CacheResource_" . ucfirst($type);
            $smarty->_cache["cacheresource_handlers"][$type] = new $cache_resource_class();
            return $smarty->_cache["cacheresource_handlers"][$type];
        }
        $cache_resource_class = "Smarty_CacheResource_" . ucfirst($type);
        if ($smarty->loadPlugin($cache_resource_class)) {
            $smarty->_cache["cacheresource_handlers"][$type] = new $cache_resource_class();
            return $smarty->_cache["cacheresource_handlers"][$type];
        }
        throw new SmartyException("Unable to load cache resource '" . $type . "'");
    }
}

?>