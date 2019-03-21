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
class CloudCode extends BaseController
{
    public function value_api()
    {
        dbface_log("info", "execute value api start");
        if ($this->config->item("disable_public_access_parameters")) {
            dbface_log("warning", "Public access parameters disallowed");
        } else {
            $root = $this->uri->segment(1);
            if ($root != "user" && $root != "team") {
                return NULL;
            }
            $user = $this->uri->segment(2);
            $api = $this->uri->segment(3);
            if (empty($user) || $api != "value") {
                return NULL;
            }
            $this->db->select("userid");
            $this->db->where("name", $user);
            $this->db->limit(1);
            $query = $this->db->get("dc_user");
            if ($query->num_rows() == 0) {
                return NULL;
            }
            $name = $this->uri->segment(4);
            $this->db->select("public, creatorid, type, name, connid, value, cached, ttl, lastupdate");
            $this->db->where("name", $name);
            $this->db->limit(1);
            $query = $this->db->get("dc_parameter");
            if ($query->num_rows() == 0) {
                return NULL;
            }
            $parameter = $query->row_array();
            $public = $parameter["public"];
            if ($public == "0") {
                return NULL;
            }
            $result = false;
            $connid = $parameter["connid"];
            $creatorid = $parameter["creatorid"];
            $db = $this->_get_db($creatorid, $connid);
            if ($parameter["type"] == 0) {
                $result = $parameter["value"];
            } else {
                if ($parameter["type"] == 1) {
                    $cached = $parameter["cached"];
                    $ttl = $parameter["ttl"];
                    if (!empty($cached) && ($ttl == 0 || time() - $parameter["lastupdate"] < $ttl)) {
                        $result = $cached;
                    } else {
                        if (!$db || !$connid) {
                            $result = $cached;
                        } else {
                            try {
                                $query = $db->query($parameter["value"]);
                                if ($query) {
                                    $fields = $query->list_fields();
                                    if (0 < count($fields)) {
                                        $is_single_value = $query->num_rows() == 1 && count($fields) == 1;
                                        if ($is_single_value) {
                                            $cached_row = $query->row_array();
                                            $cached = $cached_row[$fields[0]];
                                            $result = $cached;
                                            $this->db->update("dc_parameter", array("lastupdate" => time(), "cached" => $result), array("connid" => $connid, "creatorid" => $creatorid, "name" => $parameter["name"]));
                                        } else {
                                            $cached = $query->result_array();
                                            $result = json_encode($cached);
                                            $this->db->update("dc_parameter", array("lastupdate" => time(), "cached" => $result), array("connid" => $connid, "creatorid" => $creatorid, "name" => $parameter["name"]));
                                        }
                                    }
                                }
                            } catch (Exception $e) {
                            }
                        }
                    }
                }
            }
            if ($this->config->item("Access-Control-Allow-Origin")) {
                header("Access-Control-Allow-Origin: " . $this->config->item("Access-Control-Allow-Origin"));
            } else {
                header("Access-Control-Allow-Origin: *");
            }
            header("Access-Control-Allow-Methods: GET, POST");
            echo $result;
        }
    }
    public function index()
    {
        $login_userid = $this->session->userdata("login_userid");
        if (!empty($login_userid)) {
            error_reporting(32767 & ~8);
            ini_set("display_errors", 1);
        }
        $root = $this->uri->segment(1);
        if ($root != "user" && $root != "team") {
            return NULL;
        }
        $user = $this->uri->segment(2);
        $api = $this->uri->segment(3);
        if (empty($user) || empty($api)) {
            return NULL;
        }
        $this->db->select("userid");
        $this->db->where("name", $user);
        $this->db->limit(1);
        $query = $this->db->get("dc_user");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $userid = $query->row()->userid;
        $this->db->select("public, creatorid, content, connid");
        $this->db->where("api", $api);
        $this->db->limit(1);
        $query = $this->db->get("dc_code");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $code_info = $query->row_array();
        $public = $code_info["public"];
        if ($public == 1) {
            echo "Error Code: 10001";
        } else {
            if (!$this->config->item("enable_cloudcode") && $public != 3) {
                echo "Error Code: 10002";
            } else {
                $db = $this->_get_db($code_info["creatorid"], $code_info["connid"]);
                $smarty = $this->_get_template_engine($db, $code_info["creatorid"], $code_info["connid"]);
                $this->db = $db;
                $include_php = "user/files/" . $userid . "/" . $api . ".php";
                define("__CLOUD_CODE__", "__CLOUD_CODE__");
                if (file_exists($include_php)) {
                    try {
                        require_once $include_php;
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                } else {
                    echo "10003: Cloud Code file not found!";
                }
            }
        }
    }
}

?>