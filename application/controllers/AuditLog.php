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
class AuditLog extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("result" => "Permission Denied"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("cronid")->where("creatorid", $creatorid)->get("dc_crontab");
            $cronlogs = array();
            if (0 < $query->num_rows()) {
                $result = $query->result_array();
                foreach ($result as $row) {
                    $cronid = $row["cronid"];
                    $query = $this->db->where("cronid", $cronid)->limit(1)->order_by("startdate", "desc")->get("dc_crontab_log");
                    $cronlogresult = $query->result_array();
                    $cronlogs = array_merge($cronlogs, $cronlogresult);
                }
            }
            $this->load->library("smartyview");
            $this->smartyview->assign("cronlogs", $cronlogs);
            $this->smartyview->display("log/log.index.tpl");
        }
    }
    public function get_log()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("error" => "Error"));
        } else {
            $draw = $this->input->post("draw");
            $creatorid = $this->session->userdata("login_creatorid");
            $start = $this->input->get("start");
            $length = $this->input->get("length");
            $this->db->where(array("creatorid" => $creatorid));
            $this->db->limit($length, $start);
            $columns = array("level", "date", "userid", "ip", "content");
            $orders = $this->input->post("order");
            if ($orders && is_array($orders)) {
                foreach ($orders as $order) {
                    $this->db->order_by($columns[$order["column"]], $order["dir"]);
                }
            }
            $query = $this->db->get("dc_auditlog");
            $tmp_data = $query->result_array();
            $result = array();
            $result["draw"] = $draw;
            $result["recordsTotal"] = count($tmp_data);
            $result["recordsFiltered"] = count($tmp_data);
            $data = array();
            $usernames = array();
            foreach ($tmp_data as $row) {
                $username = "";
                if (isset($usernames[$row["userid"]])) {
                    $username = $usernames[$row["userid"]];
                } else {
                    $query = $this->db->select("name,email")->where("userid", $row["userid"])->get("dc_user");
                    if (0 < $query->num_rows()) {
                        $username = $query->row()->name . "(" . $query->row()->email . ")";
                        $usernames[$row["userid"]] = $username;
                    }
                }
                $data[] = array("level" => $row["level"], "date" => date("Y-m-d H:i:s", $row["date"]), "userid" => $username, "useragent" => $row["useragent"], "ip" => $row["ip"], "content" => $row["content"]);
            }
            $result["data"] = $data;
            echo json_encode($result);
        }
    }
}

?>