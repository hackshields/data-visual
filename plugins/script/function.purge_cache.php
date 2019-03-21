<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/**
 * Smarty plugin
 *
 * @package    Smarty
 * @subpackage PluginsFunction
 */
/**
 * {source function=value}
 *
 * call cloud function
 *
 * @param array $params parameters
 *
 * @return string
 */
function smarty_function_purge_cache($params, $template)
{
    $CI =& get_instance();
    $creatorid = $CI->session->userdata('login_creatorid');
    if (empty($creatorid)) {
        return "";
    }
    // remove all cached files
    $cached_dir = USERPATH . 'files' . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    if (file_exists($cached_dir)) {
        $di = new RecursiveDirectoryIterator($cached_dir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }
    }
    $CI->db->delete('dc_cache', array('creatorid' => $creatorid));
    dbface_log("info", "purge_cache invoked from smarty plugin");
    return "";
}

?>