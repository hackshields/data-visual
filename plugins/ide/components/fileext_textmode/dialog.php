<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "class.fileextension_textmode.php";
$fileExTM = new fileextension_textmode();
if (!isset($_GET["action"])) {
    exit("Missing \$_GET[\"action\"]");
}
switch ($_GET["action"]) {
    case "fileextension_textmode_form":
        if (!Common::checkAccess()) {
            exit("You are not allowed to edit the file extensions.");
        }
        $ext = false;
        $ext = @Common::getJSON(fileextension_textmode::storeFilename);
        if (!is_array($ext)) {
            $ext = $fileExTM->getDefaultExtensions();
        }
        $textModes = $fileExTM->getAvailiableTextModes();
        if (!@ksort($ext)) {
            exit(json_encode(array("status" => "error", "msg" => "Internal PHP error.")));
        }
        echo "        <label><span class=\"icon-pencil big-icon\"></span>";
        i18n("Extensions");
        echo "</label>\r\n        <table id=\"FileExtModeHeader\">\r\n            <thead>\r\n\t\t\t\t<tr>\r\n\t\t\t\t\t<th>";
        i18n("Extension");
        echo "</th>\r\n\t\t\t\t\t<th>";
        i18n("Mode");
        echo "</th>\r\n                </tr>\r\n\t\t\t</thead>\r\n        </table>\r\n\t\t<div id=\"FileExtTextModeDiv\">\r\n\t\t\t<table id=\"FileExtTextModeTable\">\r\n\t\t\t\t<tbody id=\"FileExtTextModeTableTbody\">\r\n\t\t\t\t";
        foreach ($ext as $ex => $mode) {
            if (!$fileExTM->validTextMode($mode)) {
                continue;
            }
            echo "\t\t\t\t\t<tr>\r\n\t\t\t\t\t\t<td><input class=\"FileExtension\" type=\"text\" name=\"extension[]\" value=\"";
            echo $ex;
            echo "\" /></td>\r\n\t\t\t\t\t\t<td>";
            echo $fileExTM->getTextModeSelect($mode);
            echo "</td>\r\n\t\t\t\t\t</tr>\r\n\t\t\t\t";
        }
        echo "\t\t\t\t</tbody>\r\n\t\t\t</table>\r\n\t\t</div>\r\n\t\t<br>\r\n\t\t<button class=\"btn-left\" onClick=\"codiad.fileext_textmode.addFieldToForm()\">";
        i18n("New Extension");
        echo "</button>\r\n";
        break;
}

?>