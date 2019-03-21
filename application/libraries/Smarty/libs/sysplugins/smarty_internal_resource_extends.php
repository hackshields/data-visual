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
 * Smarty Internal Plugin Resource Extends
 * Implements the file system as resource for Smarty which {extend}s a chain of template files templates
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Resource_Extends extends Smarty_Resource
{
    /**
     * mbstring.overload flag
     *
     * @var int
     */
    public $mbstring_overload = 0;
    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     *
     * @throws SmartyException
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = NULL)
    {
        $uid = "";
        $sources = array();
        $components = explode("|", $source->name);
        $smarty =& $source->smarty;
        $exists = true;
        foreach ($components as $component) {
            $_s = Smarty_Template_Source::load(NULL, $smarty, $component);
            if ($_s->type === "php") {
                throw new SmartyException("Resource type " . $_s->type . " cannot be used with the extends resource type");
            }
            $sources[$_s->uid] = $_s;
            $uid .= $_s->filepath;
            if ($_template) {
                $exists = $exists && $_s->exists;
            }
        }
        $source->components = $sources;
        $source->filepath = $_s->filepath;
        $source->uid = sha1($uid . $source->smarty->_joined_template_dir);
        $source->exists = $exists;
        if ($_template) {
            $source->timestamp = $_s->timestamp;
        }
    }
    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        $source->exists = true;
        foreach ($source->components as $_s) {
            $source->exists = $source->exists && $_s->exists;
        }
        $source->timestamp = $source->exists ? $_s->getTimeStamp() : false;
    }
    /**
     * Load template's source from files into current template object
     *
     * @param Smarty_Template_Source $source source object
     *
     * @return string template source
     * @throws SmartyException if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        if (!$source->exists) {
            throw new SmartyException("Unable to load template '" . $source->type . ":" . $source->name . "'");
        }
        $_components = array_reverse($source->components);
        $_content = "";
        foreach ($_components as $_s) {
            $_content .= $_s->getContent();
        }
        return $_content;
    }
    /**
     * Determine basename for compiled filename
     *
     * @param Smarty_Template_Source $source source object
     *
     * @return string resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        return str_replace(":", ".", basename($source->filepath));
    }
    /**
     * @return bool
     */
    public function checkTimestamps()
    {
        return false;
    }
}

?>