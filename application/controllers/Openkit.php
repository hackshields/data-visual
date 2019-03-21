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
class Openkit extends BaseController
{
    public function index()
    {
        $login_name = $this->session->userdata("login_username");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->library("smartyview");
        $conns = $this->_get_simple_connections($creatorid);
        $this->smartyview->assign("conns", $conns);
        $connid = $this->session->userdata("_default_connid_");
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("login_username", $login_name);
        $this->smartyview->display("openkit/frame.openkit.tpl");
    }
    /**
     * display database connection list page
     */
    public function connect()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $conns = $this->_get_connections($creatorid);
        $this->load->library("smartyview");
        $this->smartyview->assign("conns", $conns);
        $this->smartyview->display("openkit/openkit.connect.tpl");
    }
    /**
     * display all saved queries page
     */
    public function queries()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where("creatorid", $creatorid)->get("ok_queries");
        $this->load->library("smartyview");
        $this->smartyview->assign("queries", $query->result_array());
        $this->smartyview->display("openkit/openkit.queries.tpl");
    }
    /**
     * display saved dashboards page.
     */
    public function dashboards()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where("creatorid", $creatorid)->get("ok_dashboards");
        $dashboards = $query->result_array();
        $this->load->library("smartyview");
        $this->smartyview->assign("dashboards", $dashboards);
        $this->smartyview->display("openkit/openkit.dashboards.tpl");
    }
    /**
     * edit existing dashboard
     */
    public function edit_dashboard()
    {
        $this->load->library("smartyview");
        $did = $this->input->get("did");
        $this->smartyview->assign("dashboardId", $did);
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "did" => $did))->get("ok_dashboards");
        $dashboardInfo = $query->row_array();
        if (isset($dashboardInfo["filter"]) && !empty($dashboardInfo["filter"])) {
            $this->smartyview->assign("enable_filter", true);
        } else {
            $this->smartyview->assign("enable_filter", false);
        }
        $layout = json_decode($dashboardInfo["layout"], true);
        $tabs = $layout["tabs"];
        $this->smartyview->assign("tabs", $tabs);
        $this->smartyview->assign("dashboardInfo", $dashboardInfo);
        $this->smartyview->display("openkit/openkit.dashboard.create.tpl");
    }
    public function load_dashboard()
    {
        $did = $this->input->post("did");
        $creatorid = $this->session->userdata("login_creatorid");
        $tab = intval($this->input->post("tab"));
        $query = $this->db->select("layout")->where(array("creatorid" => $creatorid, "did" => $did))->get("ok_dashboards");
        $layout = json_decode($query->row()->layout, true);
        if (is_array($layout) && isset($layout["layouts"]) && isset($layout["layouts"][$tab])) {
            echo json_encode($layout["layouts"][$tab]);
        } else {
            echo json_encode(array());
        }
    }
    public function save_dashboard()
    {
        $dashboardId = $this->input->post("did");
        $layout = $this->input->post("layout");
        $tabs = $this->input->post("tabs");
        $tab = intval($this->input->post("tab"));
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        $query = $this->db->select("layout")->where(array("creatorid" => $creatorid, "did" => $dashboardId))->get("ok_dashboards");
        if ($query->num_rows() == 0) {
            $result_layout = array("tabs" => $tabs, "layouts" => array($layout));
            $this->db->insert("ok_dashboards", array("did" => $dashboardId, "creatorid" => $creatorid, "userid" => $userid, "cover" => "", "layout" => json_encode($result_layout), "_created_at" => time(), "_updated_at" => time()));
        } else {
            $result_layout = json_decode($query->row()->layout, true);
            $result_layout["tabs"] = $tabs;
            $result_layout["layouts"] = isset($result_layout["layouts"]) ? $result_layout["layouts"] : array();
            $result_layout["layouts"] = array_pad($result_layout["layouts"], $tab + 1, array());
            $result_layout["layouts"][$tab] = $layout;
            $this->db->update("ok_dashboards", array("layout" => json_encode($result_layout), "_updated_at" => time()), array("creatorid" => $creatorid, "did" => $dashboardId));
        }
        echo json_encode(array("result" => "ok", "dashboardId" => $dashboardId));
    }
    /**
     * create new dashboard
     */
    public function create_dashboard()
    {
        $dashboardId = uniqid();
        $this->load->library("smartyview");
        $this->smartyview->assign("dashboardId", $dashboardId);
        $this->smartyview->display("openkit/openkit.dashboard.create.tpl");
    }
    public function edit_query_name()
    {
        $qid = $this->input->post("qid");
        $newname = $this->input->post("newname");
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        $connid = $this->session->userdata("_default_connid_");
        if (empty($qid)) {
            $qid = uniqid();
            $this->db->insert("ok_queries", array("qid" => $qid, "icon" => "", "name" => $newname, "desc" => "", "query" => "", "display" => "table", "options" => "", "creatorid" => $creatorid, "userid" => $userid, "connid" => $connid, "rows" => 0, "cost_time" => 0, "_created_at" => time(), "_updated_at" => time()));
        } else {
            $this->db->update("ok_queries", array("name" => $newname, "_updated_at" => time()), array("creatorid" => $creatorid, "qid" => $qid));
        }
        echo json_encode(array("result" => "ok", "qid" => $qid));
    }
    public function save_query()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        $qid = $this->input->post("qid");
        $connid = $this->session->userdata("_default_connid_");
        $apptype = $this->input->post("apptype");
        $content = $this->input->post("content");
        $options = array();
        $option_keys = $this->config->item("app_available_options");
        foreach ($option_keys as $key) {
            $v = $this->input->post($key);
            if ($v != "") {
                $options[$key] = $v;
            }
        }
        $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "connid" => $connid))->get("dc_conn");
        if ($query->num_rows() == 0) {
            echo json_encode(array("result" => "faile"));
            return NULL;
        }
        if (empty($qid)) {
            $qid = uniqid();
            $this->db->insert("ok_queries", array("qid" => $qid, "icon" => "", "name" => "", "desc" => "", "query" => $content, "display" => $apptype, "options" => json_encode($options), "creatorid" => $creatorid, "userid" => $userid, "connid" => $connid, "rows" => 0, "cost_time" => 0, "_created_at" => time(), "_updated_at" => time()));
        } else {
            $this->db->update("ok_queries", array("query" => $content, "display" => $apptype, "options" => json_encode($options), "connid" => $connid, "_updated_at" => time()), array("creatorid" => $creatorid, "qid" => $qid));
        }
        echo json_encode(array("result" => "ok", "qid" => $qid));
    }
    /**
     * display all account and user group table
     */
    public function accounts()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->smartyview->assign("accounts", $this->_get_accounts($creatorid));
        $usergroups = $this->_get_usergroups($creatorid);
        $this->smartyview->assign("usergroups", $usergroups);
        $this->smartyview->display("openkit/openkit.accounts.tpl");
    }
    public function _get_accounts($creatorid)
    {
        $this->load->database();
        $query = $this->db->query("select * from dc_user where creatorid = ?", array($creatorid));
        $subaccounts = $query->result_array();
        if (function_exists("sort_subaccounts")) {
            return sort_subaccounts($subaccounts);
        }
        return $subaccounts;
    }
    public function _get_usergroups($creatorid)
    {
        $query = $this->db->select("groupid,name")->where("creatorid", $creatorid)->get("dc_usergroup");
        $usergroups = $query->result_array();
        foreach ($usergroups as &$usergroup) {
            $groupid = $usergroup["groupid"];
            $query = $this->db->select("userid, email, name")->where(array("creatorid" => $creatorid, "groupid" => $groupid, "permission" => 9))->get("dc_user");
            if (0 < $query->num_rows()) {
                $usergroup["users"] = $query->result_array();
            }
        }
        return $usergroups;
    }
    public function reports()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("openkit/openkit.reports.tpl");
    }
    public function jobs()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->library("smartyview");
        $query = $this->db->where("creatorid", $creatorid)->get("dc_sqlalert");
        $sqlalerts = $query->result_array();
        $this->smartyview->assign("sqlalerts", $sqlalerts);
        $this->smartyview->display("openkit/openkit.alerts.tpl");
    }
    public function plans()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("openkit/openkit.plans.tpl");
    }
    public function list_filters()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_assign_parameters($creatorid);
        $this->_assign_filters($creatorid);
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $this->smartyview->assign("conns", $this->_get_simple_connections($creatorid));
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->select("name");
        $this->db->where("userid", $creatorid);
        $query = $this->db->from("dc_user")->get();
        $username = $query->row()->name;
        $base_url = $this->_make_dbface_url("team/" . $username . "/value/");
        $this->smartyview->assign("parameter_base_url", $base_url);
        $this->smartyview->display("openkit/openkit.list_filters.tpl");
    }
    public function _assign_parameters($creatorid)
    {
        $enable_marketplace = $this->config->item("enable_marketplace");
        if ($enable_marketplace) {
            $this->smartyview->assign("enable_marketplace", $enable_marketplace);
        }
        $query = $this->db->where("creatorid", $creatorid)->get("dc_parameter");
        $result_array = $query->result_array();
        $this->smartyview->assign("parameters", $result_array);
    }
    public function _assign_filters($creatorid)
    {
        $query = $this->db->where("creatorid", $creatorid)->get("dc_filter");
        $result_array = $query->result_array();
        $this->smartyview->assign("filters", $result_array);
    }
    public function add_tag()
    {
        $qid = $this->input->post("qid");
        $tag = $this->input->post("tag");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($qid)) {
            echo json_encode(array("status" => "fail", "message" => "Save this query first"));
        } else {
            $this->db->insert("ok_tags", array("tag" => $tag, "qid" => $qid, "creatorid" => $creatorid, "_created_at" => time(), "_updated_at" => time()));
            echo json_encode(array("result" => "ok"));
        }
    }
    public function remove_tag()
    {
        $qid = $this->input->post("qid");
        $tag = $this->input->post("tag");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($qid)) {
            echo json_encode(array("status" => "fail", "message" => "Save this query first"));
        } else {
            $this->db->delete("ok_tags", array("tag" => $tag, "qid" => $qid, "creatorid" => $creatorid));
            echo json_encode(array("result" => "ok"));
        }
    }
    public function rename_dashboard()
    {
        $name = $this->input->post("name");
        $did = $this->input->post("did");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->update("ok_dashboards", array("name" => $name), array("did" => $did, "creatorid" => $creatorid));
        echo json_encode(array("result" => "ok", "name" => $name));
    }
    public function rename_query()
    {
        $name = $this->input->post("name");
        $qid = $this->input->post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->update("ok_queries", array("name" => $name), array("qid" => $qid, "creatorid" => $creatorid));
        echo json_encode(array("result" => "ok", "name" => $name));
    }
    public function update_star()
    {
        $qid = $this->input->post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("star")->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
        if ($query->num_rows() == 0) {
            echo json_encode(array("result" => "fail"));
        } else {
            $cur_star = $query->row()->star;
            $this->db->update("ok_queries", array("star" => $cur_star == 1 ? 0 : 1), array("creatorid" => $creatorid, "qid" => $qid));
            echo json_encode(array("result" => "ok", "star" => $cur_star == 1 ? 0 : 1));
        }
    }
    public function update_d_star()
    {
        $did = $this->input->post("did");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("star")->where(array("creatorid" => $creatorid, "did" => $did))->get("ok_dashboards");
        if ($query->num_rows() == 0) {
            echo json_encode(array("result" => "fail"));
        } else {
            $cur_star = $query->row()->star;
            $this->db->update("ok_dashboards", array("star" => $cur_star == 1 ? 0 : 1), array("creatorid" => $creatorid, "did" => $did));
            echo json_encode(array("result" => "ok", "star" => $cur_star == 1 ? 0 : 1));
        }
    }
    public function settings()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("openkit/openkit.settings.tpl");
    }
    public function remove_did()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $did = $this->input->post("did");
        $this->db->delete("ok_dashboards", array("creatorid" => $creatorid, "did" => $did));
        echo json_encode(array("result" => "ok"));
    }
    public function query_snippets()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("openkit/social/query_snippets.tpl");
    }
    public function shared_dashboards()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("openkit/social/shared_dashboards.tpl");
    }
    public function enable_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $did = $this->input->post("did");
        $default_filter = json_encode(array("status" => 1));
        $this->db->update("ok_dashboards", array("filter" => $default_filter, "_updated_at" => time()), array("creatorid" => $creatorid, "did" => $did));
        echo json_encode(array("result" => "ok"));
    }
    public function list_queries_json()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("qid, name")->where("creatorid", $creatorid)->get("ok_queries");
        echo json_encode(array("result" => "ok", "data" => $query->result_array()));
    }
    public function remove_query()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $qid = $this->input->post("qid");
        if (empty($creatorid) || empty($qid)) {
            echo "error:";
        } else {
            $this->load->library("smartyview");
            $this->db->delete("ok_queries", array("creatorid" => $creatorid, "qid" => $qid));
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
            $this->smartyview->display("new/box.queries.tpl");
        }
    }
    public function remove_dashboard()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $did = $this->input->post("did");
        if (empty($creatorid) || empty($did)) {
            echo "";
        } else {
            $this->db->delete("ok_dashboards", array("creatorid" => $creatorid, "did" => $did));
            $this->load->library("smartyview");
            $query = $this->db->where("creatorid", $creatorid)->get("ok_dashboards");
            $dashboards = $query->result_array();
            $this->smartyview->assign("queryboards", $dashboards);
            $this->smartyview->display("new/box.queryboards.tpl");
        }
    }
    public function copy_dashboard()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $did = $this->input->post("did");
        if (empty($creatorid) || empty($did)) {
            echo json_encode(array("result" => "fail", "message" => "Permission Denied"));
        } else {
            $query = $this->db->where(array("creatorid" => $creatorid, "did" => $did))->get("ok_dashboards");
            if ($query->num_rows() == 0) {
                echo json_encode(array("result" => "fail", "message" => "Query Dashboard has been removed."));
            } else {
                $org_dashboard = $query->row();
                $dashboardId = uniqid();
                $userid = $this->session->userdata("login_userid");
                $this->db->insert("ok_dashboards", array("did" => $dashboardId, "name" => "Copy of " . $org_dashboard->name, "creatorid" => $creatorid, "userid" => $userid, "filter" => $org_dashboard->filter, "cover" => $org_dashboard->cover, "layout" => $org_dashboard->layout, "_created_at" => time(), "_updated_at" => time()));
                echo json_encode(array("result" => "ok", "did" => $dashboardId));
            }
        }
    }
}

?>