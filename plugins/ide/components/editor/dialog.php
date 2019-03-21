<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
checkSession();
echo "<form onsubmit=\"return false;\">\r\n";
switch ($_GET["action"]) {
    case "search":
        $type = $_GET["type"];
        echo "    <label>";
        i18n("Find:");
        echo "</label>\r\n    <input type=\"text\" name=\"find\" autofocus=\"autofocus\" autocomplete=\"off\">\r\n    \r\n    ";
        if ($type == "replace") {
            echo "\r\n    <label>";
            i18n("Replace:");
            echo "</label>\r\n    <input type=\"text\" name=\"replace\">\r\n    \r\n    ";
        }
        echo "\r\n    <button class=\"btn-left\" onclick=\"codiad.editor.search('find');return false;\">";
        i18n("Find");
        echo "</button>\r\n    ";
        if ($type == "replace") {
            echo "        <button class=\"btn-mid\" onclick=\"codiad.editor.search('replace');return false;\">";
            i18n("Replace");
            echo "</button>\r\n        <button class=\"btn-mid\" onclick=\"codiad.editor.search('replaceAll');return false;\">";
            i18n("Replace ALL");
            echo "</button>\r\n    ";
        }
        echo "    <button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
}
echo "</form>\r\n<script>\r\n\$(function(){\r\n    ";
if ($_GET["action"] == "search") {
    echo "    \$('input[name=\"find\"]').val(codiad.active.getSelectedText());\r\n    ";
}
echo "});\r\n\r\n</script>\r\n";

?>