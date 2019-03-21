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
defined("BASEPATH") or exit("No direct script access.");
class Embed extends CI_Controller
{
    public function index()
    {
        $SID = $this->input->get_post("SID");
        if ($this->config->item("embed_session_key") && (!isset($_SESSION["_EMBED_KEY_"]) || $_SESSION["_EMBED_KEY_"] != $this->config->item("embed_session_key"))) {
            exit("Access Denied!");
        }
        $this->load->library("smartyview");
        if (!empty($SID)) {
            $this->smartyview->assign("SID", $this->input->get_post("SID"));
            $this->smartyview->assign("embed", true);
            $this->smartyview->display("dashboard/standalone.tpl");
        } else {
            $OBJID = $this->input->get_post("OBJID");
            $parameters = $this->input->get();
            unset($parameters["OBJID"]);
            unset($parameters["module"]);
            unset($parameters["action"]);
            if (!empty($OBJID)) {
                $this->_dislay_app_shared($OBJID);
            } else {
                $token = $this->input->get("token");
                if (!empty($token)) {
                    require APPPATH . "third_party/php-jwt/vendor/autoload.php";
                    $key = $this->config->item("app_access_key");
                    $decoded = Firebase\JWT\JWT::decode(urldecode($token), $key, array("HS256"));
                    $creatorid = $decoded->creatorid;
                    $appid = $decoded->appid;
                    $date = $decoded->date;
                    $ttl = $this->config->item("ttl_access_url");
                    if ($ttl != false && is_numeric($ttl) && $ttl < time() - $date) {
                        show_error("URL expired");
                        return NULL;
                    }
                    if (!empty($creatorid)) {
                        $user_config = "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "config.php";
                        if (file_exists($user_config)) {
                            include $user_config;
                            if (isset($config) && is_array($config)) {
                                $loaded_config =& get_config();
                                $loaded_config = array_merge($loaded_config, $config);
                            }
                        }
                    }
                    $this->smartyview->assign("token", $token);
                    $query = $this->db->query("select css from dc_customcss where creatorid=?", array($creatorid));
                    if (0 < $query->num_rows()) {
                        $row = $query->row_array();
                        $this->smartyview->assign("custom_css", $row["css"]);
                    }
                    $this->smartyview->assign("parameters", $parameters);
                    $this->smartyview->assign("embed", true);
                    $this->smartyview->assign("chart_theme", $this->config->item("chart_theme"));
                    $this->smartyview->display("new/frame.standalone.token.tpl");
                } else {
                    show_error("Application Not Found, Forgot to check the embed checkbox and saved the status?");
                }
            }
        }
    }
    public function app($OBJID)
    {
        $this->_dislay_app_shared($OBJID);
    }
    public function ma($appid)
    {
        $this->load->library("smartyview");
        $parameters = $this->input->get();
        unset($parameters["appid"]);
        unset($parameters["module"]);
        unset($parameters["action"]);
        $query = $this->db->query("select appid, type, options, creatorid from dc_app where appid=?", array($appid));
        if (0 < $query->num_rows()) {
            $creatorid = $query->row()->creatorid;
            if (!empty($creatorid)) {
                $user_config = "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "config.php";
                if (file_exists($user_config)) {
                    include $user_config;
                    if (isset($config) && is_array($config)) {
                        $loaded_config =& get_config();
                        $loaded_config = array_merge($loaded_config, $config);
                    }
                }
            }
            $apptype = $query->row()->type;
            if ($apptype == "htmlreport") {
                $options = json_decode($query->row()->options, true);
                $template_name = isset($options["html_template"]) ? $options["html_template"] : false;
                if (!empty($template_name)) {
                    $container = html_template_container($template_name);
                    if (!empty($container)) {
                        $this->smartyview->assign("embed_container", $container);
                    }
                }
            }
            $this->smartyview->assign("appid", $appid);
            $query = $this->db->query("select css from dc_customcss where creatorid=?", array($creatorid));
            if (0 < $query->num_rows()) {
                $row = $query->row_array();
                $this->smartyview->assign("custom_css", $row["css"]);
            }
            $this->smartyview->assign("parameters", $parameters);
            $this->smartyview->assign("embed", true);
            $this->smartyview->assign("chart_theme", $this->config->item("chart_theme"));
            $this->smartyview->display("mobile/mobile.template.tpl");
        }
    }
    public function _dislay_app_shared($OBJID)
    {
        $this->load->library("smartyview");
        $parameters = $this->input->get();
        unset($parameters["OBJID"]);
        unset($parameters["module"]);
        unset($parameters["action"]);
        $query = $this->db->query("select appid, type, options, creatorid from dc_app where embedcode=?", array($OBJID));
        if (0 < $query->num_rows()) {
            $creatorid = $query->row()->creatorid;
            if (!empty($creatorid)) {
                $user_config = "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "config.php";
                if (file_exists($user_config)) {
                    include $user_config;
                    if (isset($config) && is_array($config)) {
                        $loaded_config =& get_config();
                        $loaded_config = array_merge($loaded_config, $config);
                    }
                }
            }
            $apptype = $query->row()->type;
            if ($apptype == "htmlreport") {
                $options = json_decode($query->row()->options, true);
                $template_name = isset($options["html_template"]) ? $options["html_template"] : false;
                if (!empty($template_name)) {
                    $container = html_template_container($template_name);
                    if (!empty($container)) {
                        $this->smartyview->assign("embed_container", $container);
                    }
                }
            } else {
                if ($apptype == "freeboard") {
                    $appid = $query->row()->appid;
                    $this->smartyview->assign("appid", $appid);
                    $this->smartyview->assign("freeboard_mode", "run");
                    $this->smartyview->display("freeboard/freeboard.tpl");
                    return NULL;
                }
            }
            $this->smartyview->assign("OBJID", $OBJID);
            $query = $this->db->query("select css from dc_customcss where creatorid=?", array($creatorid));
            if (0 < $query->num_rows()) {
                $row = $query->row_array();
                $this->smartyview->assign("custom_css", $row["css"]);
            }
            $this->smartyview->assign("parameters", $parameters);
            $this->smartyview->assign("embed", true);
            $this->smartyview->assign("chart_theme", $this->config->item("chart_theme"));
            $this->smartyview->display("new/frame.standalone.tpl");
        }
    }
}

?>