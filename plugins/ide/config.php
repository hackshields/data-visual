<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

if (!defined("SELF")) {
    define("SELF", pathinfo(__FILE__, PATHINFO_BASENAME));
}
if (!defined("BASE_PATH")) {
    define("BASE_PATH", str_replace(SELF, "", __FILE__));
}
define("BASE_URL", "");
define("THEME", "default");
if (!defined("DATA")) {
    $user_file_dir = realpath(BASE_PATH . "/../../user/files/" . $_SESSION["userid"] . "/ide");
    if (!file_exists($user_file_dir)) {
        mkdir($user_file_dir, 511, true);
    }
    define("DATA", $user_file_dir);
}
define("COMPONENTS", BASE_PATH . "/components");
define("PLUGINS", BASE_PATH . "/plugins");
define("THEMES", BASE_PATH . "/themes");
if (!defined("WORKSPACE")) {
    define("WORKSPACE", realpath(BASE_PATH . "/../../user/files"));
}
define("WSURL", BASE_URL . "/workspace");
define("LANGUAGE", "en");

?>