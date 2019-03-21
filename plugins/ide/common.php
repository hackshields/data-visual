<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

Common::startSession();
class Common
{
    public static $debugMessageStack = array();
    public static function construct()
    {
        global $cookie_lifetime;
        $path = str_replace("index.php", "", $_SERVER["SCRIPT_FILENAME"]);
        foreach (array("components", "plugins") as $folder) {
            if (strpos($_SERVER["SCRIPT_FILENAME"], $folder)) {
                $path = substr($_SERVER["SCRIPT_FILENAME"], 0, strpos($_SERVER["SCRIPT_FILENAME"], $folder));
                break;
            }
        }
        require "config.php";
    }
    public static function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        Common::construct();
        if (defined("AUTH_PATH")) {
            require_once AUTH_PATH;
        }
        global $lang;
        if (isset($_SESSION["lang"])) {
            include BASE_PATH . "/languages/" . $_SESSION["lang"] . ".php";
        } else {
            include BASE_PATH . "/languages/" . LANGUAGE . ".php";
        }
    }
    public static function readDirectory($foldername)
    {
        $tmp = array();
        $allFiles = scandir($foldername);
        foreach ($allFiles as $fname) {
            if ($fname == "." || $fname == "..") {
                continue;
            }
            if (is_dir($foldername . "/" . $fname)) {
                $tmp[] = $fname;
            }
        }
        return $tmp;
    }
    public static function debug($message)
    {
        Common::$debugMessageStack[] = $message;
    }
    public static function getConstant($key, $default = NULL)
    {
        return defined($key) ? constant($key) : $default;
    }
    public static function i18n($key, $args = array())
    {
        echo Common::get_i18n($key, $args);
    }
    public static function get_i18n($key, $args = array())
    {
        global $lang;
        $key = ucwords(strtolower($key));
        $return = isset($lang[$key]) ? $lang[$key] : $key;
        foreach ($args as $k => $v) {
            $return = str_replace("%{" . $k . "}%", $v, $return);
        }
        return $return;
    }
    public static function checkSession()
    {
        $api_keys = array();
        $key = "";
        if (isset($_GET["key"])) {
            $key = $_GET["key"];
        }
        if (!isset($_SESSION["user"]) && !in_array($key, $api_keys)) {
            exit("{\"status\":\"error\",\"message\":\"Authentication Error\"}");
        }
    }
    public static function getJSON($file, $namespace = "")
    {
        $path = DATA . "/";
        if (file_exists($path)) {
            $json = file_get_contents($path . $file);
            $json = str_replace(array("\n\r", "\r", "\n"), "", $json);
            $json = str_replace("|*/?>", "", str_replace("<?php/*|", "", $json));
            $json = json_decode($json, true);
            return $json;
        }
        return "";
    }
    public static function saveJSON($file, $data, $namespace = "")
    {
        $path = DATA . "/";
        $data = "<?php\r\n/*|" . json_encode($data) . "|*/\r\n?>";
        $write = fopen($path . $file, "w") or exit("can't open file " . $path . $file);
        fwrite($write, $data);
        fclose($write);
    }
    public static function formatJSEND($status, $data = false)
    {
        $debug = "";
        if (0 < count(Common::$debugMessageStack)) {
            $debug .= ",\"debug\":";
            $debug .= json_encode(Common::$debugMessageStack);
        }
        if ($status == "success") {
            if ($data) {
                $jsend = "{\"status\":\"success\",\"data\":" . json_encode($data) . $debug . "}";
            } else {
                $jsend = "{\"status\":\"success\",\"data\":null" . $debug . "}";
            }
        } else {
            $jsend = "{\"status\":\"error\",\"message\":\"" . $data . "\"" . $debug . "}";
        }
        return $jsend;
    }
    public static function checkAccess()
    {
        return !file_exists(DATA . "/" . $_SESSION["user"] . "_acl.php");
    }
    public static function checkPath($path)
    {
        return true;
    }
    public static function isAvailable($func)
    {
        if (ini_get("safe_mode")) {
            return false;
        }
        $disabled = ini_get("disable_functions");
        if ($disabled) {
            $disabled = explode(",", $disabled);
            $disabled = array_map("trim", $disabled);
            return !in_array($func, $disabled);
        }
        return true;
    }
    public static function isAbsPath($path)
    {
        return true;
    }
    public static function isWINOS()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === "WIN";
    }
}
function debug($message)
{
    Common::debug($message);
}
function i18n($key, $args = array())
{
    echo Common::i18n($key, $args);
}
function get_i18n($key, $args = array())
{
    return Common::get_i18n($key, $args);
}
function checkSession()
{
    Common::checkSession();
}
function getJSON($file, $namespace = "")
{
    return Common::getJSON($file, $namespace);
}
function saveJSON($file, $data, $namespace = "")
{
    Common::saveJSON($file, $data, $namespace);
}
function formatJSEND($status, $data = false)
{
    return Common::formatJSEND($status, $data);
}
function checkAccess()
{
    return Common::checkAccess();
}
function checkPath($path)
{
    return Common::checkPath($path);
}
function isAvailable($func)
{
    return Common::isAvailable($func);
}

?>