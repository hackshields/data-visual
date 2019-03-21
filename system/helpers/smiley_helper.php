<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (!function_exists("smiley_js")) {
    /**
     * Smiley Javascript
     *
     * Returns the javascript required for the smiley insertion.  Optionally takes
     * an array of aliases to loosely couple the smiley array to the view.
     *
     * @param	mixed	alias name or array of alias->field_id pairs
     * @param	string	field_id if alias name was passed in
     * @param	bool
     * @return	array
     */
    function smiley_js($alias = "", $field_id = "", $inline = true)
    {
        static $do_setup = true;
        $r = "";
        if ($alias !== "" && !is_array($alias)) {
            $alias = array($alias => $field_id);
        }
        if ($do_setup === true) {
            $do_setup = false;
            $m = array();
            if (is_array($alias)) {
                foreach ($alias as $name => $id) {
                    $m[] = "\"" . $name . "\" : \"" . $id . "\"";
                }
            }
            $m = "{" . implode(",", $m) . "}";
            $r .= "\t\t\tvar smiley_map = " . $m . ";\r\n\r\n\t\t\tfunction insert_smiley(smiley, field_id) {\r\n\t\t\t\tvar el = document.getElementById(field_id), newStart;\r\n\r\n\t\t\t\tif ( ! el && smiley_map[field_id]) {\r\n\t\t\t\t\tel = document.getElementById(smiley_map[field_id]);\r\n\r\n\t\t\t\t\tif ( ! el)\r\n\t\t\t\t\t\treturn false;\r\n\t\t\t\t}\r\n\r\n\t\t\t\tel.focus();\r\n\t\t\t\tsmiley = \" \" + smiley;\r\n\r\n\t\t\t\tif ('selectionStart' in el) {\r\n\t\t\t\t\tnewStart = el.selectionStart + smiley.length;\r\n\r\n\t\t\t\t\tel.value = el.value.substr(0, el.selectionStart) +\r\n\t\t\t\t\t\t\t\t\tsmiley +\r\n\t\t\t\t\t\t\t\t\tel.value.substr(el.selectionEnd, el.value.length);\r\n\t\t\t\t\tel.setSelectionRange(newStart, newStart);\r\n\t\t\t\t}\r\n\t\t\t\telse if (document.selection) {\r\n\t\t\t\t\tdocument.selection.createRange().text = smiley;\r\n\t\t\t\t}\r\n\t\t\t}";
        } else {
            if (is_array($alias)) {
                foreach ($alias as $name => $id) {
                    $r .= "smiley_map[\"" . $name . "\"] = \"" . $id . "\";\n";
                }
            }
        }
        return $inline ? "<script type=\"text/javascript\" charset=\"utf-8\">/*<![CDATA[ */" . $r . "// ]]></script>" : $r;
    }
}
if (!function_exists("get_clickable_smileys")) {
    /**
     * Get Clickable Smileys
     *
     * Returns an array of image tag links that can be clicked to be inserted
     * into a form field.
     *
     * @param	string	the URL to the folder containing the smiley images
     * @param	array
     * @return	array
     */
    function get_clickable_smileys($image_url, $alias = "")
    {
        if (is_array($alias)) {
            $smileys = $alias;
        } else {
            if (false === ($smileys = _get_smiley_array())) {
                return false;
            }
        }
        $image_url = rtrim($image_url, "/") . "/";
        $used = array();
        foreach ($smileys as $key => $val) {
            if (isset($used[$smileys[$key][0]])) {
                continue;
            }
            $link[] = "<a href=\"javascript:void(0);\" onclick=\"insert_smiley('" . $key . "', '" . $alias . "')\"><img src=\"" . $image_url . $smileys[$key][0] . "\" alt=\"" . $smileys[$key][3] . "\" style=\"width: " . $smileys[$key][1] . "; height: " . $smileys[$key][2] . "; border: 0;\" /></a>";
            $used[$smileys[$key][0]] = true;
        }
        return $link;
    }
}
if (!function_exists("parse_smileys")) {
    /**
     * Parse Smileys
     *
     * Takes a string as input and swaps any contained smileys for the actual image
     *
     * @param	string	the text to be parsed
     * @param	string	the URL to the folder containing the smiley images
     * @param	array
     * @return	string
     */
    function parse_smileys($str = "", $image_url = "", $smileys = NULL)
    {
        if ($image_url === "" || !is_array($smileys) && false === ($smileys = _get_smiley_array())) {
            return $str;
        }
        $image_url = rtrim($image_url, "/") . "/";
        foreach ($smileys as $key => $val) {
            $str = str_replace($key, "<img src=\"" . $image_url . $smileys[$key][0] . "\" alt=\"" . $smileys[$key][3] . "\" style=\"width: " . $smileys[$key][1] . "; height: " . $smileys[$key][2] . "; border: 0;\" />", $str);
        }
        return $str;
    }
}
if (!function_exists("_get_smiley_array")) {
    /**
     * Get Smiley Array
     *
     * Fetches the config/smiley.php file
     *
     * @return	mixed
     */
    function _get_smiley_array()
    {
        static $_smileys = NULL;
        if (!is_array($_smileys)) {
            if (file_exists(APPPATH . "config/smileys.php")) {
                include APPPATH . "config/smileys.php";
            }
            if (file_exists(APPPATH . "config/" . ENVIRONMENT . "/smileys.php")) {
                include APPPATH . "config/" . ENVIRONMENT . "/smileys.php";
            }
            if (empty($smileys) || !is_array($smileys)) {
                $_smileys = array();
                return false;
            }
            $_smileys = $smileys;
        }
        return $_smileys;
    }
}

?>