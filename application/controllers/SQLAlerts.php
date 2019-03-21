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
class SQLAlerts extends BaseController
{
    public function create2()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $query = $this->db->select("1")->get("dc_crontab_log");
        if ($query->num_rows() == 0) {
            $this->smartyview->assign("display_cron_settings_info", true);
            $executor_key = $this->config->item("crontab_execution_key");
            $crontab_line = "*/5 * * * * /usr/bin/php " . FCPATH . "index.php" . " cron " . $executor_key;
            $this->smartyview->assign("crontab_line", $crontab_line);
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $connid = $this->session->userdata("_default_connid_");
        $conns = $this->_get_simple_connections($creatorid);
        $this->smartyview->assign("conns", $conns);
        $this->smartyview->assign("selected_connid", $connid);
        $this->smartyview->display("openkit/sqlalerts/box.sqlalerts.tpl");
    }
    public function create()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || !$this->_is_admin_or_developer()) {
            exit;
        }
        $query = $this->db->select("1")->get("dc_crontab_log");
        if ($query->num_rows() == 0) {
            $this->smartyview->assign("display_cron_settings_info", true);
            $executor_key = $this->config->item("crontab_execution_key");
            $crontab_line = "*/5 * * * * /usr/bin/php " . FCPATH . "index.php" . " cron " . $executor_key;
            $this->smartyview->assign("crontab_line", $crontab_line);
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $connid = $this->session->userdata("_default_connid_");
        $conns = $this->_get_simple_connections($creatorid);
        $this->smartyview->assign("conns", $conns);
        $this->smartyview->assign("selected_connid", $connid);
        $this->smartyview->display("sqlalerts/box.sqlalerts.tpl");
    }
    public function save()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $alertid = $this->input->post("alertid");
        $status = $this->input->post("status");
        $name = $this->input->post("name");
        $criteria_type = $this->input->post("criteria_type");
        $criteria_value = $this->input->post("criteria_value");
        $frequency = $this->input->post("frequency");
        $emails = $this->input->post("emails");
        $description = $this->input->post("description");
        $sql = $this->input->post("sql");
        $connid = $this->input->post("connid");
        $action = $this->input->post("action");
        $params = $this->input->post("params");
        if ($alertid == 0) {
            $this->db->insert("dc_sqlalert", array("creatorid" => $creatorid, "connid" => $connid, "name" => $name, "criteriatype" => $criteria_type, "criteriavalue" => $criteria_value, "frequency" => $frequency, "action" => $action, "email" => $emails, "params" => json_encode($params), "description" => $description, "sql" => $sql, "status" => $status, "_created_at" => time(), "_updated_at" => time()));
            $alertid = $this->db->insert_id();
        } else {
            $this->db->update("dc_sqlalert", array("connid" => $connid, "name" => $name, "criteriatype" => $criteria_type, "criteriavalue" => $criteria_value, "frequency" => $frequency, "action" => $action, "email" => $emails, "params" => json_encode($params), "description" => $description, "sql" => $sql, "status" => $status, "_updated_at" => time()), array("creatorid" => $creatorid, "alertid" => $alertid));
        }
        echo json_encode(array("status" => 1, "alertid" => $alertid));
    }
    public function edit()
    {
        $this->load->library("smartyview");
        $id = $this->input->get("id");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "alertid" => $id))->get("dc_sqlalert");
        $alertinfo = $query->row_array();
        $params = json_decode($alertinfo["params"], true);
        $report = isset($params["appid"]) ? $params["appid"] : false;
        if ($report) {
            $alertinfo["report"] = $report;
        }
        $iwu = isset($params["iwu"]) ? $params["iwu"] : false;
        if ($iwu) {
            $alertinfo["iwn"] = $iwu;
        }
        $cloudcode = isset($params["cloudcode"]) ? $params["cloudcode"] : false;
        if ($cloudcode) {
            $alertinfo["cloudcode"] = $cloudcode;
        }
        $this->smartyview->assign("sqlalert", $alertinfo);
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $connid = $this->session->userdata("_default_connid_");
        $conns = $this->_get_simple_connections($creatorid);
        $this->smartyview->assign("conns", $conns);
        $this->smartyview->assign("selected_connid", $connid);
        $this->smartyview->assign("alertid", $alertinfo["alertid"]);
        $this->smartyview->display("sqlalerts/box.sqlalerts.tpl");
    }
    public function update_status()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $alertid = $this->input->post("id");
        $status = $this->input->post("status");
        if (empty($creatorid) || empty($alertid)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->where(array("creatorid" => $creatorid, "alertid" => $alertid))->get("dc_sqlalert");
            if ($query->num_rows() != 1) {
                echo json_encode(array("status" => 0));
            } else {
                $this->db->update("dc_sqlalert", array("status" => $status, "_updated_at" => time()), array("creatorid" => $creatorid, "alertid" => $alertid));
                dbface_log("info", "update SQLalerts status: " . $this->db->last_query());
                echo json_encode(array("status" => 1, "to" => $status));
            }
        }
    }
    public function duplicate()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $alertid = $this->input->post("id");
        if (empty($creatorid) || empty($alertid)) {
            echo json_encode(array("status" => 0));
        } else {
            $query = $this->db->where(array("creatorid" => $creatorid, "alertid" => $alertid))->get("dc_sqlalert");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0));
            } else {
                $row_array = $query->row_array();
                unset($row_array["alertid"]);
                $row_array["_created_at"] = time();
                $row_array["_updated_at"] = time();
                $row_array["name"] = "Copy of " . $row_array["name"];
                $this->db->insert("dc_sqlalert", $row_array);
                $id = $this->db->insert_id();
                echo json_encode(array("status" => 1, "alertid" => $id));
            }
        }
    }
    public function delalert()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $alertid = $this->input->post("id");
        if (empty($creatorid) || empty($alertid)) {
            echo json_encode(array("status" => 0));
        } else {
            $this->db->delete("dc_sqlalert", array("creatorid" => $creatorid, "alertid" => $alertid));
            echo json_encode(array("status" => 1, "alterid" => $alertid));
        }
    }
}

?>