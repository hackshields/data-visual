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
 * Inline Runtime Methods render, setSourceByUid, setupSubTemplate
 *
 * @package    Smarty
 * @subpackage PluginsInternal
 * @author     Uwe Tews
 *
 **/
class Smarty_Internal_Runtime_CacheModify
{
    /**
     * check client side cache
     *
     * @param \Smarty_Template_Cached   $cached
     * @param \Smarty_Internal_Template $_template
     * @param  string                   $content
     *
     * @throws \Exception
     * @throws \SmartyException
     */
    public function cacheModifiedCheck(Smarty_Template_Cached $cached, Smarty_Internal_Template $_template, $content)
    {
        $_isCached = $_template->isCached() && !$_template->compiled->has_nocache_code;
        $_last_modified_date = @substr($_SERVER["HTTP_IF_MODIFIED_SINCE"], 0, @strpos($_SERVER["HTTP_IF_MODIFIED_SINCE"], "GMT") + 3);
        if ($_isCached && $cached->timestamp <= strtotime($_last_modified_date)) {
            switch (PHP_SAPI) {
                case "cgi":
                case "cgi-fcgi":
                case "fpm-fcgi":
                    header("Status: 304 Not Modified");
                    break;
                case "cli":
                    if (!empty($_SERVER["SMARTY_PHPUNIT_DISABLE_HEADERS"])) {
                        $_SERVER["SMARTY_PHPUNIT_HEADERS"][] = "304 Not Modified";
                    }
                    break;
                default:
                    if (!empty($_SERVER["SMARTY_PHPUNIT_DISABLE_HEADERS"])) {
                        $_SERVER["SMARTY_PHPUNIT_HEADERS"][] = "304 Not Modified";
                    } else {
                        header($_SERVER["SERVER_PROTOCOL"] . " 304 Not Modified");
                    }
                    break;
            }
        } else {
            switch (PHP_SAPI) {
                case "cli":
                    if (!empty($_SERVER["SMARTY_PHPUNIT_DISABLE_HEADERS"])) {
                        $_SERVER["SMARTY_PHPUNIT_HEADERS"][] = "Last-Modified: " . gmdate("D, d M Y H:i:s", $cached->timestamp) . " GMT";
                    }
                    break;
                default:
                    header("Last-Modified: " . gmdate("D, d M Y H:i:s", $cached->timestamp) . " GMT");
                    break;
            }
            echo $content;
        }
    }
}

?>