<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
checkSession();
switch ($_GET["action"]) {
    case "sidelist":
        $projects_assigned = false;
        if (file_exists(BASE_PATH . "/data/" . $_SESSION["user"] . "_acl.php")) {
            $projects_assigned = getJSON($_SESSION["user"] . "_acl.php");
        }
        echo "  \r\n                    \r\n            <ul>\r\n            \r\n            ";
        $projects = getJSON("projects.php");
        sort($projects);
        foreach ($projects as $project => $data) {
            $show = true;
            if ($projects_assigned && !in_array($data["path"], $projects_assigned)) {
                $show = false;
            }
            if ($show) {
                if ($_GET["trigger"] == "true") {
                    echo "                <li onclick=\"codiad.project.open('";
                    echo $data["path"];
                    echo "');\"><div class=\"icon-archive icon\"></div>";
                    echo $data["name"];
                    echo "</li>\r\n                \r\n                ";
                } else {
                    echo "                <li ondblclick=\"codiad.project.open('";
                    echo $data["path"];
                    echo "');\"><div class=\"icon-archive icon\"></div>";
                    echo $data["name"];
                    echo "</li>\r\n                \r\n                ";
                }
            }
        }
        echo "            \r\n            </ul>\r\n                    \r\n            ";
        break;
    case "list":
        $projects_assigned = false;
        if (file_exists(BASE_PATH . "/data/" . $_SESSION["user"] . "_acl.php")) {
            $projects_assigned = getJSON($_SESSION["user"] . "_acl.php");
        }
        echo "            <label>";
        i18n("Project List");
        echo "</label>\r\n            <div id=\"project-list\">\r\n            <table width=\"100%\">\r\n                <tr>\r\n                    <th width=\"70\">";
        i18n("Open");
        echo "</th>\r\n                    <th width=\"150\">";
        i18n("Project Name");
        echo "</th>\r\n                    <th width=\"250\">";
        i18n("Path");
        echo "</th>\r\n                    ";
        if (checkAccess()) {
            echo "<th width=\"70\">";
            i18n("Delete");
            echo "</th>";
        }
        echo "                </tr>\r\n            </table>\r\n            <div class=\"project-wrapper\">\r\n            <table width=\"100%\" style=\"word-wrap: break-word;word-break: break-all;\">    \r\n            ";
        $projects = getJSON("projects.php");
        sort($projects);
        foreach ($projects as $project => $data) {
            $show = true;
            if ($projects_assigned && !in_array($data["path"], $projects_assigned)) {
                $show = false;
            }
            if ($show) {
                echo "                <tr>\r\n                    <td width=\"70\"><a onclick=\"codiad.project.open('";
                echo $data["path"];
                echo "');\" class=\"icon-folder bigger-icon\"></a></td>\r\n                    <td width=\"150\">";
                echo $data["name"];
                echo "</td>\r\n                    <td width=\"250\">";
                echo $data["path"];
                echo "</td>\r\n                    ";
                if (checkAccess()) {
                    if ($_SESSION["project"] == $data["path"]) {
                        echo "                            <td width=\"70\"><a onclick=\"codiad.message.error(i18n('Active Project Cannot Be Removed'));\" class=\"icon-block bigger-icon\"></a></td>\r\n                            ";
                    } else {
                        echo "                            <td width=\"70\"><a onclick=\"codiad.project.delete('";
                        echo $data["name"];
                        echo "','";
                        echo $data["path"];
                        echo "');\" class=\"icon-cancel-circled bigger-icon\"></a></td>\r\n                            ";
                    }
                }
                echo "                </tr>\r\n                ";
            }
        }
        echo "            </table>\r\n            </div>\r\n            </div>\r\n            ";
        if (checkAccess()) {
            echo "<button class=\"btn-left\" onclick=\"codiad.project.create();\">";
            i18n("New Project");
            echo "</button>";
        }
        echo "    \t\t<button class=\"";
        if (checkAccess()) {
            echo "btn-right";
        }
        echo "\" onclick=\"codiad.modal.unload();return false;\">";
        i18n("Close");
        echo "</button>\r\n            ";
        break;
    case "create":
        echo "            ";
        break;
    case "rename":
        echo "        <form>\r\n        <input type=\"hidden\" name=\"project_path\" value=\"";
        echo $_GET["path"];
        echo "\">\r\n        <label><span class=\"icon-pencil\"></span>";
        i18n("Rename Project");
        echo "</label>    \r\n        <input type=\"text\" name=\"project_name\" autofocus=\"autofocus\" autocomplete=\"off\" value=\"";
        echo $_GET["name"];
        echo "\">  \r\n        <button class=\"btn-left\">";
        i18n("Rename");
        echo "</button>&nbsp;<button class=\"btn-right\" onclick=\"codiad.modal.unload(); return false;\">";
        i18n("Cancel");
        echo "</button>\r\n        <form>\r\n        ";
        break;
    case "delete":
        echo "            ";
        break;
}
echo "        \r\n";

?>