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
class Analytize extends BaseController
{
    public function index()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $login_name = $this->session->userdata("login_username");
        $connid = $this->session->userdata("_default_connid_");
        $editor_theme = $this->_get_ace_editor_theme($creatorid);
        $conns = $this->_get_simple_connections($creatorid);
        $this->load->library("smartyview");
        $this->smartyview->assign("conns", $conns);
        $this->smartyview->assign("connid", $connid);
        $this->smartyview->assign("login_username", $login_name);
        $this->smartyview->assign("ace_editor_theme", $editor_theme);
        $this->smartyview->display("openkit/openkit.analytics.tpl");
    }
    public function create()
    {
        $this->index();
    }
    public function copy()
    {
        $userid = $this->session->userdata("login_userid");
        $creatorid = $this->session->userdata("login_creatorid");
        $qid = $this->input->get_post("qid");
        $query = $this->db->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
        if ($query->num_rows() == 0) {
            echo json_encode(array("result" => "fail"));
        } else {
            $query_info = $query->row_array();
            $qid = uniqid();
            $this->db->insert("ok_queries", array("qid" => $qid, "icon" => $query_info["icon"], "name" => "Copy of " . $query_info["name"], "desc" => $query_info["desc"], "query" => $query_info["query"], "display" => $query_info["display"], "options" => $query_info["options"], "creatorid" => $creatorid, "userid" => $userid, "connid" => $query_info["connid"], "rows" => 0, "cost_time" => 0, "_created_at" => time(), "_updated_at" => time()));
            echo json_encode(array("result" => "ok", "qid" => $qid));
        }
    }
    public function edit()
    {
        $this->load->library("smartyview");
        $qid = $this->input->get_post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $login_name = $this->session->userdata("login_username");
        $connid = $this->session->userdata("_default_connid_");
        $conns = $this->_get_simple_connections($creatorid);
        $editor_theme = $this->_get_ace_editor_theme($creatorid);
        $query = $this->db->select("tag")->where(array("creatorid" => $creatorid, "qid" => $qid))->get("oK_tags");
        if (0 < $query->num_rows()) {
            $tags = array();
            foreach ($query->result_array() as $row) {
                if (!in_array($row["tag"], $tags)) {
                    $tags[] = $row["tag"];
                }
            }
            $this->smartyview->assign("tags", implode(",", $tags));
        }
        $query = $this->db->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
        $query_info = $query->row_array();
        $this->smartyview->assign("conns", $conns);
        $this->smartyview->assign("connid", $query_info["connid"]);
        $this->smartyview->assign("qid", $qid);
        $this->smartyview->assign("query_info", $query_info);
        $this->smartyview->assign("display_type", $query_info["display"]);
        $this->smartyview->assign("login_username", $login_name);
        $this->smartyview->assign("ace_editor_theme", $editor_theme);
        $this->smartyview->display("openkit/openkit.analytics.tpl");
    }
    /**
     * display the query logs revision.
     */
    public function logs()
    {
        $qid = $this->input->post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
    }
    /**
     * display the chart options based on the chart type
     */
    public function settings()
    {
        $qid = $this->input->post("qid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "qid" => $qid))->get("ok_queries");
        $this->load->library("smartyview");
        $this->smartyview->display("openkit/openkit.analytize.settings.tpl");
    }
}

?>