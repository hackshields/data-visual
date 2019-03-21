<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../lib/diff_match_patch.php";
require_once "../../common.php";
class Filemanager extends Common
{
    public $root = "";
    public $project = "";
    public $rel_path = "";
    public $path = "";
    public $patch = "";
    public $type = "";
    public $new_name = "";
    public $content = "";
    public $destination = "";
    public $upload = "";
    public $controller = "";
    public $upload_json = "";
    public $search_string = "";
    public $search_file_type = "";
    public $query = "";
    public $foptions = "";
    public $status = "";
    public $data = "";
    public $message = "";
    public function __construct($get, $post, $files)
    {
        $this->path = $get["path"];
        if ($this->path == "/") {
            $this->path = "";
        }
        $this->rel_path = $this->path;
        if ($this->rel_path == "/") {
            $this->rel_path = "";
        }
        if ($this->rel_path != "/") {
            $this->rel_path .= "/";
        }
        if (!empty($get["query"])) {
            $this->query = $get["query"];
        }
        if (!empty($get["options"])) {
            $this->foptions = $get["options"];
        }
        $this->root = $get["root"];
        if (!empty($post["search_string"])) {
            $this->search_string = $post["search_string"];
        }
        if (!empty($post["search_file_type"])) {
            $this->search_file_type = $post["search_file_type"];
        }
        if (!empty($get["type"])) {
            $this->type = $get["type"];
        }
        if (!empty($get["new_name"])) {
            $this->new_name = $get["new_name"];
        }
        foreach (array("content", "mtime", "patch") as $key) {
            if (!empty($post[$key])) {
                if (get_magic_quotes_gpc()) {
                    $this->{$key} = stripslashes($post[$key]);
                } else {
                    $this->{$key} = $post[$key];
                }
            }
        }
        if (!empty($get["destination"])) {
            $get["destination"] = Filemanager::cleanPath($get["destination"]);
            if ($this->isAbsPath($get["path"])) {
                $this->destination = $get["destination"];
            } else {
                $this->destination = $this->root . $get["destination"];
            }
        }
    }
    public function index()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->rel_path = $this->path;
            $this->path = WORKSPACE . "/" . $userid . "/" . $this->path;
            if (file_exists($this->path)) {
                $index = array();
                if (is_dir($this->path) && ($handle = opendir($this->path))) {
                    while (false !== ($object = readdir($handle))) {
                        if ($object != "." && $object != ".." && $object != $this->controller) {
                            if (is_dir($this->path . "/" . $object)) {
                                $type = "directory";
                                $size = count(glob($this->path . "/" . $object . "/*"));
                            } else {
                                $type = "file";
                                $size = @filesize($this->path . "/" . $object);
                            }
                            $index[] = array("name" => $this->rel_path . "/" . $object, "type" => $type, "size" => $size);
                        }
                    }
                    $folders = array();
                    $files = array();
                    foreach ($index as $item => $data) {
                        if ($data["type"] == "directory") {
                            $folders[] = array("name" => $data["name"], "type" => $data["type"], "size" => $data["size"]);
                        }
                        if ($data["type"] == "file") {
                            $files[] = array("name" => $data["name"], "type" => $data["type"], "size" => $data["size"]);
                        }
                    }
                    function sorter($a, $b, $key = "name")
                    {
                        return strnatcmp($a[$key], $b[$key]);
                    }
                    usort($folders, "sorter");
                    usort($files, "sorter");
                    $output = array_merge($folders, $files);
                    $this->status = "success";
                    $this->data = "\"index\":" . json_encode($output);
                } else {
                    $this->status = "error";
                    $this->message = "Not A Directory";
                }
            } else {
                $this->status = "error";
                $this->message = "Path Does Not Exist";
            }
            $this->respond();
        }
    }
    public function find()
    {
        if (!function_exists("shell_exec")) {
            $this->status = "error";
            $this->message = "Shell_exec() Command Not Enabled.";
        } else {
            chdir($this->path);
            $input = str_replace("\"", "", $this->query);
            $vinput = preg_quote($input);
            $cmd = "find -L ";
            if ($this->foptions && $this->foptions["strategy"]) {
                switch ($this->f_options["strategy"]) {
                    case "left_prefix":
                        $cmd = (string) $cmd . " -iname \"" . $vinput . "*\"";
                        break;
                    case "substring":
                        $cmd = (string) $cmd . " -iname \"*" . $vinput . "*\"";
                        break;
                    case "regexp":
                        $cmd = (string) $cmd . " -regex \"" . $input . "\"";
                        break;
                }
            } else {
                $cmd = "find -L -iname \"" . $input . "*\"";
            }
            $cmd = (string) $cmd . "  -printf \"%h/%f %y\n\"";
            $output = shell_exec($cmd);
            $file_arr = explode("\n", $output);
            $output_arr = array();
            error_reporting(0);
            foreach ($file_arr as $i => $fentry) {
                $farr = explode(" ", $fentry);
                $fname = trim($farr[0]);
                if ($farr[1] == "f") {
                    $ftype = "file";
                } else {
                    $ftype = "directory";
                }
                if (strlen($fname) != 0) {
                    $fname = $this->rel_path . substr($fname, 2);
                    $f = array("path" => $fname, "type" => $ftype);
                    array_push($output_arr, $f);
                }
            }
            if (count($output_arr) == 0) {
                $this->status = "error";
                $this->message = "No Results Returned";
            } else {
                $this->status = "success";
                $this->data = "\"index\":" . json_encode($output_arr);
            }
        }
        $this->respond();
    }
    public function search()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            if (!function_exists("shell_exec")) {
                $this->status = "error";
                $this->message = "Shell_exec() Command Not Enabled.";
            } else {
                if ($_GET["type"] == 1) {
                    $this->path = WORKSPACE;
                }
                $input = str_replace("\"", "", $this->search_string);
                $input = preg_quote($input);
                $output = shell_exec("find -L " . $this->path . " -iregex  \".*" . $this->search_file_type . "\" -type f | xargs grep -i -I -n -R -H \"" . $input . "\"");
                $output_arr = explode("\n", $output);
                $return = array();
                foreach ($output_arr as $line) {
                    $data = explode(":", $line);
                    $da = array();
                    if (2 < count($data)) {
                        $da["line"] = $data[1];
                        $da["file"] = str_replace($this->path, "", $data[0]);
                        $da["result"] = str_replace($this->root, "", $data[0]);
                        $da["string"] = str_replace($data[0] . ":" . $data[1] . ":", "", $line);
                        $return[] = $da;
                    }
                }
                if (count($return) == 0) {
                    $this->status = "error";
                    $this->message = "No Results Returned";
                } else {
                    $this->status = "success";
                    $this->data = "\"index\":" . json_encode($return);
                }
            }
            $this->respond();
        }
    }
    public function open()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            if (is_file($this->path)) {
                $output = file_get_contents($this->path);
                if (extension_loaded("mbstring") && !mb_check_encoding($output, "UTF-8")) {
                    if (mb_check_encoding($output, "ISO-8859-1")) {
                        $output = utf8_encode($output);
                    } else {
                        $output = mb_convert_encoding($content, "UTF-8");
                    }
                }
                $this->status = "success";
                $this->data = "\"content\":" . json_encode($output);
                $mtime = filemtime($this->path);
                $this->data .= ", \"mtime\":" . $mtime;
            } else {
                $this->status = "error";
                $this->message = "Not A File :" . $this->path;
            }
            $this->respond();
        }
    }
    public function openinbrowser()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            if (file_exists($this->path)) {
                $ext = pathinfo($this->path, PATHINFO_EXTENSION);
                if ($ext == "png" || $ext == "jpg" || $ext == "gif") {
                    $fileOut = $this->path;
                    $imageInfo = getimagesize($fileOut);
                    switch ($imageInfo[2]) {
                        case IMAGETYPE_JPEG:
                            header("Content-Type: image/jpeg");
                            break;
                        case IMAGETYPE_GIF:
                            header("Content-Type: image/gif");
                            break;
                        case IMAGETYPE_PNG:
                            header("Content-Type: image/png");
                            break;
                        default:
                            break;
                    }
                    header("Content-Length: " . filesize($fileOut));
                    readfile($fileOut);
                    return NULL;
                }
            }
            $this->status = "error";
            $this->data = "Not supported, only image files preview";
            $this->respond();
        }
    }
    public function create()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            if ($this->type == "file") {
                if (!file_exists($this->path)) {
                    if ($file = fopen($this->path, "w")) {
                        if ($this->content) {
                            fwrite($file, $this->content);
                        }
                        $this->data = "\"mtime\":" . filemtime($this->path);
                        fclose($file);
                        $this->status = "success";
                    } else {
                        $this->status = "error";
                        $this->message = "Cannot Create File";
                    }
                } else {
                    $this->status = "error";
                    $this->message = "File Already Exists";
                }
            }
            if ($this->type == "directory") {
                if (!is_dir($this->path)) {
                    mkdir($this->path);
                    $this->status = "success";
                } else {
                    $this->status = "error";
                    $this->message = "Directory Already Exists";
                }
            }
            $this->respond();
        }
    }
    public function delete()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            function rrmdir($path, $follow)
            {
                if (is_file($path)) {
                    unlink($path);
                } else {
                    $files = array_diff(scandir($path), array(".", ".."));
                    foreach ($files as $file) {
                        if (is_link((string) $path . "/" . $file)) {
                            if ($follow) {
                                rrmdir((string) $path . "/" . $file, $follow);
                            }
                            unlink((string) $path . "/" . $file);
                        } else {
                            if (is_dir((string) $path . "/" . $file)) {
                                rrmdir((string) $path . "/" . $file, $follow);
                            } else {
                                unlink((string) $path . "/" . $file);
                            }
                        }
                    }
                    return rmdir($path);
                }
            }
            if (file_exists($this->path)) {
                if (isset($_GET["follow"])) {
                    rrmdir($this->path, true);
                } else {
                    rrmdir($this->path, false);
                }
                $this->status = "success";
            } else {
                $this->status = "error";
                $this->message = "Path Does Not Exist ";
            }
            $this->respond();
        }
    }
    public function modify()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            if ($this->new_name) {
                $explode = explode("/", $this->path);
                array_pop($explode);
                $new_path = implode("/", $explode) . "/" . $this->new_name;
                if (!file_exists($new_path)) {
                    if (rename($this->path, $new_path)) {
                        $this->status = "success";
                    } else {
                        $this->status = "error";
                        $this->message = "Could Not Rename";
                    }
                } else {
                    $this->status = "error";
                    $this->message = "Path Already Exists";
                }
            } else {
                if ($this->content || $this->patch) {
                    if ($this->content == " ") {
                        $this->content = "";
                    }
                    if ($this->patch && !$this->mtime) {
                        $this->status = "error";
                        $this->message = "mtime parameter not found";
                        $this->respond();
                        return NULL;
                    }
                    if (is_file($this->path)) {
                        $serverMTime = filemtime($this->path);
                        $fileContents = file_get_contents($this->path);
                        if ($this->patch && $this->mtime != $serverMTime) {
                            $this->status = "error";
                            $this->message = "Client is out of sync";
                            $this->respond();
                            return NULL;
                        }
                        if (strlen(trim($this->patch)) == 0 && !$this->content) {
                            $this->status = "success";
                            $this->data = "\"mtime\":" . $serverMTime;
                            $this->respond();
                            return NULL;
                        }
                        if ($file = fopen($this->path, "w")) {
                            if ($this->patch) {
                                $dmp = new diff_match_patch();
                                $p = $dmp->patch_apply($dmp->patch_fromText($this->patch), $fileContents);
                                $this->content = $p[0];
                            }
                            if (fwrite($file, $this->content) === false) {
                                $this->status = "error";
                                $this->message = "could not write to file";
                            } else {
                                clearstatcache();
                                $this->data = "\"mtime\":" . filemtime($this->path);
                                $this->status = "success";
                            }
                            fclose($file);
                        } else {
                            $this->status = "error";
                            $this->message = "Cannot Write to File";
                        }
                    } else {
                        $this->status = "error";
                        $this->message = "Not A File";
                    }
                } else {
                    $file = fopen($this->path, "w");
                    fclose($file);
                    $this->data = "\"mtime\":" . filemtime($this->path);
                    $this->status = "success";
                }
            }
            $this->respond();
        }
    }
    public function duplicate()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            if (!file_exists($this->path)) {
                $this->status = "error";
                $this->message = "Invalid Source";
            }
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            function recurse_copy($src, $dst)
            {
                $dir = opendir($src);
                @mkdir($dst);
                while (false !== ($file = readdir($dir))) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($src . "/" . $file)) {
                            recurse_copy($src . "/" . $file, $dst . "/" . $file);
                        } else {
                            copy($src . "/" . $file, $dst . "/" . $file);
                        }
                    }
                }
                closedir($dir);
            }
            if ($this->status != "error") {
                if (is_file($this->path)) {
                    copy($this->path, $this->destination);
                    $this->status = "success";
                } else {
                    recurse_copy($this->path, $this->destination);
                    if (!$this->response) {
                        $this->status = "success";
                    }
                }
            }
            $this->respond();
        }
    }
    public function upload()
    {
        $userid = $_SESSION["userid"];
        if (empty($userid)) {
            $this->status = "error";
            $this->message = "Path Does Not Exist";
            $this->respond();
        } else {
            $this->path = WORKSPACE . "/" . $_SESSION["userid"] . "/" . $this->path;
            if (is_file($this->path)) {
                $this->status = "error";
                $this->message = "Path Not A Directory";
            } else {
                $info = array();
                while (list($key, $value) = each($_FILES["upload"]["name"])) {
                    if (!empty($value)) {
                        $filename = $value;
                        $add = $this->path . "/" . $filename;
                        if (@move_uploaded_file($_FILES["upload"]["tmp_name"][$key], $add)) {
                            $info[] = array("name" => $value, "size" => filesize($add), "url" => $filename, "thumbnail_url" => $filename, "delete_url" => $filename, "delete_type" => "DELETE");
                        }
                    }
                }
                $this->upload_json = json_encode($info);
            }
            $this->respond();
        }
    }
    public function respond()
    {
        if ($this->status == "success") {
            if ($this->data) {
                $json = "{\"status\":\"success\",\"data\":{" . $this->data . "}}";
            } else {
                $json = "{\"status\":\"success\",\"data\":null}";
            }
        } else {
            if ($this->upload_json != "") {
                $json = $this->upload_json;
            } else {
                $json = "{\"status\":\"error\",\"message\":\"" . $this->message . "\"}";
            }
        }
        echo $json;
    }
    public static function cleanPath($path)
    {
        $path = str_replace("\\", "/", $path);
        $path = str_replace(chr(0), "", $path);
        while (strpos($path, "../") !== false) {
            $path = str_replace("../", "", $path);
        }
        return $path;
    }
}

?>