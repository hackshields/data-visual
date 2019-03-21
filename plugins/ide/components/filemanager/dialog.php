<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
require_once "class.filemanager.php";
checkSession();
echo "<form>\r\n";
switch ($_GET["action"]) {
    case "create":
        echo "    <input type=\"hidden\" name=\"path\" value=\"";
        echo $_GET["path"];
        echo "\">\r\n    <input type=\"hidden\" name=\"type\" value=\"";
        echo $_GET["type"];
        echo "\">\r\n    <label><span class=\"icon-pencil\"></span>";
        echo i18n(ucfirst($_GET["type"]));
        echo "</label>\r\n    <input type=\"text\" name=\"object_name\" autofocus=\"autofocus\" autocomplete=\"off\">\r\n    <button class=\"btn-left\">";
        i18n("Create");
        echo "</button>\r\n    <button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
    case "rename":
        echo "    <input type=\"hidden\" name=\"path\" value=\"";
        echo $_GET["path"];
        echo "\">\r\n    <input type=\"hidden\" name=\"type\" value=\"";
        echo $_GET["type"];
        echo "\">\r\n    <label><span class=\"icon-pencil\"></span> ";
        i18n("Rename");
        echo " ";
        echo i18n(ucfirst($_GET["type"]));
        echo "</label>\r\n    <input type=\"text\" name=\"object_name\" autofocus=\"autofocus\" autocomplete=\"off\" value=\"";
        echo $_GET["short_name"];
        echo "\">\r\n    <button class=\"btn-left\">";
        i18n("Rename");
        echo "</button>\r\n\t<button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
    case "delete":
        echo "    <input type=\"hidden\" name=\"path\" value=\"";
        echo $_GET["path"];
        echo "\">\r\n    <label>";
        i18n("Are you sure you wish to delete the following:");
        echo "</label>\r\n    <pre>";
        if (!FileManager::isAbsPath($_GET["path"])) {
            echo "/";
        }
        echo $_GET["path"];
        echo "</pre>\r\n    <button class=\"btn-left\">";
        i18n("Delete");
        echo "</button>\r\n\t<button class=\"btn-right\" onclick=\"codiad.modal.unload();return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
    case "preview":
        echo "    <label>";
        i18n("Inline Preview");
        echo "</label>\r\n    <div><br><br><img src=\"";
        echo $_GET["path"];
        echo "\"><br><br></div>\r\n    <button class=\"btn-right\" onclick=\"codiad.modal.unload();return false;\">";
        i18n("Close");
        echo "</button>\r\n    ";
        break;
    case "overwrite":
        echo "    <input type=\"hidden\" name=\"path\" value=\"";
        echo $_GET["path"];
        echo "\">\r\n    <label>";
        i18n("Would you like to overwrite or duplicate the following:");
        echo "</label>\r\n    <pre>";
        if (!FileManager::isAbsPath($_GET["path"])) {
            echo "/";
        }
        echo $_GET["path"];
        echo "</pre>\r\n    <select name=\"or_action\">\r\n        <option value=\"0\">";
        i18n("Overwrite Original");
        echo "</option>\r\n        <option value=\"1\">";
        i18n("Create Duplicate");
        echo "</option>\r\n    </select>\r\n    <button class=\"btn-left\">";
        i18n("Continue");
        echo "</button>\r\n\t<button class=\"btn-right\" onclick=\"codiad.modal.unload();return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
    case "search":
        echo "    <input type=\"hidden\" name=\"path\" value=\"";
        echo $_GET["path"];
        echo "\">\r\n    <table class=\"file-search-table\">\r\n        <tr>\r\n            <td width=\"65%\">\r\n               <label>";
        i18n("Search Files:");
        echo "</label>\r\n               <input type=\"text\" name=\"search_string\" autofocus=\"autofocus\">\r\n            </td>\r\n            <td width=\"5%\">&nbsp;&nbsp;</td>\r\n            <td>\r\n                <label>";
        i18n("In:");
        echo "</label>\r\n                <select name=\"search_type\">\r\n                    <option value=\"0\">";
        i18n("Current Project");
        echo "</option>\r\n                    ";
        if (checkAccess()) {
            echo "                    <option value=\"1\">";
            i18n("Workspace Projects");
            echo "</option>\r\n                    ";
        }
        echo "                </select>\r\n            </td>\r\n        </tr>\r\n        <tr>\r\n            <td colspan=\"3\">\r\n               <label>";
        i18n("File Type:");
        echo "</label>\r\n               <input type=\"text\" name=\"search_file_type\" placeholder=\"";
        i18n("space seperated file types eg: js c php");
        echo "\">\r\n            </td>\r\n        </tr>\r\n    </table>\r\n    <pre id=\"filemanager-search-results\"></pre>\r\n    <div id=\"filemanager-search-processing\"></div>\r\n    <button class=\"btn-left\">";
        i18n("Search");
        echo "</button>\r\n\t<button class=\"btn-right\" onclick=\"codiad.modal.unload();return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
}
echo "</form>\r\n";

?>