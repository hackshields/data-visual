<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
$config["twig_conf"]["template_file_ext"] = ".twig";
$config["twig_conf"]["delimiters"] = array("tag_comment" => array("{#", "#}"), "tag_block" => array("{%", "%}"), "tag_variable" => array("[{", "}]"));
$config["twig_conf"]["environment"]["cache"] = false;
$config["twig_conf"]["environment"]["debug"] = false;
$config["twig_conf"]["environment"]["charset"] = "utf-8";
$config["twig_conf"]["environment"]["base_template_class"] = "Twig_Template";
$config["twig_conf"]["environment"]["auto_reload"] = NULL;
$config["twig_conf"]["environment"]["strict_variables"] = false;
$config["twig_conf"]["environment"]["autoescape"] = false;
$config["twig_conf"]["environment"]["optimizations"] = -1;
$config["twig_conf"]["twig_cache_dir"] = APPPATH . "cache/twig/";
$config["twig_conf"]["themes_base_dir"] = "themes/";
$config["twig_conf"]["include_apppath"] = true;
$config["twig_conf"]["default_theme"] = "";
$config["twig_conf"]["default_layout"] = "index";
$config["twig_conf"]["default_template"] = "index";
$config["twig_conf"]["register_functions"] = array();
$config["twig_conf"]["register_filters"] = array();
$config["twig_conf"]["title_separator"] = " | ";

?>