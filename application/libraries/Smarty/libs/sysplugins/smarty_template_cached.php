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
 * Smarty Resource Data Object
 * Cache Data Container for Template Files
 *
 * @package    Smarty
 * @subpackage TemplateResources
 * @author     Rodney Rehm
 */
class Smarty_Template_Cached extends Smarty_Template_Resource_Base
{
    /**
     * Cache Is Valid
     *
     * @var boolean
     */
    public $valid = NULL;
    /**
     * CacheResource Handler
     *
     * @var Smarty_CacheResource
     */
    public $handler = NULL;
    /**
     * Template Cache Id (Smarty_Internal_Template::$cache_id)
     *
     * @var string
     */
    public $cache_id = NULL;
    /**
     * saved cache lifetime in seconds
     *
     * @var integer
     */
    public $cache_lifetime = 0;
    /**
     * Id for cache locking
     *
     * @var string
     */
    public $lock_id = NULL;
    /**
     * flag that cache is locked by this instance
     *
     * @var bool
     */
    public $is_locked = false;
    /**
     * Source Object
     *
     * @var Smarty_Template_Source
     */
    public $source = NULL;
    /**
     * Nocache hash codes of processed compiled templates
     *
     * @var array
     */
    public $hashes = array();
    /**
     * Flag if this is a cache resource
     *
     * @var bool
     */
    public $isCache = true;
    /**
     * create Cached Object container
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @throws \SmartyException
     */
    public function __construct(Smarty_Internal_Template $_template)
    {
        $this->compile_id = $_template->compile_id;
        $this->cache_id = $_template->cache_id;
        $this->source = $_template->source;
        if (!class_exists("Smarty_CacheResource", false)) {
            require SMARTY_SYSPLUGINS_DIR . "smarty_cacheresource.php";
        }
        $this->handler = Smarty_CacheResource::load($_template->smarty);
    }
    /**
     * @param Smarty_Internal_Template $_template
     *
     * @return Smarty_Template_Cached
     */
    public static function load(Smarty_Internal_Template $_template)
    {
        $_template->cached = new Smarty_Template_Cached($_template);
        $_template->cached->handler->populate($_template->cached, $_template);
        if (!$_template->caching || $_template->source->handler->recompiled) {
            $_template->cached->valid = false;
        }
        return $_template->cached;
    }
    /**
     * Render cache template
     *
     * @param \Smarty_Internal_Template $_template
     * @param  bool                     $no_output_filter
     *
     * @throws \Exception
     */
    public function render(Smarty_Internal_Template $_template, $no_output_filter = true)
    {
        if ($this->isCached($_template)) {
            if ($_template->smarty->debugging) {
                if (!isset($_template->smarty->_debug)) {
                    $_template->smarty->_debug = new Smarty_Internal_Debug();
                }
                $_template->smarty->_debug->start_cache($_template);
            }
            if (!$this->processed) {
                $this->process($_template);
            }
            $this->getRenderedTemplateCode($_template);
            if ($_template->smarty->debugging) {
                $_template->smarty->_debug->end_cache($_template);
            }
        } else {
            $_template->smarty->ext->_updateCache->updateCache($this, $_template, $no_output_filter);
        }
    }
    /**
     * Check if cache is valid, lock cache if required
     *
     * @param \Smarty_Internal_Template $_template
     *
     * @return bool flag true if cache is valid
     */
    public function isCached(Smarty_Internal_Template $_template)
    {
        if ($this->valid !== NULL) {
            return $this->valid;
        }
        if (true) {
            while (true) {
                if ($this->exists === false || $_template->smarty->force_compile || $_template->smarty->force_cache) {
                    $this->valid = false;
                } else {
                    $this->valid = true;
                }
                if ($this->valid && $_template->caching === Smarty::CACHING_LIFETIME_CURRENT && 0 <= $_template->cache_lifetime && $this->timestamp + $_template->cache_lifetime < time()) {
                    $this->valid = false;
                }
                if ($this->valid && $_template->compile_check === Smarty::COMPILECHECK_ON && $this->timestamp < $_template->source->getTimeStamp()) {
                    $this->valid = false;
                }
                if ($this->valid || !$_template->smarty->cache_locking) {
                    break;
                }
                if (!$this->handler->locked($_template->smarty, $this)) {
                    $this->handler->acquireLock($_template->smarty, $this);
                    break 2;
                }
                $this->handler->populate($this, $_template);
            }
            if ($this->valid) {
                if (!$_template->smarty->cache_locking || $this->handler->locked($_template->smarty, $this) === NULL) {
                    if ($_template->smarty->debugging) {
                        $_template->smarty->_debug->start_cache($_template);
                    }
                    if ($this->handler->process($_template, $this) === false) {
                        $this->valid = false;
                    } else {
                        $this->processed = true;
                    }
                    if ($_template->smarty->debugging) {
                        $_template->smarty->_debug->end_cache($_template);
                    }
                    if ($this->valid && $_template->caching === Smarty::CACHING_LIFETIME_SAVED && 0 <= $_template->cached->cache_lifetime && $_template->cached->timestamp + $_template->cached->cache_lifetime < time()) {
                        $this->valid = false;
                    }
                    if ($_template->smarty->cache_locking) {
                        if (!$this->valid) {
                            $this->handler->acquireLock($_template->smarty, $this);
                        } else {
                            if ($this->is_locked) {
                                $this->handler->releaseLock($_template->smarty, $this);
                            }
                        }
                    }
                    return $this->valid;
                }
                $this->is_locked = true;
                continue;
            }
            return $this->valid;
        }
        return $this->valid;
    }
    /**
     * Process cached template
     *
     * @param Smarty_Internal_Template $_template template object
     * @param bool                     $update    flag if called because cache update
     */
    public function process(Smarty_Internal_Template $_template, $update = false)
    {
        if ($this->handler->process($_template, $this, $update) === false) {
            $this->valid = false;
        }
        if ($this->valid) {
            $this->processed = true;
        } else {
            $this->processed = false;
        }
    }
    /**
     * Read cache content from handler
     *
     * @param Smarty_Internal_Template $_template template object
     *
     * @return string|false content
     */
    public function read(Smarty_Internal_Template $_template)
    {
        if (!$_template->source->handler->recompiled) {
            return $this->handler->readCachedContent($_template);
        }
        return false;
    }
}

?>