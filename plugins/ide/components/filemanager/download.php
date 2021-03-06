<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
checkSession();
if (!isset($_GET["path"]) || preg_match("#^[\\\\/]?\$#i", trim($_GET["path"])) || preg_match("#[\\:*?\\\"<>\\|]#i", $_GET["path"]) || 0 < substr_count($_GET["path"], "./")) {
    exit("<script>parent.codiad.message.error(\"Wrong data send\")</script>");
}
if (isset($_GET["type"]) && ($_GET["type"] == "directory" || $_GET["type"] == "root")) {
    $filename = explode("/", $_GET["path"]);
    $filename = array_pop($filename) . "-" . date("Y.m.d");
    $targetPath = DATA . "/";
    $dir = WORKSPACE . "/" . $_GET["path"];
    if (!is_dir($dir)) {
        exit("<script>parent.codiad.message.error(\"Directory not found.\")</script>");
    }
    if (isAvailable("system") && stripos(PHP_OS, "win") === false) {
        $filename .= ".tar.gz";
        system("tar -pczf " . escapeshellarg($targetPath . $filename) . " -C " . escapeshellarg(WORKSPACE) . " " . escapeshellarg($_GET["path"]));
        $download_file = $targetPath . $filename;
    } else {
        if (extension_loaded("zip")) {
            require_once "class.dirzip.php";
            $filename .= ".zip";
            $download_file = $targetPath . $filename;
            DirZip::zipDir($dir, $targetPath . $filename);
        } else {
            exit("<script>parent.codiad.message.error(\"Could not pack the folder, zip-extension missing\")</script>");
        }
    }
} else {
    $filename = explode("/", $_GET["path"]);
    $filename = array_pop($filename);
    $download_file = WORKSPACE . "/" . $_GET["path"];
}
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"" . basename($filename) . "\"");
header("Content-Transfer-Encoding: binary");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: " . filesize($download_file));
if (ob_get_contents()) {
    ob_end_clean();
}
flush();
readfile($download_file);
if ($_GET["type"] == "directory" || $_GET["type"] == "root") {
    unlink($download_file);
}

?>