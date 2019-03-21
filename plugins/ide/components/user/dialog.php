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
    case "list":
        $projects_assigned = false;
        if (!checkAccess()) {
            echo "            <label>";
            i18n("Restricted");
            echo "</label>\r\n            <pre>";
            i18n("You can not edit the user list");
            echo "</pre>\r\n            <button onclick=\"codiad.modal.unload();return false;\">";
            i18n("Close");
            echo "</button>\r\n            ";
        } else {
            echo "            <label>";
            i18n("User List");
            echo "</label>\r\n            <div id=\"user-list\">\r\n            <table width=\"100%\">\r\n                <tr>\r\n                    <th width=\"150\">";
            i18n("Username");
            echo "</th>\r\n                    <th width=\"85\">";
            i18n("Password");
            echo "</th>\r\n                    <th width=\"75\">";
            i18n("Projects");
            echo "</th>\r\n                    <th width=\"70\">";
            i18n("Delete");
            echo "</th>\r\n                </tr>\r\n            </table>\r\n            <div class=\"user-wrapper\">\r\n            <table width=\"100%\" style=\"word-wrap: break-word;word-break: break-all;\">    \r\n            ";
            $users = getJSON("users.php");
            foreach ($users as $user => $data) {
                echo "            <tr>\r\n                <td width=\"150\">";
                echo $data["username"];
                echo "</td>\r\n                <td width=\"85\"><a onclick=\"codiad.user.password('";
                echo $data["username"];
                echo "');\" class=\"icon-flashlight bigger-icon\"></a></td>\r\n                <td width=\"75\"><a onclick=\"codiad.user.projects('";
                echo $data["username"];
                echo "');\" class=\"icon-archive bigger-icon\"></a></td>\r\n                ";
                if ($_SESSION["user"] == $data["username"]) {
                    echo "                    <td width=\"75\"><a onclick=\"codiad.message.error('You Cannot Delete Your Own Account');\" class=\"icon-block bigger-icon\"></a></td>\r\n                    ";
                } else {
                    echo "                    <td width=\"70\"><a onclick=\"codiad.user.delete('";
                    echo $data["username"];
                    echo "');\" class=\"icon-cancel-circled bigger-icon\"></a></td>\r\n                    ";
                }
                echo "            </tr>\r\n            ";
            }
            echo "            </table>\r\n            </div>\r\n            </div>\r\n            <button class=\"btn-left\" onclick=\"codiad.user.createNew();\">";
            i18n("New Account");
            echo "</button>\r\n    \t\t<button class=\"btn-right\" onclick=\"codiad.modal.unload();return false;\">";
            i18n("Close");
            echo "</button>\r\n            ";
        }
        break;
    case "create":
        echo "            <form>\r\n            <label>";
        i18n("Username");
        echo "</label>\r\n            <input type=\"text\" name=\"username\" autofocus=\"autofocus\" autocomplete=\"off\">\r\n            <label>";
        i18n("Password");
        echo "</label>\r\n            <input type=\"password\" name=\"password1\">\r\n            <label>";
        i18n("Confirm Password");
        echo "</label>\r\n            <input type=\"password\" name=\"password2\">\r\n            <button class=\"btn-left\">";
        i18n("Create Account");
        echo "</button>\r\n\t\t\t<button class=\"btn-right\" onclick=\"codiad.user.list();return false;\">";
        i18n("Cancel");
        echo "</button>\r\n            <form>\r\n            ";
        break;
    case "projects":
        $projects = getJSON("projects.php");
        $projects_assigned = false;
        if (file_exists(BASE_PATH . "/data/" . $_GET["username"] . "_acl.php")) {
            $projects_assigned = getJSON($_GET["username"] . "_acl.php");
        }
        echo "            <form>\r\n            <input type=\"hidden\" name=\"username\" value=\"";
        echo $_GET["username"];
        echo "\">\r\n            <label>";
        i18n("Project Access for ");
        echo ucfirst($_GET["username"]);
        echo "</label>\r\n            <select name=\"access_level\" onchange=\"if(\$(this).val()=='0'){ \$('#project-selector').slideUp(300); }else{ \$('#project-selector').slideDown(300).css({'overflow-y':'scroll'}); }\">\r\n                <option value=\"0\" ";
        if (!$projects_assigned) {
            echo "selected=\"selected\"";
        }
        echo ">";
        i18n("Access ALL Projects");
        echo "</option>\r\n                <option value=\"1\" ";
        if ($projects_assigned) {
            echo "selected=\"selected\"";
        }
        echo ">";
        i18n("Only Selected Projects");
        echo "</option>\r\n            </select>\r\n            <div id=\"project-selector\" ";
        if (!$projects_assigned) {
            echo "style=\"display: none;\"";
        }
        echo ">\r\n                <table>\r\n                ";
        foreach ($projects as $project => $data) {
            $sel = "";
            if ($projects_assigned && in_array($data["path"], $projects_assigned)) {
                $sel = "checked=\"checked\"";
            }
            echo "<tr><td width=\"5\"><input type=\"checkbox\" name=\"project\" " . $sel . " id=\"" . $data["path"] . "\" value=\"" . $data["path"] . "\"></td><td>" . $data["name"] . "</td></tr>";
        }
        echo "                </table>\r\n            </div>\r\n            <button class=\"btn-left\">";
        i18n("Confirm");
        echo "</button>\r\n\t\t\t<button class=\"btn-right\" onclick=\"codiad.user.list();return false;\">";
        i18n("Close");
        echo "</button>\r\n            ";
        break;
    case "delete":
        echo "            <form>\r\n            <input type=\"hidden\" name=\"username\" value=\"";
        echo $_GET["username"];
        echo "\">\r\n            <label>";
        i18n("Confirm User Deletion");
        echo "</label>\r\n            <pre>";
        i18n("Account:");
        echo " ";
        echo $_GET["username"];
        echo "</pre>\r\n            <button class=\"btn-left\">";
        i18n("Confirm");
        echo "</button>\r\n\t\t\t<button class=\"btn-right\" onclick=\"codiad.user.list();return false;\">";
        i18n("Cancel");
        echo "</button>\r\n            ";
        break;
    case "password":
        if ($_GET["username"] == "undefined") {
            $username = $_SESSION["user"];
        } else {
            $username = $_GET["username"];
        }
        echo "            <form>\r\n            <input type=\"hidden\" name=\"username\" value=\"";
        echo $username;
        echo "\">\r\n            <label>";
        i18n("New Password");
        echo "</label>\r\n            <input type=\"password\" name=\"password1\" autofocus=\"autofocus\">\r\n            <label>";
        i18n("Confirm Password");
        echo "</label>\r\n            <input type=\"password\" name=\"password2\">\r\n          <button class=\"btn-left\">";
        i18n("Change %{username}%&apos;s Password", array("username" => ucfirst($username)));
        echo "</button>\r\n\t\t\t<button class=\"btn-right\" onclick=\"codiad.modal.unload();return false;\">";
        i18n("Cancel");
        echo "</button>\r\n            ";
        break;
}

?>