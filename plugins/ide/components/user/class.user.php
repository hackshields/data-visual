<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class User
{
    public $username = "";
    public $password = "";
    public $project = "";
    public $projects = "";
    public $users = "";
    public $actives = "";
    public $lang = "";
    public $theme = "";
    public function __construct()
    {
        $this->users = getJSON("users.php");
        $this->actives = getJSON("active.php");
    }
    public function Authenticate()
    {
        echo formatJSEND("success", array("username" => $this->username));
    }
    public function Create()
    {
    }
    public function Delete()
    {
        $revised_array = array();
        foreach ($this->users as $user => $data) {
            if ($data["username"] != $this->username) {
                $revised_array[] = array("username" => $data["username"], "password" => $data["password"], "project" => $data["project"]);
            }
        }
        saveJSON("users.php", $revised_array);
        foreach ($this->actives as $active => $data) {
            if ($this->username == $data["username"]) {
                unset($this->actives[$active]);
            }
        }
        saveJSON("active.php", $this->actives, $_SESSION["userid"]);
        if (file_exists(BASE_PATH . "/data/" . $this->username . "_acl.php")) {
            unlink(BASE_PATH . "/data/" . $this->username . "_acl.php");
        }
        echo formatJSEND("success", NULL);
    }
    public function Password()
    {
        echo formatJSEND("success", NULL);
    }
    public function Project_Access()
    {
        echo formatJSEND("success", NULL);
    }
    public function Project()
    {
        echo formatJSEND("success", NULL);
    }
    public function CheckDuplicate()
    {
        $pass = true;
        foreach ($this->users as $user => $data) {
            if ($data["username"] == $this->username) {
                $pass = false;
            }
        }
        return $pass;
    }
    public function Verify()
    {
        $pass = "true";
        echo $pass;
    }
    private function EncryptPassword()
    {
        $this->password = sha1(md5($this->password));
    }
    public static function CleanUsername($username)
    {
        return preg_replace("#[^A-Za-z0-9" . preg_quote("-_@. ") . "]#", "", $username);
    }
}

?>