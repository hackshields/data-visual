<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
require_once "class.user.php";
if (!isset($_GET["action"])) {
    exit(formatJSEND("error", "Missing parameter"));
}
if ($_GET["action"] != "authenticate") {
    checkSession();
}
$User = new User();
if ($_GET["action"] == "authenticate") {
    if (!isset($_POST["username"]) || !isset($_POST["password"])) {
        exit(formatJSEND("error", "Missing username or password"));
    }
    $User->username = $_POST["username"];
    $User->password = $_POST["password"];
    require_once "../../languages/code.php";
    if (isset($languages[$_POST["language"]])) {
        $User->lang = $_POST["language"];
    } else {
        $User->lang = "en";
    }
    $User->theme = $_POST["theme"];
    $User->Authenticate();
}
if ($_GET["action"] == "logout") {
    session_unset();
    session_destroy();
}
if ($_GET["action"] == "create" && checkAccess()) {
    if (!isset($_POST["username"]) || !isset($_POST["password"])) {
        exit(formatJSEND("error", "Missing username or password"));
    }
    $User->username = User::CleanUsername($_POST["username"]);
    $User->password = $_POST["password"];
    $User->Create();
}
if ($_GET["action"] == "delete" && checkAccess()) {
    if (!isset($_GET["username"])) {
        exit(formatJSEND("error", "Missing username"));
    }
    $User->username = $_GET["username"];
    $User->Delete();
}
if ($_GET["action"] == "project_access" && checkAccess()) {
    if (!isset($_GET["username"])) {
        exit(formatJSEND("error", "Missing username"));
    }
    $User->username = $_GET["username"];
    if (isset($_POST["projects"])) {
        $User->projects = $_POST["projects"];
    } else {
        $User->projects = array();
    }
    $User->Project_Access();
}
if ($_GET["action"] == "password") {
    if (!isset($_POST["username"]) || !isset($_POST["password"])) {
        exit(formatJSEND("error", "Missing username or password"));
    }
    if (checkAccess() || $_POST["username"] == $_SESSION["user"]) {
        $User->username = $_POST["username"];
        $User->password = $_POST["password"];
        $User->Password();
    }
}
if ($_GET["action"] == "project") {
    if (!isset($_GET["project"])) {
        exit(formatJSEND("error", "Missing project"));
    }
    $User->username = $_SESSION["user"];
    $User->project = $_GET["project"];
    $User->Project();
}
if ($_GET["action"] == "verify") {
    $User->username = $_SESSION["user"];
    $User->Verify();
}

?>