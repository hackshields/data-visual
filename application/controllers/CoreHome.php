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
class CoreHome extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $username = $this->session->userdata("login_username");
        $permission = $this->session->userdata("login_permission");
        $userid = $this->session->userdata("login_userid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_easure_custom_files($creatorid);
        $this->_load_db_config($creatorid);
        $this->_check_and_set_default_conn($creatorid);
        $query = $this->db->query("select date from dc_uservisitlog where userid=? order by date desc limit 1", array($userid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $userTimeZone = $this->session->userdata("login_timezone");
            $last_visitdate = date("Y/m/d H:i:s", $row["date"]);
            if (!empty($userTimeZone)) {
                $last_visitdate = $this->_convert_to_user_date($last_visitdate, "Y/m/d H:i:s", $userTimeZone);
            }
            $this->smartyview->assign("login_lastvisitdate", $last_visitdate);
        }
        $hasLogLoginEvent = $this->session->userdata("log_loginevent");
        if (!$hasLogLoginEvent && $userid) {
            $this->log_event($userid, "Login", "Login Success");
            $this->session->set_userdata("log_loginevent", "1");
        }
        $this->smartyview->assign("login_permission", $permission);
        $pos = strrpos($username, ".", -1);
        if ($pos != false) {
            $username = substr($username, $pos + 1);
        }
        $this->smartyview->assign("login_username", $username);
        $this->smartyview->assign("login_avatar", $this->_get_user_avatar());
        $enable_conn_context_selector = false;
        if ($permission == 0 || $permission == 1) {
            $enable_conn_context_selector = $this->config->item("enable_conn_context_selector");
        }
        $this->smartyview->assign("enable_conn_context_selector", $enable_conn_context_selector);
        $this->_assign_menus($creatorid);
        $query = $this->db->query("select css from dc_customcss where creatorid=?", array($creatorid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $this->smartyview->assign("custom_css", $row["css"]);
        }
        $query = $this->db->query("select value from dc_user_options where creatorid=? and name=?", array($creatorid, "customlogo"));
        $customlogo = false;
        if (0 < $query->num_rows()) {
            $customlogo = $query->row()->value;
        }
        if (!$customlogo || empty($customlogo)) {
            $logo_settings = $this->config->item("login_logo_settings");
            if ($logo_settings && isset($logo_settings["img"])) {
                $customlogo = "<img src=\"" . $logo_settings["img"] . "\"/>";
            }
        }
        if ($customlogo && !empty($customlogo)) {
            $this->smartyview->assign("customlogo", $customlogo);
        }
        $query = $this->db->query("select value from dc_user_options where creatorid=? and name=?", array($creatorid, "clientcode"));
        if ($query->num_rows() == 0) {
            $clientcode = dp_gi();
            $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => "clientcode", "type" => "string", "value" => $clientcode));
            $this->session->set_userdata("_CLIENT_CODE_", $clientcode);
        } else {
            $clientcode = $query->row()->value;
            $this->session->set_userdata("_CLIENT_CODE_", $clientcode);
        }
        $this->smartyview->assign("userid", $userid);
        $this->smartyview->assign("chart_theme", $this->config->item("chart_theme"));
        $is_expired = $this->session->userdata("_EXPIRED_");
        if ($is_expired) {
            $this->smartyview->assign("expired", $is_expired);
        }
        $data_category_name = $this->config->item("default_data_category_name");
        if (!empty($data_category_name)) {
            $this->smartyview->assign("default_data_category_name", $data_category_name);
        }
        $this->_assign_history_apps();
        $this->_assign_favorite_apps();
        $enable_marketplace = $this->config->item("enable_marketplace");
        if ($enable_marketplace) {
            $this->smartyview->assign("enable_marketplace", $enable_marketplace);
        }
        $is_master = $this->config->item("dbface_master_host");
        if (!empty($is_master) && $is_master) {
            $this->smartyview->assign("dbface_master_host", true);
        }
        $self_host = $this->config->item("self_host");
        $license_email = $this->_get_user_option($creatorid, "license_email");
        if (empty($license_email) && $self_host) {
            $this->smartyview->assign("show_purchse_btn", true);
        }
        $this->smartyview->assign("self_host", $self_host ? 1 : 0);
        $this->smartyview->display("new/frame.tpl");
    }
    public function loadmenus()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_assign_menus($creatorid, false);
        $this->smartyview->display("new/menu.new.applist.tpl");
    }
    public function _convert_to_user_date($date, $format = "n/j/Y g:i A", $userTimeZone = "America/Los_Angeles")
    {
        try {
            $serverTimeZone = date_default_timezone_get();
            $dateTime = new DateTime($date, new DateTimeZone($serverTimeZone));
            $dateTime->setTimezone(new DateTimeZone($userTimeZone));
            return $dateTime->format($format);
        } catch (Exception $e) {
            return "";
        }
    }
    public function _assign_menus($creatorid, $onlyapp = false)
    {
        $this->smartyview->assign("hide_cloud_code_menu", $this->config->item("hide_cloud_code_menu"));
        $this->smartyview->assign("enable_gravatar", $this->config->item("enable_gravatar"));
        if (!$onlyapp) {
            $enabled_datamodule = $this->_get_enable_datamodule($creatorid);
            $default_connid = $this->session->userdata("_default_connid_");
            if (0 < $default_connid) {
                if ($enabled_datamodule) {
                    $tablelist = $this->_get_conn_tablenames($creatorid, $default_connid);
                    $this->smartyview->assign("tables", $tablelist);
                }
                $this->smartyview->assign("connid", $default_connid);
            }
            $conns = $this->_get_simple_connections($creatorid);
            $this->smartyview->assign("conns", $conns);
        }
        $categories = $this->_get_categories($creatorid);
        $category_by_key = array();
        $category_icons = array();
        $category_sorts = array();
        foreach ($categories as $category) {
            $category_by_key[$category["categoryid"]] = $category["name"];
            $category_icons[$category["name"]] = $category["icon"];
            $category_sorts[$category["name"]] = $category["sort"];
        }
        $permission = $this->session->userdata("login_permission");
        if ($permission == 9) {
            $apps = $this->_get_user_apps($creatorid, $this->session->userdata("login_userid"));
        } else {
            $apps = $this->_get_apps_by_status($creatorid, "publish");
        }
        $categoryapps = array();
        foreach ($apps as $app) {
            $categoryid = $app["categoryid"];
            if ($categoryid == 65535 || empty($app["name"])) {
                continue;
            }
            $categoryname = $this->config->item("default_category_name");
            if (array_key_exists($categoryid, $category_by_key)) {
                $categoryname = $category_by_key[$categoryid];
            }
            if (!array_key_exists($categoryname, $categoryapps)) {
                $categoryapps[$categoryname] = array();
            }
            $app["categoryname"] = $categoryname;
            $categoryapps[$categoryname][] = $app;
        }
        uksort($categoryapps, function ($a, $b) use($category_sorts) {
            return $category_sorts[$a] - $category_sorts[$b];
        });
        if (function_exists("sort_sidemenu")) {
            $categoryapps = sort_sidemenu($categoryapps);
        }
        $expired = $this->session->userdata("_EXPIRED_");
        if (!$expired) {
            $this->smartyview->assign("licensed", true);
        }
        $this->smartyview->assign("category_icons", $category_icons);
        $this->smartyview->assign("categoryapps", $categoryapps);
    }
    public function load_custom_js()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select js from dc_customjs where creatorid=?", array($creatorid));
        $js = "";
        if (0 < $query->num_rows()) {
            $js = $query->row()->js;
        }
        echo $js;
    }
    public function get_lang()
    {
        $lang = $this->input->get_post("lang");
        $this->lang->load("message", $lang);
        $result = $this->lang->language;
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    public function check_msg()
    {
        $this->load->library("smartyview");
        $self_host = $this->config->item("self_host");
        if ($self_host) {
            $clientcode = $this->session->userdata("_CLIENT_CODE_");
            if (empty($clientcode)) {
                $query = $this->db->query("select value from dc_user_options where name=? limit 1", array("clientcode"));
                if ($query->num_rows() == 1) {
                    $clientcode = $query->row()->value;
                }
            }
            $this->_download_lc_files();
            if (!empty($clientcode)) {
                $result = get_one_parse_object("UserMessage", array("clientcode" => $clientcode));
                if ($result) {
                    $message = $result->has("message") ? $result->get("message") : false;
                    $btn_title = $result->has("btn_title") ? $result->get("btn_title") : false;
                    $btn_link = $result->has("btn_link") ? $result->get("btn_link") : false;
                    if ($message) {
                        $this->smartyview->assign("message", $message);
                        if ($btn_title) {
                            $this->smartyview->assign("action_btn_title", $btn_title);
                        }
                        if ($btn_link) {
                            $this->smartyview->assign("action_url", $btn_link);
                        }
                        $this->smartyview->assign("dismiss_delay", -1);
                        $this->smartyview->display("inc/notification.message.tpl");
                        return NULL;
                    }
                }
            }
        }
        $check_update = $this->config->item("check_update");
        $dbface_master_host = $this->config->item("dbface_master_host");
        if ($dbface_master_host) {
            return NULL;
        }
        $require_upgrade = $this->session->userdata("__require_upgrade__");
        if (!empty($require_upgrade) && $require_upgrade) {
            $message = "Your annual Upgrade and Support plan is about to expired, would you like to renew?";
            $this->smartyview->assign("message", $message);
            $this->smartyview->assign("action_btn_title", "Renew Support and Upgrade");
            $this->smartyview->assign("internal_action_url", "module=Account&action=editprofile&tab=userplan");
            $this->smartyview->assign("dismiss_delay", -1);
            $this->smartyview->display("inc/notification.message.tpl");
        } else {
            if ($check_update) {
                $master_host = $this->config->item("dbface_master");
                if (empty($master_host)) {
                    $master_host = "dashboard.dbface.com";
                }
                $this->load->library("httpClient", array("host" => $master_host));
                $result = $this->httpclient->post("/version", array());
                if ($result) {
                    $result = $this->httpclient->getContent();
                    if (!empty($result)) {
                        $remote_info = json_decode($result, true);
                        $remote_version = isset($remote_info["version"]) ? $remote_info["version"] : "";
                        $remote_buildid = isset($remote_info["buildid"]) ? $remote_info["buildid"] : "";
                        $version = $this->config->item("version");
                        $buildid = $this->config->item("buildid");
                        if ($remote_version != "" && $version != $remote_version) {
                            $require_update = $this->session->userdata("__require_upgrade__");
                            if ($require_update) {
                                $pass_days = $this->session->userdata("__require_upgrade__");
                                $message = "<strong>New version v" . $remote_version . "(" . $remote_buildid . ") available.</strong><br/> ";
                                if (365 < $pass_days) {
                                    $message .= "Your annual Upgrade and Support plan is about to expired, would you like to renew?";
                                } else {
                                    $message .= "<span class='text-danger'>Your annual Upgrade and Support plan was expired, would you like to renew now?</span>";
                                }
                                $this->smartyview->assign("message", $message);
                                $this->smartyview->assign("action_btn_title", "Renew Support and Upgrade");
                                $this->smartyview->assign("internal_action_url", "module=Account&action=editprofile&tab=userplan");
                                $this->smartyview->assign("dismiss_delay", -1);
                                $this->smartyview->display("inc/notification.message.tpl");
                            } else {
                                if (intval($buildid) < intval($remote_buildid)) {
                                    $this->smartyview->assign("message", "<strong>New version v" . $remote_version . "(" . $remote_buildid . ") available.</strong><br/> ");
                                    $this->smartyview->assign("action_btn_title", "Upgrade");
                                    $this->smartyview->assign("action_url", "https://www.dbface.com/download-dbface");
                                    $this->smartyview->assign("dismiss_delay", 10000);
                                    $this->smartyview->display("inc/notification.message.tpl");
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function get_about_modal()
    {
        $this->load->library("smartyview");
        $version = $this->config->item("version");
        $buildid = $this->config->item("buildid");
        $this->smartyview->assign("version", $version);
        $this->smartyview->assign("buildid", $buildid);
        $query = $this->db->query("select value from dc_user_options where name=? limit 1", array("license_email"));
        $email = "";
        if ($query->num_rows() == 1) {
            $email = $query->row()->value;
            $this->smartyview->assign("licensed_email", $email);
        }
        $master_host = $this->config->item("dbface_master");
        if (empty($master_host)) {
            $master_host = "dashboard.dbface.com";
        }
        $this->load->library("httpClient", array("host" => $master_host));
        $result = $this->httpclient->post("/version", array());
        if ($result) {
            $result = $this->httpclient->getContent();
            if (!empty($result)) {
                $remote_info = json_decode($result, true);
                $remote_version = isset($remote_info["version"]) ? $remote_info["version"] : "";
                $remote_buildid = isset($remote_info["buildid"]) ? $remote_info["buildid"] : "";
                $this->smartyview->assign("remote_version", $remote_version);
                $this->smartyview->assign("remote_buildid", $remote_buildid);
            }
        }
        $this->smartyview->display("inc/about_dialog.tpl");
    }
    public function log_client()
    {
        $message_object = urldecode($this->input->post("o"));
        dbface_log("error", $message_object);
        if ($this->config->item("self_host") && $this->config->item("enable_error_report")) {
            $login_email = $this->session->userdata("login_email");
            $clientcode = $this->session->userdata("_CLIENT_CODE_");
            if (empty($clientcode)) {
                $clientcode = "";
            }
            save_parse_object("ErrorReport", array("clientcode" => $clientcode, "email" => $login_email, "error" => $message_object));
        }
    }
    public function _assign_favorite_apps()
    {
        $userid = $this->session->userdata("login_userid");
        if (!empty($userid)) {
            $limit = $this->config->item("favorite_app_num");
            if (!$limit) {
                $limit = 10;
            }
            $query = $this->db->select("dc_app.appid, dc_app.name")->from("dc_user_app_favorite")->join("dc_app", "dc_user_app_favorite.appid=dc_app.appid")->where(array("dc_user_app_favorite.userid" => $userid))->order_by("dc_user_app_favorite.date", "desc")->limit($limit)->distinct()->get();
            if ($query && 0 < $query->num_rows()) {
                $this->smartyview->assign("favorite_apps", $query->result_array());
            }
        }
    }
    public function _assign_history_apps()
    {
        $userid = $this->session->userdata("login_userid");
        if (!empty($userid)) {
            $limit = $this->config->item("history_app_num");
            if (!$limit) {
                $limit = 10;
            }
            $query = $this->db->select("appid, message")->where(array("userid" => $userid, "type" => "visit_app"))->where("message !=", "")->order_by("date", "desc")->limit($limit)->distinct()->get("dc_uservisitlog");
            if ($query && 0 < $query->num_rows()) {
                $this->smartyview->assign("history_apps", $query->result_array());
            }
        }
    }
    public function get_history_apps()
    {
        $this->load->library("smartyview");
        $this->_assign_history_apps();
        $this->_assign_favorite_apps();
        $this->smartyview->display("inc/history.apps.tpl");
    }
    public function get_favorites()
    {
        $userid = $this->session->userdata("login_userid");
        $query = $this->db->select("appid")->where("userid", $userid)->get("dc_user_app_favorite");
        $apps = array();
        foreach ($query->result_array() as $app) {
            $apps[] = $app["appid"];
        }
        echo json_encode(array("data" => $apps));
    }
    public function save_favorite()
    {
        $appid = $this->input->post("appid");
        $userid = $this->session->userdata("login_userid");
        if (empty($appid) || empty($userid)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->select("1")->where(array("userid" => $userid, "appid" => $appid))->get("dc_user_app_favorite");
            $in_favorite = 0;
            if (0 < $query->num_rows()) {
                $this->db->delete("dc_user_app_favorite", array("userid" => $userid, "appid" => $appid));
                $in_favorite = 0;
            } else {
                $this->db->insert("dc_user_app_favorite", array("userid" => $userid, "appid" => $appid, "date" => time()));
                $in_favorite = 1;
            }
            echo json_encode(array("status" => 1, "appid" => $appid, "infavorite" => $in_favorite));
        }
    }
    public function load_sys_log()
    {
        $this->load->library("smartyview");
        $filename = "app-" . date("Y-m-d") . ".log";
        $creatorid = $this->session->userdata("login_creatorid");
        $log_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . $filename;
        $data = array();
        if (file_exists($log_path)) {
            $filecontent = file_get_contents($log_path);
            $data_tmp = preg_split("/\r\n|\n|\r/", $filecontent);
            foreach ($data_tmp as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $line = str_replace("[]", "", $line);
                    $icon = "pficon pficon-info";
                    if (strpos($line, " app.ERROR:") !== false) {
                        $icon = "pficon pficon-error-circle-o";
                        $line = str_replace(" app.ERROR:", "", $line);
                    } else {
                        if (strpos($line, " app.DEBUG:") !== false) {
                            $icon = "pficon pficon-warning-triangle-o";
                            $line = str_replace(" app.DEBUG:", "", $line);
                        } else {
                            if (strpos($line, " app.INFO:") !== false) {
                                $line = str_replace(" app.INFO:", "", $line);
                            }
                        }
                    }
                    $data[] = array("content" => $line, "icon" => $icon);
                }
            }
            $data = array_reverse($data);
        }
        $self_host = $this->config->item("self_host");
        if ($self_host) {
            $this->smartyview->assign("enable_ide", true);
        }
        $system_logs_data = array();
        if ($self_host) {
            $filename = "log-" . date("Y-m-d") . ".log";
            $log_path = USERPATH . "logs" . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($log_path)) {
                $filecontent = file_get_contents($log_path);
                $data_tmp = preg_split("/\r\n|\n|\r/", $filecontent);
                foreach ($data_tmp as $line) {
                    $line = trim($line);
                    if (!empty($line)) {
                        $line = str_replace("[]", "", $line);
                        $icon = "pficon pficon-info";
                        if (strpos($line, " app.ERROR:") !== false) {
                            $icon = "pficon pficon-error-circle-o";
                            $line = str_replace(" app.ERROR:", "", $line);
                        } else {
                            if (strpos($line, " app.DEBUG:") !== false) {
                                $icon = "pficon pficon-warning-triangle-o";
                                $line = str_replace(" app.DEBUG:", "", $line);
                            } else {
                                if (strpos($line, " app.INFO:") !== false) {
                                    $line = str_replace(" app.INFO:", "", $line);
                                }
                            }
                        }
                        $system_logs_data[] = array("content" => $line, "icon" => $icon);
                    }
                }
                $system_logs_data = array_reverse($system_logs_data);
            }
        }
        if (!empty($data)) {
            $this->smartyview->assign("logs", $data);
        }
        if (!empty($system_logs_data)) {
            $this->smartyview->assign("system_logs", $system_logs_data);
        }
        $this->smartyview->display("inc/inc.systemlog.tpl");
    }
    /**
     * index page for
     */
    public function managedIndex()
    {
        $userid = $this->session->userdata("login_userid");
        $username = $this->session->userdata("login_username");
        $this->load->library("smartyview");
        $query = $this->db->where("userid", $userid)->get("dc_user_premium");
        if (0 < $query->num_rows()) {
            $info = $query->row_array();
            $container_id = $info["container_id"];
            $appurl = $info["full_url"];
            $customdomain = $info["customdomain"];
            $this->smartyview->assign("container_id", $container_id);
            $this->smartyview->assign("app_url", $appurl);
            $this->smartyview->assign("slug", $info["slug"]);
            $this->smartyview->assign("customdomain", $customdomain);
            $expired = $info["expiredate"] <= time();
            $docker_remote_base_url = $this->config->item("docker_remote_base_url");
            $this->smartyview->assign("docker_remote_base_url", $docker_remote_base_url);
            $this->smartyview->assign("expired", $expired);
            $this->smartyview->assign("login_username", $username);
        }
        $this->smartyview->display("frame.managed.tpl");
    }
}

?>