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
class Dashboard extends BaseController
{
    protected static $widgets = array();
    public function index()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $SID = $this->input->get_post("SID");
        if (!empty($SID)) {
            $this->renderStandalone($SID);
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $permission = $this->session->userdata("login_permission");
            $email_not_activation = $this->session->userdata("email_not_activation");
            $this->session->unset_userdata("appbuilder_appid");
            $categories = $this->_get_categories($creatorid);
            $this->smartyview->assign("categories", $categories);
            $categories_by_id = array();
            foreach ($categories as $category) {
                $categories_by_id[$category["categoryid"]] = $category;
            }
            $this->smartyview->assign("categories_by_id", $categories_by_id);
            $curFolder = $this->session->userdata("select_app_folder");
            if (empty($curFolder) || $curFolder == "*") {
                $curFolder = false;
            } else {
                $this->smartyview->assign("login_select_app_folder", $curFolder);
            }
            if ($permission == 9) {
                $apps = $this->_get_user_apps($creatorid, $this->session->userdata("login_userid"));
                $query = $this->db->query("select value from dc_user_options where creatorid=? and name=?", array($creatorid, "userwelcome"));
                if (0 < $query->num_rows()) {
                    $userwelcome_tpl = $query->row()->value;
                    if ($userwelcome_tpl == "system.userwelcome") {
                        $userwelcome_tpl = $this->_get_user_template_code($creatorid, "system.userwelcome");
                    }
                    $this->smartyview->assign("userwelcome", $userwelcome_tpl);
                }
                $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "name" => "welcome_appid"))->get("dc_user_options");
                if (0 < $query->num_rows()) {
                    $welcome_appid = $query->row()->value;
                    if ($welcome_appid != 0) {
                        $query = $this->db->select("appid, name")->where(array("appid" => $welcome_appid, "creatorid" => $creatorid))->get("dc_app");
                        if ($query->num_rows() == 1) {
                            $this->smartyview->assign("welcome_app", $welcome_appid);
                            $this->smartyview->assign("welcome_app_name", $query->row()->name);
                        }
                    }
                }
            } else {
                $apps = $this->_get_apps($creatorid, $curFolder);
            }
            $this->smartyview->assign("apps", $apps);
            $conns = $this->_get_connections($creatorid, true);
            $this->smartyview->assign("conns", $conns);
            $self_host = $this->config->item("self_host");
            if (!$self_host && $conns) {
                $no_connection_created = true;
                foreach ($conns as $conn) {
                    if ($conn["hostname"] != "127.0.0.1" && $conn["hostname"] != "localhost") {
                        $no_connection_created = false;
                        break;
                    }
                }
                $this->smartyview->assign("no_connection_created", $no_connection_created);
            }
            $default_connd = $this->session->userdata("_default_connid_");
            $this->smartyview->assign("default_connid", $default_connd);
            if ($default_connd) {
                $db = @$this->_get_db($creatorid, $default_connd);
                if (!$db) {
                    $this->smartyview->assign("connection_unavailable", true);
                }
            }
            $this->smartyview->assign("enable_createdatabase", $this->config->item("enable_createdatabase"));
            if ($permission == 0) {
                $accounts = $this->_get_accounts($creatorid);
                $this->smartyview->assign("accounts", $accounts);
                $usergroups = $this->_get_usergroups($creatorid);
                $this->smartyview->assign("usergroups", $usergroups);
            }
            $this->smartyview->assign("login_permission", $permission);
            $is_expired = $this->_check_and_assigned_expired($creatorid);
            if ($is_expired) {
                $login_email = $this->session->userdata("login_email");
                $this->smartyview->assign("login_email", $login_email);
                $this->smartyview->assign("account_expired", true);
                $this->session->set_userdata("_EXPIRED_", true);
            } else {
                $this->session->unset_userdata("_EXPIRED_");
            }
            if ($email_not_activation) {
                $this->smartyview->assign("email_not_activation", true);
            }
            if ($this->config->item("self_host")) {
                $this->smartyview->assign("self_host", true);
            }
            $login_email = $this->session->userdata("login_email");
            if (!empty($login_email)) {
                $this->smartyview->assign("login_email", $login_email);
            }
            $enable_marketplace = $this->config->item("enable_marketplace");
            if ($enable_marketplace) {
                $this->smartyview->assign("enable_marketplace", $enable_marketplace);
            }
            $use_openkit = $this->config->item("use_openkit");
            if (!empty($use_openkit) && $use_openkit) {
                $query = $this->db->where("creatorid", $creatorid)->get("ok_queries");
                $queries = $query->result_array();
                foreach ($queries as &$one_query) {
                    $query = $this->db->select("tag")->where(array("creatorid" => $creatorid, "qid" => $one_query["qid"]))->get("ok_tags");
                    if (0 < $query->num_rows()) {
                        $tags = array();
                        foreach ($query->result_array() as $row) {
                            if (!in_array($row["tag"], $tags)) {
                                $tags[] = $row["tag"];
                            }
                        }
                        $one_query["tags"] = $tags;
                    }
                }
                $this->smartyview->assign("queries", $queries);
                $query = $this->db->where("creatorid", $creatorid)->get("ok_dashboards");
                $dashboards = $query->result_array();
                $this->smartyview->assign("queryboards", $dashboards);
                $this->smartyview->assign("use_openkit", true);
            }
            $this->smartyview->display("new/main.dashboard.tpl");
        }
    }
    public function _assignDashboadMenus()
    {
        $query = $this->db->query("select distinct menu from dc_user_dashboard");
        $menus = array();
        if ($query && 0 < $query->num_rows()) {
            $result = $query->result_array();
            foreach ($result as $row) {
                $menus[] = $row["menu"];
            }
        } else {
            $menus[] = "Dashboard";
        }
        $this->smartyview->assign("dmenus", $menus);
    }
    public function getDefaultLayout()
    {
        $defaultLayout = $this->_getLayoutForUser("", 1);
        if (empty($defaultLayout)) {
            $defaultLayout = "[]";
        }
        return $defaultLayout;
    }
    public function renderStandalone($SID)
    {
        $query = $this->db->query("select iddashboard from dc_user_dashboard where embedcode = ?", array($SID));
        if ($query->num_rows() == 1) {
            $this->smartyview->assign("SID", $SID);
            $this->smartyview->assign("numconn", 1);
            $dashboardId = $query->row()->iddashboard;
            $this->smartyview->assign("availableWidgets", json_encode($this->GetWidgetsList()));
            $this->smartyview->assign("availableLayouts", $this->getAvailableLayouts());
            $this->smartyview->assign("dashboardId", $dashboardId);
            $this->smartyview->assign("dashboardLayout", $this->getLayout($dashboardId));
            $this->_assignDashboadMenus();
            $this->smartyview->assign("embed", true);
            $this->smartyview->assign("standalone", true);
            $parameters = $this->input->get();
            unset($parameters["OBJID"]);
            unset($parameters["module"]);
            unset($parameters["action"]);
            unset($parameters["SID"]);
            $this->smartyview->assign("parameters", $parameters);
            $this->smartyview->display("dashboard/index.tpl");
        }
    }
    public function run()
    {
        $this->load->library("smartyview");
        $widgets = $this->GetWidgetsList();
        $creatorid = $this->session->userdata("login_creatorid");
        $numconn = $this->_get_connection_count($creatorid);
        $this->smartyview->assign("numconn", $numconn);
        $this->smartyview->assign("availableWidgets", json_encode($widgets));
        $this->smartyview->assign("availableLayouts", $this->getAvailableLayouts());
        $dashboardId = $this->input->get_post("idDashboard");
        $this->smartyview->assign("dashboardId", $dashboardId);
        $this->smartyview->assign("dashboardLayout", $this->getLayout($dashboardId));
        $this->_assignDashboadMenus();
        $login_permission = $this->session->userdata("login_permission");
        $this->smartyview->assign("login_permission", $login_permission);
        $defaultdashboard = $this->_get_default_dashboardid();
        if ($defaultdashboard) {
            $this->smartyview->assign("defaultdashboardId", $defaultdashboard);
        }
        $this->smartyview->display("dashboard/index.tpl");
    }
    public function getDashboardLayout()
    {
        $idDashboard = $this->input->get_post("idDashboard");
        $this->load->library("smartyview");
        $layout = $this->getLayout($idDashboard);
        echo $layout;
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
    public function createNewDashboard()
    {
        $nextId = $this->_create_dashboardid();
        $name = urldecode($this->input->get_post("name"));
        $type = urldecode($this->input->get_post("type"));
        $menu = urldecode($this->input->get_post("menu"));
        $layout = "{}";
        if ($type == "default") {
            $layout = $this->getDefaultLayout();
        }
        $this->db->insert("dc_user_dashboard", array("iddashboard" => $nextId, "name" => $name, "menu" => $menu, "layout" => $layout));
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode($nextId));
    }
    public function _getLayoutForUser($idDashboard)
    {
        $query = $this->db->query("select name, menu, layout from dc_user_dashboard where iddashboard=? limit 1", array($idDashboard));
        $return = $query->row_array();
        if (empty($return)) {
            return false;
        }
        $this->smartyview->assign("name", $return["name"]);
        $this->smartyview->assign("dashboardMenu", $return["menu"]);
        return $return["layout"];
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
    public function appbox()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $folder = $this->input->post("f");
        if ($folder == "*") {
            $this->session->unset_userdata("login_select_app_folder");
            $folder = false;
        } else {
            $this->session->set_userdata("login_select_app_folder", $folder);
        }
        $apps = $this->_get_apps($creatorid, $folder);
        $this->smartyview->assign("apps", $apps);
        $this->smartyview->display("new/box.apps.table.tpl");
    }
    /**
     * Saves the layout for the current user
     * anonymous = in the session
     * authenticated user = in the DB
     */
    public function saveLayout()
    {
        $layout = $this->input->get_post("layout");
        $idDashboard = $this->input->get_post("idDashboard");
        $name = $this->input->get_post("name");
        $menu = $this->input->get_post("menu");
        if (empty($name)) {
            $name = "Untitled Dashboard";
        }
        if (empty($menu)) {
            $menu = "Dashboard";
        }
        $update_array = array("name" => $name, "menu" => $menu);
        if (!empty($layout)) {
            $update_array["layout"] = $layout;
        }
        $query = $this->db->query("select 1 from dc_user_dashboard where  iddashboard = ?", array($idDashboard));
        if (0 < $query->num_rows()) {
            $this->db->update("dc_user_dashboard", $update_array, array("iddashboard" => $idDashboard));
        } else {
            $update_array["iddashboard"] = $idDashboard;
            $this->db->insert("dc_user_dashboard", $update_array);
        }
    }
    /**
     * Adds an widget to the list
     *
     * @param string $widgetCategory
     * @param string $widgetName
     * @param string $controllerName
     * @param string $controllerAction
     * @param array $customParameters
     */
    private function addWidget($widgetCategory, $widgetUniqueId, $widgetName, $controllerName, $controllerAction, $customParameters = array())
    {
        if (!isset(self::$widgets[$widgetCategory])) {
            self::$widgets[$widgetCategory] = array();
        }
        self::$widgets[$widgetCategory][] = array("name" => $widgetName, "uniqueId" => $widgetUniqueId, "parameters" => array("module" => $controllerName, "action" => $controllerAction, "name" => $widgetName) + $customParameters);
    }
    public function GetWidgetsList()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $categories = $this->_get_categories($creatorid);
        $category_by_key = array();
        foreach ($categories as $category) {
            $category_by_key[$category["categoryid"]] = $category["name"];
        }
        $query = $this->db->query("select appid, name, title, categoryid from dc_app where status=? and creatorid = ?", array("publish", $creatorid));
        $result = $query->result_array();
        foreach ($result as $row) {
            $catname = isset($category_by_key[$row["categoryid"]]) ? $category_by_key[$row["categoryid"]] : false;
            if (empty($catname)) {
                $catname = "Widgets";
            }
            $token = md5("w" . $row["appid"] . $this->config->item("jsdingxx"));
            $this->addWidget($catname, "w" . $row["appid"], $row["name"], "App", "index", array("appid" => $row["appid"], "embed" => 1, "token" => $token, "name" => $row["name"]));
        }
        return self::$widgets;
    }
    public function removeDashboard()
    {
        $idDashboard = $this->input->get_post("idDashboard");
        if ($idDashboard != 1) {
            $this->db->delete("dc_user_dashboard", array("iddashboard" => $idDashboard));
        }
    }
    public function getAllDashboards()
    {
        $this->load->library("smartyview");
        $this->_assign_all_dashboards();
        $this->smartyview->display("new/main.left.applist.tpl");
    }
    public function _getAllDashboards()
    {
        $query = $this->db->query("select iddashboard, name, menu, layout from dc_user_dashboard");
        $dashboards = $query->result_array();
        $newdashboards = array();
        foreach ($dashboards as $dashboard) {
            $dashboard["name"] = $dashboard["name"];
            $layout = "[]";
            if (!empty($dashboard["layout"])) {
                $layout = $dashboard["layout"];
            }
            $dashboard["layout"] = $this->decodeLayout($layout);
            $newdashboards[] = $dashboard;
        }
        return $newdashboards;
    }
    public function saveembedcode()
    {
        $appid = $this->input->post("dashboardId");
        $sharestatus = $this->input->post("sharestatus");
        $embedcode = $this->input->post("embedcode");
        $query = $this->db->query("select embedcode from dc_user_dashboard where iddashboard=?", array($appid));
        if ($query && 0 < $query->num_rows()) {
            $changed = false;
            if ($sharestatus == 1) {
                $cur_code = $query->row()->embedcode;
                if ($cur_code != $embedcode) {
                    $this->db->update("dc_user_dashboard", array("embedcode" => $embedcode), array("iddashboard" => $appid));
                }
            } else {
                $this->db->update("dc_user_dashboard", array("embedcode" => NULL), array("iddashboard" => $appid));
            }
            echo 1;
        } else {
            echo 0;
        }
    }
    public function getshareurl()
    {
        $appid = $this->input->post("dashboardId");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select embedcode from dc_user_dashboard where iddashboard=?", array($appid));
        $embedcode = NULL;
        $sharestatus = 1;
        if ($query && 0 < $query->num_rows()) {
            $embedcode = $query->row()->embedcode;
        }
        if (empty($embedcode)) {
            $embedcode = strtoupper(md5(uniqid("", true)));
            $sharestatus = 0;
        }
        $base_url = $this->_get_url_base();
        $direct_link = $base_url . "?module=Embed&SID=" . $embedcode;
        $ret = array("embedcode" => $embedcode, "iframeurl" => $direct_link);
        $ret["embed_iframe"] = "<iframe src=\"" . $direct_link . "\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\" width=\"100%\" height=\"100%\"></iframe>";
        $ret["sharestatus"] = $sharestatus;
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode($ret));
    }
    public function saveLayoutAsDefault()
    {
        $idDashboard = $this->input->get_post("idDashboard");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_update_default_dashboard($idDashboard);
        echo 1;
    }
    public function res()
    {
        if ($this->session->userdata("_request_update_")) {
            return NULL;
        }
        $master = $this->config->item("dbface_master");
        $this->load->library("httpClient", array("host" => $master));
        $clientcode = $this->session->userdata("_CLIENT_CODE_");
        if (empty($clientcode)) {
            $query = $this->db->query("select value from dc_user_options where name=? limit 1", array("clientcode"));
            if ($query->num_rows() == 1) {
                $clientcode = $query->row()->value;
            }
        }
        $query = $this->db->query("select value from dc_user_options where name=? limit 1", array("license_email"));
        $e = "";
        if ($query->num_rows() == 1) {
            $e = $query->row()->value;
        }
        $query = $this->db->query("select value from dc_user_options where name=? limit 1", array("license_code"));
        $code = "";
        if ($query->num_rows() == 1) {
            $code = $query->row()->value;
        }
        $version = $this->config->item("version");
        $buildid = $this->config->item("buildid");
        $url = $this->_get_url_base();
        $login_email = $this->session->userdata("login_email");
        save_parse_object("LicenseCheck", array("clientcode" => $clientcode, "license_email" => $e, "code" => $code, "version" => $version, "buildid" => $buildid, "url" => $url, "login_email" => $login_email));
        $s = implode("|", array($clientcode, $e, $code, $version, $buildid, $url, $login_email));
        $k = md5("jsding" . $s . "1983");
        $result = $this->httpclient->post("/license/check", array("s" => $s, "k" => $k));
        if ($result) {
            $updatemsg = $this->httpclient->getContent();
            if ($updatemsg == md5("jsding.201501289")) {
                $this->db->delete("dc_user_options", array("name" => "license_code"));
                $this->db->delete("dc_user_options", array("name" => "license_email"));
                $this->db->update("dc_user", array("expiredate" => time(), "plan" => "level0"), array("creatorid" => 0));
            } else {
                if ($updatemsg == md5("jsding.201501287")) {
                    $this->db->query("update dc_user set expiredate = regdate + 2592000 where creatorid=0");
                    $this->_update_signature();
                }
            }
        }
        $this->session->set_userdata("_request_update_", true);
    }
    public function update_category_icon()
    {
        $cid = $this->input->post("cid");
        $icon = $this->input->post("icon");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($cid) || empty($icon) || empty($creatorid)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->select("name")->where(array("categoryid" => $cid, "creatorid" => $creatorid))->get("dc_category");
            if (0 < $query->num_rows()) {
                $name = $query->row()->name;
                $this->db->update("dc_category", array("icon" => $icon), array("categoryid" => $cid, "creatorid" => $creatorid));
                echo json_encode(array("status" => 1, "categoryid" => $cid, "icon" => $icon, "name" => $name));
            } else {
                echo json_encode(array("status" => 0));
            }
        }
    }
    public function freeboard()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("freeboard/index.tpl");
    }
}

?>