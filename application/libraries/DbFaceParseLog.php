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
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require_once "parse/autoload.php";
class DbFaceParseLog
{
    /**
     * Application Id
     * @var string
     */
    public static $appId = "com.playstrap.pk";
    /**
     * Rest API Key
     * @var string
     */
    public static $restKey = "";
    /**
     * Master Key
     * @var string
     */
    public static $masterKey = "";
    /**
     * Account Key (for parse.com)
     * @var string
     */
    public static $accountKey = "";
    public function __construct()
    {
    }
    public function save_object($clsName, $params = array())
    {
    }
    public function get_one_object($clsName, $filters = array())
    {
        return false;
    }
}

?>