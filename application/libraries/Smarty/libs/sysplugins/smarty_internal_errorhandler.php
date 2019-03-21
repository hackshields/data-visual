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
* Smarty error handler
*
*
* @package    Smarty
* @subpackage PluginsInternal
* @author     Uwe Tews
*
* @deprecated
Smarty does no longer use @filemtime()
*/
class Smarty_Internal_ErrorHandler
{
    /**
     * contains directories outside of SMARTY_DIR that are to be muted by muteExpectedErrors()
     */
    public static $mutedDirectories = array();
    /**
     * error handler returned by set_error_handler() in self::muteExpectedErrors()
     */
    private static $previousErrorHandler = NULL;
    /**
     * Enable error handler to mute expected messages
     *
     * @return boolean
     */
    public static function muteExpectedErrors()
    {
        $error_handler = array("Smarty_Internal_ErrorHandler", "mutingErrorHandler");
        $previous = set_error_handler($error_handler);
        if ($previous !== $error_handler) {
            self::$previousErrorHandler = $previous;
        }
    }
    /**
     * Error Handler to mute expected messages
     *
     * @link http://php.net/set_error_handler
     *
     * @param  integer $errno Error level
     * @param          $errstr
     * @param          $errfile
     * @param          $errline
     * @param          $errcontext
     *
     * @return bool
     */
    public static function mutingErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        $_is_muted_directory = false;
        if (!isset(self::$mutedDirectories[SMARTY_DIR])) {
            $smarty_dir = realpath(SMARTY_DIR);
            if ($smarty_dir !== false) {
                self::$mutedDirectories[SMARTY_DIR] = array("file" => $smarty_dir, "length" => strlen($smarty_dir));
            }
        }
        foreach (self::$mutedDirectories as $key => &$dir) {
            if (!$dir) {
                $file = realpath($key);
                if ($file === false) {
                    unset(self::$mutedDirectories[$key]);
                    continue;
                }
                $dir = array("file" => $file, "length" => strlen($file));
            }
            if (!strncmp($errfile, $dir["file"], $dir["length"])) {
                $_is_muted_directory = true;
                break;
            }
        }
        if (!$_is_muted_directory || $errno && $errno & error_reporting()) {
            if (self::$previousErrorHandler) {
                return call_user_func(self::$previousErrorHandler, $errno, $errstr, $errfile, $errline, $errcontext);
            }
            return false;
        }
    }
}

?>