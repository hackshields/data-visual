<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
echo "<label><span class=\"icon-doc-text big-icon\"></span>";
i18n("System Settings");
echo "</label>\r\n<hr>\r\n<label></label>\r\n<table class=\"settings\">\r\n    <tr>\r\n    \r\n        <td>";
i18n("Right Sidebar Trigger");
echo "</td>\r\n        <td>\r\n            <select class=\"setting\" data-setting=\"codiad.editor.rightSidebarTrigger\">\r\n                <option value=\"false\" default>";
i18n("Hover");
echo "</option>\r\n                <option value=\"true\">";
i18n("Click");
echo "</option>\r\n            </select>\r\n        </td>\r\n\r\n    </tr>\r\n    \r\n    <tr>\r\n    \r\n        <td>";
i18n("Filemanager Trigger");
echo "</td>\r\n        <td>\r\n            <select class=\"setting\" data-setting=\"codiad.editor.fileManagerTrigger\">\r\n                <option value=\"false\" default>";
i18n("Double Click");
echo "</option>\r\n                <option value=\"true\">";
i18n("Single Click");
echo "</option>\r\n            </select>\r\n        </td>\r\n\r\n    </tr>\r\n    \r\n    <tr>\r\n    \r\n        <td>";
i18n("Persistent Modal");
echo "</td>\r\n        <td>\r\n            <select class=\"setting\" data-setting=\"codiad.editor.persistentModal\">\r\n                <option value=\"true\" default>";
i18n("Yes");
echo "</option>\r\n                <option value=\"false\">";
i18n("No");
echo "</option>\r\n            </select>\r\n        </td>\r\n\r\n    </tr>\r\n    \r\n    <tr>\r\n    \r\n        <td>";
i18n("Sync system settings on all devices");
echo "</td>\r\n        <td>\r\n            <select class=\"setting\" data-setting=\"codiad.settings.system.sync\">\r\n                <option value=\"true\">";
i18n("Yes");
echo "</option>\r\n                <option value=\"false\" default>";
i18n("No");
echo "</option>\r\n            </select>\r\n        </td>\r\n\r\n    </tr>\r\n\r\n    <tr>\r\n    \r\n        <td>";
i18n("Sync plugin settings on all devices");
echo "</td>\r\n        <td>\r\n            <select class=\"setting\" data-setting=\"codiad.settings.plugin.sync\">\r\n                <option value=\"true\">";
i18n("Yes");
echo "</option>\r\n                <option value=\"false\" default>";
i18n("No");
echo "</option>\r\n            </select>\r\n        </td>\r\n\r\n    </tr>\r\n</table>\r\n";

?>