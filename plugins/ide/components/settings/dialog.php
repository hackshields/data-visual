<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
checkSession();
if (!isset($_GET["action"])) {
    $_GET["action"] = "settings";
}
switch ($_GET["action"]) {
    case "settings":
        echo "            <div class=\"settings-view\">\r\n                <div class=\"config-menu\">\r\n                    <label>";
        i18n("Settings");
        echo "</label>\r\n                    <div class=\"panels-components\">\r\n                        <ul>\r\n                            <li name=\"editor-settings\" data-file=\"components/settings/settings.editor.php\" data-name=\"editor\" class=\"active\">\r\n                                <a><span class=\"icon-home bigger-icon\"></span>";
        i18n("Editor");
        echo "</a>\r\n                            </li>\r\n                            <li name=\"system-settings\" data-file=\"components/settings/settings.system.php\" data-name=\"system\">\r\n                                <a><span class=\"icon-doc-text bigger-icon\"></span>";
        i18n("System");
        echo "</a>\r\n                            </li>\r\n                            ";
        if (COMMON::checkAccess()) {
            echo "                                    <li name=\"extension-settings\" data-file=\"components/fileext_textmode/dialog.php?action=fileextension_textmode_form\" data-name=\"fileext_textmode\">\r\n                                        <a><span class=\"icon-pencil bigger-icon\"></span>";
            i18n("Extensions");
            echo "</a>\r\n                                    </li>\r\n                                    ";
        }
        echo "                        </ul>\r\n                    </div>\r\n                    <hr>\r\n                    <div class=\"panels-plugins\">\r\n                        ";
        $plugins = Common::readDirectory(PLUGINS);
        foreach ($plugins as $plugin) {
            if (file_exists(PLUGINS . "/" . $plugin . "/plugin.json")) {
                $datas = json_decode(file_get_contents(PLUGINS . "/" . $plugin . "/plugin.json"), true);
                foreach ($datas as $data) {
                    if (isset($data["config"])) {
                        foreach ($data["config"] as $config) {
                            if (isset($config["file"]) && isset($config["icon"]) && isset($config["title"])) {
                                echo "<li data-file=\"plugins/" . $plugin . "/" . $config["file"] . "\" data-name=\"" . $data["name"] . "\">\r\n                                                        <a><span class=\"" . $config["icon"] . " bigger-icon\"></span>" . $config["title"] . "</a></li>";
                            }
                        }
                    }
                }
            }
        }
        echo "                    </div>\r\n                </div>\r\n                <div class=\"panels\">\r\n                    <div class=\"panel active\" data-file=\"components/settings/settings.editor.php\">\r\n                        ";
        include "settings.editor.php";
        echo "                    </div>\r\n                </div>\r\n            </div>\r\n            <button class=\"btn-right\" onclick=\"save(); return false;\">";
        i18n("Save");
        echo "</button>\r\n            <button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Close");
        echo "</button>\r\n            <script>\r\n                \$('.settings-view .config-menu li').click(function(){\r\n                    codiad.settings._showTab(\$(this).attr('data-file'));\r\n                });\r\n            \r\n                function save() {\r\n                    \$('.setting').each(function(){\r\n                        var setting = \$(this).data('setting');\r\n                        var val     = \$(this).val();\r\n                        if(val===null){\r\n                            codiad.message.alert(i18n(\"You Must Choose A Value\"));\r\n                            return;\r\n                        }else{\r\n                            switch(setting){\r\n                                case 'codiad.editor.theme':\r\n                                    codiad.editor.setTheme(val);\r\n                                    break;\r\n                                case 'codiad.editor.fontSize':\r\n                                    codiad.editor.setFontSize(val);\r\n                                    break;\r\n                                case 'codiad.editor.highlightLine':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setHighlightLine(bool_val);\r\n                                    break;\r\n                                case 'codiad.editor.indentGuides':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setIndentGuides(bool_val);\r\n                                    break;\r\n                                case 'codiad.editor.printMargin':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setPrintMargin(bool_val);\r\n                                    break;\r\n                                case 'codiad.editor.printMarginColumn':\r\n                                    var int_val = (!isNaN(parseFloat(val)) && isFinite(val))\r\n                                        ? parseInt(val, 10)\r\n                                        : 80;\r\n                                    codiad.editor.setPrintMarginColumn(int_val);\r\n                                    break;\r\n                                case 'codiad.editor.wrapMode':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setWrapMode(bool_val);\r\n                                    break;\r\n                                case 'codiad.editor.rightSidebarTrigger':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setRightSidebarTrigger(bool_val);\r\n                                    break;\r\n                                case 'codiad.editor.fileManagerTrigger':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setFileManagerTrigger(bool_val);\r\n                                    break;    \r\n                                case 'codiad.editor.persistentModal':\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setPersistentModal(bool_val);\r\n                                    break;      \r\n                                case \"codiad.editor.softTabs\":\r\n                                    var bool_val = (val == \"true\");\r\n                                    codiad.editor.setSoftTabs(bool_val);\r\n                                break;\r\n                                case \"codiad.editor.tabSize\":\r\n                                    codiad.editor.setTabSize(val);\r\n                                break;\r\n                            }\r\n                        }\r\n                        localStorage.setItem(setting, val);\r\n                    });\r\n                    /* Notify listeners */\r\n                    amplify.publish('settings.dialog.save',{});\r\n                    codiad.modal.unload();\r\n                    codiad.settings.save();\r\n                }\r\n            </script>\r\n";
        break;
    case "iframe":
        echo "            <script>\r\n                /*\r\n                 *  Storage Event:\r\n                 *  Note: Event fires only if change was made in different window and not in this one\r\n                 *  Details: http://dev.w3.org/html5/webstorage/#dom-localstorage\r\n                 */\r\n                window.addEventListener('storage', function(e){\r\n                    if (/^codiad/.test(e.key)) {\r\n                        var obj = { key: e.key, oldValue: e.oldValue, newValue: e.newValue };\r\n                        /* Notify listeners */\r\n                        window.parent.amplify.publish('settings.changed', obj);\r\n                    }\r\n                }, false);\r\n            </script>\r\n";
        break;
    default:
        break;
}

?>