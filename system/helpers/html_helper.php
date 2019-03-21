<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("heading")) {
    /**
     * Heading
     *
     * Generates an HTML heading tag.
     *
     * @param	string	content
     * @param	int	heading level
     * @param	string
     * @return	string
     */
    function heading($data = "", $h = "1", $attributes = "")
    {
        return "<h" . $h . _stringify_attributes($attributes) . ">" . $data . "</h" . $h . ">";
    }
}
if (!function_exists("ul")) {
    /**
     * Unordered List
     *
     * Generates an HTML unordered list from an single or multi-dimensional array.
     *
     * @param	array
     * @param	mixed
     * @return	string
     */
    function ul($list, $attributes = "")
    {
        return _list("ul", $list, $attributes);
    }
}
if (!function_exists("ol")) {
    /**
     * Ordered List
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param	array
     * @param	mixed
     * @return	string
     */
    function ol($list, $attributes = "")
    {
        return _list("ol", $list, $attributes);
    }
}
if (!function_exists("_list")) {
    /**
     * Generates the list
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @param	string
     * @param	mixed
     * @param	mixed
     * @param	int
     * @return	string
     */
    function _list($type = "ul", $list = array(), $attributes = "", $depth = 0)
    {
        if (!is_array($list)) {
            return $list;
        }
        $out = str_repeat(" ", $depth) . "<" . $type . _stringify_attributes($attributes) . ">\n";
        static $_last_list_item = "";
        foreach ($list as $key => $val) {
            $_last_list_item = $key;
            $out .= str_repeat(" ", $depth + 2) . "<li>";
            if (!is_array($val)) {
                $out .= $val;
            } else {
                $out .= $_last_list_item . "\n" . _list($type, $val, "", $depth + 4) . str_repeat(" ", $depth + 2);
            }
            $out .= "</li>\n";
        }
        return $out . str_repeat(" ", $depth) . "</" . $type . ">\n";
    }
}
if (!function_exists("img")) {
    /**
     * Image
     *
     * Generates an <img /> element
     *
     * @param	mixed
     * @param	bool
     * @param	mixed
     * @return	string
     */
    function img($src = "", $index_page = false, $attributes = "")
    {
        if (!is_array($src)) {
            $src = array("src" => $src);
        }
        if (!isset($src["alt"])) {
            $src["alt"] = "";
        }
        $img = "<img";
        foreach ($src as $k => $v) {
            if ($k === "src" && !preg_match("#^(data:[a-z,;])|(([a-z]+:)?(?<!data:)//)#i", $v)) {
                if ($index_page === true) {
                    $img .= " src=\"" . get_instance()->config->site_url($v) . "\"";
                } else {
                    $img .= " src=\"" . get_instance()->config->slash_item("base_url") . $v . "\"";
                }
            } else {
                $img .= " " . $k . "=\"" . $v . "\"";
            }
        }
        return $img . _stringify_attributes($attributes) . " />";
    }
}
if (!function_exists("doctype")) {
    /**
     * Doctype
     *
     * Generates a page document type declaration
     *
     * Examples of valid options: html5, xhtml-11, xhtml-strict, xhtml-trans,
     * xhtml-frame, html4-strict, html4-trans, and html4-frame.
     * All values are saved in the doctypes config file.
     *
     * @param	string	type	The doctype to be generated
     * @return	string
     */
    function doctype($type = "html5")
    {
        static $doctypes = NULL;
        if (!is_array($doctypes)) {
            if (file_exists(APPPATH . "config/doctypes.php")) {
                include APPPATH . "config/doctypes.php";
            }
            if (file_exists(APPPATH . "config/" . ENVIRONMENT . "/doctypes.php")) {
                include APPPATH . "config/" . ENVIRONMENT . "/doctypes.php";
            }
            if (empty($_doctypes) || !is_array($_doctypes)) {
                $doctypes = array();
                return false;
            }
            $doctypes = $_doctypes;
        }
        return isset($doctypes[$type]) ? $doctypes[$type] : false;
    }
}
if (!function_exists("link_tag")) {
    /**
     * Link
     *
     * Generates link to a CSS file
     *
     * @param	mixed	stylesheet hrefs or an array
     * @param	string	rel
     * @param	string	type
     * @param	string	title
     * @param	string	media
     * @param	bool	should index_page be added to the css path
     * @return	string
     */
    function link_tag($href = "", $rel = "stylesheet", $type = "text/css", $title = "", $media = "", $index_page = false)
    {
        $CI =& get_instance();
        $link = "<link ";
        if (is_array($href)) {
            foreach ($href as $k => $v) {
                if ($k === "href" && !preg_match("#^([a-z]+:)?//#i", $v)) {
                    if ($index_page === true) {
                        $link .= "href=\"" . $CI->config->site_url($v) . "\" ";
                    } else {
                        $link .= "href=\"" . $CI->config->slash_item("base_url") . $v . "\" ";
                    }
                } else {
                    $link .= $k . "=\"" . $v . "\" ";
                }
            }
        } else {
            if (preg_match("#^([a-z]+:)?//#i", $href)) {
                $link .= "href=\"" . $href . "\" ";
            } else {
                if ($index_page === true) {
                    $link .= "href=\"" . $CI->config->site_url($href) . "\" ";
                } else {
                    $link .= "href=\"" . $CI->config->slash_item("base_url") . $href . "\" ";
                }
            }
            $link .= "rel=\"" . $rel . "\" type=\"" . $type . "\" ";
            if ($media !== "") {
                $link .= "media=\"" . $media . "\" ";
            }
            if ($title !== "") {
                $link .= "title=\"" . $title . "\" ";
            }
        }
        return $link . "/>\n";
    }
}
if (!function_exists("meta")) {
    /**
     * Generates meta tags from an array of key/values
     *
     * @param	array
     * @param	string
     * @param	string
     * @param	string
     * @return	string
     */
    function meta($name = "", $content = "", $type = "name", $newline = "\n")
    {
        if (!is_array($name)) {
            $name = array(array("name" => $name, "content" => $content, "type" => $type, "newline" => $newline));
        } else {
            if (isset($name["name"])) {
                $name = array($name);
            }
        }
        $allowed_types = array("charset", "http-equiv", "name", "property");
        $str = "";
        foreach ($name as $meta) {
            if (isset($meta["type"])) {
                if ($meta["type"] === "equiv") {
                    $meta["type"] === "http-equiv";
                } else {
                    if (!in_array($meta["type"], $allowed_types, true)) {
                        $meta["type"] = "name";
                    }
                }
            }
            $type = isset($meta["type"]) ? $meta["type"] : "name";
            $name = isset($meta["name"]) ? $meta["name"] : "";
            $content = isset($meta["content"]) ? $meta["content"] : "";
            $newline = isset($meta["newline"]) ? $meta["newline"] : "\n";
            $str .= "<meta " . $type . "=\"" . $name . ($type === "charset" ? "" : "\" content=\"" . $content) . "\" />" . $newline;
        }
        return $str;
    }
}

?>