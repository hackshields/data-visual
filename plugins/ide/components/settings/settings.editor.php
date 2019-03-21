<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
echo "<label><span class=\"icon-home big-icon\"></span>";
i18n("Editor Settings");
echo "</label>\r\n<hr>\r\n<table class=\"settings\">\r\n    \r\n    <tr>\r\n    \r\n        <td width=\"50%\">";
i18n("Theme");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.theme\">\r\n            <option value=\"ambiance\">Ambiance</option>\r\n            <option value=\"chaos\">Chaos</option>\r\n            <option value=\"chrome\">Chrome</option>\r\n            <option value=\"clouds\">Clouds</option>\r\n            <option value=\"clouds_midnight\">Clouds - Midnight</option>\r\n            <option value=\"cobalt\">Cobalt</option>\r\n            <option value=\"crimson_editor\">Crimson Editor</option>\r\n            <option value=\"dawn\">Dawn</option>\r\n            <option value=\"dreamweaver\">Dreamweaver</option>\r\n            <option value=\"eclipse\">Eclipse</option>\r\n            <option value=\"github\">GitHub</option>\r\n            <option value=\"idle_fingers\">Idle Fingers</option>\r\n            <option value=\"iplastic\">IPlastic</option>\r\n            <option value=\"katzenmilch\">Katzenmilch</option>\r\n            <option value=\"kuroir\">Kuroir</option>\r\n            <option value=\"kr_theme\">krTheme</option>\r\n            <option value=\"merbivore\">Merbivore</option>\r\n            <option value=\"merbivore_soft\">Merbivore Soft</option>\r\n            <option value=\"mono_industrial\">Mono Industrial</option>\r\n            <option value=\"monokai\">Monokai</option>\r\n            <option value=\"pastel_on_dark\">Pastel On Dark</option>\r\n            <option value=\"solarized_dark\">Solarized Dark</option>\r\n            <option value=\"solarized_light\">Solarized Light</option>\r\n            <option value=\"sqlserver\">SQL Server</option>\r\n            <option value=\"terminal\">Terminal</option>\r\n            <option value=\"textmate\">Textmate</option>\r\n            <option value=\"tomorrow\">Tomorrow</option>\r\n            <option value=\"tomorrow_night\">Tomorrow Night</option>\r\n            <option value=\"tomorrow_night_blue\">Tomorrow Night Blue</option>\r\n            <option value=\"tomorrow_night_bright\">Tomorrow Night Bright</option>\r\n            <option value=\"tomorrow_night_eighties\">Tomorrow Night Eighties</option>\r\n            <option value=\"twilight\" selected>Twilight</option>\r\n            <option value=\"vibrant_ink\">Vibrant Ink</option>\r\n            <option value=\"xcode\">XCode</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Font Size");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.fontSize\">\r\n            <option value=\"10px\">10px</option>\r\n            <option value=\"11px\">11px</option>\r\n            <option value=\"12px\">12px</option>\r\n            <option value=\"13px\" selected>13px</option>\r\n            <option value=\"14px\">14px</option>\r\n            <option value=\"15px\">15px</option>\r\n            <option value=\"16px\">16px</option>\r\n            <option value=\"17px\">17px</option>\r\n            <option value=\"18px\">18px</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Highlight Active Line");
echo "</td>\r\n        <td>\r\n        \r\n            <select class=\"setting\" data-setting=\"codiad.editor.highlightLine\">\r\n                <option value=\"true\" selected>";
i18n("Yes");
echo "</option>\r\n                <option value=\"false\">";
i18n("No");
echo "</option>\r\n            </select>\r\n            \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Indent Guides");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.indentGuides\">\r\n            <option value=\"true\" selected>";
i18n("On");
echo "</option>\r\n            <option value=\"false\">";
i18n("Off");
echo "</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Print Margin");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.printMargin\">\r\n            <option value=\"true\">";
i18n("Show");
echo "</option>\r\n            <option value=\"false\" selected>";
i18n("Hide");
echo "</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Print Margin Column");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.printMarginColumn\">\r\n            <option value=\"80\" selected>80</option>\r\n            <option value=\"85\">85</option>\r\n            <option value=\"90\">90</option>\r\n            <option value=\"95\">95</option>\r\n            <option value=\"100\">100</option>\r\n            <option value=\"105\">105</option>\r\n            <option value=\"110\">110</option>\r\n            <option value=\"115\">115</option>\r\n            <option value=\"120\">120</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Wrap Lines");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.wrapMode\">\r\n            <option value=\"false\" selected>";
i18n("No wrap");
echo "</option>\r\n            <option value=\"true\">";
i18n("Wrap Lines");
echo "</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Tab Size");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.tabSize\">\r\n            <option value=\"2\">2</option>\r\n            <option value=\"3\">3</option>\r\n            <option value=\"4\" selected>4</option>\r\n            <option value=\"5\">5</option>\r\n            <option value=\"6\">6</option>\r\n            <option value=\"7\">7</option>\r\n            <option value=\"8\">8</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n    <tr>\r\n    \r\n        <td>";
i18n("Soft Tabs");
echo "</td>\r\n        <td>\r\n        \r\n        <select class=\"setting\" data-setting=\"codiad.editor.softTabs\">\r\n            <option value=\"false\" selected>";
i18n("No");
echo "</option>\r\n            <option value=\"true\">";
i18n("Yes");
echo "</option>\r\n        </select>\r\n        \r\n        </td>\r\n        \r\n    </tr>\r\n</table>\r\n";

?>