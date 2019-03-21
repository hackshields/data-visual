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
 * Smarty Extension Loadplugin
 *
 * $smarty->loadPlugin() method
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 */
class Smarty_Internal_Method_LoadPlugin
{
    /**
     * Cache of searched plugin files
     *
     * @var array
     */
    public $plugin_files = array();
    /**
     * Takes unknown classes and loads plugin files for them
     * class name format: Smarty_PluginType_PluginName
     * plugin filename format: plugintype.pluginname.php
     *
     * @param \Smarty $smarty
     * @param  string $plugin_name class plugin name to load
     * @param  bool   $check       check if already loaded
     *
     * @return bool|string
     * @throws \SmartyException
     */
    public function loadPlugin(Smarty $smarty, $plugin_name, $check)
    {
        if ($check && (is_callable($plugin_name) || class_exists($plugin_name, false))) {
            return true;
        }
        if (!preg_match("#^smarty_((internal)|([^_]+))_(.+)\$#i", $plugin_name, $match)) {
            throw new SmartyException("plugin " . $plugin_name . " is not a valid name format");
        }
        if (!empty($match[2])) {
            $file = SMARTY_SYSPLUGINS_DIR . strtolower($plugin_name) . ".php";
            if (isset($this->plugin_files[$file])) {
                if ($this->plugin_files[$file] !== false) {
                    return $this->plugin_files[$file];
                }
                return false;
            }
            if (is_file($file)) {
                $this->plugin_files[$file] = $file;
                require_once $file;
                return $file;
            }
            $this->plugin_files[$file] = false;
            return false;
        }
        $_plugin_filename = (string) $match[1] . "." . $match[4] . ".php";
        $_lower_filename = strtolower($_plugin_filename);
        if (isset($this->plugin_files)) {
            if (isset($this->plugin_files["plugins_dir"][$_lower_filename]) && (!$smarty->use_include_path || $this->plugin_files["plugins_dir"][$_lower_filename] !== false)) {
                return $this->plugin_files["plugins_dir"][$_lower_filename];
            }
            if (!$smarty->use_include_path || $smarty->ext->_getIncludePath->isNewIncludePath($smarty)) {
                unset($this->plugin_files["include_path"]);
            } else {
                if (isset($this->plugin_files["include_path"][$_lower_filename])) {
                    return $this->plugin_files["include_path"][$_lower_filename];
                }
            }
        }
        $_file_names = array($_plugin_filename);
        if ($_lower_filename !== $_plugin_filename) {
            $_file_names[] = $_lower_filename;
        }
        $_p_dirs = $smarty->getPluginsDir();
        if (!isset($this->plugin_files["plugins_dir"][$_lower_filename])) {
            foreach ($_p_dirs as $_plugin_dir) {
                foreach ($_file_names as $name) {
                    $file = $_plugin_dir . $name;
                    if (is_file($file)) {
                        $this->plugin_files["plugins_dir"][$_lower_filename] = $file;
                        require_once $file;
                        return $file;
                    }
                    $this->plugin_files["plugins_dir"][$_lower_filename] = false;
                }
            }
        }
        if ($smarty->use_include_path) {
            foreach ($_file_names as $_file_name) {
                $file = $smarty->ext->_getIncludePath->getIncludePath($_p_dirs, $_file_name, $smarty);
                $this->plugin_files["include_path"][$_lower_filename] = $file;
                if ($file !== false) {
                    require_once $file;
                    return $file;
                }
            }
        }
        return false;
    }
}

?>