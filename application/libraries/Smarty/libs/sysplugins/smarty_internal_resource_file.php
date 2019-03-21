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
 * Smarty Internal Plugin Resource File
 * Implements the file system as resource for Smarty templates
 *
 * @package    Smarty
 * @subpackage TemplateResources
 */
class Smarty_Internal_Resource_File extends Smarty_Resource
{
    /**
     * build template filepath by traversing the template_dir array
     *
     * @param Smarty_Template_Source    $source    source object
     * @param  Smarty_Internal_Template $_template template object
     *
     * @return string fully qualified filepath
     * @throws SmartyException
     */
    protected function buildFilepath(Smarty_Template_Source $source, Smarty_Internal_Template $_template = NULL)
    {
        $file = $source->name;
        if ($file[0] === "/" || $file[1] === ":") {
            $file = $source->smarty->_realpath($file, true);
            return is_file($file) ? $file : false;
        }
        if ($file[0] === "." && $_template && $_template->_isSubTpl() && preg_match("#^[.]{1,2}[\\\\/]#", $file)) {
            if ($_template->parent->source->type !== "file" && $_template->parent->source->type !== "extends" && !isset($_template->parent->_cache["allow_relative_path"])) {
                throw new SmartyException("Template '" . $file . "' cannot be relative to template of resource type '" . $_template->parent->source->type . "'");
            }
            $path = $source->smarty->_realpath(dirname($_template->parent->source->filepath) . DIRECTORY_SEPARATOR . $file);
            return is_file($path) ? $path : false;
        }
        if (strpos($file, DIRECTORY_SEPARATOR === "/" ? "\\" : "/") !== false) {
            $file = str_replace(DIRECTORY_SEPARATOR === "/" ? "\\" : "/", DIRECTORY_SEPARATOR, $file);
        }
        $_directories = $source->smarty->getTemplateDir(NULL, $source->isConfig);
        if ($file[0] === "[" && preg_match("#^\\[([^\\]]+)\\](.+)\$#", $file, $fileMatch)) {
            $file = $fileMatch[2];
            $_indices = explode(",", $fileMatch[1]);
            $_index_dirs = array();
            foreach ($_indices as $index) {
                $index = trim($index);
                if (isset($_directories[$index])) {
                    $_index_dirs[] = $_directories[$index];
                } else {
                    if (is_numeric($index)) {
                        $index = (int) $index;
                        if (isset($_directories[$index])) {
                            $_index_dirs[] = $_directories[$index];
                        } else {
                            $keys = array_keys($_directories);
                            if (isset($_directories[$keys[$index]])) {
                                $_index_dirs[] = $_directories[$keys[$index]];
                            }
                        }
                    }
                }
            }
            if (empty($_index_dirs)) {
                return false;
            }
            $_directories = $_index_dirs;
        }
        foreach ($_directories as $_directory) {
            $path = $_directory . $file;
            if (is_file($path)) {
                return strpos($path, "." . DIRECTORY_SEPARATOR) !== false ? $source->smarty->_realpath($path) : $path;
            }
        }
        if (!isset($_index_dirs)) {
            $path = $source->smarty->_realpath($file, true);
            if (is_file($path)) {
                return $path;
            }
        }
        if ($source->smarty->use_include_path) {
            return $source->smarty->ext->_getIncludePath->getIncludePath($_directories, $file, $source->smarty);
        }
        return false;
    }
    /**
     * populate Source Object with meta data from Resource
     *
     * @param Smarty_Template_Source   $source    source object
     * @param Smarty_Internal_Template $_template template object
     *
     * @throws \SmartyException
     */
    public function populate(Smarty_Template_Source $source, Smarty_Internal_Template $_template = NULL)
    {
        $source->filepath = $this->buildFilepath($source, $_template);
        if ($source->filepath !== false) {
            if (isset($source->smarty->security_policy) && is_object($source->smarty->security_policy)) {
                $source->smarty->security_policy->isTrustedResourceDir($source->filepath, $source->isConfig);
            }
            $source->exists = true;
            $source->uid = sha1($source->filepath . ($source->isConfig ? $source->smarty->_joined_config_dir : $source->smarty->_joined_template_dir));
            $source->timestamp = filemtime($source->filepath);
        } else {
            $source->timestamp = $source->exists = false;
        }
    }
    /**
     * populate Source Object with timestamp and exists from Resource
     *
     * @param Smarty_Template_Source $source source object
     */
    public function populateTimestamp(Smarty_Template_Source $source)
    {
        if (!$source->exists) {
            $source->timestamp = $source->exists = is_file($source->filepath);
        }
        if ($source->exists) {
            $source->timestamp = filemtime($source->filepath);
        }
    }
    /**
     * Load template's source from file into current template object
     *
     * @param  Smarty_Template_Source $source source object
     *
     * @return string                 template source
     * @throws SmartyException        if source cannot be loaded
     */
    public function getContent(Smarty_Template_Source $source)
    {
        if ($source->exists) {
            if (function_exists("ioncube_read_file")) {
                return ioncube_read_file($source->filepath);
            }
            return file_get_contents($source->filepath);
        }
        throw new SmartyException("Unable to read " . ($source->isConfig ? "config" : "template") . " " . $source->type . " '" . $source->name . "'");
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
        return basename($source->filepath);
    }
}

?>