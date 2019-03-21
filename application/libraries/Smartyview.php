<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require_once "Smarty/libs/Smarty.class.php";
/**
* @file system/application/libraries/SmartyView.php
*/
class Smartyview extends Smarty
{
    public function __construct()
    {
        parent::__construct();
        $config =& get_config();
        $CI =& get_instance();
        $creatorid = $CI->session->userdata("login_creatorid");
        if ($creatorid && !empty($creatorid)) {
            $this->setTemplateDir(USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates");
        }
        $this->addTemplateDir(array(FCPATH . "config" . DIRECTORY_SEPARATOR . "templates", APPPATH . "views"));
        $this->addPluginsDir(array(FCPATH . "plugins" . DIRECTORY_SEPARATOR . "script"));
        $this->setCompileDir(USERPATH . "cache");
        $this->setCacheDir(USERPATH . "cache");
        $this->config_vars["title"] = $config["df.title"];
        $this->config_vars["base_url"] = $config["base_url"];
        $this->config_vars["site_language"] = $config["language"];
        $this->config_vars["s_base"] = $config["df.static"];
        $assets_url = isset($config["assets_base_url"]) ? $config["assets_base_url"] : $config["df.static"];
        $this->config_vars["assets_base_url"] = $assets_url;
        $product_assets_url = isset($config["product_assets_url"]) ? $config["product_assets_url"] : $config["df.static"];
        if (!empty($product_assets_url)) {
            $this->config_vars["product_assets_url"] = $product_assets_url;
        }
        $use_assets_cdn = $this->config_vars["use_assets_cdn"];
        if ($use_assets_cdn == true) {
            $this->assign("use_assets_cdn", true);
        }
        $production = $config["production"];
        if ($production == "1") {
            $this->config_vars["production"] = true;
        } else {
            $this->config_vars["production"] = false;
        }
        if ($config["enable_onlinesupport"]) {
            $this->config_vars["enable_onlinesupport"] = true;
        }
        if (isset($config["disable_analytics"]) && $config["disable_analytics"]) {
            $this->config_vars["disable_analytics"] = true;
        }
        if (!empty($config["chart_theme"])) {
            $this->assign("chart_theme", $config["chart_theme"]);
        }
        $version = isset($config["version"]) ? $config["version"] : "6.0.0";
        $buildid = isset($config["buildid"]) ? $config["buildid"] : "20161001";
        $this->config_vars["build_version"] = (string) $version . "(" . $buildid . ")";
        $this->config_vars["buildid"] = $buildid;
        if (isset($config["customer_additonal_css"]) && $config["customer_additonal_css"]) {
            $this->assign("customer_additional_css", $config["customer_additonal_css"]);
        }
        if (isset($config["form_date_format"]) && !empty($config["form_date_format"])) {
            $this->assign("form_date_format", $config["form_date_format"]);
        }
        if (isset($config["form_datetime_format"]) && !empty($config["form_datetime_format"])) {
            $this->assign("form_datetime_format", $config["form_datetime_format"]);
        }
        if (isset($config["template_variables"]) && !empty($config["template_variables"])) {
            foreach ($config["template_variables"] as $k => $v) {
                $this->assign($k, $v);
            }
        }
        if ($creatorid && !empty($creatorid)) {
            $this->assign("_creatorid_", $creatorid);
        }
        $this->left_delimiter = "[{";
        $this->right_delimiter = "}]";
        $this->error_reporting = 1 || 2;
        $this->compile_check = true;
        if (function_exists("site_url")) {
            $this->assign("site_url", site_url());
        }
    }
}

?>