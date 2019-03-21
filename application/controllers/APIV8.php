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
require APPPATH . "/libraries/REST_Controller.php";
class APIV8 extends REST_Controller
{
    private $api_owner_userid = false;
    public function _remap($object_called, $arguments = array())
    {
        $enable_feature = $this->config->item("feature_dbface_api");
        if (!$enable_feature) {
            $this->response("Feature Disabled", 400);
        } else {
            $request_method = $this->request->method;
            $creator_name = $arguments[0];
            $arg_count = count($arguments);
            if ($creator_name == "ip") {
                $ip = $this->input->ip_address();
                if ($arg_count == 2) {
                    $ip = $arguments[1];
                }
                $db_file = defined("IP2LOCATION_DATABASE") ? IP2LOCATION_DATABASE : USERPATH . "data" . DIRECTORY_SEPARATOR . "IP2LOCATION-LITE-DB3.BIN";
                if (!file_exists($db_file)) {
                    $this->response("IP2Location database not found, please download the latest IP2Location database from https://www.ip2location.com/.", 400);
                } else {
                    $this->load->library("ip2location_lib");
                    $countryCode = $this->ip2location_lib->getCountryCode($ip);
                    $countryName = $this->ip2location_lib->getCountryName($ip);
                    $regionName = $this->ip2location_lib->getRegionName($ip);
                    $cityName = $this->ip2location_lib->getCityName($ip);
                    $this->response(json_encode(array("countryCode" => $countryCode, "countryName" => $countryName, "region" => $regionName, "city" => $cityName)));
                }
            } else {
                if ($arg_count < 2) {
                    $this->response("Error API URL", 400);
                } else {
                    $module = $arguments[1];
                    $query = $this->db->select("userid")->where(array("name" => $creator_name, "creatorid" => 0))->get("dc_user");
                    if ($query->num_rows() == 0) {
                        $this->response("Invalid API owner", 401);
                    } else {
                        $userid = $query->row()->userid;
                        $this->api_owner_userid = $userid;
                        $query = $this->db->select("value")->where(array("creatorid" => $userid, "name" => "enable_databaseapi", "type" => "string"))->get("dc_user_options");
                        $enable_api = false;
                        if (0 < $query->num_rows()) {
                            $enable_api = $query->row()->value == "1";
                        }
                        if (!$enable_api) {
                            $this->response("API Disabled by owner", 400);
                        } else {
                            $check_header = true;
                            $query = $this->db->select("value")->where(array("creatorid" => $userid, "name" => "enable_public_dbapi", "type" => "string"))->get("dc_user_options");
                            if ($query->num_rows() == 1 && $query->row()->value == "1") {
                                $check_header = false;
                            }
                            if ($check_header) {
                                $query = $this->db->select("value")->where(array("creatorid" => $userid, "name" => "dbapi_master_key", "type" => "string"))->get("dc_user_options");
                                if ($query->num_rows() == 0) {
                                    $this->response("401 - Unauthorized\t", 401);
                                    return NULL;
                                }
                                $master_key = $query->row()->value;
                                $headers = $this->input->request_headers();
                                $keys_in_headers = isset($headers["HTTP-X-DBFACE-AUTH"]) ? $headers["HTTP-X-DBFACE-AUTH"] : false;
                                if (!$keys_in_headers || $keys_in_headers != $master_key) {
                                    $this->response("ERROR", REST_Controller::HTTP_UNAUTHORIZED);
                                    return NULL;
                                }
                            }
                            $query = $this->db->select("value")->where(array("creatorid" => $userid, "name" => "api_ipwhitelist", "type" => "string"))->get("dc_user_options");
                            if (0 < $query->num_rows()) {
                                $white_str = trim($query->row()->value);
                                $ip_check_ok = false;
                                if (!empty($white_str)) {
                                    $white_list = explode(",", $white_str);
                                    $ip_address = $this->input->ip_address();
                                    foreach ($white_list as $white) {
                                        if (!empty($white) && $this->ipIsInNet($ip_address, $white)) {
                                            $ip_check_ok = true;
                                        }
                                    }
                                } else {
                                    $ip_check_ok = true;
                                }
                                if (!$ip_check_ok) {
                                    $this->response("ERROR", REST_Controller::HTTP_UNAUTHORIZED);
                                    return NULL;
                                }
                            }
                            if ($module == "_cloud") {
                                if ($arg_count < 3) {
                                    $this->response("Not found cloud function name", 400);
                                    return NULL;
                                }
                                $func = $arguments[2];
                                if (function_exists($func)) {
                                    $params = $this->input->get_post("params");
                                    if (!empty($params)) {
                                        $result = call_user_func($func, json_decode($params, true));
                                    } else {
                                        $result = call_user_func($func);
                                    }
                                    if (is_array($result)) {
                                        echo json_encode($result);
                                    } else {
                                        echo $result;
                                    }
                                    return NULL;
                                }
                                $this->response("Cloud function not found", 400);
                            } else {
                                if ($module == "_db") {
                                    if ($arg_count == 2) {
                                    } else {
                                        if ($arg_count == 3) {
                                            $dbname = $arguments[2];
                                        } else {
                                            if ($arg_count == 5 && $arguments[3] == "_table") {
                                                list(, , $dbname, , $tablename) = $arguments;
                                            }
                                        }
                                    }
                                } else {
                                    if ($module == "screenshot_requests") {
                                        $appid = isset($arguments[2]) ? $arguments[2] : false;
                                        if (empty($appid)) {
                                            $this->response("wrong parameters for screenshot_requests API", 400);
                                            return NULL;
                                        }
                                        $query = $this->db->select("1")->where(array("creatorid" => $userid, "appid" => $appid))->get("dc_app");
                                        if ($query->num_rows() == 0) {
                                            $this->response("API Disabled by owner", 400);
                                            return NULL;
                                        }
                                        $format = $this->input->get_post("format");
                                        $url = $this->_generate_access_url($appid);
                                        if (empty($format) || $format != "png" && $format != "pdf") {
                                            $format = "png";
                                        }
                                        $file = uniqid();
                                        $info = $this->call_capture_service($file, $url, $format, "file");
                                        if (is_array($info) && $info["status"] == 1) {
                                            $file = $info["filename"];
                                            $this->output->set_content_type($format)->set_output(file_get_contents($file));
                                        } else {
                                            $this->response("Generate screenshot for application failed", 400);
                                            return NULL;
                                        }
                                    } else {
                                        if ($module == "create_snapshot") {
                                            $self_host = $this->config->item("self_host");
                                            if (!$self_host) {
                                                echo json_encode(array("error" => "create_snapshot not supported on this installation"));
                                                return NULL;
                                            }
                                            $snapshot_dir = $this->config->item("snapshot_dir");
                                            if (!$snapshot_dir) {
                                                $this->smartyview->assign("disable_snapshots", true);
                                                echo json_encode(array("result" => 0, "error" => "Snapshots feature not available on this installation."));
                                                return NULL;
                                            }
                                            if (!file_exists($snapshot_dir) && !mkdir($snapshot_dir)) {
                                                echo json_encode(array("result" => 0, "error" => "Snapshots directory not available. path: " . $snapshot_dir));
                                                return NULL;
                                            }
                                            $this->load->library("zip");
                                            $filename = md5(time()) . ".zip";
                                            $zip_filename = $snapshot_dir . DIRECTORY_SEPARATOR . $filename;
                                            $this->zip->read_dir(USERPATH . "files", false, FCPATH);
                                            $this->zip->read_dir(USERPATH . "data", false, FCPATH);
                                            $this->zip->archive($zip_filename);
                                            require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
                                            $token = array("creatorid" => $userid, "userid" => $userid, "filename" => $filename, "date" => time());
                                            $access_key = $this->config->item("app_access_key");
                                            $jwt = urlencode(Firebase\JWT\JWT::encode($token, $access_key));
                                            $url_base = $this->_get_url_base();
                                            $url = $url_base . "files/snapshot?id=" . $jwt;
                                            echo json_encode(array("result" => "ok", "url" => $url));
                                            return NULL;
                                        }
                                        if ($module == "_w") {
                                            if ($arg_count < 3) {
                                                $this->response("Invalid warehouse API request", 400);
                                                return NULL;
                                            }
                                            $warehouse_action = $arguments[2];
                                            if (!method_exists($this, "_warehouse_" . $warehouse_action)) {
                                                $this->response("Invalid warehouse API request", 400);
                                                return NULL;
                                            }
                                            $data = urldecode($this->input->get_post("content"));
                                            $json = json_decode($data, true);
                                            if (json_last_error() != JSON_ERROR_NONE) {
                                                $this->response("Missing required parameter data, Invalid JSON object", 400);
                                                return NULL;
                                            }
                                            call_user_func(array($this, "_warehouse_" . $warehouse_action), $json);
                                        } else {
                                            if ($module == "all_apps") {
                                                $userid = $this->input->get_post("user");
                                                $usergroup = $this->input->get_post("group");
                                                $allowed_apps = array();
                                                if (!empty($userid)) {
                                                    $query = $this->db->select("appid")->where("userid", $userid)->get("dc_app_permission");
                                                    if (0 < $query->num_rows()) {
                                                        foreach ($query->result_array() as $row) {
                                                            $allowed_apps[] = $row["appid"];
                                                        }
                                                    }
                                                }
                                                if (!empty($usergroup)) {
                                                    $query = $this->db->select("appid")->where("groupid", $usergroup)->get("dc_usergroup_permission");
                                                    if (0 < $query->num_rows()) {
                                                        foreach ($query->result_array() as $row) {
                                                            $allowed_apps[] = $row["appid"];
                                                        }
                                                    }
                                                }
                                                $connid = $this->input->get_post("datasource");
                                                if (!empty($connid) && !is_integer($connid)) {
                                                    $query = $this->db->select("connid")->where(array("creatorid" => $userid, "name" => $connid))->get("dc_conn");
                                                    if ($query->num_rows() == 1) {
                                                        $connid = $query->row()->connid;
                                                    }
                                                }
                                                $this->db->select("appid, name, title, desc");
                                                $this->db->where(array("creatorid" => $userid, "status" => "publish"));
                                                if (!empty($connid)) {
                                                    $this->db->where("connid", $connid);
                                                }
                                                $query = $this->db->get("dc_app");
                                                $result = $query->result_array();
                                                $apps = array();
                                                foreach ($result as $row) {
                                                    if (!empty($allowed_apps) && !in_array($row["appid"], $allowed_apps)) {
                                                        continue;
                                                    }
                                                    $app = array("appid" => $row["appid"], "name" => $row["name"], "title" => $row["title"], "description" => $row["desc"]);
                                                    $access_url = $this->_generate_access_url($row["appid"], $userid);
                                                    $ttl = $this->config->item("ttl_access_url");
                                                    $app["url"] = $access_url;
                                                    $app["ttl"] = $ttl;
                                                    $apps[] = $app;
                                                }
                                                $this->response(json_encode($apps, JSON_UNESCAPED_UNICODE));
                                            } else {
                                                if ($module == "poll") {
                                                    $poll_type = isset($arguments[2]) ? $arguments[2] : false;
                                                    $poll_value = isset($arguments[3]) ? $arguments[3] : false;
                                                    if (empty($poll_type) || empty($poll_value)) {
                                                        $this->response("Invalid DbFace Poll API", 400);
                                                        return NULL;
                                                    }
                                                    $this->_poll($userid, $poll_type, $poll_value);
                                                } else {
                                                    $this->response("Invalid DbFace API", 400);
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
    /**
     * DbFace poll API
     */
    public function _poll($userid, $type, $value)
    {
        $type = strtolower($type);
        if ($type == "tpl" || $type == "template") {
        } else {
            if ($type == "query") {
            } else {
                if ($type == "var") {
                }
            }
        }
    }
    /**
     * http://dbface/dbface/jsding/_w/find?content={json_condition}
     */
    public function _warehouse_find($json)
    {
        $table = isset($json["table"]) ? $json["table"] : false;
        $search = isset($json["search"]) ? $json["search"] : false;
        if (!$table && $search) {
            $this->response("table or search required in data", 400);
        } else {
            $connid = $this->_get_warehouse_connid($this->api_owner_userid);
            $db = $this->_get_db($this->api_owner_userid, $connid);
            if (!$db) {
                $this->response("Can not connect to warehouse database", 500);
            } else {
                if (!emtpy($search)) {
                    $query = $db->query($search);
                    echo json_encode($query->result_array());
                } else {
                    if (!empty($table)) {
                        if (isset($json["where"]) && is_array($json["where"])) {
                            $db->where($json["where"]);
                        }
                        $query = $db->query($table);
                        echo json_encode($query->result_array());
                    } else {
                        $this->response("Invalid find command", 500);
                    }
                }
            }
        }
    }
    /**
     * http://dbface/dbface/jsding/_w/save?content={json_condition}
     */
    public function _warehouse_save($json)
    {
        $this->response("Not implement", 400);
    }
    /**
     * http://dbface/dbface/jsding/_w/delete?content={json_condition}
     */
    public function _warehouse_delete($json)
    {
        $table = isset($json["table"]) ? $json["table"] : false;
        $search = isset($json["search"]) ? $json["search"] : false;
        if (!$table && $search) {
            $this->response("table or search required in JSON object", 400);
        } else {
            $connid = $this->_get_warehouse_connid($this->api_owner_userid);
            $db = $this->_get_db($this->api_owner_userid, $connid);
            if (!$db) {
                $this->response("Can not connect to warehouse database", 500);
            } else {
                if (!emtpy($search)) {
                    $result = $db->delete($search);
                    $affected_rows = $db->affected_rows();
                    echo json_encode(array("result" => "ok", "affected_rows" => $affected_rows));
                } else {
                    if (!empty($table)) {
                        if (isset($json["where"]) && is_array($json["where"])) {
                            $db->where($json["where"]);
                        }
                        $query = $db->delete($table);
                        $affected_rows = $db->affected_rows();
                        echo json_encode(array("result" => "ok", "affected_rows" => $affected_rows));
                    } else {
                        $this->response("Invalid delete command", 500);
                    }
                }
            }
        }
    }
    /**
     * clear schema data in warehouse
     */
    public function _warehouse_truncate($json)
    {
        $connid = $this->_get_warehouse_connid($this->api_owner_userid);
        $db = $this->_get_db($this->api_owner_userid, $connid);
        $schema = isset($json["table"]) ? $json["table"] : false;
        if (empty($schema)) {
            $this->response("Missing parameter table in JSON object", 400);
        } else {
            $db->truncate($schema);
            $this->response(json_encode(array("result" => "ok", "table" => $schema)));
        }
    }
    /**
     * drop schema in warehouse
     */
    public function _warehouse_drop($json)
    {
        $connid = $this->_get_warehouse_connid($this->api_owner_userid);
        $db = $this->_get_db($this->api_owner_userid, $connid);
        $schema = isset($json["table"]) ? $json["table"] : false;
        if (empty($schema)) {
            $this->response("Missing parameter table in JSON object", 400);
        } else {
            $dbforge = $this->load->dbforge($db, true);
            $dbforge->drop_table($schema, true);
            $this->response(json_encode(array("result" => "ok", "table" => $schema)));
        }
    }
}

?>