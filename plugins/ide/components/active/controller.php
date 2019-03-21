<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
require_once "class.active.php";
$Active = new Active();
checkSession();
if ($_GET["action"] == "list") {
    $Active->username = $_SESSION["user"];
    $Active->ListActive();
}
if ($_GET["action"] == "add") {
    $Active->username = $_SESSION["user"];
    $Active->path = $_GET["path"];
    $Active->Add();
}
if ($_GET["action"] == "rename") {
    $Active->username = $_SESSION["user"];
    $Active->path = $_GET["old_path"];
    $Active->new_path = $_GET["new_path"];
    $Active->Rename();
}
if ($_GET["action"] == "check") {
    $Active->username = $_SESSION["user"];
    $Active->path = $_GET["path"];
    $Active->Check();
}
if ($_GET["action"] == "remove") {
    $Active->username = $_SESSION["user"];
    $Active->path = $_GET["path"];
    $Active->Remove();
}
if ($_GET["action"] == "removeall") {
    $Active->username = $_SESSION["user"];
    $Active->RemoveAll();
}
if ($_GET["action"] == "focused") {
    $Active->username = $_SESSION["user"];
    $Active->path = $_GET["path"];
    $Active->MarkFileAsFocused();
}

?>