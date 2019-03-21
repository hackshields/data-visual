<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

require_once "../../common.php";
class fileextension_textmode
{
    private $defaultExtensions = array("html" => "html", "htm" => "html", "tpl" => "smarty", "js" => "javascript", "css" => "css", "scss" => "scss", "sass" => "scss", "less" => "less", "php" => "php", "php4" => "php", "php5" => "php", "phtml" => "php", "json" => "json", "java" => "java", "xml" => "xml", "sql" => "sql", "md" => "markdown", "c" => "c_cpp", "cpp" => "c_cpp", "d" => "d", "h" => "c_cpp", "hpp" => "c_cpp", "py" => "python", "rb" => "ruby", "erb" => "html_ruby", "jade" => "jade", "coffee" => "coffee", "vm" => "velocity");
    private $availiableTextModes = array("abap", "abc", "actionscript", "ada", "apache_conf", "applescript", "asciidoc", "assembly_x86", "autohotkey", "batchfile", "c9search", "c_cpp", "cirru", "clojure", "cobol", "coffee", "coldfusion", "csharp", "css", "curly", "d", "dart", "diff", "django", "dockerfile", "dot", "eiffel", "ejs", "elixir", "elm", "erlang", "forth", "ftl", "gcode", "gherkin", "gitignore", "glsl", "gobstones", "golang", "groovy", "haml", "handlebars", "haskell", "haxe", "html", "html_elixir", "html_ruby", "ini", "io", "jack", "jade", "java", "javascript", "json", "jsoniq", "jsp", "jsx", "julia", "latex", "lean", "less", "liquid", "lisp", "livescript", "logiql", "lsl", "lua", "luapage", "lucene", "makefile", "markdown", "mask", "matlab", "maze", "mel", "mips_assembler", "mushcode", "mysql", "nix", "nsis", "objectivec", "ocaml", "pascal", "perl", "pgsql", "php", "plain_text", "powershell", "praat", "prolog", "protobuf", "python", "r", "razor", "rdoc", "rhtml", "rst", "ruby", "rust", "sass", "scad", "scala", "scheme", "scss", "sh", "sjs", "smarty", "snippets", "soy_template", "space", "sql", "sqlserver", "stylus", "svg", "swift", "swig", "tcl", "tex", "text", "textile", "toml", "twig", "typescript", "vala", "vbscript", "velocity", "verilog", "vhdl", "wollok", "xml", "xquery", "yaml");
    const storeFilename = "extensions.php";
    public function __construct()
    {
        Common::checkSession();
    }
    public function getAvailiableTextModes()
    {
        return $this->availiableTextModes;
    }
    public function getDefaultExtensions()
    {
        return $this->defaultExtensions;
    }
    public function validateExtension($extension)
    {
        return preg_match("#^[a-z0-9\\_]+\$#i", $extension);
    }
    public function validTextMode($mode)
    {
        return in_array($mode, $this->availiableTextModes);
    }
    private function processFileExtTextModeForm()
    {
        if (!Common::checkAccess()) {
            return array("status" => "error", "msg" => "You are not allowed to edit the file extensions.");
        }
        if (!isset($_POST["extension"]) || !is_array($_POST["extension"]) || !isset($_POST["textMode"]) || !is_array($_POST["textMode"])) {
            return json_encode(array("status" => "error", "msg" => "incorrect data send"));
        }
        $exMap = array();
        $warning = "";
        foreach ($_POST["extension"] as $key => $extension) {
            if (trim($extension) == "") {
                continue;
            }
            if (!isset($_POST["textMode"][$key])) {
                return json_encode(array("status" => "error", "msg" => "incorrect data send."));
            }
            $extension = strtolower(trim($extension));
            $textMode = strtolower(trim($_POST["textMode"][$key]));
            if (!$this->validateExtension($extension)) {
                return json_encode(array("status" => "error", "msg" => "incorrect extension:" . htmlentities($extension)));
            }
            if (!$this->validTextMode($textMode)) {
                return json_encode(array("status" => "error", "msg" => "incorrect text mode:" . htmlentities($textMode)));
            }
            if (isset($exMap[$extension])) {
                $warning = htmlentities($extension) . " is already set.<br/>";
            } else {
                $exMap[$extension] = $textMode;
            }
        }
        Common::saveJSON(fileextension_textmode::storeFilename, $exMap);
        if ($warning != "") {
            return json_encode(array("status" => "warning", "msg" => $warning, "extensions" => $exMap));
        }
        return json_encode(array("status" => "success", "msg" => "File extensions are saved successfully.", "extensions" => $exMap));
    }
    public function processForms()
    {
        if (!isset($_GET["action"])) {
            return json_encode(array("status" => "error", "msg" => "incorrect data send."));
        }
        switch ($_GET["action"]) {
            case "FileExtTextModeForm":
                return $this->processFileExtTextModeForm();
            case "GetFileExtTextModes":
                return $this->prcessGetFileExtTextModes();
        }
        return json_encode(array("status" => "error", "msg" => "Incorrect data send"));
    }
    private function prcessGetFileExtTextModes()
    {
        $ext = false;
        $ext = @Common::getJSON(fileextension_textmode::storeFilename);
        if (!is_array($ext)) {
            $ext = $this->defaultExtensions;
        }
        $availEx = array();
        foreach ($ext as $ex => $mode) {
            if (in_array($mode, $this->availiableTextModes)) {
                $availEx[$ex] = $mode;
            }
        }
        return json_encode(array("status" => "success", "extensions" => $availEx, "textModes" => $this->availiableTextModes));
    }
    public function getTextModeSelect($extension)
    {
        $extension = trim(strtolower($extension));
        $find = false;
        $ret = "<select name=\"textMode[]\" class=\"textMode\">" . "\n";
        foreach ($this->getAvailiableTextModes() as $textmode) {
            $ret .= "\t<option";
            if ($textmode == $extension) {
                $ret .= " selected=\"selected\"";
                $find = true;
            }
            $ret .= ">" . $textmode . "</option>" . "\n";
        }
        if (!$find && $extension != "") {
            $ret .= "\t<option selected=\"selected\">" . $textmode . "</option>" . "\n";
        }
        $ret .= "</select>" . "\n";
        return $ret;
    }
}

?>