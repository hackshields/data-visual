<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
require_once "class.project.php";
checkSession();
$Project = new Project();
$no_return = false;
if (isset($_GET["no_return"])) {
    $no_return = true;
}
if ($_GET["action"] == "get_current") {
    if (!isset($_SESSION["project"])) {
        if ($no_return) {
            $Project->no_return = true;
        }
        $Project->GetFirst();
    } else {
        $Project->path = $_SESSION["project"];
        $project_name = $Project->GetName();
        if (!$no_return) {
            echo formatJSEND("success", array("name" => $project_name, "path" => $_SESSION["project"]));
        }
    }
}
if ($_GET["action"] == "open") {
    if (!checkPath($_GET["path"])) {
        exit(formatJSEND("error", "No Access"));
    }
    $Project->path = $_GET["path"];
    $Project->Open();
}
if ($_GET["action"] == "create" && checkAccess()) {
    $Project->name = $_GET["project_name"];
    if ($_GET["project_path"] != "") {
        $Project->path = $_GET["project_path"];
    } else {
        $Project->path = $_GET["project_name"];
    }
    if (!empty($_GET["git_repo"])) {
        $Project->gitrepo = $_GET["git_repo"];
        $Project->gitbranch = $_GET["git_branch"];
    }
    $Project->Create();
}
if ($_GET["action"] == "rename") {
    if (!checkPath($_GET["project_path"])) {
        exit(formatJSEND("error", "No Access"));
    }
    $Project->path = $_GET["project_path"];
    $Project->Rename();
}
if ($_GET["action"] == "delete" && checkAccess()) {
    $Project->path = $_GET["project_path"];
    $Project->Delete();
}
if ($_GET["action"] == "current") {
    if (isset($_SESSION["project"])) {
        echo formatJSEND("success", $_SESSION["project"]);
    } else {
        echo formatJSEND("error", "No Project Returned");
    }
}

?>