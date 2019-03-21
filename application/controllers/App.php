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
class App extends BaseController
{
    private $replace_null = "NULL";
    public function index()
    {
        $this->load->library("smartyview");
        $appid = $this->input->get_post("appid");
        $OBJID = $this->input->get_post("OBJID");
        $SID = $this->input->get_post("SID");
        $pid = $this->input->get_post("pid");
        dbface_log("info", "Application Started.", array_filter(array("appid" => $appid, "OBJID" => $OBJID, "SID" => $SID, "pid" => $pid)));
        $userid = $this->session->userdata("login_userid");
        $creatorid = $this->session->userdata("login_creatorid");
        $login_permission = $this->session->userdata("login_permission");
        if ($appid == "__cloud-code") {
            $this->_execute_code_for_drilled();
        } else {
            $source = $this->input->get_post("_source_");
            if (!empty($source)) {
                $this->smartyview->assign("_source_", $source);
            }
            if (!$this->_check_user_app_permission($appid, $userid, $login_permission)) {
                if (!empty($source)) {
                    if (!$this->_check_user_app_permission($source, $userid, $login_permission)) {
                        dbface_log("error", "Application Access Denied.", array("appid" => $appid, "OBJID" => $OBJID, "SID" => $SID, "pid" => $pid));
                        $this->_display_app_error("Permission Denied", "You do not have permission to access this application.");
                        return NULL;
                    }
                } else {
                    dbface_log("error", "Application Access Denied.", array("appid" => $appid, "OBJID" => $OBJID, "SID" => $SID, "pid" => $pid));
                    $this->_display_app_error("Permission Denied", "You do not have permission to access this application.");
                    return NULL;
                }
            }
            $this->smartyview->assign("login_permission", $login_permission);
            $this->smartyview->assign("appid", $appid);
            if (!empty($pid)) {
                $this->smartyview->assign("pid", $pid);
                $this->smartyview->assign("standalone", true);
            }
            $this->smartyview->assign("enable_quick_edit", $this->is_allow_quickedit($appid));
            $expired = $this->session->userdata("_EXPIRED_");
            if ($expired) {
                $this->_display_app_error("Trial expired", "We would love to keep the applications flowing! You can start the Lite plan at any time. <a target=\"_blank\" href=\"https://www.dbface.com/pricing\">Start Subscription</a>");
            } else {
                $do_export = $this->input->get("do_export");
                $is_export_email = $this->input->get("do_export") == "email";
                if ($is_export_email) {
                    $this->_export_query_to_email($appid);
                } else {
                    if (!empty($do_export)) {
                        $this->config->set_item("cache_sql_query", 0);
                    }
                    $opened = $this->input->get_post("o") == "1";
                    if ($opened) {
                        $this->smartyview->assign("opened", true);
                    }
                    $sub = $this->input->get_post("sub") == "1";
                    if ($sub) {
                        $this->smartyview->assign("sub", true);
                    }
                    $welcome = $this->input->get_post("welcome") == "1";
                    if ($welcome) {
                        $this->smartyview->assign("welcome", true);
                    }
                    $no_footer = $this->input->get_post("nf") == 1;
                    if ($no_footer) {
                        $this->smartyview->assign("no_footer", true);
                    }
                    $use_fixed_height = $this->input->get_post("use_fixed_height");
                    $widget = $this->input->get_post("widget");
                    if ($use_fixed_height || $widget == "1") {
                        $this->smartyview->assign("use_fixed_height", 1);
                        $this->smartyview->assign("widget", 1);
                    }
                    $ptype = $this->input->get_post("ptype");
                    if (!empty($ptype)) {
                        $this->smartyview->assign("ptype", $ptype);
                    }
                    $from = $this->input->get("from");
                    if (!empty($from)) {
                        $query = $this->db->select("name")->where("appid", $from)->get("dc_app");
                        if (0 < $query->num_rows()) {
                            $fromname = $query->row()->name;
                            $this->smartyview->assign("from", $from);
                            $this->smartyview->assign("fromname", $fromname);
                        }
                    }
                    $viewname = $this->input->get_post("viewname");
                    $connid = $this->input->get_post("connid");
                    if (empty($appid) && !empty($viewname) && !empty($connid)) {
                        if (!$this->_is_admin_or_developer()) {
                            $this->_display_app_error("Access Denied", "The application might be deleted or tempory unavailable, please contact the developer.");
                            return NULL;
                        }
                        $db = $this->_get_db($creatorid, $connid);
                        $this->smartyview->assign("opened", true);
                        $this->smartyview->assign("disable_edit", true);
                        $categoryid = $this->_get_default_data_category($creatorid);
                        $appid = $this->_create_default_tableeditor_app($db, $connid, $creatorid, $categoryid, $viewname);
                    }
                    $standalone = $this->input->get_post("standalone");
                    if (!empty($standalone) && $standalone == "1") {
                        $this->smartyview->assign("standalone", true);
                    }
                    if (empty($appid) && !empty($OBJID)) {
                        $query = $this->db->query("select appid, creatorid from dc_app where embedcode=?", array($OBJID));
                        $this->smartyview->assign("standalone", true);
                        if ($query->num_rows() == 1) {
                            $row = $query->row();
                            $appid = $row->appid;
                            $this->smartyview->assign("appid", $appid);
                            if (!empty($creatorid) && $row->creatorid != $creatorid) {
                                $this->_display_app_error("Application Can not be processed", "You do not have permission to access this application.");
                                return NULL;
                            }
                            if (empty($creatorid)) {
                                $this->session->set_userdata("login_creatorid", $row->creatorid);
                                $creatorid = $row->creatorid;
                            }
                            $this->smartyview->assign("standalone", true);
                        } else {
                            $this->_display_app_error("Application Not Found", "The report might be deleted or tempory unavailable, please contact the developer.");
                            return NULL;
                        }
                    }
                    $widget = $this->input->get_post("widget") == "1";
                    $token = $this->input->get_post("token");
                    if (!empty($token) && !$widget) {
                        $is_md5 = strlen($token) == 32;
                        if (!$is_md5) {
                            require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
                            $key = $this->config->item("app_access_key");
                            $decoded = Firebase\JWT\JWT::decode(urldecode($token), $key, array("HS256"));
                            $creatorid = $decoded->creatorid;
                            $appid = $decoded->appid;
                            $date = $decoded->date;
                            $ttl = $this->config->item("ttl_access_url");
                            if ($ttl != false && is_numeric($ttl) && $ttl < time() - $date) {
                                $this->_display_app_error("Application Access URL Expired", "The report application URL was expired.");
                                return NULL;
                            }
                            $this->session->set_userdata("login_creatorid", $creatorid);
                            $this->smartyview->assign("appid", $appid);
                            $this->smartyview->assign("standalone", true);
                        }
                    }
                    if (!empty($appid) && empty($creatorid) && !empty($SID)) {
                        $query = $this->db->query("select appid, creatorid from dc_app where appid=?", array($appid));
                        $this->smartyview->assign("standalone", true);
                        $this->smartyview->assign("SID", $SID);
                        if ($query->num_rows() == 1) {
                            $row = $query->row();
                            $appid = $row->appid;
                            $this->session->set_userdata("login_creatorid", $row->creatorid);
                            $creatorid = $row->creatorid;
                        } else {
                            $this->_display_app_error("Application Not Found", "The report might be deleted or tempory unavailable, please contact the developer.");
                            return NULL;
                        }
                    }
                    if (!empty($appid) && empty($creatorid) && !empty($pid)) {
                        $query = $this->db->select("logintype, creatorid")->where(array("pid" => $pid, "active" => 1))->get("dc_product");
                        if ($query && $query->num_rows() == 1) {
                            $product = $query->row();
                            $logintype = $product->logintype;
                            if ($logintype == 1 || $logintype == 0) {
                                $creatorid = $product->creatorid;
                                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
                                if ($query && $query->num_rows() == 1) {
                                    $this->session->set_userdata("login_creatorid", $creatorid);
                                } else {
                                    $this->_display_app_error("Application Not Found", "The report might be deleted or tempory unavailable, please contact the developer.");
                                    return NULL;
                                }
                            } else {
                                $this->_display_app_error("Session Timeout", "Login required to access this application.");
                                return NULL;
                            }
                        } else {
                            $this->_display_app_error("Unknow Product", "The product might be deleted, tempory unavailable or unactivated, please contact the administrator.");
                            return NULL;
                        }
                    }
                    session_write_close();
                    $enable_filter = false;
                    $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "type" => "inline_filter_status"))->get("dc_app_options");
                    if (0 < $query->num_rows()) {
                        $enable_filter = $query->row()->value == 1;
                    }
                    if ($enable_filter) {
                        $filter = $this->input->post("__filter__");
                        if (empty($filter)) {
                            $this->db->select("dc_filter.name as name, dc_filter.type as type, dc_app_options.value as value, single");
                            $this->db->from("dc_app_options");
                            $this->db->join("dc_filter", "dc_app_options.key = dc_filter.filterid");
                            $this->db->where("dc_app_options.creatorid = dc_filter.creatorid");
                            $this->db->where(array("dc_app_options.creatorid" => $creatorid, "dc_app_options.appid" => $appid));
                            $query = $this->db->get();
                            if (0 < $query->num_rows()) {
                                $filter = array();
                                foreach ($query->result_array as $row) {
                                    $filter_name = $row["name"];
                                    $is_single = $row["single"] == 1;
                                    $value = json_decode($row["value"], true);
                                    if ($value && is_array($value)) {
                                        if ($row["type"] == 3) {
                                            $f_value = $value[0]["value"];
                                            $f_name = $value[0]["name"];
                                            $this->_parse_daterange_to_filter($filter, $f_name, $f_value);
                                        } else {
                                            if ($is_single) {
                                                $filter[$filter_name] = $value[0]["value"];
                                            } else {
                                                $d = array();
                                                foreach ($value as $a) {
                                                    $d[] = $a["value"];
                                                }
                                                $filter[$filter_name] = $d;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if (!empty($filter)) {
                            $this->_parse_daterange_string($filter);
                            $this->config->set_item("__filter__", $filter);
                        }
                    }
                    $this->smartyview->assign("enable_filter", $enable_filter);
                    $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "key" => "relative_apps"))->get("dc_app_options");
                    if (0 < $query->num_rows()) {
                        $relative_app_ids = json_decode($query->row()->value, true);
                        $query = $this->db->select("appid, name")->where_in("appid", $relative_app_ids)->get("dc_app");
                        $relative_apps = $query->result_array();
                        $this->smartyview->assign("relative_apps", $relative_apps);
                    }
                    $gen_sql = $this->_is_gen_sql();
                    $embed = $this->input->get_post("embed");
                    if (isset($embed) && !empty($embed)) {
                        $this->smartyview->assign("embed", $this->input->get_post("embed"));
                    }
                    $appinfo = $this->db->query("select * from dc_app where appid = ? and creatorid = ?", array($appid, $creatorid))->row_array();
                    if (!$appinfo) {
                        if ($gen_sql) {
                            echo 0;
                            return NULL;
                        }
                        $this->_display_app_error("Application Not Found", "The application might be deleted or tempory unavailable, please contact the developer.");
                        return NULL;
                    }
                    $apptype = $appinfo["type"];
                    $this->smartyview->assign("appname", $appinfo["name"]);
                    $apptitle = $appinfo["title"];
                    if (empty($apptitle)) {
                        $apptitle = $appinfo["name"];
                    }
                    $this->smartyview->assign("apptitle", $apptitle);
                    if (!empty($appinfo["desc"])) {
                        $this->smartyview->assign("appdesc", $appinfo["desc"]);
                    }
                    if ($apptype == "list" || $apptype == "htmlreport" || $apptype == "phpreport") {
                        $do_export = $this->input->get_post("do_export");
                        $no_cache = $this->input->get_post("nc");
                        $preview = $this->input->get_post("preview");
                        if (empty($do_export) && !$gen_sql && empty($no_cache) && empty($preview)) {
                            $cache_data = array();
                            $cache = $this->_get_cache($creatorid, "app", "app_" . $appid, $cache_data);
                            if ($cache) {
                                $datatype = $cache_data["datatype"];
                                if ($datatype == "json") {
                                    $this->load->helper("json");
                                    $this->output->set_content_type("application/json")->set_output($cache);
                                } else {
                                    $this->output->set_output($cache);
                                }
                                $this->db->close();
                                unset($this->db);
                                return NULL;
                            }
                        }
                    }
                    $at = $this->input->post("__at__");
                    if (!empty($at)) {
                        $this->smartyview->assign("at", true);
                    }
                    $preview = $this->input->get_post("preview");
                    if (!empty($preview)) {
                        $this->smartyview->assign("preview", $preview);
                    }
                    $this->_load_db_config($creatorid);
                    $this->smartyview->assign("conns", $this->_get_simple_connections($creatorid));
                    $this->_assign_app_filters($creatorid, $appid);
                    $this->config->set_item("running_appid", $appid);
                    $this->config->set_item("running_connid", $appinfo["connid"]);
                    $this->_execute_trigger($creatorid, "pre_application", array("creatorid" => $creatorid, "userid" => $userid, "appid" => $appid));
                    $_master_form_id = $this->input->post("_master_form_id");
                    if (!empty($_master_form_id)) {
                        $this->smartyview->assign("FORM_ID", $_master_form_id);
                    }
                    $_additional_query = $this->input->get_post("_additional_query");
                    if (!empty($_additional_query)) {
                        $this->smartyview->assign("_additional_query", $_additional_query);
                    }
                    try {
                        if ($apptype == "list") {
                            $this->_execute_list_app($appinfo);
                        } else {
                            if ($apptype == "mongotable") {
                                $this->_execute_mongotable_app($appinfo);
                            } else {
                                if ($apptype == "dynamodbtable") {
                                    $this->_execute_dynamodbtable_app($appinfo);
                                } else {
                                    if ($apptype == "htmlreport") {
                                        $this->_execute_htmlreport_app($appinfo);
                                    } else {
                                        if ($apptype == "chain") {
                                            $this->_execute_chain_app($appinfo);
                                        } else {
                                            if ($apptype == "phpreport") {
                                                $this->_execute_phpreport_app($appinfo);
                                            } else {
                                                if ($apptype == "dashboard") {
                                                    $this->_execute_dashboard_app($appinfo);
                                                } else {
                                                    if ($apptype == "story") {
                                                        $this->_execute_story_app($appinfo);
                                                    } else {
                                                        if ($apptype == "gallery") {
                                                            $this->_execute_gallery_app($appinfo);
                                                        } else {
                                                            if ($apptype == "freeformdisplay") {
                                                                $this->_execute_freeformdisplay_app($appinfo);
                                                            } else {
                                                                if ($apptype == "freeboard") {
                                                                    $this->_execute_freeboard_app($appinfo);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $trace = $e->getTraceAsString();
                        dbface_log("error", $e->getMessage() . ", trace: " . $trace);
                    }
                    $this->_execute_trigger($creatorid, "post_application", array("creatorid" => $creatorid, "userid" => $userid, "appid" => $appid));
                    $this->db->close();
                    unset($this->db);
                }
            }
        }
    }
    public function _is_gen_sql()
    {
        return $this->input->get_post("gen") == "1";
    }
    public function _output_gen_sql($sql)
    {
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode(array("sql" => $sql)));
    }
    public function _assign_app_options($options_str)
    {
        if (empty($options_str)) {
            return array();
        }
        $this->load->helper("json");
        $css_codes = array("table_header_css" => array("columnheader_font_fontfamily", "columnheader_font_fontSize", "columnheader_font_bold", "columnheader_font_underline", "columnheader_font_italic"), "table_header_th_css" => array("columnheader_font_align"), "table_body_css" => array("tabular_textformat_fontfamily", "tabular_cell_background", "tabular_2ndcell_background", "tabular_textformat_fontSize", "tabular_textformat_align"), "number_css" => array("numberfont_fontfamily", "numberfont_fontSize", "numberfont_bold", "numberfont_underline", "numberfont_italic", "numberfont_align", "numberfont_textcolor"), "number_label_css" => array("number_label_font_fontfamily", "number_label_font_fontSize", "number_label_font_bold", "number_label_font_underline", "number_label_font_italic", "number_label_font_align", "number_label_font_textcolor"));
        $css_values = array();
        $table_stripped = true;
        $options = json_decode($options_str, true);
        foreach ($options as $key => $value) {
            $this->smartyview->assign($key, $value);
            foreach ($css_codes as $css_key => $css_value) {
                if (in_array($key, $css_value)) {
                    if (!isset($css_values[$css_key])) {
                        $css_values[$css_key] = array();
                    }
                    if (0 < strpos($key, "_border")) {
                        $value = "border-" . $value;
                    } else {
                        if (0 < strpos($key, "_fontSize")) {
                            $value = "rt_fontSize" . $value;
                        } else {
                            if (0 < strpos($key, "_align")) {
                                $value = "rt_fontAlign" . $value;
                            } else {
                                if (0 < strpos($key, "_bold")) {
                                    if ($value == "bold") {
                                        $value = "rt_fontBold";
                                    } else {
                                        $value = "";
                                    }
                                } else {
                                    if (0 < strpos($key, "_underline")) {
                                        if ($value == "underline") {
                                            $value = "rt_fontUnderline";
                                        } else {
                                            $value = "";
                                        }
                                    } else {
                                        if (0 < strpos($key, "_italic")) {
                                            if ($value == "italic") {
                                                $value = "rt_fontItalic";
                                            } else {
                                                $value = "";
                                            }
                                        } else {
                                            if (0 < strpos($key, "_fontfamily")) {
                                                $value = "rt_fontFamily" . $value;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if ($key == "tabular_cell_background" && $value != "#ffffff" && $value != "default") {
                        $table_stripped = false;
                    }
                    if ($key == "tabular_2ndcell_background" && $value != "#ffffff" && $value != "default") {
                        $table_stripped = false;
                    }
                    $css_values[$css_key][] = $value;
                }
            }
        }
        foreach ($css_values as $key => $value) {
            $this->smartyview->assign($key, implode(" ", $value));
        }
        $this->smartyview->assign("table_stripped", $table_stripped);
        if (isset($options["tabular_additional_actions"]) && !empty($options["tabular_additional_actions"])) {
            $tabular_additional_actions = $options["tabular_additional_actions"];
            $actions = array();
            $tmp = explode(",", $tabular_additional_actions);
            if (0 < count($tmp)) {
                foreach ($tmp as $item) {
                    $rr = explode(":", $item);
                    if (count($rr) == 1) {
                        $actions[] = array("label" => "Submit", "action" => $rr[0]);
                    } else {
                        if (count($rr) == 2) {
                            $actions[] = array("label" => $rr[0], "action" => $rr[1]);
                        }
                    }
                }
                $this->smartyview->assign("tabular_additional_actions", $actions);
            }
        }
        return $options;
    }
    public function _execute_list_app($appinfo)
    {
        $at = $this->input->post("__at__");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $previewmode = $this->input->get_post("preview");
        if ($previewmode) {
            $this->smartyview->assign("embed", $previewmode);
        }
        $this->smartyview->assign("appid", $appinfo["appid"]);
        $formdata = json_decode($appinfo["form"], true);
        $format = $appinfo["format"];
        $appid = $appinfo["appid"];
        $scriptType = $appinfo["scripttype"];
        $db = $this->_get_db($creatorid, $connid);
        $showInForm = false;
        if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
            $this->smartyview->assign("FORM_ID", uniqid("FM_"));
            $this->smartyview->assign("formTitle", $formdata["name"]);
            if (isset($formdata["css"]) && !empty($formdata["css"])) {
                $this->smartyview->assign("formCss", $formdata["css"]);
            }
            if (isset($formdata["formDisplay"]) && !empty($formdata["formDisplay"])) {
                $this->smartyview->assign("formDisplay", $formdata["display"]);
            }
            $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
            $this->_assign_form_html($db, $creatorid, $connid, $formdata);
            $showInForm = true;
        }
        $options = $this->_assign_app_options($appinfo["options"]);
        $this->smartyview->assign("showresultset", true);
        $formID = $this->input->post("FORMID");
        if (!empty($formID)) {
            $this->smartyview->assign("FORMID", $formID);
            $this->smartyview->assign("FORM_ID", $formID);
        }
        if ($db) {
            if ($format == "editapp") {
                $this->_execute_generalop_app($db, $appinfo);
                return NULL;
            }
            if ($scriptType == 1) {
                if ($format == "tabular") {
                    $this->_preview_advrep_tabular($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $options, $showInForm);
                } else {
                    if ($format == "summary") {
                        $this->_preview_advrep_summary($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $showInForm);
                    } else {
                        if ($format == "singlenumber") {
                            $this->_preview_advrep_singlenumber($db, $connid, $appinfo["appid"], json_decode($appinfo["script"], true), $appinfo["name"], $showInForm, $options);
                        } else {
                            if ($format == "googlemap" && $options["map_type"] == "googlemap") {
                                $this->_preview_advrep_googlemap($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $options);
                            } else {
                                if ($format == "googlemap" && $options["map_type"] == "leaflet") {
                                    $this->_preview_advrep_leaflet($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $options);
                                } else {
                                    if ($format == "pivot") {
                                        $this->_preview_advrep_pivot($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $options);
                                    } else {
                                        if ($format == "tableeditor") {
                                            $this->_preview_advrep_tableeditor($db, $creatorid, $connid, json_decode($appinfo["script"], true), $appinfo);
                                        } else {
                                            if ($format == "checkpoint") {
                                                $this->_preview_advrep_checkpoint($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $options, $showInForm);
                                            } else {
                                                if ($format == "calendar") {
                                                    $this->_preview_advrep_calendar($db, $connid, $appinfo["appid"], $appinfo["name"], json_decode($appinfo["script"], true), $options, $showInForm);
                                                } else {
                                                    if (is_supported_chart($format)) {
                                                        $this->_preview_advrep_chart($db, $connid, $format, json_decode($appinfo["script"], true), $appinfo, $showInForm, $options);
                                                    } else {
                                                        $this->_display_app_error("Unknow application format", "The application format is not supported or tempory unavailable, please contact the developer.");
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                if ($format == "tabular") {
                    $this->_preview_script_tabular($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options, $showInForm);
                } else {
                    if ($format == "summary") {
                        $this->_preview_script_summary($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options, $showInForm);
                    } else {
                        if ($format == "pivot") {
                            $this->_preview_script_pivot($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options, $showInForm);
                        } else {
                            if ($format == "singlenumber") {
                                $this->_preview_script_singlenumber($db, $appinfo["appid"], $connid, $appinfo["script"], $showInForm, $options);
                            } else {
                                if ($format == "googlemap" && $options["map_type"] == "googlemap") {
                                    $this->_preview_script_googlemap($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options);
                                } else {
                                    if ($format == "googlemap" && $options["map_type"] == "leaflet") {
                                        $this->_preview_script_leaflet($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options);
                                    } else {
                                        if ($format == "checkpoint") {
                                            $this->_preview_script_checkpoint($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options, $showInForm);
                                        } else {
                                            if ($format == "calendar") {
                                                $this->_preview_script_calendar($db, $appinfo["appid"], $connid, $appinfo["name"], $appinfo["script"], $options);
                                            } else {
                                                if (is_supported_chart($format)) {
                                                    $this->_preview_script_chart($db, $format, $appinfo["script"], $appinfo, $options);
                                                } else {
                                                    $this->_display_app_error("Unknow application format", "The application format is not supported or tempory unavailable, please contact the developer.");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->_display_app_error("Lost connection to database", "Can not connect to database or tempory unavailable, please contact the developer.");
        }
    }
    public function _display_form($formdata, $previewmode, $template)
    {
        $this->smartyview->assign("showresultset", false);
        $this->smartyview->assign("formTitle", $formdata["name"]);
        $this->smartyview->assign("formCss", $formdata["css"]);
        $this->smartyview->assign("formDisplay", $formdata["display"]);
        $this->smartyview->assign("FORM_ID", uniqid("FM_"));
        $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
        $this->smartyview->assign("formHTML", $formdata["html"]);
        $this->smartyview->assign("preview", $previewmode);
        $this->smartyview->assign("app_file", $template);
        $this->smartyview->display("runtime/index.tpl");
    }
    public function _execute_generalop_app($db, $appinfo)
    {
        if ($this->config->item("disable_sql_edit_application")) {
            $this->_display_app_error("SQL Edit Application Disabled", "SQL Edit application disable by administrator.");
        } else {
            $at = $this->input->post("__at__");
            $creatorid = $this->session->userdata("login_creatorid");
            $connid = $appinfo["connid"];
            $this->smartyview->assign("rpf", "editapp");
            $options = $this->_assign_app_options($appinfo["options"]);
            $formdata = json_decode($appinfo["form"], true);
            if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
                $this->smartyview->assign("showresultset", false);
                $this->smartyview->assign("formTitle", $formdata["name"]);
                $this->smartyview->assign("formCss", $formdata["css"]);
                $this->smartyview->assign("formDisplay", $formdata["display"]);
                $this->smartyview->assign("FORM_ID", uniqid("FM_"));
                $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
                $this->_assign_form_html($db, $creatorid, $connid, $formdata);
                $this->smartyview->assign("preview", false);
                $this->smartyview->display("runtime/app.generalop.tpl");
            } else {
                $formID = $this->input->post("FORMID");
                if (!empty($formID)) {
                    $this->smartyview->assign("FORMID", $formID);
                }
                if (empty($options["confirmmsg"])) {
                    if (empty($at)) {
                        $options["confirmmsg"] = "Are you sure to execute the scripts?";
                    } else {
                        if ($at == "__confirm_form__") {
                            $at = "__confirm__";
                        }
                    }
                }
                if (empty($at) || $at == "__confirm_form__") {
                    $confirm = $this->_compile_appscripts($db, $creatorid, $connid, $options["confirmmsg"]);
                    $this->smartyview->assign("confirm", $confirm);
                    if (empty($at)) {
                        $this->smartyview->display("runtime/app.generalop.tpl");
                    } else {
                        $this->smartyview->display("runtime/app.generalop.confirm.tpl");
                    }
                } else {
                    if ($at == "__confirm__") {
                        $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $appinfo["script"]);
                        $sqls = explode(";", $sqlcontent);
                        $success = false;
                        $queryCount = 0;
                        $db->trans_begin();
                        foreach ($sqls as $sql) {
                            $result = $db->query($sql);
                            $queryCount++;
                        }
                        if ($db->trans_status() === false) {
                            $db->trans_rollback();
                        } else {
                            $db->trans_commit();
                            $success = true;
                        }
                        if ($success) {
                            $this->smartyview->assign("box_style", "success");
                            $this->smartyview->assign("resultmessage", "Completed. " . $queryCount . " completed.");
                        } else {
                            $error = $db->error();
                            $message = "";
                            if ($error) {
                                $message = $error["code"] . ": " . $error["message"];
                            }
                            $this->smartyview->assign("box_style", "danger");
                            $this->smartyview->assign("resultmessage", "Query Failed: " . $db->last_query() . "<p/>" . $queryCount . " completed.<p/>" . $message);
                        }
                        $this->smartyview->display("runtime/app.generalop.result.tpl");
                    }
                }
            }
        }
    }
    public function _execute_phpreport_app($appinfo)
    {
        $login_userid = $this->session->userdata("login_userid");
        if (!empty($login_userid)) {
            error_reporting(32767 & ~8);
            ini_set("display_errors", 1);
        }
        $at = $this->input->post("__at__");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $previewmode = $this->input->get_post("preview");
        $this->smartyview->assign("previewmode", $previewmode);
        $this->smartyview->assign("appid", $appinfo["appid"]);
        $this->smartyview->assign("rpf", "phpreport");
        $options = $this->_assign_app_options($appinfo["options"]);
        $mixedhtml = true;
        $formdata = json_decode($appinfo["form"], true);
        $format = $appinfo["format"];
        $appid = $appinfo["appid"];
        $scriptType = $appinfo["scripttype"];
        $db = $this->_get_db($creatorid, $connid);
        $showInForm = false;
        if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
            $this->smartyview->assign("FORM_ID", uniqid("FM_"));
            $this->smartyview->assign("formTitle", $formdata["name"]);
            $this->smartyview->assign("formCss", $formdata["css"]);
            $this->smartyview->assign("formDisplay", $formdata["display"]);
            $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
            $this->_assign_form_html($db, $creatorid, $connid, $formdata);
            $showInForm = true;
        }
        $this->smartyview->assign("showresultset", true);
        $formID = $this->input->post("FORMID");
        if (!empty($formID)) {
            $this->smartyview->assign("FORMID", $formID);
            $this->smartyview->assign("FORM_ID", $formID);
        }
        if ($showInForm) {
            $this->smartyview->assign("apptitle", $appinfo["name"]);
            $this->smartyview->assign("app_file", "runtime/app.htmlreport.tpl");
            $this->smartyview->display("runtime/index.tpl");
        } else {
            if ($db) {
                $force_build = true;
                if (!$this->config->item("reserved_instance")) {
                    $appinfo["script"] = "          PHP Report feature only available on Enterprise version. \r\n\r\n          <p/>\r\n          <b>PHP Report provides full featured PHP coding environment that you can build your own report templates.</b>;";
                } else {
                    $force_build = $previewmode ? true : false;
                }
                $htmlreport = $this->_compile_phpreport($appinfo["script"], "dbface_app_" . $appinfo["appid"], $creatorid, $db, $force_build, $connid);
                $content_only = $this->input->get_post("co");
                if ($content_only == 1 || $content_only == "true") {
                    echo $htmlreport;
                    return NULL;
                }
                $this->smartyview->assign("apptitle", $appinfo["name"]);
                $this->smartyview->assign("htmlreport", $htmlreport);
                if (empty($htmlreport)) {
                    $this->smartyview->assign("add_form_box_css", "no-border no-shadow");
                }
                $this->smartyview->assign("app_file", "runtime/app.htmlreport.tpl");
                $this->smartyview->display("runtime/index.tpl");
            }
        }
    }
    public function _execute_htmlreport_app($appinfo)
    {
        $at = $this->input->post("__at__");
        $creatorid = $this->session->userdata("login_creatorid");
        $_check_script = $this->_check_and_sync_htmlreport($creatorid, $appinfo["appid"], $appinfo["createdate"]);
        if ($_check_script != false) {
            $appinfo["script"] = $_check_script;
        }
        $connid = $appinfo["connid"];
        $previewmode = $this->input->get_post("preview");
        $this->smartyview->assign("previewmode", $previewmode);
        $this->smartyview->assign("appid", $appinfo["appid"]);
        $this->smartyview->assign("rpf", "htmlreport");
        $options = $this->_assign_app_options($appinfo["options"]);
        $formdata = json_decode($appinfo["form"], true);
        $format = $appinfo["format"];
        $appid = $appinfo["appid"];
        $scriptType = $appinfo["scripttype"];
        $db = $this->_get_db($creatorid, $connid);
        $showInForm = false;
        if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
            $this->smartyview->assign("FORM_ID", uniqid("FM_"));
            $this->smartyview->assign("formTitle", $formdata["name"]);
            $this->smartyview->assign("formCss", $formdata["css"]);
            $this->smartyview->assign("formDisplay", $formdata["display"]);
            $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
            $this->_assign_form_html($db, $creatorid, $connid, $formdata);
            $showInForm = true;
        }
        $this->smartyview->assign("showresultset", true);
        $formID = $this->input->post("FORMID");
        if (!empty($formID)) {
            $this->smartyview->assign("FORMID", $formID);
            $this->smartyview->assign("FORM_ID", $formID);
        }
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        if (empty($appinfo["script"])) {
            $this->smartyview->assign("only_form", true);
        }
        if ($showInForm) {
            $this->smartyview->assign("apptitle", $appinfo["name"]);
            $smarty = $this->_get_template_engine($db, $creatorid, $connid);
            if (isset($options["template_form"])) {
                $template_form = $options["template_form"];
                foreach ($template_form as $k => $v) {
                    $value = $this->_compile_string($smarty, $v);
                    $smarty->assign($k, $value);
                }
            }
            if (isset($options["html_template"])) {
                $template_name = $options["html_template"];
                $container = $this->_get_htmltemplate_container($template_name);
                $this->smartyview->assign("htmlreport_container", $container);
            }
            $this->smartyview->assign("app_file", "runtime/app.htmlreport.tpl");
            $this->smartyview->display("runtime/index.tpl");
            return NULL;
        } else {
            if ($db) {
                $script = $appinfo["script"];
                $smarty = $this->_get_template_engine($db, $creatorid, $connid);
                if (isset($options["template_form"])) {
                    $template_form = $options["template_form"];
                    foreach ($template_form as $k => $v) {
                        $value = $this->_compile_string($smarty, $v);
                        $smarty->assign($k, $value);
                    }
                }
                if (isset($options["html_template"])) {
                    $template_name = $options["html_template"];
                    $container = $this->_get_htmltemplate_container($template_name);
                    $this->smartyview->assign("htmlreport_container", $container);
                }
                $htmlreport = $this->_compile_string($smarty, $script);
                $enable_markdown = $this->config->item("markdown");
                if ($enable_markdown) {
                    require_once APPPATH . "libraries/Parsedown.php";
                    $Parsedown = new Parsedown();
                    $htmlreport = $Parsedown->text($htmlreport);
                }
                $this->smartyview->assign("apptitle", $appinfo["name"]);
                $this->smartyview->assign("htmlreport", $htmlreport);
                $this->smartyview->assign("app_file", "runtime/app.htmlreport.tpl");
                $this->smartyview->display("runtime/index.tpl");
            } else {
                $this->_display_app_error("Database Connection", "Can not connect to source database.");
            }
        }
    }
    public function _preview_script_tabular($db, $appid, $connid, $appname, $script, $options = false, $showInForm = false)
    {
        if ($showInForm) {
            $this->_display_advrep_tabular($appid);
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $use_json = isset($options["use_json"]) && $options["use_json"] == 1;
            if ($use_json) {
                $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script, false, "[{", "}]");
            } else {
                $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
            }
            $use_serverside = $this->config->item("tabular_serverside");
            if (!empty($use_serverside) && $use_serverside) {
                $use_serverside = true;
            } else {
                $use_serverside = false;
            }
            $this->smartyview->assign("use_serverside", $use_serverside);
            log_message("debug", $appid . ": " . $sqlcontent);
            if (empty($sqlcontent)) {
                $this->_display_app_error("Error", "Empty Query Script.");
            } else {
                if ($use_json) {
                    $db = $this->_get_adapter_db($creatorid, $connid);
                    $query = $db->query($sqlcontent);
                } else {
                    $sqls = explode(";", $sqlcontent);
                    foreach ($sqls as $sql) {
                        $sql = trim($sql);
                        if (!empty($sql)) {
                            $query = $db->query($sql);
                        }
                    }
                }
                if (!$query) {
                    $this->_display_query_error($db);
                } else {
                    $this->_log_app_last_query($appid, $db);
                    $do_export = $this->input->get("do_export");
                    if ($do_export) {
                        $this->_export_query($do_export, urlencode($appname), $query);
                    } else {
                        $fields = $query->list_fields();
                        $datas = $query->result_array();
                        if (function_exists("_hook_queryresult_")) {
                            call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                        }
                        $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
                        $this->smartyview->assign("fields", $fields);
                        $this->smartyview->assign("fieldnum", count($fields));
                        $this->smartyview->assign("totalrows", count($datas));
                        $this->smartyview->assign("datas", $datas);
                        $at = $this->input->post("__at__");
                        $submitFromForm = !empty($at);
                        if ($submitFromForm) {
                            $this->smartyview->assign("_resultset", true);
                        }
                        $this->smartyview->assign("app_file", "runtime/app.list.tpl");
                        $opened = $this->input->get_post("o") == "1";
                        if ($opened) {
                            $this->smartyview->display("runtime/index.tpl");
                        } else {
                            $output = $this->smartyview->fetch("runtime/index.tpl");
                            $this->_save_app_cache($appid, $output);
                            $this->output->set_output($output);
                        }
                    }
                }
            }
        }
    }
    public function _display_advrep_checkpoint($appid)
    {
        $at = $this->input->post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        $this->smartyview->assign("app_file", "runtime/app.checkpoint.tpl");
        $opened = $this->input->get_post("o") == "1";
        if ($opened) {
            $this->smartyview->display("runtime/index.tpl");
        } else {
            $output = $this->smartyview->fetch("runtime/index.tpl");
            $this->_save_app_cache($appid, $output);
            $this->output->set_output($output);
        }
    }
    public function _display_advrep_tabular($appid)
    {
        $at = $this->input->post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        $this->smartyview->assign("app_file", "runtime/app.list.tpl");
        $opened = $this->input->get_post("o") == "1";
        $this->_assign_apps_in_category($appid);
        if ($opened) {
            $this->smartyview->display("runtime/index.tpl");
        } else {
            $output = $this->smartyview->fetch("runtime/index.tpl");
            $this->_save_app_cache($appid, $output);
            $this->output->set_output($output);
        }
    }
    public function _preview_script_checkpoint($db, $appid, $connid, $appname, $script, $options = false, $showInForm = false)
    {
        $this->_cp_search($appid);
        $this->_display_advrep_checkpoint($appid);
    }
    public function _preview_advrep_checkpoint($db, $connid, $appid, $appname, $script_json, $options = false, $showInForm)
    {
        $this->_cp_search($appid);
        $this->_display_advrep_checkpoint($appid);
    }
    public function _preview_advrep_tabular($db, $connid, $appid, $appname, $script_json, $options = false, $showInForm)
    {
        $at = $this->input->post("__at__");
        $do_export = $this->input->get("do_export");
        $sqlconditions = isset($script_json["sqlcondition"]) ? $script_json["sqlcondition"] : false;
        $sqlops = isset($script_json["sqlop"]) ? $script_json["sqlop"] : false;
        $sqlvalues = isset($script_json["sqlvalue"]) ? $script_json["sqlvalue"] : false;
        $sqljoins = isset($script_json["sqljoin"]) ? $script_json["sqljoin"] : false;
        $tablenames = isset($script_json["tablename"]) ? $script_json["tablename"] : false;
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
        $selects = isset($script_json["select"]) ? $script_json["select"] : false;
        $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $orders = isset($script_json["order"]) ? $script_json["order"] : array();
        $ordertypes = isset($script_json["orderfun"]) ? $script_json["orderfun"] : array();
        $rowlimit = isset($script_json["rowlimit"]) ? $script_json["rowlimit"] : 0;
        $this->make_select($db, $selects, $select_funs, $select_labels);
        $groupbys = isset($script_json["groupby"]) ? $script_json["groupby"] : array();
        $groupbyfuns = isset($script_json["groupbyfun"]) ? $script_json["groupbyfun"] : array();
        $groupby_labels = isset($script_json["groupbylabel"]) ? $script_json["groupbylabel"] : false;
        if ($groupbys && 0 < count($groupbys)) {
            foreach ($groupbys as $groupby) {
                $db->group_by($groupby);
            }
        }
        if ($orders && 0 < count($orders)) {
            $index = 0;
            foreach ($orders as $order) {
                $db->order_by($order, $ordertypes[$index++]);
            }
        }
        $table_ok = $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        if (!$table_ok) {
            $this->_display_app_error("Invalid Criteria", "You need to specify at least one criteria to run this application.");
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        if (0 < $rowlimit) {
            $db->limit(is_numeric($rowlimit) ? $rowlimit : 10);
        }
        if ($options && isset($options["distinct"]) && $options["distinct"] == 1) {
            $db->distinct();
        }
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
            return NULL;
        }
        if ($showInForm) {
            $this->_display_advrep_tabular($appid);
            return NULL;
        }
        $cache_key = $connid . ":" . $appid;
        $query = $this->cached_db_get($db, $creatorid, $cache_key, !$do_export);
        $this->_log_app_last_query($appid, $db);
        if (!$query) {
            $this->_display_query_error($db);
            return NULL;
        }
        if ($do_export) {
            $this->_export_query($do_export, urlencode($appname), $query);
            return NULL;
        }
        $fields = $query->list_fields();
        $datas = $query->result_array();
        if (function_exists("_hook_queryresult_")) {
            call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
        }
        $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("fieldnum", count($fields));
        $this->smartyview->assign("totalrows", count($datas));
        $this->smartyview->assign("datas", $datas);
        $this->_display_advrep_tabular($appid);
    }
    public function _preview_script_pivot($db, $appid, $connid, $appname, $script, $options, $showInForm)
    {
        $do_export = $this->input->get_post("do_export");
        $creatorid = $this->session->userdata("login_creatorid");
        $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
        $query = $db->query($sqlcontent);
        $this->_log_app_last_query($appid, $db);
        if (!$query) {
            $this->_display_query_error($db);
        } else {
            $pivot_options = isset($options["pivot_options"]) ? $options["pivot_options"] : array();
            $rowLabels = isset($pivot_options["rows"]) ? $pivot_options["rows"] : array();
            $columnLabels = isset($pivot_options["columns"]) ? $pivot_options["columns"] : array();
            $summaryLabels = isset($pivot_options["values"]) ? $pivot_options["values"] : array();
            $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
            $this->load->helper("json");
            $org_fields = $query->field_data();
            $org_datas = $query->result_array();
            if (function_exists("_hook_queryresult_")) {
                call_user_func_array("_hook_queryresult_", array($appid, $org_fields, $org_datas));
            }
            $series_options = $options["series_options"];
            $fields = array();
            $field_names = array();
            foreach ($org_fields as $org_field) {
                $fieldname = $org_field->name;
                $field = array("name" => $fieldname, "type" => convert_sql_type($org_field->type));
                if ($series_options && isset($series_options[$fieldname])) {
                    $field = array_merge($field, $series_options[$fieldname]);
                }
                if (in_array($fieldname, $summaryLabels)) {
                    $idx = array_search($fieldname, $summaryLabels);
                    $summarytype = "sum";
                    if ($summarytype != "sum" && $summarytype != "avg") {
                        $summarytype = "sum";
                    }
                    $field["summarizable"] = $summarytype;
                }
                if (in_array($fieldname, $columnLabels)) {
                    $field["columnLabelable"] = true;
                    $field["filterable"] = true;
                } else {
                    $field["columnLabelable"] = false;
                }
                if (in_array($fieldname, $rowLabels)) {
                    $field["rowLabelable"] = true;
                    $field["filterable"] = true;
                } else {
                    $field["rowLabelable"] = false;
                }
                $fields[] = $field;
                $field_names[] = $fieldname;
            }
            $pivotdata = array();
            $pivotdata[] = $field_names;
            foreach ($org_datas as $org_row) {
                $row = array();
                foreach ($field_names as $field_name) {
                    $row[] = $org_row[$field_name];
                }
                $pivotdata[] = $row;
            }
            $summaries = array();
            $idx = 0;
            foreach ($summaryLabels as $summaryLabel) {
                $summarytype = "sum";
                if ($summarytype != "sum" && $summarytype != "avg" && $summarytype != "count") {
                    $summarytype = "sum";
                }
                $summaries[] = $summaryLabel . "_" . $summarytype;
                $idx++;
            }
            $this->smartyview->assign("fields", json_encode($fields));
            $this->smartyview->assign("rowLabels", json_encode($rowLabels));
            $this->smartyview->assign("columnLabels", json_encode($columnLabels));
            $this->smartyview->assign("pivotdata", json_encode($pivotdata));
            $this->smartyview->assign("summaries", json_encode($summaryLabels));
            $this->smartyview->assign("app_file", "runtime/app.pivot.resultset.tpl");
            $opened = $this->input->get_post("o") == "1";
            if ($opened) {
                $this->smartyview->display("runtime/index.tpl");
                return NULL;
            }
            $output = $this->smartyview->fetch("runtime/index.tpl");
            $this->_save_app_cache($appid, $output);
            $this->output->set_output($output);
        }
    }
    public function _preview_script_summary($db, $appid, $connid, $appname, $script, $options, $showInForm)
    {
        if ($showInForm) {
            $this->_display_advrep_summary($appid);
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
            $query = $db->query($sqlcontent);
            if (!$query) {
                $this->_display_query_error($db);
            } else {
                $do_export = $this->input->get("do_export");
                if ($do_export) {
                    $this->_export_query($do_export, urlencode($appname), $query);
                } else {
                    $fields = $query->list_fields();
                    $datas = $query->result_array();
                    if (function_exists("_hook_queryresult_")) {
                        call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                    }
                    $row_num = count($datas);
                    $grouped_datas = array();
                    $span_zeros = array();
                    for ($i = 0; $i < $row_num; $i++) {
                        $grouped_data = array();
                        $last_field = false;
                        $fieldindex = 0;
                        foreach ($fields as $field) {
                            $tmp = array();
                            if ($datas[$i][$field]) {
                                $tmp["data"] = $datas[$i][$field];
                            } else {
                                $tmp["data"] = "(NULL)";
                            }
                            $tmp["rowspan"] = 1;
                            if (0 < $i && $grouped_datas[$i - 1][$field]["data"] == $tmp["data"] && ($fieldindex == 0 || 0 < $fieldindex && $grouped_data[$fields[$fieldindex - 1]]["rowspan"] == 0)) {
                                for ($j = $i - 1; true; $j--) {
                                    if (0 < $grouped_datas[$j][$field]["rowspan"]) {
                                        $grouped_datas[$j][$field]["rowspan"] += 1;
                                        break;
                                    }
                                }
                                $tmp["rowspan"] = 0;
                                $span_zeros[] = array("index" => $i, "field" => $field, "fieldindex" => $fieldindex);
                            }
                            $fieldindex++;
                            $last_field = $field;
                            $grouped_data[$field] = $tmp;
                        }
                        $grouped_datas[] = $grouped_data;
                    }
                    $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
                    $this->smartyview->assign("fields", $fields);
                    $this->smartyview->assign("fieldnum", count($fields));
                    $this->smartyview->assign("totalrows", $row_num);
                    $this->smartyview->assign("datas", $grouped_datas);
                    $at = $this->input->post("__at__");
                    if (!empty($at)) {
                        $this->smartyview->assign("_resultset", true);
                    }
                    $this->smartyview->assign("app_file", "runtime/app.summary.tpl");
                    $opened = $this->input->get_post("o") == "1";
                    if ($opened) {
                        $this->smartyview->display("runtime/index.tpl");
                    } else {
                        $output = $this->smartyview->fetch("runtime/index.tpl");
                        $this->_save_app_cache($appid, $output);
                        $this->output->set_output($output);
                    }
                }
            }
        }
    }
    public function _display_advrep_summary($appid)
    {
        $at = $this->input->post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
        $this->smartyview->assign("app_file", "runtime/app.summary.tpl");
        $opened = $this->input->get_post("o") == "1";
        if ($opened) {
            $this->smartyview->display("runtime/index.tpl");
        } else {
            $output = $this->smartyview->fetch("runtime/index.tpl");
            $this->_save_app_cache($appid, $output);
            $this->output->set_output($output);
        }
    }
    public function _preview_advrep_summary($db, $connid, $appid, $appname, $script_json, $showInForm)
    {
        $gen_sql = $this->input->get_post("gen") == 1;
        $sqlconditions = $script_json["sqlcondition"];
        $sqlops = $script_json["sqlop"];
        $sqlvalues = $script_json["sqlvalue"];
        $sqljoins = $script_json["sqljoin"];
        $tablenames = $script_json["tablename"];
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : array();
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
        $groupbys = isset($script_json["groupby"]) ? $script_json["groupby"] : array();
        $groupbyfuns = isset($script_json["groupbyfun"]) ? $script_json["groupbyfun"] : array();
        $groupby_labels = isset($script_json["groupbylabel"]) ? $script_json["groupbylabel"] : false;
        $summarys = isset($script_json["summary"]) ? $script_json["summary"] : array();
        $summaryfuns = isset($script_json["summaryfun"]) ? $script_json["summaryfun"] : array();
        $summary_labels = isset($script_json["summarylabel"]) ? $script_json["summarylabel"] : false;
        $realColumns = $this->make_select($db, $groupbys, $groupbyfuns, $groupby_labels);
        if ($summarys && 0 < count($summarys)) {
            $summayfunindex = 0;
            foreach ($summarys as $summary) {
                if (!isset($summaryfuns[$summayfunindex]) || empty($summaryfuns[$summayfunindex])) {
                    $summaryfuns[$summayfunindex] = "count";
                }
                $this->make_select($db, array($summarys[$summayfunindex]), array($summaryfuns[$summayfunindex]), array($summary_labels[$summayfunindex]));
                $summayfunindex++;
            }
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        $db->group_by($realColumns);
        $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
            return NULL;
        }
        if ($showInForm) {
            $this->_display_advrep_summary($appid);
            return NULL;
        }
        $query = $db->get();
        $this->_log_app_last_query($appid, $db);
        if (!$query) {
            $this->_display_query_error($db);
            return NULL;
        }
        $do_export = $this->input->get("do_export");
        if ($do_export) {
            $this->_export_query($do_export, urlencode($appname), $query);
            return NULL;
        }
        $fields = $query->list_fields();
        $datas = $query->result_array();
        if (function_exists("_hook_queryresult_")) {
            call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
        }
        $row_num = count($datas);
        $grouped_datas = array();
        $span_zeros = array();
        for ($i = 0; $i < $row_num; $i++) {
            $grouped_data = array();
            $last_field = false;
            $fieldindex = 0;
            foreach ($fields as $field) {
                $tmp = array();
                if ($datas[$i][$field]) {
                    $tmp["data"] = $datas[$i][$field];
                } else {
                    $tmp["data"] = "(NULL)";
                }
                $tmp["rowspan"] = 1;
                if (0 < $i && $grouped_datas[$i - 1][$field]["data"] == $tmp["data"] && ($fieldindex == 0 || 0 < $fieldindex && $grouped_data[$fields[$fieldindex - 1]]["rowspan"] == 0)) {
                    for ($j = $i - 1; true; $j--) {
                        if (0 < $grouped_datas[$j][$field]["rowspan"]) {
                            $grouped_datas[$j][$field]["rowspan"] += 1;
                            break;
                        }
                    }
                    $tmp["rowspan"] = 0;
                    $span_zeros[] = array("index" => $i, "field" => $field, "fieldindex" => $fieldindex);
                }
                $fieldindex++;
                $last_field = $field;
                $grouped_data[$field] = $tmp;
            }
            $grouped_datas[] = $grouped_data;
        }
        $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("fieldnum", count($fields));
        $this->smartyview->assign("totalrows", $row_num);
        $this->smartyview->assign("datas", $grouped_datas);
        $this->_display_advrep_summary($appid);
    }
    public function _preview_script_singlenumber($db, $appid, $connid, $script, $showInForm, $options)
    {
        if ($showInForm) {
            $this->_display_advrep_singlenumber($appid);
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $use_json = isset($options["use_json"]) && $options["use_json"] == 1;
            if ($use_json) {
                $db = $this->_get_adapter_db($creatorid, $connid);
                $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script, NULL, "[{", "}]");
            } else {
                $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
            }
            if ($use_json) {
                $query = $db->query($sqlcontent);
            } else {
                $query = $this->cached_db_query($db, $sqlcontent, $creatorid, $connid);
            }
            if (!$query) {
                $this->_display_query_error($db);
            } else {
                $fields = $query->list_fields();
                $result = $query->result_array();
                $items = array();
                foreach ($fields as $field) {
                    $item = array();
                    $item["k"] = $field;
                    $size = count($result);
                    if (0 < $size) {
                        $item["v"] = $result[0][$field];
                    }
                    if (1 < $size) {
                        $item["v2"] = $result[1][$field];
                        if ($item["v2"] < $item["v"]) {
                            $item["icon"] = "fa fa-play fa-rotate-90";
                            $item["css"] = "single_number_compare_up";
                        } else {
                            $item["icon"] = "fa fa-play fa-rotate-270";
                            $item["css"] = "single_number_compare_down";
                        }
                    }
                    if (2 < $size) {
                        $sparks = array();
                        foreach ($result as $row) {
                            $sparks[] = $row[$field];
                        }
                        $item["sparks"] = implode(",", $sparks);
                    }
                    if ($size == 1 && count($fields) == 1) {
                        $track_history = isset($options["numberreport_track_history"]) ? $options["numberreport_track_history"] == 1 : false;
                        if ($track_history) {
                            $key = md5($sqlcontent);
                            $sparks = $this->_get_app_history_as_sparks($appid, $key);
                            if (!empty($sparks)) {
                                $item["sparks"] = $sparks;
                            }
                            $this->_save_app_history($appid, $creatorid, md5($sqlcontent), $item["v"]);
                        }
                    }
                    $items[] = $item;
                }
                $this->smartyview->assign("use_custom_label", count($fields) == 1);
                if (count($items) != 0) {
                    $this->smartyview->assign("sec_size", floor(12 / count($items)));
                } else {
                    $this->smartyview->assign("sec_size", 0);
                }
                $this->smartyview->assign("items", $items);
                $at = $this->input->post("__at__");
                if (!empty($at)) {
                    $this->smartyview->assign("_resultset", true);
                }
                $this->smartyview->assign("rpf", "numberreport");
                $this->smartyview->assign("app_file", "runtime/app.singlenumber.tpl");
                $opened = $this->input->get_post("o") == "1";
                if ($opened) {
                    $this->smartyview->display("runtime/index.tpl");
                    return NULL;
                }
                $output = $this->smartyview->fetch("runtime/index.tpl");
                $this->_save_app_cache($appid, $output);
                $this->output->set_output($output);
            }
        }
    }
    public function _display_advrep_singlenumber($appid)
    {
        $at = $this->input->post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        $this->smartyview->assign("rpf", "numberreport");
        $this->smartyview->assign("app_file", "runtime/app.singlenumber.tpl");
        $this->_assign_apps_in_category($appid);
        $opened = $this->input->get_post("o") == "1";
        if ($opened) {
            $this->smartyview->display("runtime/index.tpl");
        } else {
            $output = $this->smartyview->fetch("runtime/index.tpl");
            $this->_save_app_cache($appid, $output);
            $this->output->set_output($output);
        }
    }
    public function _preview_advrep_singlenumber($db, $connid, $appid, $script_json, $appname, $showInForm, $options)
    {
        $sqlconditions = $script_json["sqlcondition"];
        $sqlops = $script_json["sqlop"];
        $sqlvalues = $script_json["sqlvalue"];
        $sqljoins = $script_json["sqljoin"];
        $tablenames = $script_json["tablename"];
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : false;
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : false;
        $selects = isset($script_json["select"]) ? $script_json["select"] : false;
        $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $orders = isset($script_json["order"]) ? $script_json["order"] : false;
        $ordertypes = isset($script_json["orderfun"]) ? $script_json["orderfun"] : false;
        $rowlimit = $script_json["rowlimit"];
        $this->make_select($db, $selects, $select_funs, $select_labels);
        if ($orders && 0 < count($orders)) {
            $index = 0;
            foreach ($orders as $order) {
                $db->order_by($order, $ordertypes[$index++]);
            }
        }
        $groupbys = isset($script_json["groupby"]) ? $script_json["groupby"] : array();
        $groupbyfuns = isset($script_json["groupbyfun"]) ? $script_json["groupbyfun"] : array();
        $groupby_labels = isset($script_json["groupbylabel"]) ? $script_json["groupbylabel"] : false;
        if ($groupbys && 0 < count($groupbys)) {
            foreach ($groupbys as $groupby) {
                $db->group_by($groupby);
            }
        }
        $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        $db->limit(1);
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
            return NULL;
        }
        if ($showInForm) {
            $this->_display_advrep_singlenumber($appid);
            return NULL;
        }
        $cache_key = $appid;
        $do_export = $this->input->get("do_export");
        $query = $this->cached_db_get($db, $creatorid, $cache_key, !$do_export);
        $this->_log_app_last_query($appid, $db);
        $sqlcontent = $db->last_query();
        if (!$query) {
            $this->_display_query_error($db);
            return NULL;
        }
        if ($do_export) {
            $this->_export_query($do_export, urlencode($appname), $query);
            return NULL;
        }
        $fields = $query->list_fields();
        $result = $query->result_array();
        $items = array();
        foreach ($fields as $field) {
            $item = array();
            $item["k"] = $field;
            $size = count($result);
            if (0 < $size) {
                $item["v"] = $result[0][$field];
            }
            if (1 < $size) {
                $item["v2"] = $result[1][$field];
                if ($item["v2"] < $item["v"]) {
                    $item["icon"] = "fa fa-play fa-rotate-90";
                    $item["css"] = "single_number_compare_up";
                } else {
                    $item["icon"] = "fa fa-play fa-rotate-270";
                    $item["css"] = "single_number_compare_down";
                }
            }
            if (2 < $size) {
                $sparks = array();
                foreach ($result as $row) {
                    $sparks[] = $row[$field];
                }
                $item["sparks"] = implode(",", $sparks);
            }
            if ($size == 1 && count($fields) == 1) {
                $track_history = isset($options["numberreport_track_history"]) ? $options["numberreport_track_history"] == 1 : false;
                if ($track_history) {
                    $key = md5($sqlcontent);
                    $sparks = $this->_get_app_history_as_sparks($appid, $key);
                    if (!empty($sparks)) {
                        $item["sparks"] = $sparks;
                    }
                    $this->_save_app_history($appid, $creatorid, md5($sqlcontent), $item["v"]);
                }
            }
            $items[] = $item;
        }
        $this->smartyview->assign("use_custom_label", count($fields) == 1);
        $this->smartyview->assign("sec_size", floor(12 / count($items)));
        $this->smartyview->assign("items", $items);
        $this->_display_advrep_singlenumber($appid);
    }
    public function _check_chart_query($db, $query)
    {
        if (!$query) {
            $error = $db->error();
            $ret = array("flag" => "message");
            $this->smartyview->assign("title", "Chart Query Failed");
            $this->smartyview->assign("message", "<b>Query Failed: </b>" . $db->last_query() . "<p>" . $error["code"] . ":" . $error["message"] . "</p>");
            $ret["message"] = $this->smartyview->fetch("runtime/app.inc.errormessage.tpl");
            $this->output->set_content_type("application/json")->set_output(json_encode($ret));
            return false;
        }
        return true;
    }
    /**
     * we will focus on echarts solution since v7.7
     *
     * @param $format
     * @param bool $options
     * @return bool
     */
    public function _can_use_highcharts($format, $options = false)
    {
        return false;
    }
    public function _parse_queries_to_chart($db, $format, $querys, $appinfo, $options)
    {
        if ($this->_can_use_highcharts($format, $options)) {
            $this->_parse_queries_to_chart_highcharts($db, $format, $querys, $appinfo, $options);
        } else {
            $this->_parse_queries_to_chart_echarts($db, $format, $querys, $appinfo, $options);
        }
    }
    public function _parse_queries_to_chart_highcharts($db, $format, $querys, $appinfo, $options)
    {
        $appid = $appinfo["appid"];
        $chartjson = array();
        if (count($querys) == 1) {
            $this->load->helper("highcharts_" . $format);
            $query = $querys[0];
            $fields = $query->field_data();
            $datas = $query->result_array();
            if (function_exists("_hook_queryresult_")) {
                call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
            }
            if (count($fields) <= 2) {
                $labelname = $fields[0]->name;
                $valuename = $fields[1]->name;
                $caption = isset($appinfo["name"]) ? $appinfo["name"] : "";
                $subcaption = isset($appinfo["title"]) ? $appinfo["title"] : "";
                $xAxisName = isset($xaxislabel) && !empty($xaxislabel) ? $xaxislabel : $labelname;
                $yAxisName = isset($yaxislabel) && !empty($yaxislabel) ? $yaxislabel : $valuename;
                if (empty($xAxisName) || $xAxisName == "undefined") {
                    $xAxisName = " ";
                }
                if (empty($yAxisName) || $yAxisName == "undefined") {
                    $yAxisName = " ";
                }
                if ($format == "piechart") {
                    $chartjson["option"] = make_highcharts_piechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                } else {
                    if ($format == "linechart") {
                        $chartjson["option"] = make_highcharts_linechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                    } else {
                        if ($format == "scatterplot") {
                            $chartjson["option"] = make_highcharts_scatterplot($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                        } else {
                            if ($format == "areachart") {
                                $chartjson["option"] = make_highcharts_areachart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                            } else {
                                if ($format == "columnchart") {
                                    $chartjson["option"] = make_highcharts_columnchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                } else {
                                    if ($format == "barchart") {
                                        $chartjson["option"] = make_highcharts_barchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                    } else {
                                        if ($format == "funnel") {
                                            $chartjson["option"] = make_highcharts_funnel($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                        } else {
                                            if ($format == "gauges") {
                                                $chartjson["option"] = make_highcharts_gauges($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                            } else {
                                                if ($format == "radar") {
                                                    $chartjson["option"] = make_highcharts_radar($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $this->smartyview->assign("charttype", $format);
                $output = json_encode($this->_assign_highchart_options($chartjson, $options, $format));
                $creatorid = $this->session->userdata("login_creatorid");
                $this->_save_cache($creatorid, "app", "app_json_" . $appid, $output, "json");
                $this->output->set_content_type("application/json")->set_output($output);
                return NULL;
            }
        }
        $this->load->helper("highcharts_" . $format . "_ms");
        $categories = array();
        $tmp_datas = array();
        $legends = array();
        if (count($querys) == 1) {
            $query = $querys[0];
            $fields = $query->field_data();
            $datas = $query->result_array();
            $count = count($fields);
            $labelname = $fields[0]->name;
            for ($i = 1; $i < $count; $i++) {
                $valuename = $fields[$i]->name;
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
        } else {
            foreach ($querys as $query) {
                $fields = $query->field_data();
                $datas = $query->result_array();
                $labelname = $fields[0]->name;
                $valuename = $fields[1]->name;
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
        }
        $index = 0;
        $datasets = array();
        foreach ($tmp_datas as $data) {
            $dataset = array();
            $dataset["seriesName"] = isset($legends[$index]) ? $legends[$index] : "";
            $index++;
            $a = array();
            foreach ($categories as $category) {
                if (isset($data[$category])) {
                    $a[] = $data[$category];
                } else {
                    $a[] = NULL;
                }
            }
            $dataset["datas"] = $a;
            $datasets[] = $dataset;
        }
        $caption = isset($appinfo["name"]) ? $appinfo["name"] : "";
        $subcaption = isset($appinfo["title"]) ? $appinfo["title"] : "";
        $xAxisName = isset($xaxislabel) && !empty($xaxislabel) ? $xaxislabel : $labelname;
        $yAxisName = isset($yaxislabel) && !empty($yaxislabel) ? $yaxislabel : $valuename;
        if ($format == "piechart") {
            $chartjson["option"] = make_highcharts_piechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
        } else {
            if ($format == "linechart") {
                $chartjson["option"] = make_highcharts_linechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
            } else {
                if ($format == "scatterplot") {
                    $chartjson["option"] = make_highcharts_scatterplot_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                } else {
                    if ($format == "areachart") {
                        $chartjson["option"] = make_highcharts_areachart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                    } else {
                        if ($format == "columnchart") {
                            $chartjson["option"] = make_highcharts_columnchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                        } else {
                            if ($format == "barchart") {
                                $chartjson["option"] = make_highcharts_barchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                            } else {
                                if ($format == "funnel") {
                                    $chartjson["option"] = make_highcharts_funnel_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                } else {
                                    if ($format == "gauges") {
                                        $chartjson["option"] = make_highcharts_gauges_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                    } else {
                                        if ($format == "combinedbarlinechart") {
                                            $chartjson["option"] = make_highcharts_combinedbarlinechart($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                        } else {
                                            if ($format == "radar") {
                                                $chartjson["option"] = make_highcharts_radar_ms($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $output = json_encode($this->_assign_highchart_options($chartjson, $options, $format));
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_save_cache($creatorid, "app", "app_json_" . $appid, $output, "json");
        $this->output->set_content_type("application/json")->set_output($output);
    }
    public function _parse_queries_to_chart_echarts($db, $format, $querys, $appinfo, $options)
    {
        $appid = $appinfo["appid"];
        $chartjson = array();
        if (count($querys) == 1 || $format == "treemap" || $format == "wordcloud" || $format == "gauges") {
            $this->load->helper("echarts_" . $format);
            $query = $querys[0];
            $fields = $query->field_data();
            $datas = $query->result_array();
            if (function_exists("_hook_queryresult_")) {
                call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
            }
            if (count($fields) <= 2 || $format == "treemap" || $format == "wordcloud" || $format == "gauges") {
                $labelname = $fields[0]->name;
                $valuename = count($fields) == 2 ? $fields[1]->name : $labelname;
                $caption = isset($appinfo["name"]) ? htmlspecialchars_decode($appinfo["name"]) : "";
                $subcaption = isset($appinfo["title"]) ? htmlspecialchars_decode($appinfo["title"]) : "";
                $xAxisName = isset($xaxislabel) && !empty($xaxislabel) ? $xaxislabel : $labelname;
                $yAxisName = isset($yaxislabel) && !empty($yaxislabel) ? $yaxislabel : $valuename;
                if (empty($xAxisName) || $xAxisName == "undefined") {
                    $xAxisName = " ";
                }
                if (empty($yAxisName) || $yAxisName == "undefined") {
                    $yAxisName = " ";
                }
                if ($format == "piechart") {
                    $chartjson["option"] = make_echarts_piechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                } else {
                    if ($format == "linechart") {
                        $chartjson["option"] = make_echarts_linechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                    } else {
                        if ($format == "scatterplot") {
                            $chartjson["option"] = make_echarts_scatterplot($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                        } else {
                            if ($format == "areachart") {
                                $chartjson["option"] = make_echarts_areachart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                            } else {
                                if ($format == "columnchart") {
                                    $chartjson["option"] = make_echarts_columnchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                } else {
                                    if ($format == "barchart") {
                                        $chartjson["option"] = make_echarts_barchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                    } else {
                                        if ($format == "funnel") {
                                            $chartjson["option"] = make_echarts_funnel($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                        } else {
                                            if ($format == "gauges") {
                                                $chartjson["option"] = make_echarts_gauges($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                            } else {
                                                if ($format == "funnel") {
                                                    $chartjson["option"] = make_echarts_funnel($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                } else {
                                                    if ($format == "treemap") {
                                                        $chartjson["option"] = make_echarts_treemap($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                    } else {
                                                        if ($format == "wordcloud") {
                                                            $chartjson["option"] = make_echarts_wordcloud($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                        } else {
                                                            if ($format == "combinedbarlinechart") {
                                                                $chartjson["option"] = make_echarts_combinedbarlinechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas, $options);
                                                            } else {
                                                                if ($format == "radar") {
                                                                    $chartjson["option"] = make_echarts_radar($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                                } else {
                                                                    if ($format == "googlemap") {
                                                                        $chartjson["option"] = make_echarts_googlemap($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $this->smartyview->assign("charttype", $format);
                $output = json_encode($this->_assign_chart_options($chartjson, $options, $format));
                $creatorid = $this->session->userdata("login_creatorid");
                $this->_save_cache($creatorid, "app", "app_json_" . $appid, $output, "json");
                $this->output->set_content_type("application/json")->set_output($output);
                return NULL;
            }
        }
        $this->load->helper("echarts_" . $format . "_ms");
        $categories = array();
        $tmp_datas = array();
        $legends = array();
        if (count($querys) == 1) {
            $query = $querys[0];
            $fields = $query->field_data();
            $datas = $query->result_array();
            $count = count($fields);
            $labelname = $fields[0]->name;
            for ($i = 1; $i < $count; $i++) {
                $valuename = $fields[$i]->name;
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
        } else {
            foreach ($querys as $query) {
                $fields = $query->field_data();
                $datas = $query->result_array();
                $labelname = $fields[0]->name;
                $valuename = $fields[1]->name;
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
        }
        $index = 0;
        $datasets = array();
        foreach ($tmp_datas as $data) {
            $dataset = array();
            $dataset["seriesName"] = isset($legends[$index]) ? $legends[$index] : "";
            $index++;
            $a = array();
            foreach ($categories as $category) {
                if (isset($data[$category])) {
                    $a[] = $data[$category];
                } else {
                    $a[] = 0;
                }
            }
            $dataset["datas"] = $a;
            $datasets[] = $dataset;
        }
        $caption = isset($appinfo["name"]) ? $appinfo["name"] : "";
        $subcaption = isset($appinfo["title"]) ? $appinfo["title"] : "";
        $xAxisName = isset($xaxislabel) && !empty($xaxislabel) ? $xaxislabel : $labelname;
        $yAxisName = isset($yaxislabel) && !empty($yaxislabel) ? $yaxislabel : $valuename;
        if ($format == "piechart") {
            $chartjson["option"] = make_echarts_piechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
        } else {
            if ($format == "linechart") {
                $chartjson["option"] = make_echarts_linechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
            } else {
                if ($format == "scatterplot") {
                    $chartjson["option"] = make_echarts_scatterplot_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                } else {
                    if ($format == "areachart") {
                        $chartjson["option"] = make_echarts_areachart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                    } else {
                        if ($format == "columnchart") {
                            $chartjson["option"] = make_echarts_columnchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                        } else {
                            if ($format == "barchart") {
                                $chartjson["option"] = make_echarts_barchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                            } else {
                                if ($format == "funnel") {
                                    $chartjson["option"] = make_echarts_funnel_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                } else {
                                    if ($format == "gauges") {
                                        $chartjson["option"] = make_echarts_gauges_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                    } else {
                                        if ($format == "combinedbarlinechart") {
                                            $chartjson["option"] = make_echarts_combinedbarlinechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets, $options);
                                        } else {
                                            if ($format == "radar") {
                                                $chartjson["option"] = make_echarts_radar_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                            } else {
                                                if ($format == "googlemap") {
                                                    $chartjson["option"] = make_echarts_googlemap_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $output = json_encode($this->_assign_chart_options($chartjson, $options, $format));
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_save_cache($creatorid, "app", "app_json_" . $appid, $output, "json");
        $this->output->set_content_type("application/json")->set_output($output);
    }
    public function _preview_script_chart($db, $format, $script, $appinfo, $options)
    {
        $json = $this->input->get_post("json");
        $view_charttype = $this->input->get_post("view_charttype");
        if (!empty($view_charttype)) {
            $format = $view_charttype;
        }
        $this->smartyview->assign("view_charttype", $format);
        $this->smartyview->assign("chart_provider", $this->_can_use_highcharts($format, $options) ? "highcharts" : "default");
        $do_export = $this->input->get("do_export");
        if (empty($json) && !$do_export) {
            $at = $this->input->post("__at__");
            if (!empty($at)) {
                $this->smartyview->assign("_resultset", true);
            }
            $no_cache = $this->input->get_post("nc");
            $preview = $this->input->get_post("preview");
            if (!empty($no_cache) || !empty($preview)) {
                $this->smartyview->assign("no_cache", 1);
            } else {
                $this->smartyview->assign("no_cache", 0);
            }
            $this->smartyview->assign("rpf", "chart");
            $this->smartyview->assign("CHARTID", "CHARTID_" . $appinfo["appid"]);
            if ($this->_can_use_highcharts($format, $options)) {
                $this->smartyview->assign("app_file", "runtime/app.chart.highcharts.tpl");
            } else {
                $this->smartyview->assign("app_file", "runtime/app.chart.tpl");
            }
            $parameters = $this->_get_request_data();
            $this->smartyview->assign("parameters", $parameters);
            $this->smartyview->display("runtime/index.tpl");
        } else {
            $appid = $appinfo["appid"];
            if ($this->_service_chartreport_cache($appid)) {
                return NULL;
            }
            if ($options && isset($options["rawdata"]) && $options["rawdata"] == "1") {
                $this->smartyview->assign("charttype", $format);
                $script = $this->_compile_appscripts($db, $appinfo["creatorid"], $appinfo["connid"], $script, false, "{{", "}}");
                $creatorid = $this->session->userdata("login_creatorid");
                $this->_save_cache($creatorid, "app", "app_json_" . $appid, $script, "json");
                $this->output->set_content_type("application/json")->set_output($script);
            } else {
                $creatorid = $this->session->userdata("login_creatorid");
                $connid = $appinfo["connid"];
                $use_json = isset($options["use_json"]) && $options["use_json"] == 1 && $this->_is_mongodb($creatorid, $connid);
                $mongodb = false;
                if ($use_json) {
                    $mongodb = $this->_get_mongodb($creatorid, $connid);
                    $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script, NULL, "[{", "}]");
                } else {
                    $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
                }
                $sqls = explode(";", $sqlcontent);
                $queries = array();
                foreach ($sqls as $sql) {
                    $sql = trim($sql);
                    if (!empty($sql)) {
                        if ($mongodb && $use_json) {
                            $query = $mongodb->query($sql);
                        } else {
                            $query = $db->query($sql);
                        }
                        $this->_log_app_last_query($appid, $db);
                        if ($do_export) {
                            $this->_export_query($do_export, urlencode($appinfo["name"]), $query);
                            return NULL;
                        }
                        if (!$this->_check_chart_query($db, $query)) {
                            return NULL;
                        }
                        $queries[] = $query;
                    }
                }
                $this->_parse_queries_to_chart($db, $format, $queries, $appinfo, $options);
            }
        }
    }
    public function _service_chartreport_cache($appid)
    {
        $do_export = $this->input->get_post("do_export");
        $no_cache = $this->input->get_post("nc");
        $gen_sql = $this->_is_gen_sql();
        if (empty($do_export) && !$gen_sql && empty($no_cache)) {
            $creatorid = $this->session->userdata("login_creatorid");
            $cache_data = array();
            $cache = $this->_get_cache($creatorid, "app", "app_json_" . $appid, $cache_data);
            if ($cache) {
                $this->output->set_content_type("application/json")->set_output($cache);
                return true;
            }
        }
        return false;
    }
    public function _preview_script_leaflet($db, $appid, $connid, $appname, $script, $options = false)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
        $query = $db->query($sqlcontent);
        if (!$query) {
            $this->_display_query_error($db);
        } else {
            $do_export = $this->input->get("do_export");
            if ($do_export) {
                $this->_export_query($do_export, urlencode($appname), $query);
            } else {
                $fields = $query->list_fields();
                $datas = $query->result_array();
                if (function_exists("_hook_queryresult_")) {
                    call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                }
                $this->smartyview->assign("fields", $fields);
                $this->smartyview->assign("fieldnum", count($fields));
                $this->smartyview->assign("totalrows", count($datas));
                $markers = array();
                $fieldnum = count($fields);
                $selectnum = 1;
                $use_lat_lng_field_name = in_array("lat", $fields) && in_array("lng", $fields);
                $use_lat_lng = false;
                if (!$use_lat_lng_field_name) {
                    if (3 <= $fieldnum && 0 < count($datas)) {
                        $lat = $datas[0][$fields[0]];
                        $lng = $datas[0][$fields[1]];
                        if (is_numeric($lat) && is_numeric($lng)) {
                            $use_lat_lng = true;
                            $selectnum = 2;
                        }
                    }
                } else {
                    $use_lat_lng = true;
                }
                foreach ($datas as $row) {
                    if ($use_lat_lng_field_name) {
                        $address = $row["lat"] . "," . $row["lng"];
                        $content = "";
                        for ($i = 0; $i < $fieldnum; $i++) {
                            $fieldname = $fields[$i];
                            if ($fieldname != "lat" && $fieldname != "lng") {
                                $content .= $fieldname . ": " . $row[$fieldname] . "<br/>";
                            }
                        }
                        $d = array("address" => $address, "title" => "", "content" => $content);
                        $markers[] = $d;
                    } else {
                        $address = "";
                        $a_address = array();
                        for ($i = 0; $i < $selectnum; $i++) {
                            $a_address[] = $row[$fields[$i]];
                        }
                        $address = implode(",", $a_address);
                        $title = $address;
                        $content = "";
                        for ($i = $selectnum; $i < $fieldnum; $i++) {
                            $content .= $fields[$i] . ": " . $row[$fields[$i]] . "<br/>";
                        }
                        $d = array("address" => addslashes($address), "title" => addslashes($title), "content" => addslashes($content));
                        $markers[] = $d;
                    }
                }
                if ($use_lat_lng) {
                    $this->smartyview->assign("use_lat_lng", $use_lat_lng);
                }
                $this->smartyview->assign("googleappkey", $this->config->item("google.appkey"));
                $this->smartyview->assign("rpf", "googlemap");
                $this->smartyview->assign("markers", $markers);
                $this->smartyview->assign("uniqueid", "googlemap_" . $appid);
                $this->smartyview->assign("app_file", "runtime/app.map.leaflet.tpl");
                $opened = $this->input->get_post("o") == "1";
                if ($opened) {
                    $this->smartyview->display("runtime/index.tpl");
                    return NULL;
                }
                $output = $this->smartyview->fetch("runtime/index.tpl");
                $this->_save_app_cache($appid, $output);
                $this->output->set_output($output);
            }
        }
    }
    public function _preview_script_googlemap($db, $appid, $connid, $appname, $script, $options = false)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
        $query = $db->query($sqlcontent);
        if (!$query) {
            $this->_display_query_error($db);
        } else {
            $do_export = $this->input->get("do_export");
            if ($do_export) {
                $this->_export_query($do_export, urlencode($appname), $query);
            } else {
                $fields = $query->list_fields();
                $datas = $query->result_array();
                if (function_exists("_hook_queryresult_")) {
                    call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                }
                $this->smartyview->assign("fields", $fields);
                $this->smartyview->assign("fieldnum", count($fields));
                $this->smartyview->assign("totalrows", count($datas));
                $markers = array();
                $fieldnum = count($fields);
                $selectnum = 1;
                $use_lat_lng_field_name = in_array("lat", $fields) && in_array("lng", $fields);
                $use_lat_lng = false;
                if (!$use_lat_lng_field_name) {
                    if (3 <= $fieldnum && 0 < count($datas)) {
                        $lat = $datas[0][$fields[0]];
                        $lng = $datas[0][$fields[1]];
                        if (is_numeric($lat) && is_numeric($lng)) {
                            $use_lat_lng = true;
                            $selectnum = 2;
                        }
                    }
                } else {
                    $use_lat_lng = true;
                }
                foreach ($datas as $row) {
                    if ($use_lat_lng_field_name) {
                        $address = $row["lat"] . "," . $row["lng"];
                        $content = "";
                        for ($i = 0; $i < $fieldnum; $i++) {
                            $fieldname = $fields[$i];
                            if ($fieldname != "lat" && $fieldname != "lng") {
                                $content .= $fieldname . ": " . $row[$fieldname] . "<br/>";
                            }
                        }
                        $d = array("address" => $address, "title" => "", "content" => $content);
                        $markers[] = $d;
                    } else {
                        $address = "";
                        $a_address = array();
                        for ($i = 0; $i < $selectnum; $i++) {
                            $a_address[] = $row[$fields[$i]];
                        }
                        $address = implode(",", $a_address);
                        $title = $address;
                        $content = "";
                        for ($i = $selectnum; $i < $fieldnum; $i++) {
                            $content .= $fields[$i] . ": " . $row[$fields[$i]] . "<br/>";
                        }
                        $d = array("address" => addslashes($address), "title" => addslashes($title), "content" => addslashes($content));
                        $markers[] = $d;
                    }
                }
                if ($use_lat_lng) {
                    $this->smartyview->assign("use_lat_lng", $use_lat_lng);
                }
                $this->smartyview->assign("googleappkey", $this->config->item("google.appkey"));
                $this->smartyview->assign("rpf", "googlemap");
                $this->smartyview->assign("markers", $markers);
                $this->smartyview->assign("uniqueid", "googlemap_" . $appid);
                $this->smartyview->assign("app_file", "runtime/app.googlemap.tpl");
                $opened = $this->input->get_post("o") == "1";
                if ($opened) {
                    $this->smartyview->display("runtime/index.tpl");
                    return NULL;
                }
                $output = $this->smartyview->fetch("runtime/index.tpl");
                $this->_save_app_cache($appid, $output);
                $this->output->set_output($output);
            }
        }
    }
    public function _preview_advrep_pivot($db, $connid, $appid, $appname, $script_json, $options = false)
    {
        $do_export = $this->input->get_post("do_export");
        $sqlconditions = $script_json["sqlcondition"];
        $sqlops = $script_json["sqlop"];
        $sqlvalues = $script_json["sqlvalue"];
        $sqljoins = $script_json["sqljoin"];
        $tablenames = $script_json["tablename"];
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
        $columns = isset($script_json["column"]) ? $script_json["column"] : false;
        $column_funs = isset($script_json["columnfun"]) ? $script_json["columnfun"] : false;
        $column_labels = isset($script_json["columnlabel"]) ? $script_json["columnlabel"] : false;
        $rows = isset($script_json["row"]) ? $script_json["row"] : false;
        $row_funs = isset($script_json["rowfun"]) ? $script_json["rowfun"] : false;
        $row_labels = isset($script_json["rowlabel"]) ? $script_json["rowlabel"] : false;
        $pivotdatas = isset($script_json["pivotdata"]) ? $script_json["pivotdata"] : false;
        $pivotdata_funs = isset($script_json["pivotdatafun"]) ? $script_json["pivotdatafun"] : false;
        $pivotdata_labels = isset($script_json["pivotdatalabel"]) ? $script_json["pivotdatalabel"] : false;
        $columnLabels = $this->make_select($db, $columns, $column_funs, $column_labels, true);
        $rowLabels = $this->make_select($db, $rows, $row_funs, $row_labels, true);
        $summaryLabels = $this->make_select($db, $pivotdatas, $pivotdata_funs, $pivotdata_labels, true);
        $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        $columnLabels = is_array($columnLabels) ? $columnLabels : array();
        $rowLabels = is_array($rowLabels) ? $rowLabels : array();
        $summaryLabels = is_array($summaryLabels) ? $summaryLabels : array();
        $db->group_by(array_merge($columnLabels, $rowLabels));
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
        } else {
            $query = $db->get();
            $this->_log_app_last_query($appid, $db);
            if (!$query) {
                $this->_display_query_error($db);
            } else {
                $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
                $this->load->helper("json");
                $org_fields = $query->field_data();
                $org_datas = $query->result_array();
                if (function_exists("_hook_queryresult_")) {
                    call_user_func_array("_hook_queryresult_", array($appid, $org_fields, $org_datas));
                }
                $series_options = $options ? $options["series_options"] : array();
                $fields = array();
                $field_names = array();
                foreach ($org_fields as $org_field) {
                    $fieldname = $org_field->name;
                    $field = array("name" => $fieldname, "type" => convert_sql_type($org_field->type));
                    if ($series_options && isset($series_options[$fieldname])) {
                        $field = array_merge($field, $series_options[$fieldname]);
                    }
                    if (in_array($fieldname, $summaryLabels)) {
                        $idx = array_search($fieldname, $summaryLabels);
                        $summarytype = isset($pivotdata_funs[$idx]) ? $pivotdata_funs[$idx] : "count";
                        if ($summarytype != "sum" && $summarytype != "avg") {
                            $summarytype = "sum";
                        }
                        $field["summarizable"] = $summarytype;
                    }
                    if (in_array($fieldname, $columnLabels)) {
                        $field["columnLabelable"] = true;
                        $field["filterable"] = true;
                    } else {
                        $field["columnLabelable"] = false;
                    }
                    if (in_array($fieldname, $rowLabels)) {
                        $field["rowLabelable"] = true;
                        $field["filterable"] = true;
                    } else {
                        $field["rowLabelable"] = false;
                    }
                    $fields[] = $field;
                    $field_names[] = $fieldname;
                }
                $pivotdata = array();
                $pivotdata[] = $field_names;
                foreach ($org_datas as $org_row) {
                    $row = array();
                    foreach ($field_names as $field_name) {
                        $row[] = $org_row[$field_name];
                    }
                    $pivotdata[] = $row;
                }
                $summaries = array();
                $idx = 0;
                foreach ($summaryLabels as $summaryLabel) {
                    $summarytype = isset($pivotdata_funs[$idx]) ? $pivotdata_funs[$idx] : "count";
                    if ($summarytype != "sum" && $summarytype != "avg" && $summarytype != "count") {
                        $summarytype = "sum";
                    }
                    $summaries[] = $summaryLabel . "_" . $summarytype;
                    $idx++;
                }
                $this->smartyview->assign("fields", json_encode($fields));
                $this->smartyview->assign("rowLabels", json_encode($rowLabels));
                $this->smartyview->assign("columnLabels", json_encode($columnLabels));
                $this->smartyview->assign("pivotdata", json_encode($pivotdata));
                $this->smartyview->assign("summaries", json_encode($summaryLabels));
                $this->smartyview->assign("app_file", "runtime/app.pivot.resultset.tpl");
                $opened = $this->input->get_post("o") == "1";
                if ($opened) {
                    $this->smartyview->display("runtime/index.tpl");
                    return NULL;
                }
                $output = $this->smartyview->fetch("runtime/index.tpl");
                $this->_save_app_cache($appid, $output);
                $this->output->set_output($output);
            }
        }
    }
    public function _preview_script_calendar($db, $appid, $connid, $appname, $script, $options = false)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
        $query = $db->query($sqlcontent);
        if (!$query) {
            $this->_display_query_error($db);
        } else {
            $do_export = $this->input->get("do_export");
            if ($do_export) {
                $this->_export_query($do_export, urlencode($appname), $query);
            } else {
                $fields = $query->list_fields();
                $datas = $query->result_array();
                if (function_exists("_hook_queryresult_")) {
                    call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                }
                $this->smartyview->assign("fields", $fields);
                $this->smartyview->assign("fieldnum", count($fields));
                $this->smartyview->assign("totalrows", count($datas));
                $events = array();
                $te = $this->_get_simple_template_engine();
                foreach ($datas as $row) {
                    $date = $row[$fields[0]];
                    if (is_numeric($date)) {
                        $date = date("Y-m-d H:i:s", $date);
                    }
                    $title = $date;
                    $te->assign("data", $row);
                    $content = $te->fetch("runtime/inc.linked.data.tpl");
                    $d = array("date" => $date, "title" => addslashes($title), "html" => urlencode($content));
                    $events[] = $d;
                }
                $this->smartyview->assign("rpf", "calendar");
                $this->smartyview->assign("events", $events);
                $this->smartyview->assign("event_count", count($events));
                $this->smartyview->assign("uniqueid", "calendar_" . $appid);
                $this->smartyview->assign("app_file", "runtime/app.calendar.tpl");
                $opened = $this->input->get_post("o") == "1";
                if ($opened) {
                    $this->smartyview->display("runtime/index.tpl");
                    return NULL;
                }
                $output = $this->smartyview->fetch("runtime/index.tpl");
                $this->_save_app_cache($appid, $output);
                $this->output->set_output($output);
            }
        }
    }
    public function _preview_advrep_calendar($db, $connid, $appid, $appname, $script_json, $options = false)
    {
        $do_export = $this->input->get_post("do_export");
        $sqlconditions = $script_json["sqlcondition"];
        $sqlops = $script_json["sqlop"];
        $sqlvalues = $script_json["sqlvalue"];
        $sqljoins = $script_json["sqljoin"];
        $tablenames = $script_json["tablename"];
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
        $selects = isset($script_json["select"]) ? $script_json["select"] : false;
        $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $orders = isset($script_json["order"]) ? $script_json["order"] : array();
        $ordertypes = isset($script_json["orderfun"]) ? $script_json["orderfun"] : array();
        $order_labels = isset($script_json["orderlabel"]) ? $script_json["orderlabel"] : false;
        $this->make_select($db, $selects, $select_funs, $select_labels);
        $this->make_select($db, $orders, $ordertypes, $order_labels);
        $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
        } else {
            $query = $db->get();
            $this->_log_app_last_query($appid, $db);
            if (!$query) {
                $this->_display_query_error($db);
            } else {
                $do_export = $this->input->get("do_export");
                if ($do_export) {
                    $this->_export_query($do_export, urlencode($appname), $query);
                } else {
                    $fields = $query->list_fields();
                    $datas = $query->result_array();
                    if (function_exists("_hook_queryresult_")) {
                        call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                    }
                    $this->smartyview->assign("fields", $fields);
                    $this->smartyview->assign("fieldnum", count($fields));
                    $this->smartyview->assign("totalrows", count($datas));
                    $events = array();
                    $fieldnum = count($fields);
                    $selectnum = count($selects);
                    $te = $this->_get_simple_template_engine();
                    foreach ($datas as $row) {
                        $date = $row[$fields[0]];
                        if (is_numeric($date)) {
                            $date = date("Y-m-d H:i:s", $date);
                        }
                        $title = $date;
                        $te->assign("data", $row);
                        $content = $te->fetch("runtime/inc.linked.data.tpl");
                        $d = array("date" => $date, "title" => addslashes($title), "html" => urlencode($content));
                        $events[] = $d;
                    }
                    $this->smartyview->assign("rpf", "calendar");
                    $this->smartyview->assign("events", $events);
                    $this->smartyview->assign("event_count", count($events));
                    $this->smartyview->assign("uniqueid", "calendar_" . $appid);
                    $this->smartyview->assign("app_file", "runtime/app.calendar.tpl");
                    $opened = $this->input->get_post("o") == "1";
                    if ($opened) {
                        $this->smartyview->display("runtime/index.tpl");
                        return NULL;
                    }
                    $output = $this->smartyview->fetch("runtime/index.tpl");
                    $this->_save_app_cache($appid, $output);
                    $this->output->set_output($output);
                }
            }
        }
    }
    public function _preview_advrep_leaflet($db, $connid, $appid, $appname, $script_json, $options = false)
    {
        $do_export = $this->input->get_post("do_export");
        $sqlconditions = $script_json["sqlcondition"];
        $sqlops = $script_json["sqlop"];
        $sqlvalues = $script_json["sqlvalue"];
        $sqljoins = $script_json["sqljoin"];
        $tablenames = $script_json["tablename"];
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
        $selects = isset($script_json["select"]) ? $script_json["select"] : false;
        $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $orders = isset($script_json["order"]) ? $script_json["order"] : array();
        $ordertypes = isset($script_json["orderfun"]) ? $script_json["orderfun"] : array();
        $order_labels = isset($script_json["orderlabel"]) ? $script_json["orderlabel"] : false;
        $this->make_select($db, $selects, $select_funs, $select_labels);
        $this->make_select($db, $orders, $ordertypes, $order_labels);
        $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
        } else {
            $query = $db->get();
            $this->_log_app_last_query($appid, $db);
            if (!$query) {
                $this->_display_query_error($db);
            } else {
                $do_export = $this->input->get("do_export");
                if ($do_export) {
                    $this->_export_query($do_export, urlencode($appname), $query);
                } else {
                    $fields = $query->list_fields();
                    $datas = $query->result_array();
                    if (function_exists("_hook_queryresult_")) {
                        call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                    }
                    $this->smartyview->assign("fields", $fields);
                    $this->smartyview->assign("fieldnum", count($fields));
                    $this->smartyview->assign("totalrows", count($datas));
                    $markers = array();
                    $fieldnum = count($fields);
                    $selectnum = count($selects);
                    $use_lat_lng = false;
                    $use_lat_lng_field_name = in_array("lat", $selects) && in_array("lng", $selects);
                    if (!$use_lat_lng_field_name) {
                        if ($selectnum == 2 && 0 < count($datas)) {
                            $lat = $datas[0][$fields[0]];
                            $lng = $datas[0][$fields[1]];
                            if (is_numeric($lat) && is_numeric($lng)) {
                                $use_lat_lng = true;
                            }
                        }
                    } else {
                        $use_lat_lng = true;
                    }
                    foreach ($datas as $row) {
                        if ($use_lat_lng_field_name) {
                            $address = $row["lat"] . "," . $row["lng"];
                            $content = "";
                            for ($i = 0; $i < $fieldnum; $i++) {
                                $fieldname = $fields[$i];
                                if ($fieldname != "lat" && $fieldname != "lng") {
                                    $content .= $fieldname . ": " . $row[$fieldname] . "<br/>";
                                }
                            }
                            $d = array("address" => $address, "title" => "", "content" => $content);
                            $markers[] = $d;
                        } else {
                            $address = "";
                            $a_address = array();
                            for ($i = 0; $i < $selectnum; $i++) {
                                $a_address[] = $row[$fields[$i]];
                            }
                            $address = implode(",", $a_address);
                            $title = $address;
                            $content = "";
                            for ($i = $selectnum; $i < $fieldnum; $i++) {
                                $content .= $fields[$i] . ": " . $row[$fields[$i]] . "<br/>";
                            }
                            $d = array("address" => addslashes($address), "title" => addslashes($title), "content" => addslashes($content));
                            $markers[] = $d;
                        }
                    }
                    if ($use_lat_lng) {
                        $this->smartyview->assign("use_lat_lng", $use_lat_lng);
                    }
                    $this->smartyview->assign("rpf", "googlemap");
                    $this->smartyview->assign("googleappkey", $this->config->item("google.appkey"));
                    $this->smartyview->assign("markers", $markers);
                    $this->smartyview->assign("uniqueid", "googlemap_" . $appid);
                    $this->smartyview->assign("app_file", "runtime/app.map.leaflet.tpl");
                    $opened = $this->input->get_post("o") == "1";
                    if ($opened) {
                        $this->smartyview->display("runtime/index.tpl");
                        return NULL;
                    }
                    $output = $this->smartyview->fetch("runtime/index.tpl");
                    $this->_save_app_cache($appid, $output);
                    $this->output->set_output($output);
                }
            }
        }
    }
    public function _preview_advrep_googlemap($db, $connid, $appid, $appname, $script_json, $options = false)
    {
        $do_export = $this->input->get_post("do_export");
        $sqlconditions = $script_json["sqlcondition"];
        $sqlops = $script_json["sqlop"];
        $sqlvalues = $script_json["sqlvalue"];
        $sqljoins = $script_json["sqljoin"];
        $tablenames = $script_json["tablename"];
        $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
        $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
        $selects = isset($script_json["select"]) ? $script_json["select"] : false;
        $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $orders = isset($script_json["order"]) ? $script_json["order"] : array();
        $ordertypes = isset($script_json["orderfun"]) ? $script_json["orderfun"] : array();
        $order_labels = isset($script_json["orderlabel"]) ? $script_json["orderlabel"] : false;
        $this->make_select($db, $selects, $select_funs, $select_labels);
        $this->make_select($db, $orders, $ordertypes, $order_labels);
        $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
        $creatorid = $this->session->userdata("login_creatorid");
        $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
        if ($this->_is_gen_sql()) {
            $this->_output_gen_sql($db->get_compiled_select());
        } else {
            $query = $db->get();
            $this->_log_app_last_query($appid, $db);
            if (!$query) {
                $this->_display_query_error($db);
            } else {
                $do_export = $this->input->get("do_export");
                if ($do_export) {
                    $this->_export_query($do_export, urlencode($appname), $query);
                } else {
                    $fields = $query->list_fields();
                    $datas = $query->result_array();
                    if (function_exists("_hook_queryresult_")) {
                        call_user_func_array("_hook_queryresult_", array($appid, $fields, $datas));
                    }
                    $this->smartyview->assign("fields", $fields);
                    $this->smartyview->assign("fieldnum", count($fields));
                    $this->smartyview->assign("totalrows", count($datas));
                    $markers = array();
                    $fieldnum = count($fields);
                    $selectnum = count($selects);
                    $use_lat_lng = false;
                    $use_lat_lng_field_name = in_array("lat", $selects) && in_array("lng", $selects);
                    if (!$use_lat_lng_field_name) {
                        if ($selectnum == 2 && 0 < count($datas)) {
                            $lat = $datas[0][$fields[0]];
                            $lng = $datas[0][$fields[1]];
                            if (is_numeric($lat) && is_numeric($lng)) {
                                $use_lat_lng = true;
                            }
                        }
                    } else {
                        $use_lat_lng = true;
                    }
                    foreach ($datas as $row) {
                        if ($use_lat_lng_field_name) {
                            $address = $row["lat"] . "," . $row["lng"];
                            $content = "";
                            for ($i = 0; $i < $fieldnum; $i++) {
                                $fieldname = $fields[$i];
                                if ($fieldname != "lat" && $fieldname != "lng") {
                                    $content .= $fieldname . ": " . $row[$fieldname] . "<br/>";
                                }
                            }
                            $d = array("address" => $address, "title" => "", "content" => $content);
                            $markers[] = $d;
                        } else {
                            $address = "";
                            $a_address = array();
                            for ($i = 0; $i < $selectnum; $i++) {
                                $a_address[] = $row[$fields[$i]];
                            }
                            $address = implode(",", $a_address);
                            $title = $address;
                            $content = "";
                            for ($i = $selectnum; $i < $fieldnum; $i++) {
                                $content .= $fields[$i] . ": " . $row[$fields[$i]] . "<br/>";
                            }
                            $d = array("address" => addslashes($address), "title" => addslashes($title), "content" => addslashes($content));
                            $markers[] = $d;
                        }
                    }
                    if ($use_lat_lng) {
                        $this->smartyview->assign("use_lat_lng", $use_lat_lng);
                    }
                    $this->smartyview->assign("rpf", "googlemap");
                    $this->smartyview->assign("googleappkey", $this->config->item("google.appkey"));
                    $this->smartyview->assign("markers", $markers);
                    $this->smartyview->assign("uniqueid", "googlemap_" . $appid);
                    $this->smartyview->assign("app_file", "runtime/app.googlemap.tpl");
                    $opened = $this->input->get_post("o") == "1";
                    if ($opened) {
                        $this->smartyview->display("runtime/index.tpl");
                        return NULL;
                    }
                    $output = $this->smartyview->fetch("runtime/index.tpl");
                    $this->_save_app_cache($appid, $output);
                    $this->output->set_output($output);
                }
            }
        }
    }
    public function _preview_advrep_chart($db, $connid, $format, $script_json, $appinfo, $showInForm, $options)
    {
        $json = $this->input->get_post("json");
        $view_charttype = $this->input->get_post("view_charttype");
        if (!empty($view_charttype)) {
            $format = $view_charttype;
        }
        $this->smartyview->assign("view_charttype", $format);
        $this->smartyview->assign("chart_provider", $this->_can_use_highcharts($format, $options) ? "highcharts" : "default");
        $gen_sql = $this->_is_gen_sql();
        $do_export = $this->input->get("do_export");
        if (empty($json) && !$gen_sql && !$do_export) {
            $at = $this->input->post("__at__");
            if (!empty($at)) {
                $this->smartyview->assign("_resultset", true);
            }
            $no_cache = $this->input->get_post("nc");
            $preview = $this->input->get_post("preview");
            if (!empty($no_cache) || !empty($preview)) {
                $this->smartyview->assign("no_cache", 1);
            } else {
                $this->smartyview->assign("no_cache", 0);
            }
            $this->smartyview->assign("CHARTID", "CHARTID_" . $appinfo["appid"]);
            $this->smartyview->assign("rpf", "chart");
            if ($this->_can_use_highcharts($format, $options)) {
                $this->smartyview->assign("app_file", "runtime/app.chart.highcharts.tpl");
            } else {
                $this->smartyview->assign("app_file", "runtime/app.chart.tpl");
            }
            $parameters = $this->_get_request_data();
            $this->smartyview->assign("parameters", $parameters);
            $chart_linkapp = isset($options["chart_linkapp"]) ? $options["chart_linkapp"] : false;
            if ($chart_linkapp) {
                $this->smartyview->assign("chart_linkapp", $chart_linkapp);
            }
            $this->smartyview->display("runtime/index.tpl");
        } else {
            $appid = $appinfo["appid"];
            if ($this->_service_chartreport_cache($appid)) {
                return NULL;
            }
            $sqlconditions = isset($script_json["sqlcondition"]) ? $script_json["sqlcondition"] : false;
            $sqlops = isset($script_json["sqlop"]) ? $script_json["sqlop"] : false;
            $sqlvalues = isset($script_json["sqlvalue"]) ? $script_json["sqlvalue"] : false;
            $sqljoins = isset($script_json["sqljoin"]) ? $script_json["sqljoin"] : false;
            $tablenames = isset($script_json["tablename"]) ? $script_json["tablename"] : false;
            $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : array();
            $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
            $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
            $xaxis = isset($script_json["xaxis"]) ? $script_json["xaxis"] : false;
            $xaxisfun = isset($script_json["xaxisfun"]) ? $script_json["xaxisfun"] : false;
            $yaxiss = isset($script_json["yaxis"]) ? $script_json["yaxis"] : false;
            $yaxisfuns = isset($script_json["yaxisfun"]) ? $script_json["yaxisfun"] : false;
            $xaxislabel = isset($script_json["xaxislabel"]) ? $script_json["xaxislabel"] : false;
            $yaxislabel = isset($script_json["yaxislabel"]) ? $script_json["yaxislabel"] : false;
            if (!$xaxis || empty($xaxis)) {
                $xaxis = $yaxiss[0];
                $xaxisfun = $yaxisfuns[0];
            }
            if (!$yaxiss || empty($yaxiss)) {
                $yaxiss = array($xaxis[0]);
                $yaxisfuns = array("count");
            }
            $querys = array();
            $index = 0;
            $groupColumns = false;
            $creatorid = $this->session->userdata("login_creatorid");
            foreach ($yaxiss as $yaxis) {
                $groupColumns = $this->make_select($db, $xaxis, $xaxisfun, $xaxislabel);
                if (!$groupColumns && $format != "gauges") {
                    echo json_encode(array());
                    return NULL;
                }
                $this->make_select($db, array($yaxis), array($yaxisfuns[$index]), array($yaxislabel[$index]));
                $db->group_by($groupColumns, false);
                foreach ($groupColumns as $groupColumn) {
                    $db->order_by($groupColumn, "asc", false);
                }
                $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
                $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$gen_sql);
                if ($gen_sql) {
                    $this->_output_gen_sql($db->get_compiled_select());
                    return NULL;
                }
                $query = $db->get();
                $this->_log_app_last_query($appid, $db);
                if ($do_export) {
                    $this->_export_query($do_export, urlencode($appinfo["name"]), $query);
                    return NULL;
                }
                if (!$this->_check_chart_query($db, $query)) {
                    return NULL;
                }
                $querys[] = $query;
                $index++;
            }
            $this->_parse_queries_to_chart($db, $format, $querys, $appinfo, $options);
        }
    }
    public function _make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype)
    {
        if (count($tablenames) == 0 || empty($tablenames[0])) {
            return false;
        }
        if (count($tablenames) == 1) {
            $db->from($tablenames[0]);
        } else {
            $db->from($tablenames[0]);
            for ($i = 0; $i < count($jointype); $i++) {
                if (!empty($tablenames[$i + 1]) && !empty($left_columnnames[$i]) && !empty($right_columnnames[$i])) {
                    if ($jointype[$i] == "join") {
                        $db->join($tablenames[$i + 1], $left_columnnames[$i] . "=" . $right_columnnames[$i]);
                    } else {
                        if ($jointype[$i] == "leftjoin") {
                            $db->join($tablenames[$i + 1], $left_columnnames[$i] . "=" . $right_columnnames[$i], "left");
                        } else {
                            if ($jointype[$i] == "rightjoin") {
                                $db->join($tablenames[$i + 1], $left_columnnames[$i] . "=" . $right_columnnames[$i], "right");
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
    public function _check_databasetype($db)
    {
        $dbdriver = $db->dbdriver;
        $subdriver = $db->subdriver;
        if ($dbdriver == "pdo") {
            return $subdriver;
        }
        return $dbdriver;
    }
    public function _make_year_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Year", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli" || $dbtype == "sqlsrv") {
            $db->select("YEAR(" . $select . ") as " . $label, false);
        } else {
            $db->select((string) $select . " as " . $label);
        }
    }
    public function _make_fulldate_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Full Date", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("DATE_FORMAT(" . $select . ", '%Y-%m-%d') as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("Convert(varchar, " . $select . ", 101) as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_datetime_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Datetime", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("DATE_FORMAT(" . $select . ", '%Y-%m-%d %H:%i:%s') as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("Convert(varchar, " . $select . ", 120) as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_quarterAndYear_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Quarter Year", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("CONCAT('Q', QUARTER(" . $select . "), ' ', YEAR(" . $select . ")) as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("'Q' + convert(char(1), DATEPART(Quarter, " . $select . ")) + ' ' + convert(char(4), year(" . $select . ")) as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_quarter_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Quarter", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("CONCAT('Q', QUARTER(" . $select . ")) as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("'Q' + convert(char(1), DATEPART(Quarter, " . $select . ")) as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_monthAndYear_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "MonthAndYear", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("DATE_FORMAT(" . $select . ", '%Y-%m') as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("Convert(varchar(7), " . $select . ", 120) as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_month_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Month", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("MONTHNAME(" . $select . ") as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("DATENAME(month, " . $select . ") as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_weekAndYear_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "WeekAndYear", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("CONCAT('W', WEEKOFYEAR(" . $select . "), ' ', YEAR(" . $select . ")) as " . $label, false);
        } else {
            $db->select((string) $select . " as " . $label);
        }
    }
    public function _make_week_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Week", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("CONCAT('W', WEEKOFYEAR(" . $select . ")) as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("DATEPART(ww, " . $select . ") as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_weekday_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Week Day", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("DAYNAME(" . $select . ") as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("DATEPART(dw, " . $select . ") as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_day_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Day", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("DAYOFMONTH(" . $select . ") as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("DATEPART(dd, " . $select . ") as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function _make_hour_select($db, $select, $label)
    {
        if (function_exists("api_date_format") && call_user_func_array("api_date_format", array($db, "Hour", $select, $label))) {
            return NULL;
        }
        $dbtype = $this->_check_databasetype($db);
        if ($dbtype == "mysql" || $dbtype == "mysqli") {
            $db->select("HOUR(" . $select . ") as " . $label, false);
        } else {
            if ($dbtype == "sqlsrv") {
                $db->select("DATEPART(hour, " . $select . ") as " . $label, false);
            } else {
                $db->select((string) $select . " as " . $label);
            }
        }
    }
    public function make_select($db, $selects, $select_funs, $select_labels = false, $return_unescaped_label = false)
    {
        if (empty($selects) || count($selects) == 0) {
            return false;
        }
        $index = 0;
        $realColumns = array();
        foreach ($selects as $select) {
            if (!isset($select_funs[$index]) || empty($select_funs[$index]) || $select_funs[$index] == "actual") {
                $label = get_array_value($select_labels, $index);
                if ($label) {
                    $db->select((string) $select . " as " . $db->escape_identifiers($label), false);
                    $realColumns[] = $return_unescaped_label ? $label : $db->escape_identifiers($label);
                } else {
                    $db->select($select);
                    $realColumns[] = $select;
                }
            } else {
                $label = get_array_value($select_labels, $index);
                switch ($select_funs[$index]) {
                    case "Full Date":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Date_of_" . $select;
                            $label = $db->escape_identifiers("Date_of_" . $select);
                        }
                        $this->_make_fulldate_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Year":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Year_of_" . $select;
                            $label = $db->escape_identifiers("Year_of_" . $select);
                        }
                        $this->_make_year_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Datetime":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Datetime_of_" . $select;
                            $label = $db->escape_identifiers("Datetime_of_" . $select);
                        }
                        $this->_make_datetime_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "QuarterAndYear":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Quarter_of_" . $select;
                            $label = $db->escape_identifiers("Quarter_of_" . $select);
                        }
                        $this->_make_quarterAndYear_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "MonthAndYear":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Month_of_" . $select;
                            $label = $db->escape_identifiers("Month_of_" . $select);
                        }
                        $this->_make_monthAndYear_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "WeekAndYear":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Week_of_" . $select;
                            $label = $db->escape_identifiers("Week_of_" . $select);
                        }
                        $this->_make_weekAndYear_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Quarter":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Quarter_of_" . $select;
                            $label = $db->escape_identifiers("Quarter_of_" . $select);
                        }
                        $this->_make_quarter_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Month":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Month_of_" . $select;
                            $label = $db->escape_identifiers("Month_of_" . $select);
                        }
                        $this->_make_month_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Week":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Week_of_" . $select;
                            $label = $db->escape_identifiers("Week_of_" . $select);
                        }
                        $this->_make_week_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Week Day":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "WeekDay_of_" . $select;
                            $label = $db->escape_identifiers("WeekDay_of_" . $select);
                        }
                        $this->_make_weekday_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Day":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Day_of_" . $select;
                            $label = $db->escape_identifiers("Day_of_" . $select);
                        }
                        $this->_make_day_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "Hour":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Hour_of_" . $select;
                            $label = $db->escape_identifiers("Hour_of_" . $select);
                        }
                        $this->_make_hour_select($db, $select, $label);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "sum":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Sum_of_" . $select;
                            $label = $db->escape_identifiers(DF_escape_for_db("Sum_of_" . $select));
                        }
                        $db->select("SUM(" . $select . ") as " . $label, false);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "max":
                        if ($label) {
                            $db->select_max($select, $label);
                            $realColumns[] = $label;
                        } else {
                            $db->select_max($select, "Max_of_" . $select);
                            $realColumns[] = $return_unescaped_label ? "Max_of_" . $select : $db->escape_identifiers("Max_of_" . $select);
                        }
                        break;
                    case "min":
                        if ($label) {
                            $db->select_min($select, $label);
                            $realColumns[] = $label;
                        } else {
                            $db->select_min($select, "Min_of_" . $select);
                            $realColumns[] = $return_unescaped_label ? "Min_of_" . $select : $db->escape_identifiers("Min_of_" . $select);
                        }
                        break;
                    case "avg":
                        if ($label) {
                            $db->select_avg($select, $label);
                            $realColumns[] = $label;
                        } else {
                            $db->select_avg($select, "AVG_of_" . $select);
                            $realColumns[] = $return_unescaped_label ? "AVG_of_" . $select : $db->escape_identifiers("AVG_of_" . $select);
                        }
                        break;
                    case "count":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Count_of_" . $select;
                            $label = $db->escape_identifiers(DF_escape_for_db("Count_of_" . $select));
                        }
                        $db->select("COUNT(" . $select . ") as " . $label, false);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "countdist":
                        if ($label) {
                            $org_label = $label;
                            $label = $db->escape_identifiers($label);
                        } else {
                            $org_label = "Count_of_" . $select;
                            $label = $db->escape_identifiers(DF_escape_for_db("Count_of_" . $select));
                        }
                        $db->select("COUNT(DISTINCT " . $select . ") as " . $label, false);
                        $realColumns[] = $return_unescaped_label ? $org_label : $label;
                        break;
                    case "custom":
                        $hasas = stripos($label, "as");
                        if ($hasas === false) {
                            if (!empty($label)) {
                                $columnname = $db->escape_identifiers($select);
                                $db->select($label . " as " . $columnname);
                                $realColumns[] = $columnname;
                            } else {
                                $columnname = $db->escape_identifiers($select);
                                $db->select($columnname);
                                $realColumns[] = $columnname;
                            }
                        } else {
                            $keywords = preg_split("/[\\s]+as[\\s]+/i", $label);
                            $columnname = trim($keywords[1]);
                            $db->select($label);
                            $realColumns[] = $columnname;
                        }
                        break;
                    default:
                        $db->select($select);
                        $realColumns[] = $select;
                        break;
                }
            }
            $index++;
        }
        return $realColumns;
    }
    public function _display_query_error($db)
    {
        $at = $this->input->get_post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("embed", true);
        }
        $error = $db->error();
        $this->_display_app_error("Query Failed", "<b>Query:</b><br/>" . $db->last_query() . "<p/>We can’t parse this SQL syntax. If you are using custom SQL, verify the syntax and try again. Otherwise, contact support:<br/><b>" . $error["code"] . ": </b>" . $error["message"]);
    }
    public function _display_app_error($title, $message)
    {
        $at = $this->input->post("__at__");
        $embed = $this->input->get_post("embed") == 1;
        $preview = $this->input->get_post("embed") == 1;
        if (!empty($at) || $embed || $preview) {
            $this->smartyview->assign("embed", true);
        }
        $this->smartyview->assign("hasError", true);
        $this->smartyview->assign("title", $title);
        $this->smartyview->assign("message", $message);
        $this->smartyview->display("runtime/app.error.tpl");
    }
    public function htmlreport()
    {
        $this->load->library("smartyview");
        $appid = $this->input->post("appid");
        $script = $this->input->post("s");
        $displaystyle = $this->input->post("d");
        $width = $this->input->post("w");
        $height = $this->input->post("h");
        $template = NULL;
        $this->load->helper("json");
        $this->load->database();
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select connid, form from dc_app where creatorid=? and appid=?", array($creatorid, $appid));
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        $query = $db->query($script);
        if (!$query) {
            $this->_display_query_error($db);
        } else {
            if ($displaystyle == "singlevalue") {
                $data = $query->row_array();
                $data_key = key($data);
                $data_value = $data[$data_key];
                $this->smartyview->assign("data_key", $data_key);
                $this->smartyview->assign("data_value", $data_value);
                $template = "runtime/app.htmlreport.inc.singlestring.tpl";
            } else {
                $fields = $query->list_fields();
                $datas = $query->result_array();
                $this->smartyview->assign("width", $width);
                $this->smartyview->assign("height", $height);
                $this->smartyview->assign("fields", $fields);
                $this->smartyview->assign("fieldnum", count($fields));
                $this->smartyview->assign("totalrows", count($datas));
                $this->smartyview->assign("datas", $datas);
                $this->smartyview->assign("display_style", $displaystyle);
                $template = "runtime/app.htmlreport.inc.tabular.tpl";
            }
            $this->smartyview->display($template);
        }
    }
    public function columndata()
    {
        $d = $this->input->post("columnname");
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $appinfo = $this->db->query("select connid from dc_app where appid = ? and creatorid=?", array($appid, $creatorid))->row_array();
        $connid = $appinfo["connid"];
        $tmp = explode(".", $d);
        if (!$tmp || count($tmp) != 2) {
            exit($d . " Please select a column as condition.");
        }
        list($tablename, $columnname) = $tmp;
        $db = $this->_get_db($creatorid, $connid);
        $db->select($columnname)->order_by($columnname, "asc")->from($tablename);
        $max_sample_rows = $this->config->item("max_sample_rows");
        if (!$max_sample_rows) {
            $max_sample_rows = 100;
        }
        $db->limit($max_sample_rows);
        $db->distinct();
        $query = $db->get();
        $this->_log_app_last_query($appid, $db);
        $tmpdatas = $query->result_array();
        $datas = array();
        foreach ($tmpdatas as $tmpdata) {
            $datas[] = $tmpdata[$columnname];
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->display("datalist.tpl");
    }
    public function flushcache()
    {
        $appid = $this->input->post("a");
        $creatorid = $this->session->userdata("creatorid");
        $this->db->delete("dc_cache", array("creatorid" => $creatorid, "type" => "app", "name" => "app_" . $appid));
        $rows = $this->db->affected_rows();
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode($rows));
    }
    public function _execute_dynamodbtable_app($appinfo)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $dynamodb = $this->_get_dynamodb($creatorid, $connid);
        $script_json = json_decode($appinfo["script"], true);
        $tablenames = $script_json["tablename"];
        $appid = $appinfo["appid"];
        $viewname = $tablenames[0];
        $startRow = $this->input->post("startRow") ? $this->input->post("startRow") : 0;
        $totalRows = $dynamodb->count_all($viewname);
        $pageNo = 1;
        $pageCount = $this->config->item("settings_table_lines");
        $pageNum = ceil($totalRows / $pageCount);
        $do_export = $this->input->get("do_export");
        if ($do_export) {
            return NULL;
        }
        $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $saved_filters = $query->result_array();
            $this->smartyview->assign("saved_filters", $saved_filters);
        }
        $cursor = $dynamodb->queryCollection($viewname, $startRow, $pageCount);
        $datas = array();
        $fields = array();
        $field_names = $dynamodb->list_fields($viewname);
        foreach ($field_names as $field_name) {
            $fields[] = array("name" => $field_name, "primary" => 1, "type" => 0);
        }
        $field_names[] = "Value";
        $fields[] = array("name" => "Value", "type" => 0);
        foreach ($cursor as $org_row) {
            $row_data = array();
            foreach ($field_names as $field_name) {
                if ($field_name == "Value") {
                    $row_data[$field_name] = "{ " . count($org_row) . " fields }";
                } else {
                    $row_data[$field_name] = $org_row[$field_name];
                }
            }
            $datas[] = $row_data;
        }
        $showFieldCount = count($fields);
        if ($totalRows < ($end = $startRow + $pageCount)) {
            $end = $totalRows;
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("viewname", $viewname);
        $this->smartyview->assign("totalrow", $totalRows);
        $this->smartyview->assign("start", $startRow + 1);
        $this->smartyview->assign("end", $end);
        $this->smartyview->assign("pageNo", $pageNo);
        $this->smartyview->assign("pageNum", $pageNum);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("showFieldCount", $showFieldCount);
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->assign("nav_str", $this->_get_page_nav_str_dynamodb($pageNo, $pageNum));
        $this->smartyview->assign("rpf", "tableeditor");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if (!empty($ace_editor_theme)) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $strs = explode(".", $viewname);
        if (count($strs) == 2 && $strs[1] == "files") {
            $this->smartyview->assign("gridfs_view", 1);
        }
        $at = $this->input->post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        $this->smartyview->assign("app_file", "runtime/app.dynamodb.tableeditor.tpl");
        $this->smartyview->display("runtime/index.tpl");
    }
    public function _execute_mongotable_app($appinfo)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $mongo_db = $this->_get_mongodb($creatorid, $connid);
        $script_json = json_decode($appinfo["script"], true);
        $tablenames = $script_json["tablename"];
        $appid = $appinfo["appid"];
        $viewname = $tablenames[0];
        $startRow = $this->input->post("startRow") ? $this->input->post("startRow") : 0;
        $totalRows = $mongo_db->count_all($viewname);
        $pageNo = 1;
        $pageCount = $this->config->item("settings_table_lines");
        $pageNum = ceil($totalRows / $pageCount);
        $do_export = $this->input->get("do_export");
        if ($do_export) {
            return NULL;
        }
        $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $saved_filters = $query->result_array();
            $this->smartyview->assign("saved_filters", $saved_filters);
        }
        $cursor = $mongo_db->queryCollection($viewname, $startRow, $pageCount);
        $datas = array();
        $fields = array();
        $field_names = array();
        foreach ($cursor as $row) {
            $row = $row->jsonSerialize();
            $row_data = array();
            foreach ($row as $k => $v) {
                if (!in_array($k, $field_names)) {
                    $field_names[] = $k;
                    $field_data = array();
                    $field_data["name"] = $k;
                    if (is_integer($v)) {
                        $field_data["type"] = CDT_NUMBERIC;
                    } else {
                        if (is_numeric($v)) {
                            $field_data["type"] = CDT_NUMBERIC;
                        } else {
                            $field_data["type"] = CDT_TEXTFIELD;
                        }
                    }
                    if ($k == "_id") {
                        $field_data["primary"] = true;
                    }
                    $field_data["shown"] = 1;
                    $fields[] = $field_data;
                }
                $row_data[$k] = mongo_object_to_string($v);
            }
            $datas[] = $row_data;
        }
        $showFieldCount = count($fields);
        if ($totalRows < ($end = $startRow + $pageCount)) {
            $end = $totalRows;
        }
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("viewname", $viewname);
        $this->smartyview->assign("totalrow", $totalRows);
        $this->smartyview->assign("start", $startRow + 1);
        $this->smartyview->assign("end", $end);
        $this->smartyview->assign("pageNo", $pageNo);
        $this->smartyview->assign("pageNum", $pageNum);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("showFieldCount", $showFieldCount);
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->assign("nav_str", $this->_get_page_nav_str_mongo($pageNo, $pageNum));
        $this->smartyview->assign("rpf", "tableeditor");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if (!empty($ace_editor_theme)) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $strs = explode(".", $viewname);
        if (count($strs) == 2 && $strs[1] == "files") {
            $this->smartyview->assign("gridfs_view", 1);
        }
        $at = $this->input->post("__at__");
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        $this->smartyview->assign("app_file", "runtime/app.mongo.tableeditor.tpl");
        $this->smartyview->display("runtime/index.tpl");
    }
    public function _preview_advrep_tableeditor($db, $creatorid, $connid, $script_json, $appinfo)
    {
        $tablenames = $script_json["tablename"];
        $appid = $appinfo["appid"];
        $viewname = $tablenames[0];
        $startRow = $this->input->post("startRow") ? $this->input->post("startRow") : 0;
        $this->load->database();
        $totalRows = count_table_rows($db, $viewname);
        $pageNo = 1;
        $pageCount = $this->config->item("settings_table_lines");
        $pageNum = ceil($totalRows / $pageCount);
        $selects = isset($script_json["select"]) && !empty($script_json["select"]) ? $script_json["select"] : false;
        $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $do_export = $this->input->get("do_export");
        if ($do_export) {
            $sqlcondition = $this->input->post("sqlcondition");
            $sqljoin = $this->input->post("sqljoin");
            $sqlop = $this->input->post("sqlop");
            $sqlvalue = $this->input->post("sqlvalue");
            if ($sqlcondition && $sqljoin && $sqlop && $sqlvalue) {
                $this->build_filter($db, $creatorid, $connid, $sqlcondition, $sqljoin, $sqlop, $sqlvalue);
            }
            $query = $db->select($selects)->get($viewname);
            $this->_log_app_last_query($appid, $db);
            $this->_export_query($do_export, urlencode($viewname), $query);
        } else {
            if ($selects) {
                $query = $db->select($selects)->get($viewname, $pageCount, $startRow);
            } else {
                $query = $db->get($viewname, $pageCount, $startRow);
            }
            $this->_log_app_last_query($appid, $db);
            if (!$query) {
                $error = $db->error();
                $this->_display_app_error("Application Error", $error["message"]);
            } else {
                $datas = $query->result_array();
                $pkColumnNames = array();
                $fields = array();
                $all_field_data = field_data($db, $viewname);
                $field_data = array();
                if ($selects) {
                    foreach ($selects as $select) {
                        foreach ($all_field_data as &$row) {
                            if ($select == $row->name) {
                                $field_data[] = $row;
                                break;
                            }
                        }
                    }
                } else {
                    $field_data = $all_field_data;
                }
                $fieldNum = count($field_data);
                $showFieldCount = 0;
                $tablelinks = array();
                $query = $this->db->query("select dsttable, dstcolumn, srccolumn from dc_tablelinks where srctable = ? and creatorid = ? and connid = ?", array($viewname, $creatorid, $connid));
                if (0 < $query->num_rows()) {
                    $links = $query->result_array();
                    foreach ($links as $link) {
                        $tablelinks[$link["srccolumn"]] = $link;
                    }
                }
                $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "formatter"))->get("dc_app_options");
                $table_formatters = array();
                if (0 < $query->num_rows()) {
                    $result = $query->result_array();
                    foreach ($result as $row) {
                        $extra_key = $row["key"];
                        $extra_value = $row["value"];
                        $table_formatters[$extra_key] = $extra_value;
                    }
                }
                $table_editors = array();
                $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "editor"))->get("dc_app_options");
                $result = $query->result_array();
                foreach ($result as $row) {
                    $extra_key = $row["key"];
                    $extra_value = $row["value"];
                    $table_editors[$extra_key] = $extra_value;
                }
                $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
                if (0 < $query->num_rows()) {
                    $saved_filters = $query->result_array();
                    $this->smartyview->assign("saved_filters", $saved_filters);
                }
                foreach ($field_data as $field) {
                    $field_array = array();
                    $field_array["type"] = $this->get_input_type($field->type, $field->max_length);
                    $field_array["name"] = $field->name;
                    $field_array["len"] = $field->max_length;
                    if (property_exists($field, "primary_key") && $field->primary_key == 1) {
                        $pkColumnNames[] = $field->name;
                        $field_array["primary"] = true;
                    } else {
                        $field_array["primary"] = false;
                    }
                    if ($selects == false || in_array($field->name, $selects)) {
                        $field_array["shown"] = true;
                        $showFieldCount++;
                    } else {
                        $field_array["shown"] = false;
                    }
                    if (isset($tablelinks[$field->name])) {
                        $field_array["link"] = $tablelinks[$field->name];
                        $field_array["haslink"] = true;
                    }
                    if (isset($table_formatters[$field->name])) {
                        $field_array["formatter"] = $table_formatters[$field->name];
                    }
                    if (isset($table_editors[$field->name])) {
                        $field_array["editor"] = $table_editors[$field->name];
                    }
                    $fields[] = $field_array;
                }
                $arr_datas = array();
                foreach ($datas as $row) {
                    $d = array();
                    foreach ($row as $col) {
                        $d[] = htmlentities($col, ENT_COMPAT, "UTF-8");
                    }
                    $arr_datas[] = $d;
                }
                if ($totalRows < ($end = $startRow + $pageCount)) {
                    $end = $totalRows;
                }
                $this->smartyview->assign("connid", $connid);
                $this->smartyview->assign("viewname", $viewname);
                $this->smartyview->assign("totalrow", $totalRows);
                $this->smartyview->assign("start", $startRow + 1);
                $this->smartyview->assign("end", $end);
                $this->smartyview->assign("pageNo", $pageNo);
                $this->smartyview->assign("pageNum", $pageNum);
                $this->smartyview->assign("fields", $fields);
                $this->smartyview->assign("showFieldCount", $showFieldCount);
                $this->smartyview->assign("datas", $arr_datas);
                $this->smartyview->assign("pkColumnNames", $pkColumnNames);
                $this->smartyview->assign("nav_str", $this->_get_page_nav_str($pageNo, $pageNum));
                $this->smartyview->assign("rpf", "tableeditor");
                $this->_assign_table_alias($creatorid, $appid);
                $this->_assign_table_editor_settings($creatorid, $appid);
                $filter_column = $this->input->get("filter_column");
                $filter_value = $this->input->get("filter_value");
                if (!empty($filter_column)) {
                    $this->smartyview->assign("filter_column", $filter_column);
                    $this->smartyview->assign("filter_value", $filter_value);
                    $this->smartyview->assign("srctable", $this->input->get("srctable"));
                }
                $at = $this->input->post("__at__");
                if (!empty($at)) {
                    $this->smartyview->assign("_resultset", true);
                }
                $this->smartyview->assign("app_file", "runtime/app.tableeditor.tpl");
                $this->smartyview->display("runtime/index.tpl");
            }
        }
    }
    public function dynamodb_tablesearch()
    {
        $this->load->library("smartyview");
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select connid from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query->num_rows() != 1) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $mongo_db = $this->_get_mongodb($creatorid, $connid);
        $pageNo = $this->input->post("pageNo");
        $viewname = $this->input->post("viewname");
        $pageCount = $this->config->item("settings_table_lines");
        $startRow = $pageCount * ($pageNo - 1);
        $orderColumnName = $this->input->post("orderColumnName");
        $orderMethod = $this->input->post("orderMethod");
        $sort = false;
        if (($orderMethod == "1" || $orderMethod == 2) && !empty($orderColumnName)) {
            $sort = array($orderColumnName => $orderMethod == "1" ? 1 : -1);
        }
        $mongo_filter = $this->input->post("mongo_filter");
        $hasFilters = false;
        $filters = array();
        if (!empty($mongo_filter)) {
            $filters = json_decode($mongo_filter, true);
            dbface_log("info", "Parse JSON Filter ", array("json" => $mongo_filter, "result" => json_last_error()));
            if (!$filters || empty($filters) || $filters == NULL) {
                $hasFilters = false;
            } else {
                if (json_last_error() != JSON_ERROR_NONE) {
                    $hasFilters = false;
                    $filters = array();
                } else {
                    $cmd_result = $mongo_db->tryJSONCommand($filters, $viewname);
                    if ($cmd_result) {
                        $do_search = isset($cmd_result["do_search"]) ? $cmd_result["do_search"] : false;
                        echo json_encode(array("result" => json_encode($cmd_result, JSON_PRETTY_PRINT), "do_search" => $do_search));
                        return NULL;
                    }
                    $hasFilters = true;
                }
            }
        }
        $totalRows = $mongo_db->countCollection($viewname, 0, 0, $filters);
        $cursor = $mongo_db->queryCollection($viewname, $startRow, $pageCount, $filters, $sort);
        $pageNum = ceil($totalRows / $pageCount);
        $datas = array();
        $fields = array();
        $field_names = array();
        foreach ($cursor as $row) {
            $row = $row->jsonSerialize();
            $row_data = array();
            foreach ($row as $k => $v) {
                if (!in_array($k, $field_names)) {
                    $field_names[] = $k;
                    $field_data = array();
                    $field_data["name"] = $k;
                    if (is_integer($v)) {
                        $field_data["type"] = CDT_NUMBERIC;
                    } else {
                        if (is_numeric($v)) {
                            $field_data["type"] = CDT_NUMBERIC;
                        } else {
                            $field_data["type"] = CDT_TEXTFIELD;
                        }
                    }
                    if ($k == "_id") {
                        $field_data["primary"] = true;
                    }
                    $field_data["shown"] = 1;
                    $fields[] = $field_data;
                }
                $row_data[$k] = mongo_object_to_string($v);
            }
            $datas[] = $row_data;
        }
        $showFieldCount = count($fields);
        if ($totalRows < ($end = $startRow + $this->config->item("settings_table_lines"))) {
            $end = $totalRows;
        }
        $strs = explode(".", $viewname);
        if (count($strs) == 2 && $strs[1] == "files") {
            $this->smartyview->assign("gridfs_view", 1);
        }
        $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $saved_filters = $query->result_array();
            $this->smartyview->assign("saved_filters", $saved_filters);
        }
        $this->smartyview->assign("hasFilters", $hasFilters);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("viewname", $viewname);
        $this->smartyview->assign("totalrow", $totalRows);
        $this->smartyview->assign("start", $startRow + 1);
        $this->smartyview->assign("end", $end);
        $this->smartyview->assign("pageNo", $pageNo);
        $this->smartyview->assign("pageNum", $pageNum);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("showFieldCount", $showFieldCount);
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->assign("orderColumnName", $orderColumnName);
        $this->smartyview->assign("orderMethod", $orderMethod);
        $this->smartyview->assign("nav_str", $this->_get_page_nav_str_mongo($pageNo, $pageNum));
        echo json_encode(array("banner" => $this->smartyview->fetch("new/pagebanner.mongo.tpl"), "datagrid" => $this->smartyview->fetch("datagrid.mongo.tpl")));
    }
    public function mongo_tablesearch()
    {
        $this->load->library("smartyview");
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select connid from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query->num_rows() != 1) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $mongo_db = $this->_get_mongodb($creatorid, $connid);
        $pageNo = $this->input->post("pageNo");
        $viewname = $this->input->post("viewname");
        $pageCount = $this->config->item("settings_table_lines");
        $startRow = $pageCount * ($pageNo - 1);
        $orderColumnName = $this->input->post("orderColumnName");
        $orderMethod = $this->input->post("orderMethod");
        $sort = false;
        if (($orderMethod == "1" || $orderMethod == 2) && !empty($orderColumnName)) {
            $sort = array($orderColumnName => $orderMethod == "1" ? 1 : -1);
        }
        $mongo_filter = $this->input->post("mongo_filter");
        $hasFilters = false;
        $filters = array();
        if (!empty($mongo_filter)) {
            $filters = json_decode($mongo_filter, true);
            dbface_log("info", "Parse JSON Filter ", array("json" => $mongo_filter, "result" => json_last_error()));
            if (!$filters || empty($filters) || $filters == NULL) {
                $hasFilters = false;
            } else {
                if (json_last_error() != JSON_ERROR_NONE) {
                    $hasFilters = false;
                    $filters = array();
                } else {
                    $cmd_result = $mongo_db->tryJSONCommand($filters, $viewname);
                    if ($cmd_result) {
                        $do_search = isset($cmd_result["do_search"]) ? $cmd_result["do_search"] : false;
                        echo json_encode(array("result" => json_encode($cmd_result, JSON_PRETTY_PRINT), "do_search" => $do_search));
                        return NULL;
                    }
                    $hasFilters = true;
                }
            }
        }
        $totalRows = $mongo_db->countCollection($viewname, 0, 0, $filters);
        $cursor = $mongo_db->queryCollection($viewname, $startRow, $pageCount, $filters, $sort);
        $pageNum = ceil($totalRows / $pageCount);
        $datas = array();
        $fields = array();
        $field_names = array();
        foreach ($cursor as $row) {
            $row = $row->jsonSerialize();
            $row_data = array();
            foreach ($row as $k => $v) {
                if (!in_array($k, $field_names)) {
                    $field_names[] = $k;
                    $field_data = array();
                    $field_data["name"] = $k;
                    if (is_integer($v)) {
                        $field_data["type"] = CDT_NUMBERIC;
                    } else {
                        if (is_numeric($v)) {
                            $field_data["type"] = CDT_NUMBERIC;
                        } else {
                            $field_data["type"] = CDT_TEXTFIELD;
                        }
                    }
                    if ($k == "_id") {
                        $field_data["primary"] = true;
                    }
                    $field_data["shown"] = 1;
                    $fields[] = $field_data;
                }
                $row_data[$k] = mongo_object_to_string($v);
            }
            $datas[] = $row_data;
        }
        $showFieldCount = count($fields);
        if ($totalRows < ($end = $startRow + $this->config->item("settings_table_lines"))) {
            $end = $totalRows;
        }
        $strs = explode(".", $viewname);
        if (count($strs) == 2 && $strs[1] == "files") {
            $this->smartyview->assign("gridfs_view", 1);
        }
        $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $saved_filters = $query->result_array();
            $this->smartyview->assign("saved_filters", $saved_filters);
        }
        $this->smartyview->assign("hasFilters", $hasFilters);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("viewname", $viewname);
        $this->smartyview->assign("totalrow", $totalRows);
        $this->smartyview->assign("start", $startRow + 1);
        $this->smartyview->assign("end", $end);
        $this->smartyview->assign("pageNo", $pageNo);
        $this->smartyview->assign("pageNum", $pageNum);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("showFieldCount", $showFieldCount);
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->assign("orderColumnName", $orderColumnName);
        $this->smartyview->assign("orderMethod", $orderMethod);
        $this->smartyview->assign("nav_str", $this->_get_page_nav_str_mongo($pageNo, $pageNum));
        echo json_encode(array("banner" => $this->smartyview->fetch("new/pagebanner.mongo.tpl"), "datagrid" => $this->smartyview->fetch("datagrid.mongo.tpl")));
    }
    public function tablesearch()
    {
        $this->load->library("smartyview");
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->database();
        $query = $this->db->query("select connid, script from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query->num_rows() != 1) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $this->load->helper("json");
        $script_json = json_decode($appinfo["script"], true);
        $selects = isset($script_json["select"]) && !empty($script_json["select"]) ? $script_json["select"] : false;
        $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
        $db = $this->_get_db($creatorid, $connid);
        $db->cache_off();
        $pageNo = $this->input->post("pageNo");
        $viewname = $this->input->post("viewname");
        $pageCount = $this->config->item("settings_table_lines");
        $startRow = $pageCount * ($pageNo - 1);
        $orderColumnName = $this->input->post("orderColumnName");
        $orderMethod = $this->input->post("orderMethod");
        $sqlcondition = $this->input->post("sqlcondition");
        $sqljoin = $this->input->post("sqljoin");
        $sqlop = $this->input->post("sqlop");
        $sqlvalue = $this->input->post("sqlvalue");
        if ($orderColumnName && ($orderMethod == 1 || $orderMethod == 2)) {
            $db->order_by($orderColumnName, $orderMethod == 1 ? "asc" : "desc");
        }
        if ($sqlcondition && $sqljoin && $sqlop && $sqlvalue) {
            $this->build_filter($db, $creatorid, $connid, $sqlcondition, $sqljoin, $sqlop, $sqlvalue);
        }
        $len_con = count($sqlcondition);
        $hasFilters = false;
        $i = 0;
        if ($i < $len_con) {
            if ($sqlop[$i] == "ignore" || $sqlcondition[$i] == "ignore") {
                continue;
            }
            $hasFilters = true;
            break;
        }
        $totalRows = $db->count_all_results($viewname);
        $pageNum = ceil($totalRows / $pageCount);
        if ($orderColumnName && ($orderMethod == 1 || $orderMethod == 2)) {
            $db->order_by($orderColumnName, $orderMethod == 1 ? "asc" : "desc");
        }
        if ($sqlcondition && $sqljoin && $sqlop && $sqlvalue) {
            $this->build_filter($db, $creatorid, $connid, $sqlcondition, $sqljoin, $sqlop, $sqlvalue);
        }
        if ($selects) {
            $query = $db->select($selects)->get($viewname, $pageCount, $startRow);
        } else {
            $query = $db->get($viewname, $pageCount, $startRow);
        }
        $this->_log_app_last_query($appid, $db);
        $datas = $query->result_array();
        $pkColumnNames = array();
        $fields = array();
        $all_field_data = field_data($db, $viewname);
        $field_data = array();
        if ($selects) {
            foreach ($selects as $select) {
                foreach ($all_field_data as &$row) {
                    if ($select == $row->name) {
                        $field_data[] = $row;
                        break;
                    }
                }
            }
        } else {
            $field_data = $all_field_data;
        }
        $fieldNum = count($field_data);
        $showFieldCount = 0;
        $tablelinks = array();
        $query = $this->db->query("select dsttable, dstcolumn, srccolumn from dc_tablelinks where srctable = ? and creatorid = ? and connid = ?", array($viewname, $creatorid, $connid));
        if (0 < $query->num_rows()) {
            $links = $query->result_array();
            foreach ($links as $link) {
                $tablelinks[$link["srccolumn"]] = $link;
            }
        }
        $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "formatter"))->get("dc_app_options");
        $table_formatters = array();
        if (0 < $query->num_rows()) {
            $result = $query->result_array();
            foreach ($result as $row) {
                $extra_key = $row["key"];
                $extra_value = $row["value"];
                $table_formatters[$extra_key] = $extra_value;
            }
        }
        $table_editors = array();
        $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "editor"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $result = $query->result_array();
            foreach ($result as $row) {
                $extra_key = $row["key"];
                $extra_value = $row["value"];
                $table_editors[$extra_key] = $extra_value;
            }
        }
        $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $saved_filters = $query->result_array();
            $this->smartyview->assign("saved_filters", $saved_filters);
        }
        foreach ($field_data as $field) {
            $field_array = array();
            $field_array["type"] = $this->get_input_type($field->type, $field->max_length);
            $field_array["name"] = $field->name;
            $field_array["len"] = $field->max_length;
            if (property_exists($field, "primary_key") && $field->primary_key == 1) {
                $field_array["primary"] = true;
                $pkColumnNames[] = $field->name;
            } else {
                $field_array["primary"] = false;
            }
            if ($selects == false || in_array($field->name, $selects)) {
                $field_array["shown"] = true;
                $showFieldCount++;
            } else {
                $field_array["shown"] = false;
            }
            if (isset($tablelinks[$field->name])) {
                $field_array["link"] = $tablelinks[$field->name];
                $field_array["haslink"] = true;
            }
            if (isset($table_formatters[$field->name])) {
                $field_array["formatter"] = $table_formatters[$field->name];
            }
            if (isset($table_editors[$field->name])) {
                $field_array["editor"] = $table_editors[$field->name];
            }
            $fields[] = $field_array;
        }
        $arr_datas = array();
        foreach ($datas as $row) {
            $d = array();
            foreach ($row as $col) {
                $d[] = htmlentities($col, ENT_COMPAT, "UTF-8");
            }
            $arr_datas[] = $d;
        }
        if ($totalRows < ($end = $startRow + $this->config->item("settings_table_lines"))) {
            $end = $totalRows;
        }
        $this->_assign_table_alias($creatorid, $appid);
        $this->_assign_table_editor_settings($creatorid, $appid);
        $this->smartyview->assign("hasFilters", $hasFilters);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("viewname", $viewname);
        $this->smartyview->assign("totalrow", $totalRows);
        $this->smartyview->assign("start", $startRow + 1);
        $this->smartyview->assign("end", $end);
        $this->smartyview->assign("pageNo", $pageNo);
        $this->smartyview->assign("pageNum", $pageNum);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("showFieldCount", $showFieldCount);
        $this->smartyview->assign("datas", $arr_datas);
        $this->smartyview->assign("pkColumnNames", $pkColumnNames);
        $this->smartyview->assign("orderColumnName", $orderColumnName);
        $this->smartyview->assign("orderMethod", $orderMethod);
        $this->smartyview->assign("nav_str", $this->_get_page_nav_str($pageNo, $pageNum));
        echo json_encode(array("banner" => $this->smartyview->fetch("new/pagebanner.tpl"), "datagrid" => $this->smartyview->fetch("datagrid.tpl")));
    }
    public function _get_page_nav_str_dynamodb($pageNo, $pageNum)
    {
        $this->lang->load("message");
        $this->load->helper("language");
        $str = "[";
        if (1 < $pageNo) {
            $str .= "<a href='javascript:sv_search_dynamodb(1)'>" . lang("strFirst") . "</a>/<a href='javascript:sv_search_dynamodb(" . ($pageNo - 1) . ")'>" . lang("strPrev") . "</a>";
        } else {
            $str .= lang("strFirst") . "/" . lang("strPrev");
        }
        $str .= "]";
        $lineNum = $pageNum;
        if (15 < $pageNum) {
            $lineNum = (int) floor(($pageNo - 1) / 15) * 15 + 15;
            if ($pageNum < $lineNum) {
                $lineNum = $pageNum;
            }
        }
        for ($i = (int) (floor(($pageNo - 1) / 15) * 15); $i < $lineNum; $i++) {
            if ($pageNo == $i + 1) {
                $str .= "<strong>" . $pageNo . "</strong>";
                if ($pageNo != $pageNum) {
                    $str .= ", ";
                } else {
                    $str .= " ";
                }
            } else {
                $str .= "<a href='javascript:sv_search_dynamodb(" . ($i + 1) . ")'>" . ($i + 1) . "</a>";
                if ($i + 1 != $pageNum) {
                    $str .= ", ";
                } else {
                    $str .= " ";
                }
            }
        }
        if (15 < $pageNum) {
            $str .= "<select name='pageno_tmp' onchange='javascript:sv_search_dynamodb(this.options[this.selectedIndex].value)'>";
            for ($i = 0; $i < ceil($pageNum / 15); $i++) {
                if ($i * 15 + 1 <= $pageNo && $pageNo <= ($i + 1) * 15) {
                    $str .= "<option value='" . ($i * 15 + 1) . "' selected>" . ($i * 15 + 1) . " - ";
                    if ($pageNum < ($i + 1) * 15) {
                        $str .= $pageNum;
                    } else {
                        $str .= (int) ($i + 1) * 15;
                    }
                } else {
                    $str .= "<option value='" . ($i * 15 + 1) . "'>" . ($i * 15 + 1) . " - ";
                    if ($pageNum < ($i + 1) * 15) {
                        $str .= $pageNum;
                    } else {
                        $str .= (int) ($i + 1) * 15;
                    }
                }
            }
            $str .= "</select>";
        }
        $str .= "[";
        if ($pageNo < $pageNum) {
            $str .= "<a href='javascript:sv_search_dynamodb(" . ($pageNo + 1) . ")'>" . lang("strNext") . "</a>";
        } else {
            $str .= lang("strNext");
        }
        $str .= "/";
        if ($pageNo < $pageNum) {
            $str .= "<a href='javascript:sv_search_dynamodb(" . $pageNum . ")'>" . lang("strLast") . "</a>";
        } else {
            $str .= lang("strLast");
        }
        $str .= "]";
        return $str;
    }
    public function _get_page_nav_str_mongo($pageNo, $pageNum)
    {
        $this->lang->load("message");
        $this->load->helper("language");
        $str = "[";
        if (1 < $pageNo) {
            $str .= "<a href='javascript:sv_search_mongo(1)'>" . lang("strFirst") . "</a>/<a href='javascript:sv_search_mongo(" . ($pageNo - 1) . ")'>" . lang("strPrev") . "</a>";
        } else {
            $str .= lang("strFirst") . "/" . lang("strPrev");
        }
        $str .= "]";
        $lineNum = $pageNum;
        if (15 < $pageNum) {
            $lineNum = (int) floor(($pageNo - 1) / 15) * 15 + 15;
            if ($pageNum < $lineNum) {
                $lineNum = $pageNum;
            }
        }
        for ($i = (int) (floor(($pageNo - 1) / 15) * 15); $i < $lineNum; $i++) {
            if ($pageNo == $i + 1) {
                $str .= "<strong>" . $pageNo . "</strong>";
                if ($pageNo != $pageNum) {
                    $str .= ", ";
                } else {
                    $str .= " ";
                }
            } else {
                $str .= "<a href='javascript:sv_search_mongo(" . ($i + 1) . ")'>" . ($i + 1) . "</a>";
                if ($i + 1 != $pageNum) {
                    $str .= ", ";
                } else {
                    $str .= " ";
                }
            }
        }
        if (15 < $pageNum) {
            $str .= "<select name='pageno_tmp' onchange='javascript:sv_search_mongo(this.options[this.selectedIndex].value)'>";
            for ($i = 0; $i < ceil($pageNum / 15); $i++) {
                if ($i * 15 + 1 <= $pageNo && $pageNo <= ($i + 1) * 15) {
                    $str .= "<option value='" . ($i * 15 + 1) . "' selected>" . ($i * 15 + 1) . " - ";
                    if ($pageNum < ($i + 1) * 15) {
                        $str .= $pageNum;
                    } else {
                        $str .= (int) ($i + 1) * 15;
                    }
                } else {
                    $str .= "<option value='" . ($i * 15 + 1) . "'>" . ($i * 15 + 1) . " - ";
                    if ($pageNum < ($i + 1) * 15) {
                        $str .= $pageNum;
                    } else {
                        $str .= (int) ($i + 1) * 15;
                    }
                }
            }
            $str .= "</select>";
        }
        $str .= "[";
        if ($pageNo < $pageNum) {
            $str .= "<a href='javascript:sv_search_mongo(" . ($pageNo + 1) . ")'>" . lang("strNext") . "</a>";
        } else {
            $str .= lang("strNext");
        }
        $str .= "/";
        if ($pageNo < $pageNum) {
            $str .= "<a href='javascript:sv_search_mongo(" . $pageNum . ")'>" . lang("strLast") . "</a>";
        } else {
            $str .= lang("strLast");
        }
        $str .= "]";
        return $str;
    }
    public function _get_page_nav_str($pageNo, $pageNum)
    {
        $this->lang->load("message");
        $this->load->helper("language");
        $str = "[";
        if (1 < $pageNo) {
            $str .= "<a href='javascript:sv_search(1)'>" . lang("strFirst") . "</a>/<a href='javascript:sv_search(" . ($pageNo - 1) . ")'>" . lang("strPrev") . "</a>";
        } else {
            $str .= lang("strFirst") . "/" . lang("strPrev");
        }
        $str .= "]";
        $lineNum = $pageNum;
        if (15 < $pageNum) {
            $lineNum = (int) floor(($pageNo - 1) / 15) * 15 + 15;
            if ($pageNum < $lineNum) {
                $lineNum = $pageNum;
            }
        }
        for ($i = (int) (floor(($pageNo - 1) / 15) * 15); $i < $lineNum; $i++) {
            if ($pageNo == $i + 1) {
                $str .= "<strong>" . $pageNo . "</strong>";
                if ($pageNo != $pageNum) {
                    $str .= ", ";
                } else {
                    $str .= " ";
                }
            } else {
                $str .= "<a href='javascript:sv_search(" . ($i + 1) . ")'>" . ($i + 1) . "</a>";
                if ($i + 1 != $pageNum) {
                    $str .= ", ";
                } else {
                    $str .= " ";
                }
            }
        }
        if (15 < $pageNum) {
            $str .= "<select name='pageno_tmp' onchange='javascript:sv_search(this.options[this.selectedIndex].value)'>";
            for ($i = 0; $i < ceil($pageNum / 15); $i++) {
                if ($i * 15 + 1 <= $pageNo && $pageNo <= ($i + 1) * 15) {
                    $str .= "<option value='" . ($i * 15 + 1) . "' selected>" . ($i * 15 + 1) . " - ";
                    if ($pageNum < ($i + 1) * 15) {
                        $str .= $pageNum;
                    } else {
                        $str .= (int) ($i + 1) * 15;
                    }
                } else {
                    $str .= "<option value='" . ($i * 15 + 1) . "'>" . ($i * 15 + 1) . " - ";
                    if ($pageNum < ($i + 1) * 15) {
                        $str .= $pageNum;
                    } else {
                        $str .= (int) ($i + 1) * 15;
                    }
                }
            }
            $str .= "</select>";
        }
        $str .= "[";
        if ($pageNo < $pageNum) {
            $str .= "<a href='javascript:sv_search(" . ($pageNo + 1) . ")'>" . lang("strNext") . "</a>";
        } else {
            $str .= lang("strNext");
        }
        $str .= "/";
        if ($pageNo < $pageNum) {
            $str .= "<a href='javascript:sv_search(" . $pageNum . ")'>" . lang("strLast") . "</a>";
        } else {
            $str .= lang("strLast");
        }
        $str .= "]";
        return $str;
    }
    public function ls()
    {
        $appid = $this->input->post("appid");
        $script = $this->input->post("onchange_script");
        $this->load->helper("json");
        $this->load->database();
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select connid, form from dc_app where creatorid=? and appid=?", array($creatorid, $appid));
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $db = $this->_get_db($creatorid, $appinfo["connid"]);
        if (empty($script)) {
            $formdata = json_decode($appinfo["form"], true);
            $script = trim($this->_compile_appscripts($db, $creatorid, $appinfo["connid"], urldecode($formdata["loadingscript"])));
        } else {
            $script = trim($this->_compile_appscripts($db, $creatorid, $appinfo["connid"], urldecode($script)));
        }
        if (empty($script)) {
            return NULL;
        }
        log_message("debug", "ls: run script:" . $script);
        $db->db_debug = false;
        try {
            $query = @$db->query($script);
        } catch (Exception $e) {
        }
        if ($query) {
            $num_rows = $query->num_rows();
            if ($num_rows == 0) {
                $formwarning = "no data found!";
            } else {
                if (1 < $num_rows) {
                    $formwarning = $num_rows . " rows fetched. Click <a href='javascript:form_prev();' class='subopen'>previous</a> or <a href='javascript:form_next();' class='subopen'>Next</a> to navigate.(Current Row:&nbsp;<span id='formIndex'>1</span>)";
                } else {
                    $formwarning = "";
                }
            }
            $data = $query->result_array();
            $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 1, "message" => $formwarning, "data" => $data)));
        } else {
            log_message("error", "Query Failed:" . $db->last_query());
            $error = $db->error();
            $error_code = $error && isset($error["code"]) ? $error["code"] : 0;
            $error_message = $error && isset($error["message"]) ? $error["message"] : 0;
            $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 0, "code" => "Change Script Execution Failed: " . $error_code, "message" => $error_message, "data" => array())));
        }
    }
    public function src()
    {
        $appid = $this->input->post("appid");
        $srctype = $this->input->post("st");
        $datasource = $this->input->post("ss");
        $ret = array();
        if ($srctype == "directly") {
            $data = explode("<br>", str_replace(array("\r\n", "\r", "\n"), "<br>", $datasource));
            foreach ($data as $row) {
                $option = explode(":", $row);
                if (count($option) == 1) {
                    $ret[] = array("key" => $option[0], "value" => $option[0]);
                } else {
                    if (2 <= count($option)) {
                        $ret[] = array("key" => $option[0], "value" => $option[1]);
                    }
                }
            }
        } else {
            if ($srctype == "mappingscript") {
                $this->load->database();
                $creatorid = $this->session->userdata("login_creatorid");
                $query = $this->db->query("select connid from dc_app where creatorid=? and appid=?", array($creatorid, $appid));
                $appinfo = $query->row_array();
                $db = $this->_get_db($creatorid, $appinfo["connid"]);
                if (!$db) {
                    dbface_log("error", "Can not connect to database to execute form mapping script");
                    $this->output->set_content_type("application/json")->set_output(json_encode(array()));
                    return NULL;
                }
                $script = $this->_compile_appscripts($db, $creatorid, $appinfo["connid"], urldecode($datasource));
                dbface_log("info", "Application src query " . $script);
                $query = $db->query($script);
                if ($query) {
                    $fields = $query->list_fields();
                    $fieldnum = count($fields);
                    $data = $query->result_array();
                    dbface_log("info", "fieldnum: " . $fieldnum . ", result: " . print_r($data, true));
                    if ($fieldnum == 1) {
                        foreach ($data as $row) {
                            $ret[] = array("key" => $row[$fields[0]], "value" => $row[$fields[0]]);
                        }
                    } else {
                        if (2 <= $fieldnum) {
                            foreach ($data as $row) {
                                $ret[] = array("key" => $row[$fields[0]], "value" => $row[$fields[1]]);
                            }
                        }
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($ret));
    }
    public function rowupdate()
    {
        $success = false;
        $message = NULL;
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->database();
        $query = $this->db->query("select connid, script from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query->num_rows() != 1) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        $pkColumnNames = $this->input->post("pkColumnNames");
        $pkColumnValues = $this->input->post("pkColumnValues");
        $viewname = $this->input->post("viewname");
        $len = count($pkColumnNames);
        $this->load->helper("json");
        if ($len == 0) {
            $success = false;
            $message = "No primary key found for this table!";
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => $success, "message" => $message)));
        } else {
            for ($i = 0; $i < $len; $i++) {
                $db->where($pkColumnNames[$i], $pkColumnValues[$i]);
            }
            $keyColumns = $this->input->get_post("keyColumns");
            $keyChecks = $this->input->get_post("keyChecks");
            $keyValues = $this->input->get_post("keyValues");
            $len = count($keyColumns);
            $require_update = false;
            for ($i = 0; $i < $len; $i++) {
                if ($keyChecks[$i] == 1) {
                    $db->set($keyColumns[$i], $keyValues[$i]);
                    $require_update = true;
                }
            }
            if (!$require_update) {
                $success = false;
                $message = "Please check all the fields that need to change!";
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => $success, "message" => $message)));
            } else {
                if ($db->dbdriver != "oci" && $db->dbdriver != "oci8" && $db->dbdriver != "sqlite3") {
                    $db->limit(1);
                }
                $result = $db->update($viewname);
                if ($result) {
                    if ($db->affected_rows() == 1) {
                        $success = true;
                        $message = "The specified row has been updated!";
                    } else {
                        $success = true;
                        $message = "The specified row has been updated!:<p/>But no row affected, the specified row may has been deleted.</p>";
                    }
                } else {
                    $success = false;
                    $error = $db->error();
                    $message = "Error occurs when updating the selected row:<p/><b>" . $error["code"] . ":</b>" . $error["message"] . ".</p>";
                }
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => $success, "message" => $message)));
            }
        }
    }
    public function rowdelete()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            return NULL;
        }
        $this->load->database();
        $query = $this->db->query("select connid, script from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query->num_rows() != 1) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        $pkColumnNames = $this->input->post("pkColumnNames");
        $pkColumnValues = $this->input->post("pkColumnValues");
        $viewname = $this->input->post("viewname");
        $this->load->helper("json");
        $len = count($pkColumnNames);
        if (0 < $len) {
            for ($i = 0; $i < $len; $i++) {
                $db->where($pkColumnNames[$i], $pkColumnValues[$i]);
            }
        } else {
            $keyColumns = $this->input->post("keyColumns");
            $keyValues = $this->input->post("keyValues");
            $len = count($keyColumns);
            for ($i = 0; $i < $len; $i++) {
                $db->where($keyColumns[$i], $keyValues[$i]);
            }
        }
        if ($db->dbdriver == "mysql" || $db->dbdriver == "mysqli") {
            $db->limit(1);
        }
        $success = false;
        $result = $db->delete($viewname);
        if ($result) {
            if ($db->affected_rows() == 1) {
                $success = true;
                $message = "<b>1 row has been deleted!</b>";
            } else {
                $success = false;
                $message = "<b>Error occurs when deleting following data:</b><p>Unknow database error or the specified has been deleted.</p>";
            }
        } else {
            $success = false;
            $error = $db->error();
            $message = "<b>Error occurs when deleting the row data:</b><p><b>" . $error["code"] . ":</b>" . $error["message"] . ".</p>";
        }
        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => $success, "message" => $message)));
    }
    public function rowinsert()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->database();
        $query = $this->db->query("select connid, script from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query->num_rows() != 1) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        $columnNames = $this->input->get_post("columnNames");
        $keyValues = $this->input->get_post("keyValues");
        $viewname = $this->input->get_post("viewname");
        $len = count($columnNames);
        $data = array();
        for ($i = 0; $i < $len; $i++) {
            if (!empty($keyValues[$i])) {
                if ($this->is_sql_null($keyValues[$i])) {
                    $data[$columnNames[$i]] = NULL;
                } else {
                    $data[$columnNames[$i]] = $keyValues[$i];
                }
            }
        }
        $this->load->helper("json");
        $success = false;
        $result = $db->insert($viewname, $data);
        if ($result) {
            if ($db->affected_rows() == 1) {
                $success = true;
                $message = "<b>1 Row has been Inserted.</b>";
            } else {
                $success = true;
                $message = "<b>Error occurs when inserting the row data:</b><p>Unknow database error." . mysql_error() . "</p>";
            }
        } else {
            $success = false;
            $error = $db->error();
            $message = "<b>Error occurs when inserting the row data:</b><p><b>" . $error["code"] . ":</b>" . $error["message"] . ".</p>";
        }
        $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => $success, "message" => $message)));
    }
    public function _execute_story_app($appinfo)
    {
        $at = $this->input->post("__at__");
        $options = $this->_assign_app_options($appinfo["options"]);
        $script_json = json_decode($appinfo["script"], true);
        $this->smartyview->assign("stories", $script_json);
        $parameters = $this->_get_request_data();
        $this->smartyview->assign("parameters", $parameters);
        if (!empty($at)) {
            $storydisplayas = $options["storydisplayas"];
            if (empty($storydisplayas) || $storydisplayas == "carousel") {
                $this->smartyview->display("story/story.content.tpl");
            } else {
                if ($storydisplayas == "tabs") {
                    $this->smartyview->display("story/story.tabs.content.tpl");
                } else {
                    if ($storydisplayas == "accordion") {
                        $this->smartyview->display("story/story.accordion.content.tpl");
                    } else {
                        $this->smartyview->display("story/story.content.tpl");
                    }
                }
            }
        } else {
            $this->smartyview->display("story/index.tpl");
        }
    }
    public function _execute_freeformdisplay_app($appinfo)
    {
        $at = $this->input->post("__at__");
        $options = $this->_assign_app_options($appinfo["options"]);
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $previewmode = $this->input->get_post("preview");
        $this->smartyview->assign("previewmode", $previewmode);
        $this->smartyview->assign("appid", $appinfo["appid"]);
        $this->smartyview->assign("rpf", "htmlreport");
        $formdata = json_decode($appinfo["form"], true);
        $db = $this->_get_db($creatorid, $connid);
        $showInForm = false;
        if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
            $this->smartyview->assign("FORM_ID", uniqid("FM_"));
            $this->smartyview->assign("formTitle", $formdata["name"]);
            $this->smartyview->assign("formCss", $formdata["css"]);
            $this->smartyview->assign("formDisplay", $formdata["display"]);
            $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
            $this->_assign_form_html($db, $creatorid, $connid, $formdata);
            $showInForm = true;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        $htmlreport = $this->_compile_appscripts($db, $creatorid, $connid, $appinfo["script"], false, "[{", "}]");
        $this->smartyview->assign("html", $htmlreport);
        if (!empty($at)) {
            $this->smartyview->display("freeformdisplay/freeformdisplay.content.tpl");
        } else {
            $this->smartyview->display("freeformdisplay/index.tpl");
        }
    }
    public function _execute_gallery_app($appinfo)
    {
        $at = $this->input->post("__at__");
        $formdata = json_decode($appinfo["form"], true);
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
            $this->smartyview->assign("FORM_ID", uniqid("FM_"));
            $this->smartyview->assign("formTitle", $formdata["name"]);
            if (isset($formdata["css"]) && !empty($formdata["css"])) {
                $this->smartyview->assign("formCss", $formdata["css"]);
            }
            if (isset($formdata["display"]) && !empty($formdata["display"])) {
                $this->smartyview->assign("formDisplay", $formdata["display"]);
            }
            $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
            $this->_assign_form_html(false, $creatorid, false, $formdata);
        }
        $options = $this->_assign_app_options($appinfo["options"]);
        $galleries = json_decode($appinfo["script"], true);
        $current_sec_size = 0;
        foreach ($galleries as &$gallery) {
            $appid = $gallery["appid"];
            $info = $this->_get_app_box_info($appid);
            $gallery["name"] = $info["name"];
            $gallery["description"] = $info["description"];
            $current_sec_size = $current_sec_size + intval($gallery["size"]);
            $gallery["row"] = ceil($current_sec_size / 12) - 1;
        }
        $parameters = $this->_get_request_data();
        $this->smartyview->assign("parameters", $parameters);
        $this->smartyview->assign("galleries", $galleries);
        if (!empty($at)) {
            $this->smartyview->display("gallery/gallery.content.tpl");
        } else {
            $this->smartyview->display("gallery/index.tpl");
        }
    }
    public function _execute_dashboard_app($appinfo)
    {
        if ($this->input->get("widget")) {
            $this->_display_app_error("Error", "Dashboard application can not embed in a dashboard application.");
        } else {
            $appid = $appinfo["appid"];
            $options = $this->_assign_app_options($appinfo["options"]);
            $at = $this->input->post("__at__");
            $formdata = json_decode($appinfo["form"], true);
            $creatorid = $this->session->userdata("login_creatorid");
            if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
                $form_id = uniqid("FM_");
                $this->smartyview->assign("FORM_ID", $form_id);
                $this->smartyview->assign("DASHBOARD_MASTER_FORMID", $form_id);
                $this->smartyview->assign("formTitle", $formdata["name"]);
                if (isset($formdata["css"]) && !empty($formdata["css"])) {
                    $this->smartyview->assign("formCss", $formdata["css"]);
                }
                if (isset($formdata["display"]) && !empty($formdata["display"])) {
                    $this->smartyview->assign("formDisplay", $formdata["display"]);
                }
                $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
                $this->_assign_form_html(false, $creatorid, false, $formdata);
            }
            $this->smartyview->assign("dashboardId", $appid);
            $dashboardlayout = $appinfo["script"];
            if (empty($dashboardlayout)) {
                $dashboardlayout = array();
            }
            $this->smartyview->assign("dashboardLayout", $dashboardlayout);
            $parameters = array();
            $form_id = $this->input->post("FORMID");
            if (!empty($form_id)) {
                $parameters["FORMID"] = $form_id;
            }
            $filter = $this->config->item("__filter__");
            if (!empty($filter) && is_array($filter)) {
                foreach ($filter as $n => $filter_data) {
                    $parameters[$n] = $filter_data;
                }
            }
            $this->smartyview->assign("parameters", $parameters);
            if (!empty($at)) {
                $this->smartyview->display("dashboard/dashboard.content.tpl");
            } else {
                $this->smartyview->display("dashboard/index.tpl");
            }
        }
    }
    public function _export_query($format, $filename, $query)
    {
        if ($format == "csv") {
            $this->_export_query_to_csv($filename . ".csv", $query);
        } else {
            if ($format == "html") {
                $this->_export_query_to_html($filename . ".html", $query);
            } else {
                if ($format == "pdf") {
                    $this->_export_query_to_pdf($filename . ".pdf", $query);
                } else {
                    if ($format == "png") {
                        $this->_export_query_to_png($filename . ".png", $query);
                    }
                }
            }
        }
    }
    public function _export_query_to_email($appid)
    {
        $access_url = $this->_get_app_shareurl($appid);
        $query = $this->db->query("select name from dc_app where appid=?", array($appid));
        if (!empty($access_url) && 0 < $query->num_rows()) {
            $appname = $query->row()->name;
            $login_userid = $this->session->userdata("login_userid");
            $query = $this->db->query("select name, email from dc_user where userid = ?", array($login_userid));
            if (0 < $query->num_rows()) {
                $user_info = $query->row();
                $username = $user_info->name;
                $useremail = $user_info->email;
                $to_emails = $this->input->post("value");
                $this->load->library("email");
                $this->_init_email_settings();
                $this->email->from($useremail, $username);
                $this->email->to($to_emails);
                $this->email->subject("DbFace - " . $appname);
                $this->load->library("smartyview");
                $this->smartyview->assign("access_url", $access_url);
                $this->smartyview->assign("name", $appname);
                $this->smartyview->assign("username", $username);
                $this->email->message($this->smartyview->fetch("email/app_access.tpl"));
                $this->email->send();
                echo json_encode(array("status" => 1, "message" => "Email sent"));
                return NULL;
            }
        }
        echo json_encode(array("status" => 0, "message" => "Invalid App"));
    }
    public function _export_query_to_csv($filename, $query)
    {
        $this->load->dbutil();
        $delimiter = ",";
        $newline = "\n";
        $enclosure = "\"";
        $export_csv_settings = $this->config->item("export_csv_settings");
        if ($export_csv_settings && is_array($export_csv_settings)) {
            if (isset($export_csv_settings["delimiter"]) && !empty($export_csv_settings["delimiter"])) {
                $delimiter = $export_csv_settings["delimiter"];
            }
            if (isset($export_csv_settings["newline"]) && !empty($export_csv_settings["newline"])) {
                $newline = $export_csv_settings["newline"];
            }
            if (isset($export_csv_settings["enclosure"]) && !empty($export_csv_settings["enclosure"])) {
                $enclosure = $export_csv_settings["enclosure"];
            }
        }
        $csv = $this->dbutil->csv_from_result($query, $delimiter, $newline, $enclosure);
        $csv_excel_compatible = $this->config->item("csv_excel_compatible");
        if ($csv_excel_compatible !== false) {
            if (function_exists("mb_convert_encoding")) {
                $csv = chr(255) . chr(254) . mb_convert_encoding($csv, "UTF-16LE", "UTF-8");
            } else {
                dbface_log("error", "mbstring module required for Excel Compatible CSV.");
            }
        }
        $this->load->helper("download");
        force_download($filename, $csv);
    }
    public function _export_query_to_html($filename, $query)
    {
        $fields_cnt = $query->num_fields();
        $fields = $query->list_fields();
        $result = $query->result_array();
        $arr_datas = array();
        foreach ($result as $row) {
            $d = array();
            foreach ($row as $col) {
                if (!isset($col) || is_null($col)) {
                    array_push($d, htmlentities($this->replace_null, ENT_COMPAT, "UTF-8"));
                } else {
                    array_push($d, htmlentities($col, ENT_COMPAT, "UTF-8"));
                }
            }
            array_push($arr_datas, $d);
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("title", $filename);
        $this->smartyview->assign("datanum", count($result));
        $this->smartyview->assign("putfieldrow", true);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("datas", $arr_datas);
        $this->load->helper("download");
        $query->free_result();
        force_download($filename, $this->smartyview->fetch("inc/datatable.tpl"));
    }
    public function _export_query_to_png($filename, $query)
    {
    }
    public function _export_query_to_pdf($filename, $query)
    {
        $fields_cnt = $query->num_fields();
        $fields = $query->list_fields();
        $result = $query->result_array();
        $arr_datas = array();
        foreach ($result as $row) {
            $d = array();
            foreach ($row as $col) {
                if (!isset($col) || is_null($col)) {
                    array_push($d, htmlentities($this->replace_null, ENT_COMPAT, "UTF-8"));
                } else {
                    array_push($d, htmlentities($col, ENT_COMPAT, "UTF-8"));
                }
            }
            array_push($arr_datas, $d);
        }
        $CI =& get_instance();
        $CI->load->library("smartyview");
        $CI->smartyview->assign("datanum", count($result));
        $CI->smartyview->assign("putfieldrow", true);
        $CI->smartyview->assign("fields", $fields);
        $CI->smartyview->assign("datas", $arr_datas);
        $html = $this->smartyview->fetch("inc/datatable_pdf.tpl");
        require_once APPPATH . "libraries/tcpdf/tcpdf.php";
        $tcpdf = new TCPDF();
        $tcpdf->SetCreator(PDF_CREATOR);
        $tcpdf->SetAuthor("DbFace PDF Creator");
        $tcpdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN));
        $tcpdf->setFooterFont(array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
        $tcpdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $tcpdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $tcpdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $tcpdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $tcpdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $tcpdf->SetFont("dejavusans", "", 12);
        if (5 < count($fields)) {
            $tcpdf->setPageOrientation("L");
        }
        $tcpdf->AddPage();
        $tcpdf->writeHTML($html, true, false, true, false, "");
        $tcpdf->lastPage();
        $tcpdf->Output($filename, "D");
        $query->free_result();
    }
    public function get_tableeditor_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $appid = $this->input->post("appid");
        $name = $this->input->post("name");
        if (empty($creatorid) || empty($connid) || empty($appid) || empty($name)) {
            return NULL;
        }
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "key" => $name, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            echo $query->row()->value;
        } else {
            echo json_encode(array("status" => 0));
        }
    }
    public function remove_tableeditor_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $appid = $this->input->post("appid");
        $name = $this->input->post("name");
        if (empty($creatorid) || empty($connid) || empty($appid) || empty($name)) {
            return NULL;
        }
        $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "key" => $name, "type" => "filter"));
        echo json_encode(array("status" => 1));
    }
    public function save_tableeditor_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$creatorid) {
            return NULL;
        }
        $connid = $this->input->post("connid");
        $appid = $this->input->post("appid");
        $value = $this->input->post("value");
        $sqlconditions = $this->input->post("sqlcondition");
        $sqlops = $this->input->post("sqlop");
        $sqlvalues = $this->input->post("sqlvalue");
        $sqljoins = $this->input->post("sqljoin");
        $mongo_filter = $this->input->post("mongo_filter");
        $filter_conditions = array("sqlconditions" => $sqlconditions, "sqlops" => $sqlops, "sqlvalues" => $sqlvalues, "sqljoins" => $sqljoins, "mongofilter" => $mongo_filter);
        $where = array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "key" => $value, "type" => "filter");
        $query = $this->db->select("1")->where($where)->get("dc_app_options");
        $new = false;
        if (0 < $query->num_rows()) {
            $this->db->update("dc_app_options", array("value" => json_encode($filter_conditions)), $where);
        } else {
            $where["value"] = json_encode($filter_conditions);
            $this->db->insert("dc_app_options", $where);
            $new = true;
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("saved_filter_key", $value);
        $html = $this->smartyview->fetch("new/pagebanner.filter.tpl");
        echo json_encode(array("status" => 1, "name" => $value, "new" => $new, "html" => $html));
    }
    public function load_js_hooks()
    {
        $appid = $this->input->get_post("appid");
        $query = $this->db->query("select options from dc_app where appid = ?", array($appid));
        $js = "";
        if (0 < $query->num_rows()) {
            $options = json_decode($query->row()->options, true);
            if (isset($options["js_hooks"])) {
                echo $options["js_hooks"];
                return NULL;
            }
        }
        echo $js;
    }
    public function query_linked_data()
    {
        $d = $this->input->post("columnname");
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $appinfo = $this->db->query("select connid from dc_app where appid = ? and creatorid=?", array($appid, $creatorid))->row_array();
        $connid = $appinfo["connid"];
        $tmp = explode(".", $d);
        if (!$tmp || count($tmp) != 2) {
            exit($d . " Please select a column as condition.");
        }
        list($tablename, $columnname) = $tmp;
        $db = $this->_get_db($creatorid, $connid);
        $db->select($columnname)->order_by($columnname, "asc")->from($tablename);
        $max_sample_rows = $this->config->item("max_sample_rows");
        if (!$max_sample_rows) {
            $max_sample_rows = 100;
        }
        $db->limit($max_sample_rows);
        $db->distinct();
        $query = $db->get();
        $this->_log_app_last_query($appid, $db);
        $tmpdatas = $query->result_array();
        $datas = array();
        foreach ($tmpdatas as $tmpdata) {
            $datas[] = $tmpdata[$columnname];
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->display("query_linked_data.tpl");
    }
    public function get_linked_data()
    {
        $connid = $this->input->post("connid");
        $dsttable = $this->input->post("dsttable");
        $dstcolumn = $this->input->post("dstcolumn");
        $value = $this->input->post("value");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        $query = $db->where($dstcolumn, $value)->from($dsttable)->limit(1)->get();
        $result = $query->row_array();
        $this->load->library("smartyview");
        $this->smartyview->assign("data", $result);
        $this->smartyview->display("runtime/inc.linked.data.tpl");
    }
    public function _log_app_last_query($appid, $db)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $last_query = $db->last_query();
        $this->db->insert("dc_app_log", array("creatorid" => $creatorid, "appid" => $appid, "name" => "Query", "type" => "query", "value" => $last_query, "date" => time()));
        dbface_log("info", "Execute Query: " . $last_query, array("appid" => $appid));
    }
    public function _log_app_message($appid, $type, $message, $category = "general")
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->insert("dc_app_log", array("creatorid" => $creatorid, "appid" => $appid, "name" => $type, "type" => $category, "value" => $message, "date" => time()));
        dbface_log("info", $message, array("appid" => $appid, "name" => $type, "type" => $category));
    }
    public function cp_save()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $appid = $this->input->post("appid");
        $query = $this->db->select("connid, script, scripttype")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
        $appinfo = $query->row_array();
        $scripttype = $appinfo["scripttype"];
        $connid = $appinfo["connid"];
        $db = $this->_get_db($creatorid, $connid);
        if ($scripttype == "1") {
            $script_json = json_decode($appinfo["script"], true);
            $sqlconditions = isset($script_json["sqlcondition"]) ? $script_json["sqlcondition"] : false;
            $sqlops = isset($script_json["sqlop"]) ? $script_json["sqlop"] : false;
            $sqlvalues = isset($script_json["sqlvalue"]) ? $script_json["sqlvalue"] : false;
            $sqljoins = isset($script_json["sqljoin"]) ? $script_json["sqljoin"] : false;
            $tablenames = isset($script_json["tablename"]) ? $script_json["tablename"] : false;
            $left_columnnames = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
            $right_columnnames = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : array();
            $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : array();
            $selects = isset($script_json["select"]) ? $script_json["select"] : false;
            $select_funs = isset($script_json["selectfun"]) ? $script_json["selectfun"] : false;
            $select_labels = isset($script_json["selectlabel"]) ? $script_json["selectlabel"] : false;
            $orders = isset($script_json["order"]) ? $script_json["order"] : array();
            $ordertypes = isset($script_json["orderfun"]) ? $script_json["orderfun"] : array();
            $rowlimit = isset($script_json["rowlimit"]) ? $script_json["rowlimit"] : 0;
            $this->make_select($db, $selects, $select_funs, $select_labels);
            if ($orders && 0 < count($orders)) {
                $index = 0;
                foreach ($orders as $order) {
                    $db->order_by($order, $ordertypes[$index++]);
                }
            }
            $this->_make_join_table($db, $tablenames, $left_columnnames, $right_columnnames, $jointype);
            $this->build_filter($db, $creatorid, $connid, $sqlconditions, $sqljoins, $sqlops, $sqlvalues, !$this->_is_gen_sql());
            if (0 < $rowlimit) {
                $db->limit(is_numeric($rowlimit) ? $rowlimit : 10);
            }
            $query = $db->get();
            $this->_log_app_last_query($appid, $db);
            if (!$query) {
                $this->_display_query_error($db);
                return NULL;
            }
            $fields = $query->list_fields();
            $datas = $query->result_array();
            $content = json_encode(array("fields" => $fields, "datas" => $datas));
            if ($this->config->item("cp_max_size") && $this->config->item("cp_max_size") < strlen($content)) {
                echo "!message9";
            } else {
                $this->db->insert("dc_checkpoint", array("appid" => $appid, "recorddate" => time(), "content" => $content));
                $this->_cp_search($appid);
                $this->smartyview->display("runtime/app.checkpoint.table.tpl");
            }
        } else {
            if ($scripttype == "2") {
                $script = $appinfo["script"];
                $sqlcontent = $this->_compile_appscripts($db, $creatorid, $connid, $script);
                $query = $db->query($sqlcontent);
                $this->_log_app_last_query($appid, $db);
                if (!$query) {
                    $this->_display_query_error($db);
                    return NULL;
                }
                $fields = $query->list_fields();
                $datas = $query->result_array();
                $content = json_encode(array("fields" => $fields, "datas" => $datas));
                if ($this->config->item("cp_max_size") && $this->config->item("cp_max_size") < strlen($content)) {
                    echo "!message9";
                } else {
                    $this->db->insert("dc_checkpoint", array("appid" => $appid, "recorddate" => time(), "content" => $content));
                    $this->_cp_search($appid);
                    $this->smartyview->display("runtime/app.checkpoint.table.tpl");
                }
            }
        }
    }
    public function cp_event()
    {
        $cpid = $this->input->post("cpid");
        $query = $this->db->select("appid, content, recorddate")->where("cpid", $cpid)->get("dc_checkpoint");
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $content = json_decode($row["content"], true);
            $date = date("Y-m-d H:i:s", $row["recorddate"]);
            $fields = $content["fields"];
            $datas = $content["datas"];
            $appid = $row["appid"];
            $this->load->library("smartyview");
            $this->smartyview->assign("appid", $appid);
            $this->smartyview->assign("date", $date);
            $this->smartyview->assign("fields", $fields);
            $this->smartyview->assign("datas", $datas);
            $html = $this->smartyview->fetch("runtime/app.checkpoint.eventpop.tpl");
            echo json_encode(array("date" => $date, "html" => $html));
        }
    }
    public function _cp_search($appid)
    {
        $query = $this->db->where("appid", $appid)->order_by("recorddate", "desc")->get("dc_checkpoint");
        $cps = array();
        if ($query && 0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $c = array();
                $c["cpid"] = $row["cpid"];
                $c["paramkey"] = $row["paramkey"];
                $c["date"] = date("Y-m-d H:i:s", $row["recorddate"]);
                $content = json_decode($row["content"], true);
                $c["fields"] = $content["fields"];
                $c["datas"] = $content["datas"];
                $cps[] = $c;
            }
        }
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("datas", $cps);
    }
    public function cp_delete()
    {
        $cpid = $this->input->post("cpid");
        if (!$this->_is_admin_or_developer()) {
            return NULL;
        }
        if (empty($cpid)) {
            return NULL;
        }
        $query = $this->db->select("appid")->where("cpid", $cpid)->get("dc_checkpoint");
        if (0 < $query->num_rows()) {
            $appid = $query->row()->appid;
            $result = $this->db->delete("dc_checkpoint", array("cpid" => $cpid));
            $this->load->library("smartyview");
            $this->_cp_search($appid);
            $this->smartyview->display("runtime/app.checkpoint.table.tpl");
        } else {
            echo "0";
        }
    }
    public function _parse_option_to_chart_legend($options, &$json_data)
    {
        $legend_info = $options["chart_legend"];
        switch ($legend_info) {
            case "horizontal_top_left":
                $json_data["orient"] = "horizontal";
                $json_data["left"] = "left";
                $json_data["top"] = "top";
                break;
            case "horizontal_top_center":
                $json_data["orient"] = "horizontal";
                $json_data["left"] = "center";
                $json_data["top"] = "top";
                break;
            case "horizontal_top_right":
                $json_data["orient"] = "horizontal";
                $json_data["left"] = "right";
                $json_data["top"] = "top";
                break;
            case "horizontal_bottom_left":
                $json_data["orient"] = "horizontal";
                $json_data["left"] = "left";
                $json_data["top"] = "bottom";
                break;
            case "horizontal_bottom_center":
                $json_data["orient"] = "horizontal";
                $json_data["left"] = "center";
                $json_data["top"] = "bottom";
                break;
            case "horizontal_bottom_right":
                $json_data["orient"] = "horizontal";
                $json_data["left"] = "right";
                $json_data["top"] = "bottom";
                break;
            case "vertical_left_top":
                $json_data["orient"] = "vertical";
                $json_data["left"] = "left";
                $json_data["top"] = "top";
                break;
            case "vertical_left_middle":
                $json_data["orient"] = "vertical";
                $json_data["left"] = "left";
                $json_data["top"] = "middle";
                break;
            case "vertical_left_bottom":
                $json_data["orient"] = "vertical";
                $json_data["left"] = "left";
                $json_data["top"] = "bottom";
                break;
            case "vertical_right_top":
                $json_data["orient"] = "vertical";
                $json_data["left"] = "right";
                $json_data["top"] = "top";
                break;
            case "vertical_right_middle":
                $json_data["orient"] = "vertical";
                $json_data["left"] = "right";
                $json_data["top"] = "middle";
                break;
            case "vertical_right_bottom":
                $json_data["orient"] = "vertical";
                $json_data["left"] = "right";
                $json_data["top"] = "bottom";
                break;
        }
    }
    public function _parse_option_to_chart_text_style($options, $key, $default_text_color = "#000000")
    {
        $textStyle = array();
        if (isset($options[$key . "_fontfamily"])) {
            $fontfamily = name_to_fontfamily($options[$key . "_fontfamily"], $options[$key . "_bold"] == "bold", $options[$key . "_italic"]);
            if ($fontfamily) {
                $textStyle["fontFamily"] = $fontfamily;
            } else {
                $textStyle["fontFamily"] = "Open Sans";
            }
        }
        if (isset($options[$key . "_fontSize"])) {
            $textStyle["fontSize"] = $options[$key . "_fontSize"];
        }
        if (isset($options[$key . "_bold"]) && $options[$key . "_bold"] == "bold") {
            $textStyle["fontWeight"] = "bold";
        }
        if (isset($options[$key . "_italic"]) && $options[$key . "_italic"] == "italic") {
            $textStyle["fontStyle"] = "italic";
        }
        if (isset($options[$key . "_textcolor"])) {
            $color = colorname_to_rgb($options[$key . "_textcolor"]);
            if ($color != "default" && $color != "") {
                $textStyle["color"] = $color;
            } else {
                $textStyle["color"] = $default_text_color;
            }
        } else {
            $textStyle["color"] = $default_text_color;
        }
        return $textStyle;
    }
    public function _assign_highchart_options($chart_data, $options, $format)
    {
        return $chart_data;
    }
    public function _assign_chart_options($chart_data, $options, $format)
    {
        $chart_enable_tooltip = $options["chart_enable_tooltip"];
        if (isset($chart_enable_tooltip) && $chart_enable_tooltip == "1") {
            $chart_data["option"]["tooltip"]["show"] = true;
            $chart_tooltip_border_width = $this->config->item("chart_tooltip_border_width");
            if (!$chart_tooltip_border_width) {
                $chart_tooltip_border_width = 1;
            }
            if (isset($options["chart_tooltip_background"]) && $options["chart_tooltip_background"] != "default") {
            }
            if (!empty($options["chart_tooltip_formatter"])) {
                $chart_data["option"]["tooltip"]["formatter"] = $options["chart_tooltip_formatter"];
            }
        } else {
            $chart_data["option"]["tooltip"]["show"] = false;
        }
        if ($format == "googlemap") {
            $namemap = $this->config->item("worldmap_namemap");
            if (!empty($namemap)) {
                $chart_data["option"]["series"][0]["nameMap"] = $namemap;
            }
            return $chart_data;
        }
        $chart_legend = $options["chart_legend"];
        if (isset($chart_legend) && $chart_legend == "0") {
            if ($format == "gauges") {
                if (isset($chart_data["option"]["series"][0]["detail"])) {
                    $chart_data["option"]["series"][0]["detail"]["show"] = false;
                }
            } else {
                if (isset($chart_data["option"]["legend"])) {
                    $chart_data["option"]["legend"]["show"] = false;
                }
            }
        } else {
            if ($format == "gauges") {
            } else {
                if (!isset($chart_data["option"]["legend"])) {
                    $chart_data["option"]["legend"] = array();
                }
                $chart_data["option"]["legend"]["show"] = true;
                if (0 === strpos($options["chart_legend"], "horizontal_top")) {
                    $chart_data["option"]["legend"]["padding"] = array(30, 10);
                }
                $this->_parse_option_to_chart_legend($options, $chart_data["option"]["legend"]);
            }
        }
        if ($format == "gauges") {
            $chart_gauge_min_value = isset($options["chart_gauge_min_value"]) ? $options["chart_gauge_min_value"] : 0;
            $chart_gauge_max_value = isset($options["chart_gauge_max_value"]) ? $options["chart_gauge_max_value"] : 100;
            if (!isset($chart_data["option"]["series"][0]["min"]) || $chart_data["option"]["series"][0]["min"] === false) {
                $chart_data["option"]["series"][0]["min"] = intval($chart_gauge_min_value);
            }
            if (!isset($chart_data["option"]["series"][0]["max"]) || $chart_data["option"]["series"][0]["max"] === false) {
                $chart_data["option"]["series"][0]["max"] = intval($chart_gauge_max_value);
            }
            if (!empty($options["chart_gauge_label"])) {
                $chart_data["option"]["series"][0]["name"] = $options["chart_gauge_label"];
                $chart_data["option"]["series"][0]["data"][0]["name"] = $options["chart_gauge_label"];
            }
        }
        if ($format != "piechart" && $format != "wordcloud" && $format != "treemap" && $format != "gauges" && $format != "radar" && $format != "funnel") {
            $chart_show_xaxis_title = $options["chart_show_xaxis_title"];
            if (isset($chart_data["option"]["xAxis"]) && (!isset($chart_show_xaxis_title) || $chart_show_xaxis_title == "1")) {
                $chart_data["option"]["xAxis"][0]["name"] = isset($options["chart_xaxis_title_label"]) ? $options["chart_xaxis_title_label"] : "";
                $chart_data["option"]["xAxis"][0]["nameLocation"] = "middle";
                $chart_data["option"]["xAxis"][0]["nameGap"] = "30";
                $chart_data["option"]["xAxis"][0]["nameTextStyle"] = array("color" => "#666666");
            }
            $chart_xaxis_data_type = $options["chart_xaxis_data_type"];
            if (!empty($chart_xaxis_data_type)) {
                if ($format == "barchart") {
                    $chart_data["option"]["yAxis"][0]["type"] = $chart_xaxis_data_type;
                } else {
                    $chart_data["option"]["xAxis"][0]["type"] = $chart_xaxis_data_type;
                }
            }
            $chart_xaxis_show_data_labels = $options["chart_xaxis_show_data_labels"];
            if (!isset($chart_data["option"]["xAxis"][0]["axisLabel"])) {
                $chart_data["option"]["xAxis"][0]["axisLabel"] = array();
            }
            $chart_xaxis_name_rotate = isset($options["chart_xaxis_title_rotate"]) ? $options["chart_xaxis_title_rotate"] : 0;
            if (!empty($chart_xaxis_name_rotate) && $chart_xaxis_name_rotate != 0) {
                $chart_data["option"]["xAxis"][0]["axisLabel"]["rotate"] = $chart_xaxis_name_rotate;
            }
            if (!isset($chart_xaxis_show_data_labels) || $chart_xaxis_show_data_labels == "1") {
                $chart_data["option"]["xAxis"][0]["axisLabel"]["show"] = true;
            } else {
                $chart_data["option"]["xAxis"][0]["axisLabel"]["show"] = false;
            }
            $chart_show_yaxis_title = $options["chart_show_yaxis_title"];
            if (isset($chart_data["option"]["yAxis"]) && (!isset($chart_show_yaxis_title) || $chart_show_yaxis_title == "1")) {
                $chart_data["option"]["yAxis"][0]["name"] = isset($options["chart_yaxis_title_label"]) ? $options["chart_yaxis_title_label"] : "";
                $chart_data["option"]["yAxis"][0]["nameLocation"] = "middle";
                $chart_data["option"]["yAxis"][0]["nameGap"] = "30";
                $chart_data["option"]["yAxis"][0]["nameTextStyle"] = array("color" => "#666666");
            }
            if (isset($chart_data["option"]["yAxis"])) {
                $size = count($chart_data["option"]["yAxis"]);
                for ($i = 0; $i < $size; $i++) {
                    $chart_yaxis_show_number_labels = $options["chart_yaxis_show_number_labels"];
                    if (!isset($chart_data["option"]["yAxis"][$i]["axisLabel"])) {
                        $chart_data["option"]["yAxis"][$i]["axisLabel"] = array();
                    }
                    if (!isset($chart_yaxis_show_number_labels) || $chart_yaxis_show_number_labels == "1") {
                        $chart_data["option"]["yAxis"][$i]["axisLabel"]["show"] = true;
                        $chart_yaxis_symbol = isset($options["chart_yaxis_symbol"]) && !empty($options["chart_yaxis_symbol"]) ? $options["chart_yaxis_symbol"] : "";
                        $chart_yaxis_subfix = isset($options["chart_yaxis_subfix"]) && !empty($options["chart_yaxis_subfix"]) ? $options["chart_yaxis_subfix"] : "";
                        $chart_data["option"]["yAxis"][$i]["axisLabel"]["formatter"] = $chart_yaxis_symbol . "{value}" . $chart_yaxis_subfix;
                    } else {
                        $chart_data["option"]["yAxis"][$i]["axisLabel"]["show"] = false;
                    }
                }
            }
        }
        $chart_show_all_data_labels = $options["chart_show_all_data_labels"];
        $series =& $chart_data["option"]["series"];
        if (isset($chart_show_all_data_labels) && $chart_show_all_data_labels == "1") {
            foreach ($series as &$seris) {
                if ($format == "gauges") {
                    if (!isset($seris["axisLabel"])) {
                        $seris["axisLabel"] = array();
                    }
                    $seris["axisLabel"]["show"] = true;
                    if (!empty($options["chart_datalabels_formatter"])) {
                        $seris["axisLabel"]["formatter"] = $options["chart_datalabels_formatter"];
                    }
                } else {
                    if ($format == "radar") {
                    } else {
                        if ($format == "treemap") {
                        } else {
                            if (!isset($seris["label"])) {
                                $seris["label"] = array();
                            }
                            if (!isset($seris["label"]["normal"])) {
                                $seris["label"]["normal"] = array();
                            }
                            $seris["label"]["normal"]["show"] = true;
                            if (!empty($options["chart_datalabels_formatter"])) {
                                $seris["label"]["normal"]["formatter"] = $options["chart_datalabels_formatter"];
                            } else {
                                if ($format == "piechart") {
                                    $seris["label"]["normal"]["formatter"] = "{b}";
                                } else {
                                    $seris["label"]["normal"]["formatter"] = "{c}";
                                }
                            }
                            $seris["label"]["normal"]["position"] = "top";
                        }
                    }
                }
            }
        } else {
            foreach ($series as &$seris) {
                if ($format == "gauges") {
                    if (!isset($seris["axisLabel"])) {
                        $seris["axisLabel"] = array();
                    }
                    $seris["axisLabel"]["show"] = false;
                } else {
                    if ($format == "radar") {
                    } else {
                        if (!isset($seris["label"])) {
                            $seris["label"] = array();
                        }
                        if (!isset($seris["label"]["normal"])) {
                            $seris["label"]["normal"] = array();
                        }
                        $seris["label"]["normal"]["show"] = false;
                    }
                }
            }
        }
        $chart_gauge_angle_from = $options["chart_gauge_angle_from"];
        $chart_gauge_angle_to = $options["chart_gauge_angle_to"];
        if (!empty($chart_gauge_angle_from) && !empty($chart_gauge_angle_to)) {
            foreach ($series as &$seris) {
                if ($format == "gauges") {
                    $seris["startAngle"] = intval($chart_gauge_angle_to);
                    $seris["endAngle"] = intval($chart_gauge_angle_from);
                }
            }
        }
        if (isset($options["chart_is_stacked"]) && $options["chart_is_stacked"] == "1" && 1 < count($chart_data["option"]["series"])) {
            foreach ($series as &$seris) {
                if ($seris["type"] == "line" || $seris["type"] == "bar") {
                    $seris["stack"] = "autostack";
                }
            }
        }
        if (isset($options["general_color_background"])) {
            $bgcolor = $options["general_color_background"];
            if ($bgcolor != "default" && $bgcolor != "#ffffff") {
                $chart_data["option"]["backgroundColor"] = $options["general_color_background"];
            }
        }
        return $chart_data;
    }
    public function stv()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 100));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $viewname = $this->input->post("vn");
            $connid = $this->input->post("did");
            $db = $this->_get_db($creatorid, $connid);
            if (!$db) {
                echo json_encode(array("status" => 101));
            } else {
                $columnname = $this->input->post("name");
                $term = $this->input->post("term");
                $result = array();
                $query = $db->select($columnname)->like($columnname, $term)->limit(10)->distinct()->get($viewname);
                if ($query && 0 < $query->num_rows()) {
                    foreach ($query->result_array() as $row) {
                        $result[] = $row[$columnname];
                    }
                }
                echo json_encode(array("status" => 1, "data" => $result));
            }
        }
    }
    public function _assign_apps_in_category($appid)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("categoryid")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
        if (0 < $query->num_rows()) {
            $cid = $query->row()->categoryid;
            $query = $this->db->select("name,icon")->where(array("categoryid" => $cid, "creatorid" => $creatorid))->get("dc_category");
            if (0 < $query->num_rows()) {
                $row = $query->row();
                $category_name = $row->name;
                $category_icon = $row->icon;
                $query = $this->db->select("appid, name")->where(array("creatorid" => $creatorid, "status" => "publish", "categoryid" => $cid))->get("dc_app");
                if (0 < $query->num_rows()) {
                    $this->smartyview->assign("category_name", $category_name);
                    $this->smartyview->assign("category_icon", $category_icon);
                    $this->smartyview->assign("relative_apps", $query->result_array());
                }
            }
        }
    }
    public function execute_cloud_code($api)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        try {
            $this->db->select("public, creatorid, content, connid");
            $this->db->where("api", $api);
            $this->db->limit(1);
            $query = $this->db->get("dc_code");
            if ($query->num_rows() == 0) {
                return NULL;
            }
            $code_info = $query->row_array();
            $content = $code_info["content"];
            $filename = $this->_write_cloud_code($creatorid, $api, $content, false);
            ob_start();
            unset($this->config);
            $this->db = $this->_get_db($code_info["connid"]);
            define("__CLOUD_CODE__", "__CLOUD_CODE__");
            include FCPATH . $filename;
            $output = ob_get_clean();
            return $output;
        } catch (Throwable $e) {
            return "Error Cloud Code : " . $e->getMessage();
        }
    }
    public function save_table_column_order()
    {
        $appid = $this->input->post("appid");
        $fields = $this->input->post("fields");
        $creatorid = $this->session->userdata("login_creatorid");
        $where = array("creatorid" => $creatorid, "appid" => $appid, "status" => "system", "format" => "tableeditor");
        $query = $this->db->select("script")->where($where)->get("dc_app");
        if ($query->num_rows() == 0) {
            echo json_encode(array("status" => 0));
        } else {
            $script = json_decode($query->row()->script, true);
            if (count($script["select"]) == count($fields)) {
                $script["select"] = $fields;
                $this->db->update("dc_app", array("script" => json_encode($script)), $where);
                echo json_encode(array("status" => 1));
            } else {
                echo json_encode(array("status" => 0));
            }
        }
    }
    public function render_template()
    {
        $tpl = $this->input->get_post("tpl");
        $params = $this->input->post("params");
        $this->load->library("smartyview");
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $this->smartyview->assign($key, $value);
            }
        }
        $appid = $this->input->post("appid");
        if (!empty($appid)) {
            $query = $this->db->select("name,title,desc")->where("appid", $appid)->get("dc_app");
            $app_info = $query->row_array();
            $name = $app_info["name"];
            $title = $app_info["title"];
            $desc = $app_info["desc"];
            $this->smartyview->assign("appname", empty($title) ? $name : $title);
            $this->smartyview->assign("appdesc", $desc);
        }
        $this->smartyview->display($tpl);
    }
    public function get_dynamic_val()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $val = $this->input->post("v");
        $appid = $this->input->post("appid");
        if (empty($val) || empty($appid) || empty($creatorid)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            if ($query->num_rows() != 1) {
                echo json_encode(array("status" => 0));
            } else {
                $connid = $query->row()->connid;
                $db = $this->_get_db($creatorid, $connid);
                if (!$db) {
                    echo json_encode(array("status" => 0));
                } else {
                    $smarty = $this->_get_template_engine($db, $creatorid, $connid);
                    $result = $this->_compile_string($smarty, $val);
                    echo json_encode(array("status" => 1, "value" => $result));
                }
            }
        }
    }
    public function emailreport()
    {
        $email_settings = $this->config->item("email_settings");
        if (empty($email_settings)) {
            echo json_encode(array("status" => 0, "message" => "Unable to send email using your settings. Your server might not be configured to send mail."));
        } else {
            $this->load->library("email");
            $this->email->initialize($email_settings);
            $emails = $this->input->post("emails");
            $data = $this->input->post("data");
            $appid = $this->input->post("appid");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("name, title")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "10001: Application not available for sending."));
            } else {
                $title = $query->row()->title;
                $name = $query->row()->name;
                if (empty($title)) {
                    $title = $name;
                }
                $tmp_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "email_attachment";
                if (!file_exists($tmp_path) || !is_dir($tmp_path)) {
                    mkdir($tmp_path);
                }
                $filename = uniqid("rp_compress_") . ".png";
                $full_path = $tmp_path . DIRECTORY_SEPARATOR . $filename;
                save_base64_image($data, $full_path);
                $from = "support@dbface.com";
                $name = "";
                $reply = NULL;
                $from_settings = $this->config->item("email_settings_from");
                if (!empty($from_settings) && is_array($from_settings)) {
                    $from = isset($from_settings["from"]) ? $from_settings["from"] : "support@dbface.com";
                    $name = isset($from_settings["name"]) ? $from_settings["name"] : "";
                    $reply = isset($from_settings["reply"]) ? $from_settings["reply"] : NULL;
                }
                $this->email->from($from, $name, $reply);
                foreach ($emails as $email) {
                    $this->email->to($email);
                }
                $subject = $title . " - " . date("D, d M Y H:i");
                $this->email->subject($subject);
                $this->email->set_mailtype("html");
                $this->email->attach($full_path, "inline");
                $cid = $this->email->attachment_cid($full_path);
                $this->email->message("<img src=\"cid:" . $cid . "\" alt=\"" . $title . "\"/>");
                if ($this->email->send()) {
                    echo json_encode(array("status" => 1, "message" => "Email sent."));
                } else {
                    log_message("error", "Error sending report via email" . $this->email->print_debugger(array("headers")));
                    echo json_encode(array("status" => 2, "k" => $filename, "message" => "Unable to send email using your settings. Your server might not be configured to send mail. "));
                }
            }
        }
    }
    public function download_cache()
    {
        $filename = $this->input->get("k");
        $is_att = $this->input->get("att") == "1";
        $filepath = FCPATH . "user" . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . "email_attachment" . DIRECTORY_SEPARATOR . $filename;
        if ($is_att) {
            $this->load->helper("download");
            force_download($filepath, NULL);
        } else {
            $this->load->helper("file");
            $content = read_file($filepath);
            $this->output->set_content_type("png")->set_output($content);
        }
    }
    public function view_code()
    {
        $content = $this->input->post("content");
        $results = json_decode($content);
        if ($results) {
            $content = json_encode($results, JSON_PRETTY_PRINT);
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->library("smartyview");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $this->smartyview->assign("content", $content);
        $this->smartyview->display("inc/view_code.tpl");
    }
    public function test_capture()
    {
        $this->call_capture_service("test.pdf", "www.google.com", "pdf");
    }
    public function email_app()
    {
        $appid = $this->input->get_post("appid");
        $format = $this->input->get_post("format");
        $url = $this->_generate_access_url($appid);
        if (empty($format) || $format != "png" && $format != "pdf") {
            $format = "png";
        }
        $file = "download_" . $appid . "." . $format;
        $info = $this->call_capture_service($file, $url, $format, "file");
        echo json_encode($info);
    }
    public function download_app()
    {
        $appid = $this->input->get_post("appid");
        $format = $this->input->get_post("format");
        $url = $this->_generate_access_url($appid);
        if (empty($format) || $format != "png" && $format != "pdf") {
            $format = "png";
        }
        $file = "download_" . $appid . "." . $format;
        $this->call_capture_service($file, $url, $format);
    }
    public function _assign_app_filters($creatorid, $appid)
    {
        if (empty($creatorid) || empty($appid)) {
            return NULL;
        }
        $query = $this->db->where(array("creatorid" => $creatorid))->order_by("lastupdate", "desc")->get("dc_filter");
        $result = $query->result_array();
        $all_filters = array();
        $all_filters_by_name = array();
        foreach ($result as $row) {
            $all_filters[] = $row;
            if ($row["type"] == 0 || $row["type"] == 1) {
                $row["value"] = json_decode($row["value"], true);
            }
            $all_filters_by_name[$row["filterid"]] = $row;
        }
        $this->smartyview->assign("all_filters", $all_filters);
        $this->smartyview->assign("all_filters_by_name", $all_filters_by_name);
        $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "appid" => $appid, "type" => "inline_filter"))->get("dc_app_options");
        $result = $query->result_array();
        $app_filters = array();
        $app_filter_ids = array();
        foreach ($result as $row) {
            $f = array();
            $filterinfo = $all_filters_by_name[$row["key"]];
            $f["filterid"] = $filterinfo["filterid"];
            $f["name"] = $filterinfo["name"];
            $f["type"] = $filterinfo["type"];
            $f["value"] = !empty($row["value"]) ? json_decode($row["value"], true) : false;
            $app_filters[] = $f;
            $app_filter_ids[] = $row["key"];
        }
        $this->smartyview->assign("app_filters", $app_filters);
        $this->smartyview->assign("app_filter_ids", $app_filter_ids);
    }
    public function get_qrcode_url()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $appid = $this->input->post("appid");
        if (empty($creatorid) || empty($appid)) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $query = $this->db->select("1")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                $url = $this->_generate_access_url($appid);
                echo json_encode(array("status" => 1, "url" => $url));
            }
        }
    }
    public function _save_app_history($appid, $userid, $params, $data)
    {
        $this->db->insert("dc_app_history", array("appid" => $appid, "userid" => $userid, "params" => $params, "data" => $data, "_created_at" => time()));
    }
    public function _get_app_history_as_sparks($appid, $params = false)
    {
        $this->db->select("data");
        if ($params) {
            $this->db->where("params", $params);
        }
        $this->db->where("appid", $appid)->order_by("_created_at", "asc")->limit(50);
        $query = $this->db->get("dc_app_history");
        $arr = array();
        if ($query && 0 < $query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $v = $row["data"];
                if (is_numeric($v)) {
                    $arr[] = $v;
                }
            }
        }
        if (empty($arr)) {
            return false;
        }
        return implode(",", $arr);
    }
    /**
     * 应用是否支持快速编辑模式.
     * 快速编辑模式是指应用使用脚本的方式。
     *
     */
    public function is_allow_quickedit($appid)
    {
        $query = $this->db->select("scripttype")->where("appid", $appid)->get("dc_app");
        if ($query->num_rows() == 0) {
            return false;
        }
        $scriptype = $query->row()->scripttype;
        return $scriptype == 2 || $scriptype == 3 || $scriptype == 4 || $scriptype == 6;
    }
    public function view_script()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $appid = $this->input->post("appid");
            $query = $this->db->select("script, scripttype")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
            $script = $query->row()->script;
            $scripttype = $query->row()->scripttype;
            $mode = "ace/mode/sql";
            if ($scripttype == 4) {
                $mode = "ace/mode/html";
            } else {
                if ($scripttype == 6) {
                    $mode = "ace/mode/php";
                } else {
                    if ($scripttype == 3) {
                        $mode = "ace/mode/sql";
                    }
                }
            }
            echo json_encode(array("status" => 1, "content" => $script, "mode" => $mode));
        }
    }
    public function save_script()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $appid = $this->input->post("appid");
            $content = $this->input->post("content");
            $this->db->update("dc_app", array("script" => $content), array("appid" => $appid, "creatorid" => $creatorid));
            echo json_encode(array("status" => 1));
        }
    }
    public function run_additional_action()
    {
        $appid = $this->input->post("appid");
        $code = $this->input->post("code");
        $selected = $this->input->post("selected");
        if (!function_exists($code)) {
            echo json_encode(array("status" => 0, "message" => "Cloud function " . $code . " not defined. Please define this cloud function."));
        } else {
            $message = "Action executed";
            $status = 1;
            $result = call_user_func_array($code, array($appid, $selected));
            if ($result && is_array($result)) {
                if (isset($result["result"])) {
                    $status = $result["result"];
                }
                if (isset($result["message"])) {
                    $message = $result["message"];
                }
            }
            echo json_encode(array("message" => $message, "status" => $status));
        }
    }
    public function delete_dynamo_document()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("message" => "Access Denied"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $appid = $this->input->post("appid");
            $viewname = $this->input->post("viewname");
            $p = $this->input->post("p");
            $v = $this->input->post("v");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $dynamodb = $this->_get_dynamodb($creatorid, $connid);
            $result = $dynamodb->deleteOne($viewname, $p, $v);
            $message = $result ? "Document " . $v . " removed" : "Document " . $v . " remove failed";
            echo json_encode(array("result" => $result ? true : false, "message" => $message));
        }
    }
    public function delete_document()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("message" => "Access Denied"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $appid = $this->input->post("appid");
            $viewname = $this->input->post("viewname");
            $id = $this->input->post("id");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $mongo_db = $this->_get_mongodb($creatorid, $connid);
            $result = $mongo_db->deleteOne($viewname, $id);
            $message = $result ? "Document " . $id . " removed" : "Document " . $id . " remove failed";
            echo json_encode(array("result" => $result ? true : false, "message" => $message));
        }
    }
    public function insert_dynamodb_document()
    {
        $appid = $this->input->post("appid");
        $viewname = $this->input->post("viewname");
        $json = $this->input->post("json");
        $new_data = json_decode($json, true);
        if (empty($new_data) || json_last_error() != JSON_ERROR_NONE) {
            echo json_encode(array("result" => false, "message" => "Invalid JSON data."));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $dynamodb = $this->_get_dynamodb($creatorid, $connid);
            $result = $dynamodb->insertOneDocument($viewname, $new_data);
            dbface_log("info", "Insert Document", array("viewname" => $viewname, "data" => $new_data, "result" => $result));
            echo json_encode(array("result" => true, "message" => "Document inserted"));
        }
    }
    public function insert_document()
    {
        $appid = $this->input->post("appid");
        $viewname = $this->input->post("viewname");
        $json = $this->input->post("json");
        $new_data = json_decode($json, true);
        if (empty($new_data) || json_last_error() != JSON_ERROR_NONE) {
            echo json_encode(array("result" => false, "message" => "Invalid JSON data."));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $mongo_db = $this->_get_mongodb($creatorid, $connid);
            $result = $mongo_db->insertOneDocument($viewname, $new_data);
            dbface_log("info", "Insert Document", array("viewname" => $viewname, "data" => $new_data, "result" => $result));
            echo json_encode(array("result" => true, "message" => "Document inserted"));
        }
    }
    public function edit_dynamodb_document()
    {
        $appid = $this->input->post("appid");
        $viewname = $this->input->post("viewname");
        $p = $this->input->post("p");
        $v = $this->input->post("v");
        $json = $this->input->post("json");
        $new_data = json_decode($json, true);
        if (empty($new_data) || json_last_error() != JSON_ERROR_NONE) {
            echo json_encode(array("result" => false, "message" => "Invalid JSON data."));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $dynamodb = $this->_get_dynamodb($creatorid, $connid);
            $result = $dynamodb->updateOneDocument($viewname, $p, $v, $new_data);
            dbface_log("info", "Update Document", array("viewname" => $viewname, "id" => $v, "data" => $new_data, "result" => $result));
            echo json_encode(array("result" => true, "message" => "Document updated"));
        }
    }
    public function edit_document()
    {
        $appid = $this->input->post("appid");
        $viewname = $this->input->post("viewname");
        $id = $this->input->post("id");
        $json = $this->input->post("json");
        $new_data = json_decode($json, true);
        if (empty($new_data) || json_last_error() != JSON_ERROR_NONE) {
            echo json_encode(array("result" => false, "message" => "Invalid JSON data."));
        } else {
            $bson = MongoDB\BSON\fromJSON($json);
            $new_data = MongoDB\BSON\toPHP($bson);
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $mongo_db = $this->_get_mongodb($creatorid, $connid);
            $result = $mongo_db->updateOneDocument($viewname, $id, $new_data);
            dbface_log("info", "Update Document", array("viewname" => $viewname, "id" => $id, "data" => $new_data, "result" => $result));
            echo json_encode(array("result" => true, "message" => "Document updated"));
        }
    }
    public function get_dynamodb_document()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("message" => "Access Denied"));
        } else {
            $appid = $this->input->post("appid");
            $viewname = $this->input->post("viewname");
            $pk = $this->input->post("p");
            $pv = $this->input->post("v");
            $this->load->library("smartyview");
            $this->smartyview->assign("appid", $appid);
            $this->smartyview->assign("viewname", $viewname);
            $this->smartyview->assign("p", $pk);
            $this->smartyview->assign("v", $pv);
            $this->smartyview->assign("uniqueid", uniqid());
            $creatorid = $this->session->userdata("login_creatorid");
            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
            if (!empty($ace_editor_theme)) {
                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
            }
            $this->smartyview->assign("title", $pk . ": " . $pv);
            $this->smartyview->display("editor.dynamodb.row.tpl");
        }
    }
    public function get_document()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("message" => "Access Denied"));
        } else {
            $appid = $this->input->post("appid");
            $viewname = $this->input->post("viewname");
            $id = $this->input->post("id");
            $this->load->library("smartyview");
            $this->smartyview->assign("appid", $appid);
            $this->smartyview->assign("viewname", $viewname);
            $this->smartyview->assign("dataid", $id);
            $this->smartyview->assign("uniqueid", uniqid());
            $creatorid = $this->session->userdata("login_creatorid");
            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
            if (!empty($ace_editor_theme)) {
                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
            }
            $this->smartyview->assign("title", "_id: " . $id);
            $this->smartyview->display("editor.mongo.row.tpl");
        }
    }
    public function get_dynamodb_document_json()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("message" => "Access Denied"));
        } else {
            $appid = $this->input->post("appid");
            $viewname = $this->input->post("viewname");
            $pk = $this->input->post("p");
            $pv = $this->input->post("v");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $dynamodb = $this->_get_dynamodb($creatorid, $connid);
            $document = $dynamodb->queryOneDocument($viewname, $pk, $pv);
            if (!$document) {
                $document = $dynamodb->error();
            }
            echo json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
    public function get_document_json()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("message" => "Access Denied"));
        } else {
            $appid = $this->input->post("appid");
            $viewname = $this->input->post("viewname");
            $id = $this->input->post("id");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $mongo_db = $this->_get_mongodb($creatorid, $connid);
            $document = $mongo_db->queryOneDocument($viewname, $id);
            if ($document) {
                $document = $document->jsonSerialize();
            } else {
                $document = $mongo_db->error();
            }
            echo json_encode($document, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }
    public function view_gridfs_file()
    {
        $appid = $this->input->get("appid");
        $viewname = $this->input->get("viewname");
        $id = $this->input->get("id");
        $filename = $this->input->get("filename");
        $strs = explode(".", $viewname);
        if (count($strs) != 2 || $strs[1] != "files") {
            echo json_encode(array("result" => "fail"));
        } else {
            $bucketName = $strs[0];
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $connid = $query->row()->connid;
            $mongo_db = $this->_get_mongodb($creatorid, $connid);
            $save_path = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR . $filename;
            $result = $mongo_db->writeGridFsToFile($bucketName, $id, $save_path);
            if ($result["result"] == 0) {
                echo isset($result["message"]) ? $result["message"] : "The file can not be loaded.";
            } else {
                $data = file_get_contents($save_path);
                $this->load->helper("file");
                $mime = get_mime_by_extension($filename);
                if ($mime) {
                    $this->output->set_content_type($mime);
                }
                $this->output->set_output($data);
            }
        }
    }
    /**
     *
     * @param $appinfo
     */
    public function _execute_chain_app($appinfo)
    {
        $at = $this->input->post("__at__");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $appinfo["connid"];
        $previewmode = $this->input->get_post("preview");
        $this->smartyview->assign("previewmode", $previewmode);
        $this->smartyview->assign("appid", $appinfo["appid"]);
        $this->smartyview->assign("rpf", "chain");
        $options = $this->_assign_app_options($appinfo["options"]);
        $formdata = json_decode($appinfo["form"], true);
        $db = $this->_get_db($creatorid, $connid);
        $showInForm = false;
        if (empty($at) && !empty($formdata) && !empty($formdata["html"])) {
            $this->smartyview->assign("FORM_ID", uniqid("FM_"));
            $this->smartyview->assign("formTitle", $formdata["name"]);
            $this->smartyview->assign("formCss", $formdata["css"]);
            $this->smartyview->assign("formDisplay", $formdata["display"]);
            $this->smartyview->assign("hasLoadingScript", !empty($formdata["loadingscript"]));
            $this->_assign_form_html($db, $creatorid, $connid, $formdata);
            $showInForm = true;
        }
        $this->smartyview->assign("showresultset", true);
        $formID = $this->input->post("FORMID");
        if (!empty($formID)) {
            $this->smartyview->assign("FORMID", $formID);
            $this->smartyview->assign("FORM_ID", $formID);
        }
        if (!empty($at)) {
            $this->smartyview->assign("_resultset", true);
        }
        if ($showInForm) {
            $this->smartyview->assign("apptitle", $appinfo["name"]);
            $this->smartyview->display("runtime/app.chain.index.tpl");
        } else {
            $script = json_decode($appinfo["script"], true);
            $chain_url = $script["url"];
            $chain_formdata = isset($script["formdata"]) ? $script["formdata"] : "";
            $chain_callback = isset($script["callback"]) ? $script["callback"] : false;
            $smarty = $this->_get_template_engine($db, $creatorid, $connid);
            $chain_url = $smarty->fetch("string:" . $chain_url);
            if (!empty($chain_formdata)) {
                if (function_exists($chain_formdata)) {
                    $chain_formdata = call_user_func($chain_formdata);
                } else {
                    $chain_formdata = $smarty->fetch("string:" . $chain_formdata);
                }
            }
            $url_info = parse_url($chain_url);
            $url_schema = isset($url_info["schema"]) ? $url_info["schema"] : "http";
            $url_query = isset($url_info["query"]) ? $url_info["query"] : false;
            $ttl = $this->config->item("cache_app");
            $apptitle = $appinfo["name"];
            $display = "auto";
            $method = "POST";
            if (!empty($url_query)) {
                $url_query_arr = array();
                parse_str($url_query, $url_query_arr);
                if (isset($url_query_arr["ttl"])) {
                    $ttl = $url_query_arr["ttl"];
                    if (empty($ttl) || !is_numeric($ttl)) {
                        $ttl = $this->config->item("cache_app");
                    }
                }
                if (isset($url_query_arr["title"])) {
                    $apptitle = $url_query_arr["title"];
                }
                if (isset($url_query_arr["format"])) {
                    $display = $url_query_arr["format"];
                }
                if (isset($url_query_arr["output"])) {
                    $display = $url_query_arr["output"];
                }
                if (isset($url_query_arr["method"])) {
                    $method = $url_query_arr["method"];
                }
            }
            $this->smartyview->assign("apptitle", $apptitle);
            if ($url_schema == "file") {
                $file_path = $url_info["host"] . $url_info["path"];
                $root_path = $this->_get_media_dir($creatorid);
                $abs_file_path = $root_path . $file_path;
                if (file_exists($abs_file_path)) {
                    $body = file_get_contents($abs_file_path);
                } else {
                    dbface_log("error", "File: " . $file_path . " not found");
                }
            } else {
                $cache_name = md5($chain_url . ":" . $chain_formdata);
                $this->_init_cache($creatorid);
                if ($ttl == 0) {
                    $this->cache->delete($cache_name);
                } else {
                    $body = $this->cache->get($cache_name);
                }
                $chain_error = false;
                $chain_message = false;
                if (empty($body)) {
                    try {
                        dbface_log("info", " <<< Request: " . $chain_url . ", form: " . $chain_formdata);
                        require_once APPPATH . "third_party/guzzle/autoloader.php";
                        $client = new GuzzleHttp\Client();
                        $response = $client->request($method, $chain_url, array("query" => $chain_formdata, "verify" => false));
                        $code = $response->getStatusCode();
                        $reason = $response->getReasonPhrase();
                        $body = $response->getBody();
                        dbface_log("info", " >>> Response, code: " . $code . ", reason:" . $reason . ", body : " . $body);
                        $this->cache->save($cache_name, $body, $ttl);
                    } catch (GuzzleHttp\Exception\GuzzleException $e) {
                        dbface_log("error", $e->getMessage());
                        $chain_error = true;
                        $chain_message = $e->getMessage();
                    }
                } else {
                    dbface_log("info", "get chain data from cache: " . $chain_url);
                }
            }
            if ($chain_error) {
                $this->smartyview->assign("chain_error", true);
                $this->smartyview->assign("chain_message", $chain_message);
            } else {
                $responseData = parse_json_data($body);
                if (!empty($chain_callback)) {
                    if (function_exists($chain_callback)) {
                        $chain_result = call_user_func($chain_callback, $responseData);
                    } else {
                        if (is_array($responseData)) {
                            foreach ($responseData as $k => $v) {
                                $smarty->assign($k, $v);
                            }
                        } else {
                            $smarty->assign("response", $responseData);
                        }
                        if (str_endsWith($chain_callback, ".tpl", false)) {
                            $chain_result = $smarty->fetch($chain_callback);
                        } else {
                            $chain_result = $smarty->fetch("eval:" . $chain_callback);
                        }
                    }
                    $this->smartyview->assign("raw_result", true);
                    $this->smartyview->assign("chain_result", $chain_result);
                } else {
                    $this->_display_chain_with_response($display, $responseData);
                }
            }
            $this->smartyview->assign("app_file", "runtime/app.chain.tpl");
            $this->smartyview->display("runtime/app.chain.index.tpl");
        }
    }
    /**
     * chain application default display
     *
     * $display: tabular, linechart, barchart, singlenumber, ...will added soon
     *
     * @param $display
     * @param $response
     *
     * @return string
     */
    public function _display_chain_with_response($display, $response)
    {
        dbface_log("info", "_display_chain_with_response: " . $display);
        $display = $this->_autodetect_display($display, $response);
        if (is_array($response)) {
            if ($display == "tabular") {
                $this->smartyview->assign("display", "tabular");
                $this->_display_chain_with_response_tabular($response);
            } else {
                if ($display == "singlenumber") {
                    $this->smartyview->assign("display", "singlenumber");
                    $this->_display_chain_with_response_singlenumber($response);
                } else {
                    if ($display == "linechart" || $display == "barchart" || $display == "piechart" || $display == "columnchart" || $display == "areachart" || $display == "scatterplot") {
                        $this->smartyview->assign("display", "chart");
                        $this->_display_chain_with_response_charts($display, $response);
                    } else {
                        $this->smartyview->assign("display", "codebox");
                        $this->smartyview->assign("content", json_encode($response));
                        $this->smartyview->assign("code_language", "json");
                    }
                }
            }
        } else {
            if (stripos($response, "<html") != false) {
                $this->smartyview->assign("display", "iframe");
                $cache_id = $this->_write_filecache($response);
                $this->smartyview->assign("cache_id", $cache_id);
            } else {
                $this->smartyview->assign("display", "html");
                $this->smartyview->assign("html", $response);
            }
        }
    }
    public function _autodetect_display($display, $response)
    {
        if ($display != "auto") {
            return $display;
        }
        if (isset($response["value"])) {
            return "singlenumber";
        }
        if (0 < count($response)) {
            return "tabular";
        }
        return $display;
    }
    public function _write_filecache($content, $cache_id = false)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if ($cache_id === false) {
            $cache_id = uniqid("filecache_");
        }
        $cache_dir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR;
        $this->load->helper("file");
        $result = write_file($cache_dir . $cache_id, $content);
        return $result ? $cache_id : false;
    }
    /**
     * response: [{k1:v1, k2:v2}, {k1:v1, k2:v2}]
     *
     * @param $response
     */
    public function _display_chain_with_response_tabular($response)
    {
        $fields = array();
        $datas = array();
        foreach ($response as $row) {
            $a_fields = array_keys($row);
            foreach ($a_fields as $a_field) {
                if (!in_array($a_field, $fields)) {
                    $fields[] = $a_field;
                }
            }
        }
        foreach ($response as $row) {
            $result_row = array();
            foreach ($fields as $field) {
                $result_row[] = isset($row[$field]) ? $row[$field] : "";
            }
            $datas[] = $result_row;
        }
        $this->smartyview->assign("report_id", uniqid());
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("datas", $datas);
    }
    /**
     * response: {"name": name, "value": value, 'trends' : [v1, v2, v3]}
     * trends is optional
     *
     * @param $response
     */
    public function _display_chain_with_response_singlenumber($response)
    {
        $label = isset($response["name"]) ? $response["name"] : "";
        $value = isset($response["value"]) ? $response["value"] : "";
        $sparks = isset($response["trends"]) ? $response["trends"] : false;
        $this->smartyview->assign("report_id", uniqid());
        $this->smartyview->assign("number_label", $label);
        $this->smartyview->assign("number_value", $value);
        $this->smartyview->assign("sparks", $sparks);
    }
    /**
     *
     * @param $response
     */
    public function _display_chain_with_response_charts($charttype, $response)
    {
        $this->smartyview->assign("view_charttype", $charttype);
        $this->smartyview->assign("CHARTID", uniqid());
        if (isset($response["json"])) {
            $result_json = json_encode($response["json"]);
        } else {
            $result_json = json_encode($this->_gen_chart_json_from_response($charttype, $response));
        }
        $cache_id = $this->_write_filecache($result_json);
        $this->smartyview->assign("chart_data_file_id", $cache_id);
    }
    /**
     * make chart json from response data
     * detect labelname
     *
     * @param $charttype
     * @param $response
     * @return array
     *
     */
    public function _gen_chart_json_from_response($format, $response)
    {
        $dimension_field = isset($response["dimension"]) ? $response["dimension"] : false;
        $metrics_fields = isset($response["metrics"]) ? $response["metrics"] : false;
        $datas = isset($response["data"]) ? $response["data"] : false;
        if ($dimension_field == false || $metrics_fields == false) {
            $datas = $response;
            $keys = array_keys($datas[0]);
            $dimension_field = $keys[0];
            array_shift($keys);
            $metrics_fields = $keys;
        }
        if ($dimension_field == false || $metrics_fields == false || !is_array($metrics_fields) || count($metrics_fields) == 0) {
            return array("error" => "No dimension or metrics detected");
        }
        $caption = "";
        $subcaption = "";
        $xAxisName = $dimension_field;
        $yAxisName = $metrics_fields[0];
        $labelname = $dimension_field;
        $valuename = $metrics_fields[0];
        $chartjson = array();
        if (count($metrics_fields) == 1) {
            $this->load->helper("echarts_" . $format);
            if ($format == "piechart") {
                $chartjson["option"] = make_echarts_piechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
            } else {
                if ($format == "linechart") {
                    $chartjson["option"] = make_echarts_linechart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                } else {
                    if ($format == "scatterplot") {
                        $chartjson["option"] = make_echarts_scatterplot($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                    } else {
                        if ($format == "areachart") {
                            $chartjson["option"] = make_echarts_areachart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                        } else {
                            if ($format == "columnchart") {
                                $chartjson["option"] = make_echarts_columnchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                            } else {
                                if ($format == "barchart") {
                                    $chartjson["option"] = make_echarts_barchart($caption, $subcaption, $xAxisName, $yAxisName, $labelname, $valuename, $datas);
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $this->load->helper("echarts_" . $format . "_ms");
            $categories = array();
            $tmp_datas = array();
            $legends = array();
            $count = count($metrics_fields);
            for ($i = 0; $i < $count; $i++) {
                $valuename = $metrics_fields[$i];
                $legends[] = $valuename;
                $rowdata = array();
                foreach ($datas as $data) {
                    $rowdata[$data[$labelname]] = $data[$valuename];
                    if (!in_array($data[$labelname], $categories)) {
                        $categories[] = $data[$labelname];
                    }
                }
                $tmp_datas[] = $rowdata;
            }
            $index = 0;
            $datasets = array();
            foreach ($tmp_datas as $data) {
                $dataset = array();
                $dataset["seriesName"] = isset($legends[$index]) ? $legends[$index] : "";
                $index++;
                $a = array();
                foreach ($categories as $category) {
                    if (isset($data[$category])) {
                        $a[] = $data[$category];
                    } else {
                        $a[] = 0;
                    }
                }
                $dataset["datas"] = $a;
                $datasets[] = $dataset;
            }
            if ($format == "piechart") {
                $chartjson["option"] = make_echarts_piechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
            } else {
                if ($format == "linechart") {
                    $chartjson["option"] = make_echarts_linechart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                } else {
                    if ($format == "scatterplot") {
                        $chartjson["option"] = make_echarts_scatterplot_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                    } else {
                        if ($format == "areachart") {
                            $chartjson["option"] = make_echarts_areachart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                        } else {
                            if ($format == "columnchart") {
                                $chartjson["option"] = make_echarts_columnchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                            } else {
                                if ($format == "barchart") {
                                    $chartjson["option"] = make_echarts_barchart_ms($caption, $subcaption, $xAxisName, $yAxisName, $categories, $datasets);
                                }
                            }
                        }
                    }
                }
            }
        }
        $chart_options = array("chart_enable_tooltip" => 1);
        $chartjson = $this->_assign_chart_options($chartjson, $chart_options, $format);
        return $chartjson;
    }
    public function _execute_freeboard_app($appinfo)
    {
        $appid = $appinfo["appid"];
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("run_mode", "run");
        $this->smartyview->display("freeboard/index.tpl");
    }
    public function load_databoard()
    {
        $appid = $this->input->get_post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("script")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
        if (0 < $query->num_rows()) {
            $script = $query->row()->script;
            echo json_encode(array("result" => "ok", "data" => json_decode($script, true)));
        } else {
            echo json_encode(array("result" => "error"));
        }
    }
    /**
     * try to execute cloud code from drilled master report
     */
    public function _execute_code_for_drilled()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $func = $this->input->post("code");
        $fromapp = $this->input->post("fromapp");
        if (!empty($fromapp)) {
            $query = $this->db->select("options")->where(array("appid" => $fromapp, "creatorid" => $creatorid))->get("dc_app");
            if (0 < $query->num_rows()) {
                $options = json_decode($query->row()->options, true);
                if ($options && isset($options["chart_link_cloudcode"])) {
                    $func = $options["chart_link_cloudcode"];
                }
            }
        }
        $result_funcs = explode("#", $func);
        if (count($result_funcs) == 2) {
            list($file, $func) = $result_funcs;
            $real_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $file;
            if (file_exists($real_path)) {
                try {
                    define("__CLOUD_CODE__", true);
                    include $real_path;
                } catch (Exception $e) {
                    dbface_log("error", "execute drilldown cloud code failed: " . $e->getMessage());
                }
                return NULL;
            }
        } else {
            $detetch_file = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $func;
            if (file_exists($detetch_file)) {
                try {
                    define("__CLOUD_CODE__", true);
                    include $detetch_file;
                } catch (Exception $e) {
                    dbface_log("error", "execute drilldown cloud code failed: " . $e->getMessage());
                }
                return NULL;
            }
        }
        if (function_exists($func)) {
            $result = call_user_func($func, array());
            if ($result) {
                echo $result;
                return NULL;
            }
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("title", "Function Not Found");
        $this->smartyview->assign("message", "Please define " . $func . " in user files.");
        $this->smartyview->display("inc/app.error.body.tpl");
    }
}

?>