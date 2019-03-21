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
class Appbuilder extends BaseController
{
    protected static $widgets = array();
    public function copy()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $appid = $this->input->get("appid");
        if (empty($appid)) {
            $this->ajax_redirect("module=CoreHome#module=Appbuilder&action=create_step1");
        } else {
            $query = $this->db->get_where("dc_app", array("appid" => $appid));
            $result = $query->row_array();
            unset($result["appid"]);
            unset($result["embedcode"]);
            if (!empty($result["name"])) {
                $result["name"] = "Copy of " . $result["name"];
            }
            $this->db->insert("dc_app", $result);
            $new_appid = $this->db->insert_id();
            $query = $this->db->get_where("dc_app_permission", array("appid" => $appid));
            if (0 < $query->num_rows()) {
                $result = $query->result_array();
                foreach ($result as $row) {
                    $row["appid"] = $new_appid;
                }
                foreach ($result as $row) {
                    $this->db->insert("dc_app_permission", $row);
                }
            }
            $query = $this->db->get_where("dc_usergroup_permission", array("appid" => $appid));
            if (0 < $query->num_rows()) {
                $result = $query->result_array();
                foreach ($result as $row) {
                    $row["appid"] = $new_appid;
                }
                foreach ($result as $row) {
                    $this->db->insert("dc_usergroup_permission", $row);
                }
            }
            $url = "module=Appbuilder&action=edit&appid=" . $new_appid;
            $this->ajax_redirect($url);
        }
    }
    public function edit()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $this->load->library("smartyview");
        $enable_marketplace = $this->config->item("enable_marketplace");
        $this->smartyview->assign("enable_marketplace", $enable_marketplace);
        $enable_preview = $this->config->item("enable_live_preview");
        $this->smartyview->assign("enable_livepreview", $enable_preview);
        $this->smartyview->assign("is_edit_application", true);
        $expired = $this->session->userdata("_EXPIRED_");
        if ($expired) {
            $this->smartyview->assign("hasError", true);
            $this->smartyview->assign("title", "Expired");
            $this->smartyview->assign("message", "DbFace instance has been expired or tempory unavailable, please contact the developer.");
            $this->smartyview->display("runtime/app.error.tpl");
        } else {
            $this->load->helper("json");
            $appid = $this->input->get_post("appid");
            if (empty($appid)) {
                $this->load->helper("url");
                redirect("?module=CoreHome#module=Appbuilder&action=create");
            } else {
                $creatorid = $this->session->userdata("login_creatorid");
                $conns = $this->_get_connections($creatorid);
                $categories = $this->_get_categories($creatorid);
                $category = array("categoryid" => 0, "name" => $this->config->item("default_category_name"));
                array_unshift($categories, $category);
                $this->load->database();
                $query = $this->db->query("select * from dc_app where creatorid=? and appid=?", array($creatorid, $appid));
                if ($query->num_rows() != 1) {
                    $this->ajax_redirect("module=CoreHome#module=Dashboard");
                } else {
                    $row = $query->row_array();
                    $apptype = $row["type"];
                    if ($apptype == "phpreport") {
                        $result = $this->_check_and_sync_phpreport($creatorid, $row["appid"], $row["createdate"]);
                        if ($result != false) {
                            $row["script"] = $result;
                        }
                    }
                    if ($apptype == "htmlreport") {
                        $result = $this->_check_and_sync_htmlreport($creatorid, $row["appid"], $row["createdate"]);
                        if ($result != false) {
                            $row["script"] = $result;
                        }
                    }
                    $scriptType = $row["scripttype"];
                    $options_str = $row["options"];
                    if (!empty($options_str)) {
                        $options = json_decode($options_str, true);
                        foreach ($options as $key => $value) {
                            $this->smartyview->assign($key, $value);
                        }
                    }
                    $db = $this->_get_db($creatorid, $row["connid"]);
                    $is_mongodb = $this->_is_mongodb($creatorid, $row["connid"]);
                    if ($is_mongodb) {
                        $this->smartyview->assign("mongodb", 1);
                    }
                    $is_dynamodb = $this->_is_dynamodb($creatorid, $row["connid"]);
                    if ($is_dynamodb) {
                        $this->smartyview->assign("dynamodb", 1);
                    }
                    if ($scriptType == 1) {
                        $this->smartyview->assign("designmode", "dragdropmode");
                        $script_json = json_decode($row["script"], true);
                        $tblnames = isset($script_json["tablename"]) ? $script_json["tablename"] : false;
                        $left_columnname = isset($script_json["left_columnname"]) ? $script_json["left_columnname"] : false;
                        $right_columnname = isset($script_json["right_columnname"]) ? $script_json["right_columnname"] : false;
                        $jointype = isset($script_json["jointype"]) ? $script_json["jointype"] : false;
                        $tblcolumninfos = array();
                        $columnsInfoByTable = array();
                        $columnInfos = array();
                        $singletable = count($tblnames) == 1;
                        if ($tblnames) {
                            foreach ($tblnames as $tblname) {
                                if (!empty($tblname)) {
                                    $columns = field_data($db, $tblname);
                                    $renewColumns = array();
                                    $columnnames = array();
                                    if (is_array($columns)) {
                                        foreach ($columns as $column) {
                                            $tmp = array();
                                            $tmp["type"] = $this->get_input_type($column->type, $column->max_length);
                                            switch ($tmp["type"]) {
                                                case CDT_NUMBERIC:
                                                    $tmp["csstype"] = "fa fa-sort-numeric-asc";
                                                    break;
                                                case CDT_DATETIME:
                                                    $tmp["csstype"] = "fa fa-calendar";
                                                    break;
                                                default:
                                                    $tmp["csstype"] = "fa fa-font";
                                                    break;
                                            }
                                            $tmp["name"] = $column->name;
                                            $renewColumns[] = $tmp;
                                            $columnnames[] = $column->name;
                                            $column_key = $singletable ? $column->name : $tblname . "." . $column->name;
                                            $columnInfos[$column_key] = array("name" => $column->name, "type" => $tmp["type"], "csstype" => $tmp["csstype"], "tblname" => $tblname);
                                        }
                                    }
                                    $columnsInfoByTable[$tblname] = $columnnames;
                                    $tblcolumninfos[] = array("tblname" => $tblname, "columns" => $renewColumns);
                                }
                            }
                        }
                        $this->smartyview->assign("tablename", $tblnames);
                        if ($left_columnname) {
                            $this->smartyview->assign("left_columnname", $left_columnname);
                        }
                        if ($right_columnname) {
                            $this->smartyview->assign("right_columnname", $right_columnname);
                        }
                        if ($jointype) {
                            $this->smartyview->assign("jointype", $jointype);
                        }
                        $this->smartyview->assign("columnsInfoByTable", $columnsInfoByTable);
                        $this->smartyview->assign("singletable", $singletable);
                        $this->smartyview->assign("tblcolumninfos", $tblcolumninfos);
                        if (isset($script_json["sqlcondition"])) {
                            $this->smartyview->assign("sqlcondition", $script_json["sqlcondition"]);
                        }
                        if (isset($script_json["sqlop"])) {
                            $this->smartyview->assign("sqlop", $script_json["sqlop"]);
                        }
                        if (isset($script_json["sqlvalue"])) {
                            $this->smartyview->assign("sqlvalue", $script_json["sqlvalue"]);
                        }
                        if (isset($script_json["sqljoin"])) {
                            $this->smartyview->assign("sqljoin", $script_json["sqljoin"]);
                        }
                        $filter_column_types = array("select", "order", "groupby", "summary", "column", "row", "pivotdata", "xaxis", "yaxis");
                        foreach ($filter_column_types as $filter_type) {
                            if (array_key_exists($filter_type, $script_json)) {
                                $c = $script_json[$filter_type];
                                $f = isset($script_json[$filter_type . "fun"]) ? $script_json[$filter_type . "fun"] : array();
                                $l = array_key_exists($filter_type . "label", $script_json) ? $script_json[$filter_type . "label"] : array();
                                if (!empty($c)) {
                                    $filter_columns = array();
                                    $filter_funs = array();
                                    $filter_labels = array();
                                    $idx = 0;
                                    foreach ($c as $a) {
                                        if (!isset($columnInfos[$a])) {
                                            continue;
                                        }
                                        $filter_columns[] = $columnInfos[$a];
                                        if (isset($f[$idx])) {
                                            $filter_funs[] = $f[$idx];
                                        } else {
                                            $filter_funs[] = "";
                                        }
                                        if (isset($l[$idx])) {
                                            $filter_labels[] = $l[$idx];
                                        } else {
                                            $filter_labels[] = "";
                                        }
                                        $idx++;
                                    }
                                    $this->smartyview->assign($filter_type, $filter_columns);
                                    $this->smartyview->assign($filter_type . "fun", $filter_funs);
                                    $this->smartyview->assign($filter_type . "label", $filter_labels);
                                }
                            }
                        }
                    } else {
                        $this->smartyview->assign("designmode", "sqlmode");
                    }
                    $form_info = json_decode($row["form"], true);
                    $appinfo["form"] = $form_info["html"];
                    $this->smartyview->assign("form_option_name", $form_info["name"]);
                    if (isset($form_info["css"])) {
                        $this->smartyview->assign("form_option_css", $form_info["css"]);
                    }
                    $this->smartyview->assign("form_builder_mode", isset($form_info["form_builder_mode"]) && !empty($form_info["form_builder_mode"]) ? $form_info["form_builder_mode"] : "design");
                    $this->smartyview->assign("form_option_loadingscript", $form_info["loadingscript"]);
                    $this->smartyview->assign("form_option_display", $form_info["display"]);
                    $this->smartyview->assign("form_option_autoload", isset($form_info["autoload"]) ? $form_info["autoload"] : 1);
                    $this->smartyview->assign("disable_phpreport", $this->config->item("disable_phpreport"));
                    $this->smartyview->assign("disable_sql_edit_application", $this->config->item("disable_sql_edit_application"));
                    $this->smartyview->assign("conns", $conns);
                    $selected_connid = $row["connid"];
                    $this->session->set_userdata("appbuilder_selectconnid", $selected_connid);
                    $this->smartyview->assign("subapps", $this->_get_user_subapps($creatorid, false));
                    $this->smartyview->assign("connid", $selected_connid);
                    $this->smartyview->assign("tablenames", $this->_get_tablenames());
                    $this->smartyview->assign("categories", $categories);
                    $this->smartyview->assign("apptype", $apptype);
                    $this->smartyview->assign("appid", $appid);
                    $this->smartyview->assign("app_scriptType", $scriptType);
                    $this->smartyview->assign("appinfo", $row);
                    $this->smartyview->assign("builderboxstyle", "box box-primary");
                    $this->smartyview->assign("displayformat", $row["format"]);
                    $this->_set_general_users($creatorid, $appid);
                    if ($scriptType == 7) {
                        $script_json = json_decode($row["script"], true);
                        $this->smartyview->assign("stories", $script_json);
                    }
                    if ($scriptType == 8) {
                        $galleries = json_decode($row["script"], true);
                        foreach ($galleries as &$gallery) {
                            $appid = $gallery["appid"];
                            $info = $this->_get_app_box_info($appid);
                            $icon = $gallery["icon"];
                            $icon_arr = explode(" ", $icon);
                            if ($icon_arr && 1 < count($icon_arr)) {
                                $gallery["icon_fa"] = $icon_arr[1];
                            }
                            $gallery["name"] = $info["name"];
                            $gallery["description"] = $info["description"];
                        }
                        $this->smartyview->assign("galleries", $galleries);
                    }
                    if ($apptype == "htmlreport" && !empty($row["script_org"])) {
                        $html_report_settings = json_decode($row["script_org"], true);
                        $htmlreport_template_mode = isset($html_report_settings["htmlreport_template_mode"]) ? 1 : 0;
                        $htmlreport_tpl = $html_report_settings["htmlreport_tpl"];
                        $form_tpl = $html_report_settings["form_tpl"];
                        $htmlreport_variables = $html_report_settings["form_vars"];
                        $this->smartyview->assign("htmlreport_template_mode", $htmlreport_template_mode);
                        $this->smartyview->assign("htmlreport_tpl", $htmlreport_tpl);
                        $this->smartyview->assign("form_tpl", $form_tpl);
                        $this->smartyview->assign("htmlreport_variables", json_encode($htmlreport_variables));
                    }
                    if ($apptype == "chain") {
                        $chain_data = json_decode($row["script"], true);
                        $chain_url = $chain_data["url"];
                        $chain_formdata = isset($chain_data["formdata"]) ? $chain_data["formdata"] : "";
                        $chain_callback = isset($chain_data["callback"]) ? $chain_data["callback"] : "";
                        $chain_header = isset($chain_data["header"]) ? $chain_data["header"] : "";
                        $this->smartyview->assign("chain_url", $chain_url);
                        $this->smartyview->assign("chain_formdata", $chain_formdata);
                        $this->smartyview->assign("chain_callback", $chain_callback);
                        $this->smartyview->assign("chain_header", $chain_header);
                    }
                    $widgets = $this->GetWidgetsList($appid, true);
                    $creatorid = $this->session->userdata("login_creatorid");
                    $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "key" => "relative_apps"))->get("dc_app_options");
                    if (0 < $query->num_rows()) {
                        $relative_app_ids = json_decode($query->row()->value, true);
                        $query = $this->db->select("appid, name")->where_in("appid", $relative_app_ids)->get("dc_app");
                        $relative_apps = $query->result_array();
                        $this->smartyview->assign("relative_apps", $relative_apps);
                    }
                    $this->smartyview->assign("availableWidgets", json_encode($widgets));
                    $this->smartyview->assign("availableLayouts", $this->getAvailableLayouts());
                    $dashboardId = $appid;
                    $this->smartyview->assign("dashboardId", $dashboardId);
                    $this->smartyview->assign("dashboardLayout", $this->getLayout($dashboardId));
                    $login_permission = $this->session->userdata("login_permission");
                    $this->smartyview->assign("login_permission", $login_permission);
                    $defaultdashboard = $this->_get_default_dashboardid();
                    if ($defaultdashboard) {
                        $this->smartyview->assign("defaultdashboardId", $defaultdashboard);
                    }
                    $this->smartyview->assign("reserved_instance", $this->config->item("reserved_instance"));
                    $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
                    if ($ace_editor_theme) {
                        $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
                    }
                    $query = $this->db->select("name")->where(array("creatorid" => $creatorid, "type" => "theme"))->get("dc_user_options");
                    if (0 < $query->num_rows()) {
                        $themes = $query->result_array();
                        $this->smartyview->assign("saved_themes", $themes);
                    }
                    $this->smartyview->display("newapp.tpl");
                }
            }
        }
    }
    public function delapp()
    {
        $appid = $this->input->post("appid");
        if (!$this->_is_admin_or_developer() || empty($appid)) {
            echo "";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("appid, name")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo "";
            } else {
                $app_name = !empty($query->row()->name) ? $query->row()->name : "Untitled Application";
                $this->load->library("smartyview");
                $this->db->delete("dc_app", array("creatorid" => $creatorid, "appid" => $appid));
                $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid));
                $this->db->delete("dc_app_permission", array("appid" => $appid));
                $this->db->delete("dc_app_version", array("appid" => $appid));
                $this->db->delete("dc_app_log", array("appid" => $appid));
                $this->db->delete("dc_app_history", array("appid" => $appid));
                $this->_log_audit_log($creatorid, "Delete application " . $app_name . "(" . $appid . ")", AUDIT_LOG_LEVEL_DANGER);
                $categories = $this->_get_categories($creatorid);
                $category = array("categoryid" => 0, "name" => $this->config->item("default_category_name"));
                array_unshift($categories, $category);
                $categories_by_id = array();
                foreach ($categories as $category) {
                    $categories_by_id[$category["categoryid"]] = $category;
                }
                $this->smartyview->assign("categories_by_id", $categories_by_id);
                $this->smartyview->assign("categories", $categories);
                $apps = $this->_get_apps($creatorid);
                $this->smartyview->assign("apps", $apps);
                $this->smartyview->display("new/box.apps.tpl");
            }
        }
    }
    public function getapplistbytype()
    {
        $this->load->library("smartyview");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $categories = $this->_get_categories($creatorid);
        $apps = $this->_get_embed_simpleapps_by_status_connid($creatorid, "publish", $connid);
        $category_by_key = array();
        foreach ($categories as $category) {
            $category_by_key[$category["categoryid"]] = $category["name"];
        }
        $categoryapps = array();
        foreach ($apps as $app) {
            $categoryid = $app["categoryid"];
            $categoryname = "Applications";
            if (isset($category_by_key[$categoryid])) {
                $categoryname = $category_by_key[$categoryid];
            }
            if (!isset($categoryapps[$categoryname])) {
                $categoryapps[$categoryname] = array();
            }
            $app["categoryname"] = $categoryname;
            $categoryapps[$categoryname][] = $app;
        }
        $this->smartyview->assign("categoryapps", $categoryapps);
        $this->smartyview->display("appbuilder/modal.applist.tpl");
    }
    /**
     * V7.5: 20180204
     *    如果要切换的数据库名称完全一样, 我们认为数据库的shema不变, 不要刷新整个页面, 客户端仅仅更新当前的数据库链接ID
     *
     *
     */
    public function changeconn()
    {
        $newconnid = $this->input->post("c");
        $is_edit = $this->input->post("is_edit");
        $this->session->set_userdata("appbuilder_selectconnid", $newconnid);
        $this->session->set_userdata("appbuilder_selectconnid_changed", $newconnid);
        if (!empty($is_edit) && $is_edit == 1) {
            $this->edit();
        } else {
            $this->create();
        }
    }
    public function create()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $this->load->library("smartyview");
        $expired = $this->session->userdata("_EXPIRED_");
        if ($expired) {
            $this->smartyview->assign("hasError", true);
            $this->smartyview->assign("title", "Expired");
            $this->smartyview->assign("message", "DbFace instance has been expired or tempory unavailable, please contact the developer.");
            $this->smartyview->display("runtime/app.error.tpl");
        } else {
            $enable_marketplace = $this->config->item("enable_marketplace");
            $this->smartyview->assign("enable_marketplace", $enable_marketplace);
            $enable_preview = $this->config->item("enable_live_preview");
            $this->smartyview->assign("enable_livepreview", $enable_preview);
            $creatorid = $this->session->userdata("login_creatorid");
            $session_appid = $this->session->userdata("appbuilder_appid");
            $appid = $this->input->get_post("appid");
            $conns = $this->_get_connections($creatorid);
            $categories = $this->_get_categories($creatorid);
            $category = array("categoryid" => 0, "name" => $this->config->item("default_category_name"));
            array_unshift($categories, $category);
            $apptype = $this->input->get_post("apptype");
            if (empty($appid)) {
                $this->load->database();
                $query = $this->db->query("select connid, type from dc_app where creatorid=? and appid=?", array($creatorid, $session_appid));
                if (0 < $query->num_rows()) {
                    $result = $query->row_array();
                    if ($result["type"] == $apptype) {
                        $appid = $session_appid;
                    }
                }
            }
            $this->smartyview->assign("disable_phpreport", $this->config->item("disable_phpreport"));
            $this->smartyview->assign("disable_sql_edit_application", $this->config->item("disable_sql_edit_application"));
            $this->smartyview->assign("conns", $conns);
            $se_connid = $this->session->userdata("_default_connid_");
            if (empty($se_connid)) {
                $se_connid = $this->session->userdata("appbuilder_selectconnid");
            }
            if (!empty($se_connid)) {
                $this->session->set_userdata("appbuilder_selectconnid", $se_connid);
            }
            $appbuilder_selectconnid_changed = $this->session->userdata("appbuilder_selectconnid_changed");
            if (!empty($appbuilder_selectconnid_changed)) {
                $se_connid = $appbuilder_selectconnid_changed;
                $this->session->set_userdata("appbuilder_selectconnid", $se_connid);
            }
            $selected_connid = 0;
            if (!empty($se_connid)) {
                $exist = false;
                foreach ($conns as $conn) {
                    if ($conn["connid"] == $se_connid) {
                        $exist = true;
                        break;
                    }
                }
                if ($exist) {
                    $selected_connid = $se_connid;
                }
            }
            if ($selected_connid == 0) {
                $selected_connid = $conns[0]["connid"];
                $this->session->set_userdata("appbuilder_selectconnid", $selected_connid);
            }
            $is_mongodb = $this->_is_mongodb($creatorid, $selected_connid);
            if ($is_mongodb) {
                $this->smartyview->assign("mongodb", 1);
                if (empty($appid)) {
                    $this->smartyview->assign("use_json", 1);
                }
            }
            $is_dynamodb = $this->_is_dynamodb($creatorid, $selected_connid);
            if ($is_dynamodb) {
                $this->smartyview->assign("dynamodb", 1);
                if (empty($appid)) {
                    $this->smartyview->assign("use_json", 1);
                }
            }
            $this->smartyview->assign("connid", $selected_connid);
            $this->smartyview->assign("tablenames", $this->_get_tablenames());
            $this->smartyview->assign("categories", $categories);
            $this->smartyview->assign("apptype", $apptype);
            $this->smartyview->assign("subapps", $this->_get_user_subapps($creatorid, false));
            $scriptType = 1;
            $designmode = $this->input->get_post("mode");
            if (empty($designmode)) {
                $designmode = "dragdropmode";
            }
            if ($is_mongodb) {
                $designmode = "sqlmode";
            }
            if ($apptype == "list") {
                if ($designmode == "sqlmode") {
                    $scriptType = 2;
                } else {
                    $scriptType = 1;
                }
            } else {
                if ($apptype == "generaloperation") {
                    $scriptType = 3;
                } else {
                    if ($apptype == "htmlreport") {
                        $scriptType = 4;
                    } else {
                        if ($apptype == "phpreport") {
                            $scriptType = 6;
                        } else {
                            if ($apptype == "dashboard") {
                                $scriptType = 5;
                            }
                        }
                    }
                }
            }
            if ($appid) {
                $this->smartyview->assign("appid", $appid);
            }
            $query = $this->db->query("select count(appid) as numapp from dc_app where creatorid=?", array($creatorid));
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $numapp = $row["numapp"];
                $quote = $this->_check_quote("max_application", $numapp);
                $this->smartyview->assign("fullquote", $quote);
            }
            $this->smartyview->assign("apptype", $apptype);
            $this->smartyview->assign("designmode", $designmode);
            $this->smartyview->assign("app_scriptType", $scriptType);
            $this->smartyview->assign("builderboxstyle", "box box-primary");
            $this->_set_general_users($creatorid, $appid);
            $widgets = $this->GetWidgetsList(false, true);
            $creatorid = $this->session->userdata("login_creatorid");
            $numconn = $this->_get_connection_count($creatorid);
            $this->smartyview->assign("numconn", $numconn);
            $this->smartyview->assign("availableWidgets", json_encode($widgets));
            $this->smartyview->assign("availableLayouts", $this->getAvailableLayouts());
            if (empty($appid)) {
                $dashboardId = -1;
            } else {
                $dashboardId = $appid;
            }
            $this->smartyview->assign("dashboardId", $dashboardId);
            $this->smartyview->assign("dashboardLayout", $this->getLayout($dashboardId));
            $login_permission = $this->session->userdata("login_permission");
            $this->smartyview->assign("login_permission", $login_permission);
            $defaultdashboard = $this->_get_default_dashboardid();
            if ($defaultdashboard) {
                $this->smartyview->assign("defaultdashboardId", $defaultdashboard);
            }
            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
            if ($ace_editor_theme) {
                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
            }
            $query = $this->db->select("name")->where(array("creatorid" => $creatorid, "type" => "theme"))->get("dc_user_options");
            if (0 < $query->num_rows()) {
                $themes = $query->result_array();
                $this->smartyview->assign("saved_themes", $themes);
            }
            $this->smartyview->assign("reserved_instance", $this->config->item("reserved_instance"));
            $this->smartyview->display("newapp.tpl");
        }
    }
    /**
     * Get the dashboard layout for the current user (anonymous or logged user)
     *
     * @param int $idDashboard
     *
     * @return string $layout
     */
    public function getLayout($idDashboard)
    {
        $layout = $this->_getLayoutForUser($idDashboard);
        if (empty($layout)) {
            $layout = "[]";
        }
        return $layout;
    }
    public function _getLayoutForUser($idDashboard)
    {
        $query = $this->db->query("select type, script from dc_app where appid=? limit 1", array($idDashboard));
        $return = $query->row_array();
        if (empty($return) || $return["type"] != "dashboard") {
            return false;
        }
        return $return["script"];
    }
    /**
     * Returns all available column layouts for the dashboard
     *
     * @return array
     */
    protected function getAvailableLayouts()
    {
        return array(array(100), array(50, 50), array(67, 33), array(33, 67), array(33, 33, 33), array(40, 30, 30), array(30, 40, 30), array(30, 30, 40), array(25, 25, 25, 25));
    }
    private function addWidget($widgetCategory, $widgetUniqueId, $widgetName, $controllerName, $controllerAction, $customParameters = array())
    {
        if (!isset(self::$widgets[$widgetCategory])) {
            self::$widgets[$widgetCategory] = array();
        }
        self::$widgets[$widgetCategory][] = array("name" => $widgetName, "uniqueId" => $widgetUniqueId, "parameters" => array("module" => $controllerName, "action" => $controllerAction, "name" => $widgetName) + $customParameters);
    }
    public function getAvailableWidgets()
    {
        $exclude_appid = $this->input->get_post("appid");
        if (empty($exclude_appid)) {
            $exclude_appid = false;
        }
        echo json_encode($this->GetWidgetsList($exclude_appid));
    }
    public function GetWidgetsList($exclude_appid = false, $useSmarty = false)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $categories = $this->_get_categories($creatorid);
        $category_by_key = array();
        foreach ($categories as $category) {
            $category_by_key[$category["categoryid"]] = $category["name"];
        }
        $query = $this->db->query("select appid, name, title, format, type, categoryid from dc_app where status=? and creatorid = ?", array("publish", $creatorid));
        $result = $query->result_array();
        $apps = array();
        foreach ($result as $row) {
            if ($row["type"] == "dashboard") {
                continue;
            }
            if ($row["appid"] == $exclude_appid) {
                continue;
            }
            if ($row["categoryid"] == NULL || $row["categoryid"] == "null") {
                $row["categoryid"] = 0;
            }
            $catname = isset($category_by_key[$row["categoryid"]]) ? $category_by_key[$row["categoryid"]] : false;
            if (empty($catname)) {
                $catname = "Widgets";
            }
            $token = md5("w" . $row["appid"] . $this->config->item("jsdingxx"));
            $this->addWidget($catname, "w" . $row["appid"], $row["name"], "App", "index", array("appid" => $row["appid"], "embed" => 1, "token" => $token, "name" => $row["name"]));
            $apps[] = array("appid" => $row["appid"], "name" => $row["name"], "categoryid" => $row["categoryid"], "format" => $row["format"]);
        }
        if ($useSmarty) {
            $this->smartyview->assign("widgetList", $apps);
        }
        return self::$widgets;
    }
    public function _set_general_users($creatorid, $appid)
    {
        $query = $this->db->query("select userid, name from dc_user where creatorid = ? and permission = 9 and groupid = ?", array($creatorid, ""));
        if ($query && 0 < $query->num_rows()) {
            $this->smartyview->assign("users", $query->result_array());
        }
        $query = $this->db->query("select userid from dc_app_permission where appid=?", array($appid));
        if (0 < $query->num_rows()) {
            $userpermissions = array();
            $result = $query->result_array();
            foreach ($result as $row) {
                $userpermissions[$row["userid"]] = 1;
            }
            $this->smartyview->assign("userpermission", $userpermissions);
        }
        $query = $this->db->select("groupid, name")->where("creatorid", $creatorid)->get("dc_usergroup");
        if ($query && 0 < $query->num_rows()) {
            $this->smartyview->assign("usergroups", $query->result_array());
        }
        $query = $this->db->query("select groupid from dc_usergroup_permission where appid=?", array($appid));
        if (0 < $query->num_rows()) {
            $usergroup_permission = array();
            $result = $query->result_array();
            foreach ($result as $row) {
                $usergroup_permission[$row["groupid"]] = 1;
            }
            $this->smartyview->assign("usergroup_permission", $usergroup_permission);
        }
    }
    public function gettablenames()
    {
        $this->load->database();
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->session->userdata("appbuilder_selectconnid");
        $cache_key = "schema_gettablenames_" . $connid;
        $tablenamesInCache = $this->_get_cache($creatorid, "schema", $cache_key);
        if (!$tablenamesInCache) {
            $db = $this->_get_db($creatorid, $connid);
            $tablelist = list_tables($db);
            if ($tablelist) {
                $json_tablelist = json_encode($tablelist);
                $this->output->set_content_type("application/json")->set_output($json_tablelist);
                $this->_save_cache($creatorid, "schema", $cache_key, $json_tablelist);
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array()));
            }
        } else {
            $this->output->set_content_type("application/json")->set_output($tablenamesInCache);
        }
    }
    public function json_getcolumns()
    {
        $this->load->database();
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->session->userdata("appbuilder_selectconnid");
        $tablename = $this->input->get("tblname");
        $cache_key = "schema_json_getcolumns_" . $connid . "_" . $tablename;
        $fieldsInCache = $this->_get_cache($creatorid, "schema", $cache_key);
        if (!$fieldsInCache) {
            $db = $this->_get_db($creatorid, $connid);
            $fields = list_fields($db, $tablename);
            if ($fields) {
                $json_fields = json_encode($fields);
                $this->_save_cache($creatorid, "schema", $cache_key, $json_fields);
                $this->output->set_content_type("application/json")->set_output($json_fields);
            } else {
                $this->output->set_content_type("application/json")->set_output(json_encode(array()));
            }
        } else {
            $this->output->set_content_type("application/json")->set_output($fieldsInCache);
        }
    }
    public function save_form()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 100));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $userid = $this->session->userdata("login_userid");
            $appid = $this->input->post("appid");
            $connid = $this->input->post("connid");
            $form = trim($this->input->post("form"));
            $form_org = trim($this->input->post("form_org"));
            $form_builder_mode = $this->input->post("form_builder_mode");
            $form_option_name = trim($this->input->post("form_option_name"));
            $form_option_css = trim($this->input->post("form_option_css"));
            $form_option_loadingscript = trim($this->input->post("form_option_loadingscript"));
            $form_option_display = trim($this->input->post("form_option_display"));
            $form_option_autoload = trim($this->input->post("form_option_autoload"));
            if (empty($appid)) {
                echo json_encode(array("status" => 0));
            } else {
                $query = $this->db->select("1")->where(array("appid" => $appid, "connid" => $connid, "creatorid" => $creatorid))->get("dc_app");
                if ($query && $query->num_rows() == 1) {
                    $this->db->update("dc_app", array("form" => json_encode(array("html" => $form, "form_builder_mode" => $form_builder_mode, "display" => $form_option_display, "autoload" => $form_option_autoload, "name" => $form_option_name, "css" => $form_option_css, "loadingscript" => $form_option_loadingscript)), "form_org" => $form_org), array("appid" => $appid, "connid" => $connid, "creatorid" => $creatorid));
                    echo json_encode(array("status" => 1));
                }
            }
        }
    }
    public function _save_app($status)
    {
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        $appid = $this->input->post("appid");
        $apptype = $this->input->post("apptype");
        $connid = $this->input->post("connid");
        $name = $this->input->post("name");
        $title = $this->input->post("title");
        $desc = $this->input->post("desc");
        $categoryid = $this->input->post("categoryid");
        $form = trim($this->input->post("form"));
        $form_org = trim($this->input->post("form_org"));
        $form_builder_mode = $this->input->post("form_builder_mode");
        $form_option_name = trim($this->input->post("form_option_name"));
        $form_option_css = trim($this->input->post("form_option_css"));
        $form_option_loadingscript = trim($this->input->post("form_option_loadingscript"));
        $form_option_display = trim($this->input->post("form_option_display"));
        $form_option_autoload = trim($this->input->post("form_option_autoload"));
        $scriptType = $this->input->post("scriptType");
        if ($apptype == "generaloperation") {
            $scriptType = 3;
        } else {
            if ($apptype == "htmlreport") {
                $scriptType = 4;
            } else {
                if ($apptype == "phpreport") {
                    $scriptType = 6;
                } else {
                    if ($apptype == "dashboard") {
                        $scriptType = 5;
                    } else {
                        if ($apptype == "story") {
                            $scriptType = 7;
                        } else {
                            if ($apptype == "gallery") {
                                $scriptType = 8;
                            } else {
                                if ($apptype == "freeformdisplay") {
                                    $scriptType = 9;
                                } else {
                                    if ($apptype == "chain") {
                                        $scriptType = 10;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $format = $this->input->post("format");
        $confirm = $this->input->post("confirm");
        if ($scriptType == "1") {
            $script_arr = array();
            $sqlcondition = $this->input->post("sqlcondition");
            if (!empty($sqlcondition)) {
                $script_arr["sqlcondition"] = $sqlcondition;
            }
            $sqlop = $this->input->post("sqlop");
            if (!empty($sqlop)) {
                $script_arr["sqlop"] = $sqlop;
            }
            $sqlvalue = $this->input->post("sqlvalue");
            if (!empty($sqlvalue)) {
                $script_arr["sqlvalue"] = $sqlvalue;
            }
            $sqljoin = $this->input->post("sqljoin");
            if (!empty($sqljoin)) {
                $script_arr["sqljoin"] = $sqljoin;
            }
            $tablename = $this->input->post("tablename");
            if (!empty($tablename)) {
                $script_arr["tablename"] = $this->input->post("tablename");
            }
            $left_columnname = $this->input->post("left_columnname");
            if (!empty($left_columnname)) {
                $script_arr["left_columnname"] = $left_columnname;
            }
            $right_columnname = $this->input->post("right_columnname");
            if (!empty($right_columnname)) {
                $script_arr["right_columnname"] = $right_columnname;
            }
            $jointype = $this->input->post("jointype");
            if (!empty($jointype)) {
                $script_arr["jointype"] = $jointype;
            }
            $advrep_fields = array("select", "order", "groupby", "summary", "column", "row", "pivotdata", "xaxis", "yaxis");
            foreach ($advrep_fields as $tadvrep_field) {
                $c = $this->input->post($tadvrep_field);
                if (!empty($c)) {
                    $script_arr[$tadvrep_field] = $c;
                }
                $cf = $this->input->post($tadvrep_field . "fun");
                if (!empty($cf)) {
                    $script_arr[$tadvrep_field . "fun"] = $cf;
                }
                $cl = $this->input->post($tadvrep_field . "label");
                if (!empty($cl)) {
                    $script_arr[$tadvrep_field . "label"] = $cl;
                }
            }
            $rowlimit = $this->input->post("rowlimit");
            if (!empty($rowlimit)) {
                $script_arr["rowlimit"] = $rowlimit;
            }
            $axislabel = $this->input->post("axislabel");
            if (!empty($axislabel)) {
                $script_arr["xaxislabel"] = $axislabel;
            }
            $yaxislabel = $this->input->post("yaxislabel");
            if (!empty($yaxislabel)) {
                $script_arr["yaxislabel"] = $yaxislabel;
            }
            $script = json_encode($script_arr);
            $script_org = $this->input->post("script_org");
        } else {
            $script = $this->input->post("script");
            $script_org = $this->input->post("script_org");
            if ($scriptType == 6) {
                $this->_write_cloud_code($creatorid, "dbface_app_" . $appid, $script, true);
            }
            if ($scriptType == 4) {
                $this->_write_htmlreport_code($creatorid, "dbface_app_" . $appid, $script, true);
            }
        }
        $options = array();
        $option_keys = $this->config->item("app_available_options");
        foreach ($option_keys as $key) {
            $v = $this->input->post($key);
            if ($v != "") {
                $options[$key] = $v;
            }
        }
        if ($format == "tabular" || $format == "summary" || $format == "pivot") {
            $series_options = $this->input->post("series_options");
            if (!empty($series_options)) {
                $options["series_options"] = $series_options;
            }
        }
        if ($format == "combinedbarlinechart") {
            $series_options = $this->input->post("barline_options");
            if (!empty($series_options)) {
                $options["series_options"] = $series_options;
            }
        }
        if ($format == "pivot" && $scriptType == "2") {
            $pivot_options = $this->input->post("pivot_options");
            if (!empty($pivot_options)) {
                $options["pivot_options"] = $pivot_options;
            }
        }
        if ($scriptType == 6 && !isset($options["mixedhtml"])) {
            $options["mixedhtml"] = "0";
        }
        $use_json = $this->input->post("use_json");
        if ($use_json == "1") {
            $options["use_json"] = 1;
        }
        if ($apptype == "list" && $format != "editapp" && $this->_is_writable_script($script)) {
            $this->output->set_content_type("application/json")->set_output(json_encode(array("appid" => 0, "message" => "Writable script disallowed in query application.")));
            return NULL;
        }
        if (!empty($name)) {
            $query = $this->db->query("select appid from dc_app where creatorid=? and name=?", array($creatorid, $name));
            if (0 < $query->num_rows()) {
                $tappid = $query->row()->appid;
                if ($tappid != $appid) {
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("appid" => 0, "message" => "Application name already used, please choose another one.")));
                    return NULL;
                }
            }
        }
        if (empty($appid)) {
            $query = $this->db->query("select count(appid) as numapp from dc_app where creatorid=?", array($creatorid));
            $row = $query->row_array();
            $numapp = $row["numapp"];
            $quote = $this->_check_quote("max_application", $numapp);
            if ($quote) {
                $this->output->set_content_type("application/json")->set_output(json_encode(array("appid" => 0, "message" => "No application slot available to save this application. Please upgrade your plan.")));
                return NULL;
            }
            $insert_array = array("connid" => $connid, "creatorid" => $creatorid, "type" => $apptype, "name" => $name, "title" => $title, "desc" => $desc, "categoryid" => $categoryid, "form" => json_encode(array("html" => $form, "form_builder_mode" => $form_builder_mode, "name" => $form_option_name, "css" => $form_option_css, "display" => $form_option_display, "autoload" => $form_option_autoload, "loadingscript" => $form_option_loadingscript)), "form_org" => $form_org, "scripttype" => $scriptType, "confirm" => $confirm, "format" => $format, "options" => json_encode($options), "status" => $status, "createdate" => time());
            if ($apptype != "dashboard") {
                $insert_array["script"] = $script;
                $insert_array["script_org"] = $script_org;
            }
            $this->db->insert("dc_app", $insert_array);
            $appid = $this->db->insert_id();
            if (empty($name)) {
                $this->_log_audit_log($creatorid, "Create Application " . $appid, AUDIT_LOG_LEVEL_SUCCESS);
            } else {
                $this->_log_audit_log($creatorid, "Create Application " . $appid . "(" . $name . ")", AUDIT_LOG_LEVEL_SUCCESS);
            }
        } else {
            $query = $this->db->select("options")->where("appid", $appid)->get("dc_app");
            $options_org = json_decode($query->row()->options, true);
            if (!isset($options["series_options"]) && isset($options_org["series_options"])) {
                $options["series_options"] = $options_org["series_options"];
            }
            if ($format == "pivot" && $scriptType == "2" && !isset($options["pivot_options"]) && isset($options_org["pivot_options"])) {
                $options["pivot_options"] = $options_org["pivot_options"];
            }
            $update_array = array("creatorid" => $creatorid, "connid" => $connid, "type" => $apptype, "name" => $name, "title" => $title, "desc" => $desc, "categoryid" => $categoryid, "form" => json_encode(array("html" => $form, "form_builder_mode" => $form_builder_mode, "name" => $form_option_name, "css" => $form_option_css, "display" => $form_option_display, "autoload" => $form_option_autoload, "loadingscript" => $form_option_loadingscript)), "form_org" => $form_org, "scripttype" => $scriptType, "confirm" => $confirm, "format" => $format, "options" => json_encode($options), "status" => $status, "createdate" => time());
            if ($apptype != "dashboard") {
                $update_array["script"] = $script;
                $update_array["script_org"] = $script_org;
            }
            $this->db->update("dc_app", $update_array, array("appid" => $appid));
        }
        $this->db->delete("dc_app_permission", array("appid" => $appid));
        $userpermissions = $this->input->post("userpermission");
        if (!empty($userpermissions)) {
            foreach ($userpermissions as $userpermission) {
                $p = array("userid" => $userpermission, "appid" => $appid);
                $this->db->insert("dc_app_permission", $p);
            }
        }
        $this->db->delete("dc_usergroup_permission", array("appid" => $appid));
        $usergrouppermissions = $this->input->post("usergrouppermission");
        if (!empty($usergrouppermissions)) {
            foreach ($usergrouppermissions as $usergrouppermission) {
                $p = array("groupid" => $usergrouppermission, "appid" => $appid, "date" => time());
                $this->db->insert("dc_usergroup_permission", $p);
            }
        }
        $this->_save_as_app_version($appid, "");
        $this->session->set_userdata("appbuilder_appid", $appid);
        $this->output->set_content_type("application/json")->set_output(json_encode(array("appid" => $appid)));
    }
    public function draft()
    {
        $this->_save_app("draft");
    }
    public function publish()
    {
        $this->_save_app("publish");
    }
    public function loadDDEditor()
    {
        $this->load->helper("dbface");
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->session->userdata("appbuilder_selectconnid");
        $db = $this->_get_db($creatorid, $connid);
        $rpf = $this->input->post("rpf");
        $tblnames = $this->input->post("tablename");
        $tblcolumninfos = array();
        foreach ($tblnames as $tblname) {
            if (!empty($tblname)) {
                $columns = field_data($db, $tblname);
                $renewColumns = array();
                if (!empty($columns)) {
                    foreach ($columns as $column) {
                        $tmp = array();
                        $tmp["type"] = $this->get_input_type($column->type, $column->max_length);
                        switch ($tmp["type"]) {
                            case CDT_NUMBERIC:
                                $tmp["csstype"] = "fa fa-sort-numeric-asc";
                                break;
                            case CDT_DATETIME:
                                $tmp["csstype"] = "fa fa-calendar";
                                break;
                            default:
                                $tmp["csstype"] = "fa fa-font";
                                break;
                        }
                        $tmp["name"] = $column->name;
                        $renewColumns[] = $tmp;
                    }
                }
                $tblcolumninfos[] = array("tblname" => $tblname, "columns" => $renewColumns);
            }
        }
        $this->smartyview->assign("singletable", count($tblcolumninfos) == 1);
        $this->smartyview->assign("tblcolumninfos", $tblcolumninfos);
        $this->smartyview->assign("tablename", $this->input->post("tablename"));
        $this->smartyview->assign("left_columnname", $this->input->post("left_columnname"));
        $this->smartyview->assign("right_columnname", $this->input->post("right_columnname"));
        $this->smartyview->assign("jointype", $this->input->post("jointype"));
        $this->smartyview->assign("rpf", $rpf);
        $this->smartyview->assign("builderboxstyle", "box box-primary");
        $this->smartyview->assign("displayformat", $this->input->post("rpf"));
        $this->smartyview->display("appbuilder/script.builder.dd.fields.tpl");
    }
    public function columndata()
    {
        $d = $this->input->post("columnname");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $tmp = explode(".", $d);
        if (!$tmp || count($tmp) != 2) {
            exit($d . " Please select a column as condition.");
        }
        list($tablename, $columnname) = $tmp;
        $db = $this->_get_db($creatorid, $connid);
        $e_columnname = $db->protect_identifiers($columnname);
        $e_tablename = $db->protect_identifiers($tablename);
        $query = $db->query("SELECT DISTINCT " . $e_columnname . " FROM " . $e_tablename . " order by " . $e_columnname);
        if (!$query) {
            $error = $db->error();
            echo $error["message"] . ": " . $db->last_query();
        } else {
            $tmpdatas = $query->result_array();
            $datas = array();
            foreach ($tmpdatas as $tmpdata) {
                $datas[] = $tmpdata[$columnname];
            }
            $this->load->library("smartyview");
            $this->smartyview->assign("datas", $datas);
            $this->smartyview->display("datalist.tpl");
        }
    }
    public function gensimplescript()
    {
        $connid = $this->input->post("cn");
        $columns = $this->input->post("c");
        $tablename = $this->input->post("tb");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        foreach ($columns as $column) {
            $db->select($column);
        }
        $db->distinct();
        $db->from($tablename);
        echo $db->get_compiled_select();
    }
    public function genscriptHelper()
    {
        $this->load->helper("json");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $cache_key = "tablefields_" . $connid;
        $cache = $this->_get_cache($creatorid, "schema", $cache_key);
        $tablefields = NULL;
        if (!$cache) {
            $db = $this->_get_db($creatorid, $connid);
            $tablefields = array();
            $tablelist = list_tables($db);
            foreach ($tablelist as $tablename) {
                $fields = list_fields($db, $tablename);
                $tablefields[$tablename] = $fields;
            }
            $this->_save_cache($creatorid, "schema", $cache_key, json_encode($tablefields));
        } else {
            $tablefields = json_decode($this->_get_cache($creatorid, "schema", $cache_key), true);
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("tablefields", $tablefields);
        $this->smartyview->display("gen.sql.helper.tpl");
    }
    public function genresultscript()
    {
        $connid = $this->input->post("cn");
        $columns = $this->input->post("c");
        $tablename = $this->input->post("tb");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        foreach ($columns as $column) {
            $db->select($column);
        }
        $db->distinct();
        $db->from($tablename);
        $query = $db->get();
        $this->load->library("smartyview");
        $fields = $query->list_fields();
        $datas = $query->result_array();
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("datas", $datas);
        $this->smartyview->assign("ID_RESULTSET", uniqid("RS_"));
        $this->smartyview->display("gen.dataselector.tpl");
    }
    public function gendataHelper()
    {
        $this->load->helper("json");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $cache_key = "tablefields_" . $connid;
        $cache = $this->_get_cache($creatorid, "schema", $cache_key);
        $tablefields = NULL;
        if (!$cache) {
            $db = $this->_get_db($creatorid, $connid);
            $tablefields = array();
            $tablelist = list_tables($db);
            foreach ($tablelist as $tablename) {
                $fields = list_fields($db, $tablename);
                $tablefields[$tablename] = $fields;
            }
            $this->_save_cache($creatorid, "schema", $cache_key, json_encode($tablefields));
        } else {
            $tablefields = json_decode($this->_get_cache($creatorid, "schema", $cache_key), true);
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("tablefields", $tablefields);
        $this->smartyview->display("gen.data.helper.tpl");
    }
    public function tagcloud()
    {
        if ($this->config->item("enable_database_tip") === false) {
            echo "";
        } else {
            $this->load->library("smartyview");
            $tags = array();
            $connid = $this->session->userdata("appbuilder_selectconnid");
            $creatorid = $this->session->userdata("login_creatorid");
            $input_connid = $this->input->get_post("connid");
            if (!empty($input_connid)) {
                $connid = $input_connid;
            }
            $cache_key = "tag_" . $connid;
            $cache_str = $this->_get_cache($creatorid, "schema", $cache_key);
            $table_fields = NULL;
            if (!$cache_str) {
                $db = $this->_get_db($creatorid, $connid);
                $tables = list_tables($db);
                $table_fields = array();
                foreach ($tables as $table) {
                    $fields = list_fields($db, $table);
                    if ($fields) {
                        $table_fields[$table] = $fields;
                    }
                }
                if (!empty($table_fields)) {
                    $this->_save_cache($creatorid, "schema", $cache_key, json_encode($table_fields));
                }
            } else {
                $table_fields = json_decode($cache_str, true);
            }
            $this->smartyview->assign("table_fields", $table_fields);
            $this->smartyview->display("inc/tagcloud.tpl");
        }
    }
    public function saveembedcode()
    {
        $appid = $this->input->post("appid");
        $sharestatus = $this->input->post("sharestatus");
        $embedcode = $this->input->post("embedcode");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select embedcode from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        if ($query && 0 < $query->num_rows()) {
            $changed = false;
            if ($sharestatus == 1) {
                $cur_code = $query->row()->embedcode;
                if ($cur_code != $embedcode) {
                    $this->db->update("dc_app", array("embedcode" => $embedcode), array("appid" => $appid, "creatorid" => $creatorid));
                }
            } else {
                $this->db->update("dc_app", array("embedcode" => NULL), array("appid" => $appid, "creatorid" => $creatorid));
            }
            echo 1;
        } else {
            echo 0;
        }
    }
    public function getshareurl()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select embedcode from dc_app where appid=? and creatorid=?", array($appid, $creatorid));
        $embedcode = NULL;
        $sharestatus = 1;
        if ($query && 0 < $query->num_rows()) {
            $embedcode = $query->row()->embedcode;
        }
        if (empty($embedcode)) {
            $embedcode = uniqid("ap");
            $sharestatus = 0;
        }
        $base_url = $this->_get_url_base();
        $direct_link = $base_url . "?module=Embed&OBJID=" . $embedcode;
        $ret = array("embedcode" => $embedcode, "iframeurl" => $direct_link);
        $ret["embed_iframe"] = "<iframe src=\"" . $direct_link . "\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\" width=\"100%\" height=\"100%\"></iframe>";
        $ret["sharestatus"] = $sharestatus;
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode($ret));
    }
    /**
     * Saves the layout for the current user
     * anonymous = in the session
     * authenticated user = in the DB
     */
    public function saveLayout()
    {
        $idDashboard = $this->input->get_post("idDashboard");
        $layout = $this->input->get_post("layout");
        if (empty($layout)) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $permission = $this->session->userdata("login_permission");
        if (empty($creatorid) || $permission === NULL || $permission == 9) {
            echo json_encode(array("status" => 0, "message" => "Permisson Denied", "creatorid" => $creatorid, "permission" => $permission));
        } else {
            $update_array = array("script" => $layout, "creatorid" => $creatorid);
            $create_dashboard = false;
            if (empty($idDashboard) || $idDashboard == -1) {
                $create_dashboard = true;
            } else {
                $query = $this->db->query("select 1 from dc_app where appid = ? and creatorid = ?", array($idDashboard, $creatorid));
                if ($query->num_rows() == 0) {
                    $create_dashboard = true;
                }
            }
            $appid = $idDashboard;
            if ($create_dashboard === false) {
                $this->db->update("dc_app", $update_array, array("appid" => $idDashboard));
            } else {
                $this->db->insert("dc_app", $update_array);
                $appid = $this->db->insert_id();
            }
            echo json_encode(array("status" => 1, "appid" => $appid));
        }
    }
    public function clearapplog()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($appid)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0));
            } else {
                $this->db->delete("dc_app_log", array("creatorid" => $creatorid, "appid" => $appid));
                echo json_encode(array("status" => 1));
            }
        }
    }
    public function applog()
    {
        $this->load->library("smartyview");
        $appid = $this->input->post("appid");
        $s = $this->input->post("s");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->select("name, type, value, date");
        $this->db->where(array("creatorid" => $creatorid, "appid" => $appid));
        $this->db->order_by("date", "desc");
        $this->db->limit(10);
        $query = $this->db->get("dc_app_log");
        $applogs = $query->result_array();
        $this->smartyview->assign("applogs", $applogs);
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->display("appbuilder/appbuilder.realtime.log.item.tpl");
    }
    public function get_app_smallbox()
    {
        $appid = $this->input->post("appid");
        $appinfo = $this->_get_app_box_info($appid);
        $this->load->library("smartyview");
        if ($appinfo) {
            $this->smartyview->assign("appid", $appinfo["appid"]);
            $this->smartyview->assign("name", $appinfo["name"]);
            $this->smartyview->assign("description", $appinfo["description"]);
        }
        $this->smartyview->display("appbuilder/section.gallery.inc.tpl");
    }
    public function prev_source_form()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $source_appid = $this->input->post("source");
            $query = $this->db->select("form, form_org")->where("appid", $source_appid)->get("dc_app");
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $form = json_decode($row["form"], true);
                $name = isset($form["name"]) ? $form["name"] : false;
                $html = $form["html"];
                $this->load->library("smartyview");
                $this->smartyview->assign("formTitle", $name);
                $this->smartyview->assign("formHTML", $html);
                $this->smartyview->display("appbuilder/library.form.preview.tpl");
            }
        }
    }
    public function get_source_form()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $source_appid = $this->input->post("source");
            $mode = $this->input->post("mode");
            $query = $this->db->select("form, form_org")->where("appid", $source_appid)->get("dc_app");
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                if ($mode == "source") {
                    $form_data = $row["form"];
                    $form = json_decode($row["form"], true);
                    $this->output->set_output($form["html"]);
                } else {
                    $this->output->set_output($row["form_org"]);
                }
            }
        }
    }
    public function code_template_library()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $connid = $this->input->post("connid");
            $creatorid = $this->session->userdata("login_creatorid");
            $this->load->library("smartyview");
            $query = $this->db->select("api")->where(array("creatorid" => $creatorid, "connid" => $connid))->get("dc_code");
            $this->smartyview->assign("codes", $query->result_array());
            $this->smartyview->display("appbuilder/library.cloudcode.indialog.tpl");
        }
    }
    public function import_cloud_code()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $connid = $this->input->post("connid");
            $api = $this->input->post("api");
            $query = $this->db->select("content")->where(array("creatorid" => $creatorid, "connid" => $connid, "api" => $api))->get("dc_code");
            if ($query->num_rows() == 1) {
                echo $query->row()->content;
            } else {
                echo "";
            }
        }
    }
    public function htmlreport_template_library()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $this->load->library("smartyview");
            $this->smartyview->display("appbuilder/library.htmlreport.template.tpl");
        }
    }
    public function form_library()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $appid = $this->input->post("appid");
            $connid = $this->input->post("connid");
            if (!empty($appid)) {
                $query = $this->db->select("connid")->where("appid", $appid)->get("dc_app");
                $connid = $query->row()->connid;
            }
            $query = $this->db->select("appid, name, form, form_org")->where("form is not null")->where("connid", $connid)->get("dc_app");
            $tmp_forms = $query->result_array();
            $forms = array();
            foreach ($tmp_forms as $row) {
                $source_appid = $row["appid"];
                if ($source_appid == $appid) {
                    continue;
                }
                $source_appname = $row["name"];
                $form = json_decode($row["form"], true);
                $form_build_mode = isset($form["form_builder_mode"]) ? $form["form_builder_mode"] : "design";
                $form_name = isset($form["name"]) ? $form["name"] : "";
                $form_html = $form["html"];
                if (!empty($form_html)) {
                    $form_name = empty($form_name) ? $source_appname : $form_name;
                    if (!empty($form_name)) {
                        $forms[] = array("source" => $source_appid, "name" => $form_name, "mode" => $form_build_mode);
                    }
                }
            }
            $this->load->library("smartyview");
            $this->smartyview->assign("forms", $forms);
            $this->smartyview->display("appbuilder/library.form.indialog.tpl");
        }
    }
    public function script_library()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "Permission Denied";
        } else {
            $appid = $this->input->post("appid");
            $connid = $this->input->post("connid");
            if (!empty($appid)) {
                $query = $this->db->select("connid")->where("appid", $appid)->get("dc_app");
                $connid = $query->row()->connid;
            }
            $query = $this->db->select("name, title, script")->where(array("connid" => $connid, "scripttype" => 2))->order_by("appid", "desc")->get("dc_app");
            $result_array = $query->result_array();
            $scripts = array();
            foreach ($result_array as &$row) {
                if (!empty($row["script"])) {
                    $scripts[] = $row;
                }
            }
            $this->load->library("smartyview");
            $this->smartyview->assign("scripts", $scripts);
            $this->smartyview->display("appbuilder/library.script.indialog.tpl");
        }
    }
    public function wa_template()
    {
        $widget = $this->input->post("wt");
        $title = $this->input->post("wp");
        $appid = $this->input->post("appid");
        $this->load->library("smartyview");
        $this->smartyview->assign("uniqid", uniqid("wt_"));
        if ($widget == "basic") {
            $this->smartyview->display("wa/wa.basic." . $title . ".tpl");
        } else {
            if ($widget == "shape") {
                $this->smartyview->assign("shape_dir", "wa/shapes/svg/" . $title . ".svg");
                if ($title == "Star" || $title == "Rounded square" || $title == "Line" || $title == "Triangle" || $title == "Square" || $title == "Pentagon" || $title == "Hexagon" || $title == "Octagon" || $title == "Circle") {
                    $this->smartyview->assign("viewBox", "0 0 100 100");
                }
                $this->smartyview->display("wa/wa.shape.template.tpl");
            } else {
                if ($widget == "app") {
                    $this->smartyview->assign("appid", $appid);
                    $this->smartyview->assign("container_id", uniqid());
                    $this->smartyview->display("wa/wa.application.template.tpl");
                }
            }
        }
    }
    public function wa_dashboard_template()
    {
        $tid = $this->input->post("tid");
        $tab_id = $this->input->post("tab_id");
        if (!file_exists(APPPATH . "views/wa/tpl_json/" . $tid . ".json")) {
            echo APPPATH . "views/wa/tpl_json/" . $tid . ".json" . " not found";
        } else {
            $json = @file_get_contents(APPPATH . "views/wa/tpl_json/" . $tid . ".json");
            $settings = json_decode($json, true);
            $this->load->library("smartyview");
            $this->smartyview->assign("settings", $settings);
            $this->smartyview->assign("tab_id", $tab_id);
            $this->smartyview->display("wa/wa.dashboard.layout.template.tpl");
        }
    }
    public function list_predefined_variables()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $connid = $this->input->post("connid");
        $this->load->library("smartyview");
        $mapData = array();
        $this->_assign_public_default_variables($mapData);
        if (!empty($creatorid) && !empty($connid)) {
            $db = $this->_get_db($creatorid, $connid);
            $parameters = $this->_assign_connection_config_parameters($db, $creatorid, $connid);
            if ($parameters) {
                foreach ($parameters as $key => $value) {
                    $mapData[$key] = $value;
                }
            }
        }
        $this->smartyview->assign("mapData", $mapData);
        $this->smartyview->display("variables.helper.tpl");
    }
    public function save_option()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $appid = $this->input->post("appid");
            $name = $this->input->post("n");
            $value = $this->input->post("v");
            $query = $this->db->select("options")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0));
            } else {
                $result = false;
                if ($name == "title") {
                    $result = $this->db->update("dc_app", array("title" => $value), array("appid" => $appid, "creatorid" => $creatorid));
                } else {
                    if ($name == "desc") {
                        $result = $this->db->update("dc_app", array("desc" => $value), array("appid" => $appid, "creatorid" => $creatorid));
                    } else {
                        $options_str = $query->row()->options;
                        if (!empty($options_str)) {
                            $options = json_decode($options_str, true);
                            if (!isset($options[$name]) || $options[$name] != $value) {
                                $options[$name] = $value;
                                $result = $this->db->update("dc_app", array("options" => json_encode($options)), array("appid" => $appid, "creatorid" => $creatorid));
                            }
                        }
                    }
                }
                if ($result) {
                    echo json_encode(array("status" => 1, "name" => $name));
                } else {
                    echo json_encode(array("status" => 0, "name" => $name));
                }
            }
        }
    }
    public function get_query_fields()
    {
        $connid = $this->input->post("connid");
        $sql = $this->input->post("query");
        $creatorid = $this->session->userdata("login_creatorid");
        $db = $this->_get_db($creatorid, $connid);
        if (!$db) {
            echo json_encode(array("items" => array()));
        } else {
            $query = $db->query($sql);
            $fields = $query->list_fields();
            $result = array();
            foreach ($fields as $field) {
                $result[] = array("id" => $field, "text" => $field);
            }
            echo json_encode(array("items" => $result));
        }
    }
    public function change_category()
    {
        $appid = $this->input->post("appid");
        $category = $this->input->post("category");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("categoryid")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
        $old_category = $query->row()->categoryid;
        if ($category != $old_category) {
            $query = $this->db->select("name,icon")->where(array("creatorid" => $creatorid, "categoryid" => $category))->get("dc_category");
            if (0 < $query->num_rows()) {
                $new_category_info = $query->row_array();
                $this->db->update("dc_app", array("categoryid" => $category), array("creatorid" => $creatorid, "appid" => $appid));
                echo json_encode(array("status" => 1, "name" => $new_category_info["name"], "icon" => $new_category_info["icon"]));
                return NULL;
            }
        }
        echo json_encode(array("status" => 0));
    }
    public function editform()
    {
        $appid = $this->input->get("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $appinfo = $query->row_array();
        $this->load->library("smartyview");
        $this->smartyview->assign("appid", $appid);
        $form_info = json_decode($appinfo["form"], true);
        $this->smartyview->assign("form_option_name", $form_info["name"]);
        if (isset($form_info["css"])) {
            $this->smartyview->assign("form_option_css", $form_info["css"]);
        }
        $this->smartyview->assign("form_builder_mode", isset($form_info["form_builder_mode"]) && !empty($form_info["form_builder_mode"]) ? $form_info["form_builder_mode"] : "design");
        $this->smartyview->assign("form_option_loadingscript", $form_info["loadingscript"]);
        $this->smartyview->assign("form_option_display", $form_info["display"]);
        $this->smartyview->assign("form_option_autoload", isset($form_info["autoload"]) ? $form_info["autoload"] : 1);
        $this->smartyview->assign("appinfo", $appinfo);
        $this->smartyview->assign("builderboxstyle", "box box-solid box-primary");
        $this->smartyview->assign("onlyform", true);
        $this->smartyview->display("appbuilder/formbuilder.standalone.tpl");
    }
    public function get_series_page()
    {
        $appid = $this->input->post("appid");
        $type = $this->input->post("type");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->library("smartyview");
        if (empty($appid) || empty($creatorid)) {
            $this->_display_series_page(false, $type);
        } else {
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid))->order_by("date", "desc")->get("dc_app_log");
            if ($query->num_rows() == 0) {
                $this->_display_series_page(false, $type);
            } else {
                $sql = $query->row()->value;
                $query = $this->db->select("format, connid, options")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
                $connid = $query->row()->connid;
                $format = $query->row()->format;
                $db = $this->_get_db($creatorid, $connid);
                if (!$db) {
                    $this->_display_series_page($format, $type);
                } else {
                    $options_str = $query->row()->options;
                    if (!$this->_is_writable_script($sql)) {
                        $query = $db->query($sql);
                        $fields = $query->field_data();
                        $this->smartyview->assign("fields", $fields);
                    }
                    $options = json_decode($options_str, true);
                    if (isset($options["series_options"])) {
                        $series_options = $options["series_options"];
                        $this->smartyview->assign("series", $series_options);
                    }
                    $this->_display_series_page($format, $type);
                }
            }
        }
    }
    public function _display_series_page($format, $type)
    {
        if ($format == "combinedbarlinechart" || $type == "barline") {
            $this->smartyview->display("appbuilder/options/series.content.chart.tpl");
        } else {
            $this->smartyview->display("appbuilder/options/series.content.tpl");
        }
    }
    public function save_theme_template()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied!"));
        } else {
            $theme = $this->input->post("theme");
            if (empty($theme)) {
                echo json_encode(array("status" => 0, "message" => "Please input a name for the theme template!"));
            } else {
                $creatorid = $this->session->userdata("login_creatorid");
                $options = $this->input->post("options");
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "name" => $theme, "type" => "theme"))->get("dc_user_options");
                if ($query->num_rows() == 0) {
                    $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => $theme, "type" => "theme", "value" => json_encode($options)));
                    echo json_encode(array("status" => 1, "created" => 1, "theme" => $theme));
                } else {
                    $this->db->update("dc_user_options", array("value" => json_encode($options)), array("creatorid" => $creatorid, "name" => $theme, "type" => "theme"));
                    echo json_encode(array("status" => 1, "created" => 0, "theme" => $theme));
                }
            }
        }
    }
    public function remove_theme()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied!"));
        } else {
            $theme = $this->input->post("t");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "type" => "theme", "name" => $theme))->get("dc_user_options");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "Theme not exists!"));
            } else {
                $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "type" => "theme", "name" => $theme));
                echo json_encode(array("status" => 1, "message" => "Theme removed!"));
            }
        }
    }
    public function get_theme()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied!"));
        } else {
            $theme = $this->input->post("theme");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "type" => "theme", "name" => $theme))->get("dc_user_options");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "Theme not exists!"));
            } else {
                $options = $query->row()->value;
                echo json_encode(array("status" => 1, "options" => json_decode($options, true)));
            }
        }
    }
    public function _get_table_share_url_key()
    {
        return strtolower(md5(uniqid("", true)));
    }
    public function _get_table_share_url($key = false)
    {
        $embedcode = $key;
        if (!$key) {
            $embedcode = $this->_get_table_share_url_key();
        }
        $this->load->helper("url");
        $base_url = base_url();
        return $base_url . "share/table/" . $embedcode;
    }
    public function gen_table_share_url()
    {
        $appid = $this->input->post("appid");
        $key = $this->_get_table_share_url_key();
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid, "format" => "tableeditor"))->get("dc_app");
        if ($query->num_rows() == 0) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $connid = $query->row()->connid;
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare"))->get("dc_app_options");
            if (0 < $query->num_rows()) {
                $value = json_decode($query->row()->value, true);
                $value["key"] = $key;
                $this->db->update("dc_app_options", array("value" => json_encode($value), "key" => $key), array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare"));
            } else {
                $value = array("key" => $key);
                $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare", "key" => $key, "value" => json_encode($value)));
            }
            $url = $this->_get_table_share_url($key);
            echo json_encode(array("status" => 1, "url" => $url, "key" => $key));
        }
    }
    public function save_table_share_settings()
    {
        $appid = $this->input->post("share_appid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($appid) || empty($creatorid)) {
            echo json_encode(array("status" => 0, "message" => "1001: Permission Denied!"));
        } else {
            $query = $this->db->select("connid")->where(array("creatorid" => $creatorid, "appid" => $appid, "format" => "tableeditor"))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "1002: Permission Denied!"));
            } else {
                $connid = $query->row()->connid;
                $fields = $this->input->post("fields");
                $features = $this->input->post("features");
                $filter = $this->input->post("filter");
                $share_key = $this->input->post("share_key");
                $enable = $this->input->post("enable_table_share");
                $options = array();
                $options["key"] = $share_key;
                $options["enable"] = $enable && $enable == "1" ? 1 : 0;
                if (!empty($filter)) {
                    $options["filter"] = $filter;
                }
                if (!empty($features)) {
                    $options["features"] = $features;
                }
                if (!empty($fields)) {
                    $options["fields"] = $fields;
                }
                $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare"))->get("dc_app_options");
                if ($query->num_rows() == 0) {
                    $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare", "key" => $share_key, "value" => json_encode($options)));
                } else {
                    $this->db->update("dc_app_options", array("value" => json_encode($options)), array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare"));
                }
                echo json_encode(array("status" => 1, "message" => "Table share options saved!"));
            }
        }
    }
    public function get_table_share_settings()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("connid, script")->where(array("creatorid" => $creatorid, "appid" => $appid, "format" => "tableeditor"))->get("dc_app");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $connid = $query->row()->connid;
        $script = json_decode($query->row()->script, true);
        if (is_string($script["tablename"])) {
            $tablename = $script["tablename"];
        } else {
            $tablename = $script["tablename"][0];
        }
        $db = $this->_get_db($creatorid, $connid);
        $this->load->library("smartyview");
        $fields = list_fields($db, $tablename);
        $this->smartyview->assign("all_fields", $fields);
        $query = $this->db->select("key")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "filter"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $saved_filters = $query->result_array();
            $this->smartyview->assign("saved_filters", $saved_filters);
        }
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "type" => "tableshare"))->get("dc_app_options");
        if (0 < $query->num_rows()) {
            $table_share = json_decode($query->row()->value, true);
            $key = $table_share["key"];
            $url = $this->_get_table_share_url($key);
            $this->smartyview->assign("table_share_url", $url);
            $this->smartyview->assign("table_share", $table_share);
            $this->smartyview->assign("share_key", $key);
            $this->smartyview->assign("enable", isset($table_share["enable"]) ? $table_share["enable"] : 0);
            if (isset($table_share["filter"])) {
                $this->smartyview->assign("filter", $table_share["filter"]);
            }
            if (isset($table_share["features"])) {
                $this->smartyview->assign("features", $table_share["features"]);
            }
            if (isset($table_share["fields"])) {
                $this->smartyview->assign("fields", $table_share["fields"]);
            }
        } else {
            $key = $this->_get_table_share_url_key();
            $url = $this->_get_table_share_url($key);
            $this->smartyview->assign("table_share_url", $url);
            $this->smartyview->assign("share_key", $key);
        }
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->display("runtime/app.tableeditor.dialog.share.content.tpl");
    }
    public function get_field_settings()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($appid) || empty($creatorid)) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        }
    }
    public function get_all_field_formats()
    {
        $appid = $this->input->get_post("appid");
        $query = $this->db->select("key,value")->where(array("appid" => $appid, "type" => "condition_format"))->get("dc_app_options");
        if ($query->num_rows() == 0) {
            echo json_encode(array("status" => 0));
        } else {
            $result = $query->result_array();
            $formats = array();
            foreach ($result as $row) {
                $field = $row["key"];
                $formats[$field] = json_decode($row["value"], true);
            }
            echo json_encode(array("status" => 1, "formats" => $formats));
        }
    }
    public function get_field_format()
    {
        $appid = $this->input->post("appid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($appid) || empty($creatorid)) {
            return NULL;
        }
        $field = $this->input->post("field");
        $has_default = $this->input->post("nodefault");
        if (empty($has_default)) {
            $has_default = true;
        } else {
            $has_default = false;
        }
        $this->load->library("smartyview");
        $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "key" => $field, "appid" => $appid, "type" => "condition_format"))->get("dc_app_options");
        if ($query->num_rows() == 1) {
            $settings = json_decode($query->row()->value, true);
            $bgcolor = $settings["bgcolor"];
            $textcolor = $settings["textcolor"];
            $bold = $settings["bold"];
            $underline = $settings["underline"];
            $italic = $settings["italic"];
            $align = $settings["align"];
            $formats = $settings["formats"];
            $this->smartyview->assign("bgcolor", $bgcolor);
            $this->smartyview->assign("textcolor", $textcolor);
            $this->smartyview->assign("bold", $bold);
            $this->smartyview->assign("underline", $underline);
            $this->smartyview->assign("italic", $italic);
            $this->smartyview->assign("align", $align);
            $this->smartyview->assign("formats", $formats);
        }
        $this->smartyview->assign("field", $field);
        $this->smartyview->assign("hasdefault", $has_default);
        $this->smartyview->display("runtime/app.tableeditor.dialog.conditionformat.content.tpl");
    }
    public function save_field_format()
    {
        $appid = $this->input->post("appid");
        $bgcolor = $this->input->post("bgcolor");
        $textcolor = $this->input->post("textcolor");
        $bold = $this->input->post("bold");
        $underline = $this->input->post("underline");
        $italic = $this->input->post("italic");
        $align = $this->input->post("align");
        $formats = $this->input->post("formats");
        $field = $this->input->post("field");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($appid) || empty($field)) {
            echo json_encode(array("status" => 0, "error" => "10001"));
        } else {
            $query = $this->db->select("connid")->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "error" => 10002));
            } else {
                $connid = $query->row()->connid;
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "key" => $field, "appid" => $appid, "connid" => $connid, "type" => "condition_format"))->get("dc_app_options");
                $format = array("bgcolor" => $bgcolor, "textcolor" => $textcolor, "bold" => $bold, "underline" => $underline, "italic" => $italic, "align" => $align, "formats" => $formats);
                if ($query->num_rows() == 0) {
                    $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => $connid, "appid" => $appid, "key" => $field, "type" => "condition_format", "value" => json_encode($format)));
                } else {
                    $this->db->update("dc_app_options", array("value" => json_encode($format)), array("creatorid" => $creatorid, "key" => $field, "appid" => $appid, "connid" => $connid, "type" => "condition_format"));
                }
                echo json_encode(array("status" => 1, "appid" => $appid, "connid" => $connid, "field" => $field));
            }
        }
    }
    public function save_table_field_alias()
    {
        $appid = $this->input->post("appid");
        $connid = $this->input->post("connid");
        $creatorid = $this->session->userdata("login_creatorid");
        $tablename = $this->input->post("tbl");
        $field = $this->input->post("field");
        $name = $this->input->post("name");
        $desc = $this->input->post("desc");
        if (empty($appid) || empty($connid) || empty($creatorid) || empty($tablename) || empty($field) || empty($name)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $tablename, "type" => "table_field_alias"))->get("dc_app_options");
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $tablename, "type" => "table_field_alias", "value" => json_encode(array($field => array("name" => $name, "desc" => $desc)))));
            } else {
                $value = json_decode($query->row()->value, true);
                $value[$field] = array("name" => $name, "desc" => $desc);
                $this->db->update("dc_app_options", array("value" => json_encode($value)), array("creatorid" => $creatorid, "appid" => $appid, "connid" => $connid, "key" => $tablename, "type" => "table_field_alias"));
            }
            echo json_encode(array("status" => 1));
        }
    }
    public function save_relative_apps()
    {
        $appid = $this->input->post("appid");
        $apps = $this->input->post("apps");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "Application not found in your account"));
            } else {
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "appid" => $appid, "key" => "relative_apps"))->get("dc_app_options");
                if (empty($apps) && 0 < $query->num_rows()) {
                    $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "connid" => 0, "appid" => $appid, "key" => "relative_apps", "type" => "json"));
                    echo json_encode(array("status" => 1, "message" => "Updated"));
                } else {
                    if (is_string($apps)) {
                        $apps = array($apps);
                    }
                    if ($query->num_rows() == 0) {
                        $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => 0, "appid" => $appid, "key" => "relative_apps", "type" => "json", "value" => json_encode($apps)));
                    } else {
                        $this->db->update("dc_app_options", array("value" => json_encode($apps)), array("creatorid" => $creatorid, "connid" => 0, "appid" => $appid, "key" => "relative_apps", "type" => "json"));
                    }
                    echo json_encode(array("status" => 1, "message" => "Updated"));
                }
            }
        }
    }
    /**
     * 取得应用的编辑历史
     */
    public function get_app_revision()
    {
        if (!$this->_is_admin_or_developer()) {
            return NULL;
        }
        $appid = $this->input->post("appid");
        $query = $this->db->select("version, version_desc, userid, date")->where("appid", $appid)->order_by("date", "desc")->get("dc_app_version");
        $result = $query->result_array();
        require APPPATH . "libraries" . DIRECTORY_SEPARATOR . "time-ago" . DIRECTORY_SEPARATOR . "TimeAgo.php";
        $versions = array();
        $usernames = array();
        $timeAgo = new TimeAgo();
        foreach ($result as $row) {
            $version = $row;
            if (!isset($usernames[$row["userid"]])) {
                $query = $this->db->select("name")->where("userid", $row["userid"])->get("dc_user");
                $usernames[$row["userid"]] = $query->row()->name;
            }
            $version["username"] = $usernames[$row["userid"]];
            $version["date_string"] = $timeAgo->inWords($row["date"]);
            $versions[] = $version;
        }
        $this->load->library("smartyview");
        $this->smartyview->assign("appid", $appid);
        $this->smartyview->assign("app_versions", $versions);
        $this->smartyview->display("appbuilder/inc.app.history.tpl");
    }
    /**
     * 将应用revert到特定的版本。
     *
     * @param $appid
     * @param $version
     */
    public function _revert_app_version($appid, $version)
    {
        if (empty($appid) || empty($version)) {
            return false;
        }
        $query = $this->db->where(array("appid" => $appid, "version" => $version))->get("dc_app_version");
        if ($query->num_rows() == 0) {
            return false;
        }
        $app_version_info = $query->row_array();
        $this->_save_as_app_version($appid, "Auto save revision before reverting to " . $version);
        $this->db->update("dc_app", array("connid" => $app_version_info["connid"], "creatorid" => $app_version_info["creatorid"], "type" => $app_version_info["type"], "name" => $app_version_info["name"], "title" => $app_version_info["title"], "desc" => $app_version_info["desc"], "categoryid" => $app_version_info["categoryid"], "form" => $app_version_info["form"], "form_org" => $app_version_info["form_org"], "script" => $app_version_info["script"], "script_org" => $app_version_info["script_org"], "scripttype" => $app_version_info["scripttype"], "confirm" => $app_version_info["confirm"], "format" => $app_version_info["format"], "options" => $app_version_info["options"]), array("appid" => $appid));
        return true;
    }
    /**
     * 将应用的当前数据保存为新的版本.
     */
    public function _save_as_app_version($appid, $version_desc)
    {
        if (empty($appid)) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        $query = $this->db->where(array("appid" => $appid, "creatorid" => $creatorid))->get("dc_app");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $app_info = $query->row_array();
        $query = $this->db->select_max("version", "max_version")->where("appid", $appid)->get("dc_app_version");
        $version = 1;
        if (0 < $query->num_rows()) {
            $version = $query->row()->max_version;
            if (!is_numeric($version)) {
                $version = 1;
            }
        }
        $max_app_revision = $this->config->item("max_app_revision");
        if (!empty($max_app_revision) && is_numeric($max_app_revision) && 0 < $max_app_revision) {
            $query = $this->db->query("select count(1) as num from dc_app_version where appid = ?", array($appid));
            $num_version = 0;
            if ($query->num_rows() == 1) {
                $num_version = $query->row()->num;
            }
            if ($max_app_revision < $num_version) {
                $query = $this->db->select_min("version", "min_version")->where("appid", $appid)->get("dc_app_version");
                $min_version = $query->row()->min_version;
                $this->db->delete("dc_app_version", array("appid" => $appid, "version" => $min_version));
            }
        }
        $this->db->insert("dc_app_version", array("appid" => $appid, "version" => $version + 1, "version_desc" => $version_desc, "connid" => $app_info["connid"], "creatorid" => $app_info["creatorid"], "type" => $app_info["type"], "name" => $app_info["name"], "title" => $app_info["title"], "desc" => $app_info["desc"], "categoryid" => $app_info["categoryid"], "form" => $app_info["form"], "form_org" => $app_info["form_org"], "script" => $app_info["script"], "script_org" => $app_info["script_org"], "scripttype" => $app_info["scripttype"], "confirm" => $app_info["confirm"], "format" => $app_info["format"], "options" => $app_info["options"], "userid" => $userid, "ip" => $this->input->ip_address(), "date" => time()));
    }
    public function apply_version()
    {
        $appid = $this->input->post("appid");
        $version = $this->input->post("version");
        $result = $this->_revert_app_version($appid, $version);
        echo json_encode(array("success" => $result ? 1 : 0, "appid" => $appid));
    }
    public function remove_version()
    {
        $appid = $this->input->post("appid");
        $version = $this->input->post("version");
        $this->db->delete("dc_app_version", array("appid" => $appid, "version" => $version));
        echo json_encode(array("success" => 1));
    }
    public function save_status()
    {
        $appid = $this->input->post("appid");
        $status = $this->input->post("status");
        if ($status != "draft" && $status != "publish") {
            echo json_encode(array("result" => "fail"));
        } else {
            $this->db->update("dc_app", array("status" => $status), array("appid" => $appid));
            echo json_encode(array("result" => "ok"));
        }
    }
    public function freeboard()
    {
        $this->load->library("smartyview");
        $appid = $this->input->get_post("appid");
        if (!empty($appid)) {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("appid,name")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
            $this->smartyview->assign("appid", $appid);
            $this->smartyview->assign("appname", $query->row()->name);
        }
        $this->smartyview->display("freeboard/index.tpl");
    }
    public function save_databoard_name()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $appid = $this->input->post("appid");
        $name = $this->input->post("name");
        if (!empty($appid)) {
            $this->db->update("dc_app", array("name" => $name), array("creatorid" => $creatorid, "appid" => $appid));
            echo json_encode(array("result" => "ok", "appid" => $appid, "name" => $name));
        } else {
            echo json_encode(array("result" => "error", "message" => "Please save the data board first."));
        }
    }
    public function save_freeboard()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $boardid = $this->input->post("appid");
        $board_data = $this->input->post("json");
        $name = $this->input->post("name");
        if (empty($boardid)) {
            $this->db->insert("dc_app", array("connid" => 0, "creatorid" => $creatorid, "type" => "freeboard", "name" => $name, "title" => $name, "desc" => 0, "categoryid" => 0, "script" => $board_data, "format" => "freeboard", "status" => "publish", "createdate" => time()));
            $boardid = $this->db->insert_id();
        } else {
            $this->db->update("dc_app", array("script" => $board_data, "createdate" => time()), array("creatorid" => $creatorid, "appid" => $boardid));
        }
        echo json_encode(array("result" => "ok", "appid" => $boardid));
    }
}

?>