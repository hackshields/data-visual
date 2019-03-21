<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
class Active extends Common
{
    public $username = "";
    public $path = "";
    public $new_path = "";
    public $actives = "";
    public function __construct()
    {
        $this->actives = getJSON("active.php");
        $this->username = $_SESSION["user"];
    }
    public function ListActive()
    {
        $active_list = array();
        $tainted = false;
        $root = WORKSPACE . "/" . $_SESSION["userid"] . "/";
        if ($this->actives) {
            foreach ($this->actives as $active => $data) {
                if (is_array($data) && isset($data["username"]) && $data["username"] == $this->username) {
                    if (file_exists($root . $data["path"])) {
                        $focused = isset($data["focused"]) ? $data["focused"] : false;
                        $active_list[] = array("path" => $data["path"], "focused" => $focused);
                    } else {
                        unset($this->actives[$active]);
                        $tainted = true;
                    }
                }
            }
        }
        if ($tainted) {
            saveJSON("active.php", $this->actives, $_SESSION["userid"]);
        }
        echo formatJSEND("success", $active_list);
    }
    public function Check()
    {
        $cur_users = array();
        foreach ($this->actives as $active => $data) {
            if (is_array($data) && isset($data["username"]) && $data["username"] != $this->username && $data["path"] == $this->path) {
                $cur_users[] = $data["username"];
            }
        }
        if (count($cur_users) != 0) {
            echo formatJSEND("error", "Warning: File " . substr($this->path, strrpos($this->path, "/") + 1) . " Currently Opened By: " . implode(", ", $cur_users));
        } else {
            echo formatJSEND("success");
        }
    }
    public function Add()
    {
        $process_add = true;
        foreach ($this->actives as $active => $data) {
            if (is_array($data) && isset($data["username"]) && $data["username"] == $this->username && $data["path"] == $this->path) {
                $process_add = false;
            }
        }
        if ($process_add) {
            $this->actives[] = array("username" => $this->username, "path" => $this->path);
            saveJSON("active.php", $this->actives, $_SESSION["userid"]);
            echo formatJSEND("success");
        }
    }
    public function Rename()
    {
        $revised_actives = array();
        foreach ($this->actives as $active => $data) {
            if (is_array($data) && isset($data["username"])) {
                $revised_actives[] = array("username" => $data["username"], "path" => str_replace($this->path, $this->new_path, $data["path"]));
            }
        }
        saveJSON("active.php", $revised_actives, $_SESSION["userid"]);
        echo formatJSEND("success");
    }
    public function Remove()
    {
        foreach ($this->actives as $active => $data) {
            if (is_array($data) && isset($data["username"]) && $this->username == $data["username"] && $this->path == $data["path"]) {
                unset($this->actives[$active]);
            }
        }
        saveJSON("active.php", $this->actives, $_SESSION["userid"]);
        echo formatJSEND("success");
    }
    public function RemoveAll()
    {
        foreach ($this->actives as $active => $data) {
            if (is_array($data) && isset($data["username"]) && $this->username == $data["username"]) {
                unset($this->actives[$active]);
            }
        }
        saveJSON("active.php", $this->actives, $_SESSION["userid"]);
        echo formatJSEND("success");
    }
    public function MarkFileAsFocused()
    {
        foreach ($this->actives as $active => $data) {
            if (is_array($data) && isset($data["username"]) && $this->username == $data["username"]) {
                $this->actives[$active]["focused"] = false;
                if ($this->path == $data["path"]) {
                    $this->actives[$active]["focused"] = true;
                }
            }
        }
        saveJSON("active.php", $this->actives, $_SESSION["userid"]);
        echo formatJSEND("success");
    }
}

?>