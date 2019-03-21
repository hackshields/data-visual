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
if (version_compare(PHP_VERSION, "5.4.0", "<")) {
    throw new Exception("The Parse SDK requires PHP version 5.4 or higher.");
}
spl_autoload_register(function ($class) {
    $prefix = "Parse\\";
    $base_dir = defined("PARSE_SDK_DIR") ? PARSE_SDK_DIR : __DIR__ . "/src/Parse/";
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return NULL;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace("\\", "/", $relative_class) . ".php";
    if (file_exists($file)) {
        require $file;
    }
});

?>