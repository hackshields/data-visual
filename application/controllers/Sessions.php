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
class Sessions extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("result" => "Permission Denied"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("ip,useragent,logout_at,_created_at,_updated_at")->where("creatorid", $creatorid)->order_by("_updated_at", "desc")->limit(50)->get("dc_loginsessions");
            $result_array = $query->result_array();
            $active_sessions = array();
            $inactive_sessions = array();
            require_once APPPATH . "libraries" . DIRECTORY_SEPARATOR . "time-ago" . DIRECTORY_SEPARATOR . "TimeAgo.php";
            $timeAgo = new TimeAgo();
            foreach ($result_array as $row) {
                $ip = $row["ip"];
                $useragent = $row["useragent"];
                $login_at = $row["_created_at"] == 0 ? "" : date("Y-m-d", $row["_created_at"]);
                if ($login_at == date("Y-m-d")) {
                    $login_at = $row["_created_at"] == 0 ? "" : $timeAgo->inWords($row["_created_at"]);
                    $logout_at = $row["logout_at"] == 0 ? "" : date("Y-m-d H:i:s", $row["logout_at"]);
                    $active_sessions[] = array("ip" => $ip, "login_date" => $login_at, "useragent" => $useragent, "logout_date" => $logout_at);
                } else {
                    $logout_at = $row["logout_at"] == 0 ? "" : date("Y-m-d", $row["logout_at"]);
                    $inactive_sessions[] = array("ip" => $ip, "login_date" => $login_at, "useragent" => $useragent, "logout_date" => $logout_at);
                }
            }
            $this->load->library("smartyview");
            $this->smartyview->assign("active_sessions", $active_sessions);
            $this->smartyview->assign("inactive_sessions", $inactive_sessions);
            $this->smartyview->display("log/sessions.index.tpl");
        }
    }
}

?>