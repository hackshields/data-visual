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
 * Smarty Resource Plugin
 * Base implementation for resource plugins
 *
 * @package    Smarty
 * @subpackage TemplateResources
 *
 * @method renderUncompiled(Smarty_Template_Source $source, Smarty_Internal_Template $_template)
 * @method populateCompiledFilepath(Smarty_Template_Compiled $compiled, Smarty_Internal_Template $_template)
 * @method process(Smarty_Internal_Template $_smarty_tpl)
 */
abstract class Smarty_Resource
{
    /**
     * resource types provided by the core
     *
     * @var array
     */
    public static $sysplugins = array("file" => "smarty_internal_resource_file.php", "string" => "smarty_internal_resource_string.php", "extends" => "smarty_internal_resource_extends.php", "stream" => "smarty_internal_resource_stream.php", "eval" => "smarty_internal_resource_eval.php", "php" => "smarty_internal_resource_php.php");
    /**
     * Source is bypassing compiler
     *
     * @var boolean
     */
    public $uncompiled = false;
    /**
     * Source must be recompiled on every occasion
     *
     * @var boolean
     */
    public $recompiled = false;
    /**
     * Flag if resource does implement populateCompiledFilepath() method
     *
     * @var bool
     */
    public $hasCompiledHandler = false;
    /**
     * Load Resource Handler
     *
     * @param  Smarty $smarty smarty object
     * @param  string $type   name of the resource
     *
     * @throws SmartyException
     * @return Smarty_Resource Resource Handler
     */
    public static function load(Smarty $smarty, $type)
    {
        if (isset($smarty->_cache["resource_handlers"][$type])) {
            return $smarty->_cache["resource_handlers"][$type];
        }
        if (isset($smarty->registered_resources[$type])) {
            $smarty->_cache["resource_handlers"][$type] = $smarty->registered_resources[$type] instanceof Smarty_Resource ? $smarty->registered_resources[$type] : new Smarty_Internal_Resource_Registered();
            return $smarty->_cache["resource_handlers"][$type];
        }
        if (isset(self::$sysplugins[$type])) {
            $_resource_class = "Smarty_Internal_Resource_" . ucfirst($type);
            $smarty->_cache["resource_handlers"][$type] = new $_resource_class();
            return $smarty->_cache["resource_handlers"][$type];
        }
        $_resource_class = "Smarty_Resource_" . ucfirst($type);
        if ($smarty->loadPlugin($_resource_class)) {
            if (class_exists($_resource_class, false)) {
                $smarty->_cache["resource_handlers"][$type] = new $_resource_class();
                return $smarty->_cache["resource_handlers"][$type];
            }
            $smarty->registerResource($type, array("smarty_resource_" . $type . "_source", "smarty_resource_" . $type . "_timestamp", "smarty_resource_" . $type . "_secure", "smarty_resource_" . $type . "_trusted"));
            return self::load($smarty, $type);
        }
        $_known_stream = stream_get_wrappers();
        if (in_array($type, $_known_stream)) {
            if (is_object($smarty->security_policy)) {
                $smarty->security_policy->isTrustedStream($type);
            }
            $smarty->_cache["resource_handlers"][$type] = new Smarty_Internal_Resource_Stream();
            return $smarty->_cache["resource_handlers"][$type];
        }
        throw new SmartyException("Unknown resource type '" . $type . "'");
    }
    /**
     * extract resource_type and resource_name from template_resource and config_resource
     * @note "C:/foo.tpl" was forced to file resource up till Smarty 3.1.3 (including).
     *
     * @param  string $resource_name    template_resource or config_resource to parse
     * @param  string $default_resource the default resource_type defined in $smarty
     *
     * @return array with parsed resource name and type
     */
    public static function parseResourceName($resource_name, $default_resource)
    {
        if (preg_match("/^([A-Za-z0-9_\\-]{2,})[:]/", $resource_name, $match)) {
            $type = $match[1];
            $name = substr($resource_name, strlen($match[0]));
        } else {
            $type = $default_resource;
            $name = $resource_name;
        }
        return array($name, $type);
    }
    /**
     * modify template_resource according to resource handlers specifications
     *
     * @param  \Smarty_Internal_Template|\Smarty $obj               Smarty instance
     * @param  string                            $template_resource template_resource to extract resource handler and name of
     *
     * @return string unique resource name
     * @throws \SmartyException
     */
    public static function getUniqueTemplateName($obj, $template_resource)
    {
        $smarty = $obj->_getSmartyObj();
        list($name, $type) = self::parseResourceName($template_resource, $smarty->default_resource_type);
        $resource = Smarty_Resource::load($smarty, $type);
        $_file_is_dotted = $name[0] === "." && ($name[1] === "." || $name[1] === "/");
        if ($obj->_isTplObj() && $_file_is_dotted && ($obj->source->type === "file" || $obj->parent->source->type === "extends")) {
            $name = $smarty->_realpath(dirname($obj->parent->source->filepath) . DIRECTORY_SEPARATOR . $name);
        }
        return $resource->buildUniqueResourceName($smarty, $name);
    }
    /**
     * initialize Source Object for given resource
     * wrapper for backward compatibility to versions < 3.1.22
     * Either [$_template] or [$smarty, $template_resource] must be specified
     *
     * @param  Smarty_Internal_Template $_template         template object
     * @param  Smarty                   $smarty            smarty object
     * @param  string                   $template_resource resource identifier
     *
     * @return \Smarty_Template_Source Source Object
     * @throws \SmartyException
     */
    public static function source(Smarty_Internal_Template $_template = NULL, Smarty $smarty = NULL, $template_resource = NULL)
    {
        return Smarty_Template_Source::load($_template, $smarty, $template_resource);
    }
    /**
     * Load template's source into current template object
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 template source
     * @throws SmartyException        if source cannot be loaded
     */
    public abstract function getContent(Smarty_Template_Source $source);
    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     */
    public abstract function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template);
    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
    }
    /**
     * modify resource_name according to resource handlers specifications
     *
     * @param  Smarty  $smarty        Smarty instance
     * @param  string  $resource_name resource_name to make unique
     * @param  boolean $isConfig      flag for config resource
     *
     * @return string unique resource name
     */
    public function buildUniqueResourceName(Smarty $smarty, $resource_name, $isConfig = false)
    {
        if ($isConfig) {
            if (!isset($smarty->_joined_config_dir)) {
                $smarty->getTemplateDir(NULL, true);
            }
            return get_class($this) . "#" . $smarty->_joined_config_dir . "#" . $resource_name;
        }
        if (!isset($smarty->_joined_template_dir)) {
            $smarty->getTemplateDir();
        }
        return get_class($this) . "#" . $smarty->_joined_template_dir . "#" . $resource_name;
    }
    /**
     * Determine basename for compiled filename
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 resource's basename
     */
    public function getBasename(Smarty_Template_Source $source)
    {
        return basename(preg_replace("![^\\w]+!", "_", $source->name));
    }
    /**
     * @return bool
     */
    public function checkTimestamps()
    {
        return true;
    }
}

?>