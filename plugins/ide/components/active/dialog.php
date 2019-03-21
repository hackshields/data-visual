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
    case "confirm":
        $path = $_GET["path"];
        echo "    <label>";
        i18n("Close Unsaved File?");
        echo "</label>\r\n    \r\n    <pre>";
        echo $path;
        echo "</pre>\r\n\r\n    <button class=\"btn-left\" onclick=\"save_and_close('";
        echo $path;
        echo "'); return false;\">";
        i18n("Save & Close");
        echo "</button>\r\n    <button class=\"btn-mid\" onclick=\"close_without_save('";
        echo $path;
        echo "'); return false;\">";
        i18n("Discard Changes");
        echo "</button>\r\n    <button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
    case "confirmAll":
        echo "    <label>";
        i18n("Close Unsaved Files?");
        echo "</label>\r\n    \r\n    <button class=\"btn-left\" onclick=\"save_and_close_all(); return false;\">";
        i18n("Save & Close");
        echo "</button>\r\n    <button class=\"btn-mid\" onclick=\"close_without_save_all(); return false;\">";
        i18n("Discard Changes");
        echo "</button>\r\n    <button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Cancel");
        echo "</button>\r\n    ";
        break;
}
echo "</form>\r\n<script>\r\n\r\n    function save_and_close(path){\r\n        /*var id = codiad.editor.getId(path);\r\n        var content = codiad.editor.getContent(id);\r\n        codiad.filemanager.saveFile(path,content, {\r\n            success: function(){\r\n                \$('#active-files a[data-path=\"'+path+'\"]').removeClass('changed');\r\n                codiad.active.removeDraft(path);\r\n            }\r\n        });*/\r\n        codiad.active.save(path);\r\n        codiad.active.close(path);        \r\n        codiad.modal.unload();\r\n    }\r\n\r\n    function close_without_save(path){\r\n        codiad.active.close(path);        \r\n        codiad.modal.unload();\r\n    }\r\n\r\n    function save_and_close_all(){\r\n        codiad.active.saveAll();\r\n        codiad.active.removeAll(true);\r\n        codiad.modal.unload();\r\n    }\r\n\r\n    function close_without_save_all(){\r\n        codiad.active.removeAll(true);\r\n        codiad.modal.unload();\r\n    }\r\n</script>\r\n";

?>