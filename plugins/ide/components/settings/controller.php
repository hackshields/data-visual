<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
require_once "class.settings.php";
if (!isset($_GET["action"])) {
    exit(formatJSEND("error", "Missing parameter"));
}
checkSession();
$Settings = new Settings();
if ($_GET["action"] == "save") {
    if (!isset($_POST["settings"])) {
        exit(formatJSEND("error", "Missing settings"));
    }
    $Settings->username = $_SESSION["user"];
    $Settings->settings = json_decode($_POST["settings"], true);
    $Settings->Save();
}
if ($_GET["action"] == "load") {
    $Settings->username = $_SESSION["user"];
    $Settings->Load();
}

?>