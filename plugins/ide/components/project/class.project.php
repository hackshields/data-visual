<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
class Project extends Common
{
    public $name = "";
    public $path = "";
    public $gitrepo = false;
    public $gitbranch = "";
    public $projects = "";
    public $no_return = false;
    public $assigned = false;
    public $command_exec = "";
    public function __construct()
    {
        $this->projects = getJSON("projects.php");
        if (file_exists(BASE_PATH . "/data/" . $_SESSION["user"] . "_acl.php")) {
            $this->assigned = getJSON($_SESSION["user"] . "_acl.php");
        }
    }
    public function GetFirst()
    {
        $projects_assigned = false;
        if ($this->assigned) {
            foreach ($this->projects as $project => $data) {
                if (in_array($data["path"], $this->assigned)) {
                    $this->name = $data["name"];
                    $this->path = $data["path"];
                    break;
                }
            }
        } else {
            $this->name = $this->projects[0]["name"];
            $this->path = $this->projects[0]["path"];
        }
        $_SESSION["project"] = $this->path;
        if (!$this->no_return) {
            echo formatJSEND("success", array("name" => $this->name, "path" => $this->path));
        }
    }
    public function GetName()
    {
        foreach ($this->projects as $project => $data) {
            if ($data["path"] == $this->path) {
                $this->name = $data["name"];
            }
        }
        return $this->name;
    }
    public function Open()
    {
        $pass = false;
        foreach ($this->projects as $project => $data) {
            if ($data["path"] == $this->path) {
                $pass = true;
                $this->name = $data["name"];
                $_SESSION["project"] = $data["path"];
            }
        }
        if ($pass) {
            echo formatJSEND("success", array("name" => $this->name, "path" => $this->path));
        } else {
            echo formatJSEND("error", "Error Opening Project");
        }
    }
    public function Create()
    {
        if ($this->name != "" && $this->path != "") {
            $this->path = $this->cleanPath();
            $this->name = htmlspecialchars($this->name);
            if (!$this->isAbsPath($this->path)) {
                $this->path = $this->SanitizePath();
            }
            if ($this->path != "") {
                $pass = $this->checkDuplicate();
                if ($pass) {
                    if (!$this->isAbsPath($this->path)) {
                        mkdir(WORKSPACE . "/" . $this->path);
                    } else {
                        if (defined("WHITEPATHS")) {
                            $allowed = false;
                            foreach (explode(",", WHITEPATHS) as $whitepath) {
                                if (strpos($this->path, $whitepath) === 0) {
                                    $allowed = true;
                                }
                            }
                            if (!$allowed) {
                                exit(formatJSEND("error", "Absolute Path Only Allowed for " . WHITEPATHS));
                            }
                        }
                        if (!file_exists($this->path)) {
                            if (!mkdir($this->path . "/", 493, true)) {
                                exit(formatJSEND("error", "Unable to create Absolute Path"));
                            }
                        } else {
                            if (!is_writable($this->path) || !is_readable($this->path)) {
                                exit(formatJSEND("error", "No Read/Write Permission"));
                            }
                        }
                    }
                    $this->projects[] = array("name" => $this->name, "path" => $this->path);
                    saveJSON("projects.php", $this->projects);
                    if ($this->gitrepo && filter_var($this->gitrepo, FILTER_VALIDATE_URL) !== false) {
                        $this->git_branch = $this->SanitizeGitBranch();
                        if (!$this->isAbsPath($this->path)) {
                            $this->command_exec = "cd " . escapeshellarg(WORKSPACE . "/" . $this->path) . " && git init && git remote add origin " . escapeshellarg($this->gitrepo) . " && git pull origin " . escapeshellarg($this->gitbranch);
                        } else {
                            $this->command_exec = "cd " . escapeshellarg($this->path) . " && git init && git remote add origin " . escapeshellarg($this->gitrepo) . " && git pull origin " . escapeshellarg($this->gitbranch);
                        }
                        $this->ExecuteCMD();
                    }
                    echo formatJSEND("success", array("name" => $this->name, "path" => $this->path));
                } else {
                    echo formatJSEND("error", "A Project With the Same Name or Path Exists");
                }
            } else {
                echo formatJSEND("error", "Project Name/Folder not allowed");
            }
        } else {
            echo formatJSEND("error", "Project Name/Folder is empty");
        }
    }
    public function SanitizeGitBranch()
    {
        $sanitized = str_replace(array("..", chr(40), chr(177), "~", "^", ":", "?", "*", "[", "@{", "\\"), array(""), $this->git_branch);
        return $sanitized;
    }
    public function Rename()
    {
        $revised_array = array();
        foreach ($this->projects as $project => $data) {
            if ($data["path"] != $this->path) {
                $revised_array[] = array("name" => $data["name"], "path" => $data["path"]);
            }
        }
        $this->projects[] = array("name" => $_GET["project_name"], "path" => $this->path);
        $revised_array[] = $this->projects;
        saveJSON("projects.php", $revised_array);
        echo formatJSEND("success", NULL);
    }
    public function Delete()
    {
        $revised_array = array();
        foreach ($this->projects as $project => $data) {
            if ($data["path"] != $this->path) {
                $revised_array[] = array("name" => $data["name"], "path" => $data["path"]);
            }
        }
        saveJSON("projects.php", $revised_array);
        echo formatJSEND("success", NULL);
    }
    public function CheckDuplicate()
    {
        $pass = true;
        foreach ($this->projects as $project => $data) {
            if ($data["name"] == $this->name || $data["path"] == $this->path) {
                $pass = false;
            }
        }
        return $pass;
    }
    public function SanitizePath()
    {
        $sanitized = str_replace(" ", "_", $this->path);
        return preg_replace("/[^\\w-]/", "", $sanitized);
    }
    public function cleanPath()
    {
        $path = str_replace(chr(0), "", $this->path);
        while (strpos($path, "../") !== false) {
            $path = str_replace("../", "", $path);
        }
        return $path;
    }
    public function ExecuteCMD()
    {
        if (function_exists("system")) {
            ob_start();
            system($this->command_exec);
            ob_end_clean();
        } else {
            if (function_exists("passthru")) {
                ob_start();
                passthru($this->command_exec);
                ob_end_clean();
            } else {
                if (function_exists("exec")) {
                    exec($this->command_exec, $this->output);
                } else {
                    if (function_exists("shell_exec")) {
                        shell_exec($this->command_exec);
                    }
                }
            }
        }
    }
}

?>