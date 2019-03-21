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
 * Smarty Internal Write File Class
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 */
class Smarty_Internal_Runtime_WriteFile
{
    /**
     * Writes file in a safe way to disk
     *
     * @param  string $_filepath complete filepath
     * @param  string $_contents file content
     * @param  Smarty $smarty    smarty instance
     *
     * @throws SmartyException
     * @return boolean true
     */
    public function writeFile($_filepath, $_contents, Smarty $smarty)
    {
        $_error_reporting = error_reporting();
        error_reporting($_error_reporting & ~8 & ~2);
        $_file_perms = property_exists($smarty, "_file_perms") ? $smarty->_file_perms : 420;
        $_dir_perms = property_exists($smarty, "_dir_perms") ? isset($smarty->_dir_perms) ? $smarty->_dir_perms : 511 : 505;
        if ($_file_perms !== NULL) {
            $old_umask = umask(0);
        }
        $_dirpath = dirname($_filepath);
        if ($_dirpath !== ".") {
            $i = 0;
            while (!is_dir($_dirpath)) {
                if (@mkdir($_dirpath, $_dir_perms, true)) {
                    break;
                }
                clearstatcache();
                if (++$i === 3) {
                    error_reporting($_error_reporting);
                    throw new SmartyException("unable to create directory " . $_dirpath);
                }
                sleep(1);
            }
        }
        $_tmp_file = $_dirpath . DIRECTORY_SEPARATOR . str_replace(array(".", ","), "_", uniqid("wrt", true));
        if (!file_put_contents($_tmp_file, $_contents)) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file " . $_tmp_file);
        }
        if (Smarty::$_IS_WINDOWS) {
            if (is_file($_filepath)) {
                @unlink($_filepath);
            }
            $success = @rename($_tmp_file, $_filepath);
        } else {
            $success = @rename($_tmp_file, $_filepath);
            if (!$success) {
                if (is_file($_filepath)) {
                    @unlink($_filepath);
                }
                $success = @rename($_tmp_file, $_filepath);
            }
        }
        if (!$success) {
            error_reporting($_error_reporting);
            throw new SmartyException("unable to write file " . $_filepath);
        }
        if ($_file_perms !== NULL) {
            chmod($_filepath, $_file_perms);
            umask($old_umask);
        }
        error_reporting($_error_reporting);
        return true;
    }
}

?>