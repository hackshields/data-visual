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
class Parameters extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
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
        $this->smartyview->display("new/parameters.list.tpl");
    }
    public function remove()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $name = $this->input->post("name");
        $this->db->delete("dc_parameter", array("creatorid" => $creatorid, "name" => $name));
        $this->load->library("smartyview");
        $this->_assign_parameters($creatorid);
        $this->smartyview->display("new/parameters.table.tpl");
    }
    public function refresh()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $name = $this->input->post("name");
        $connid = $this->input->post("connid");
        $query = $this->db->select("type, connid, value")->where(array("creatorid" => $creatorid, "name" => $name, "connid" => $connid))->get("dc_parameter");
        if (0 < $query->num_rows()) {
            $data = $query->row_array();
            if ($data["type"] == 1) {
                $sql = $data["value"];
                $connid = $data["connid"];
                $db = $this->_get_db($creatorid, $connid);
                if ($db) {
                    $smarty = $this->_get_template_engine($db, $creatorid, $connid);
                    $sql = $this->_compile_string($smarty, $sql);
                    $query = $db->query($sql);
                    log_message("debug", "Parameter Refresh: " . $db->last_query());
                    if ($query) {
                        $fields = $query->list_fields();
                        if ($fields && 0 < count($fields)) {
                            $is_single_value = $query->num_rows() == 1 && count($fields) == 1;
                            if ($is_single_value) {
                                $cached_row = $query->row_array();
                                $cached = $cached_row[$fields[0]];
                            } else {
                                $cached = json_encode($query->result_array());
                            }
                            $time = time();
                            $this->db->update("dc_parameter", array("lastupdate" => $time, "cached" => $cached), array("connid" => $connid, "creatorid" => $creatorid, "name" => $name));
                        }
                    }
                    echo json_encode(array("status" => 1, "newvalue" => $cached, "lastupdate" => date("Y-m-d H:i:s", $time)));
                    return NULL;
                }
            } else {
                if ($data["type"] == 3) {
                }
            }
        }
        echo json_encode(array("status" => 0));
    }
    public function create()
    {
        if (!$this->_is_admin_or_developer()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $name = $this->input->post("name");
        $type = $this->input->post("type");
        $value = $this->input->post("value");
        $connid = $this->input->post("connid");
        $ttl = $this->input->post("ttl");
        $public = $this->input->post("public");
        if (empty($name)) {
            return NULL;
        }
        $cached = $value;
        $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "name" => $name))->get("dc_parameter");
        if ($query->num_rows() == 0) {
            if ($type == 0) {
                $connid = 0;
                $ttl = 0;
            } else {
                if ($type == 1) {
                    $db = $this->_get_db($creatorid, $connid);
                    $_smarty = $this->_get_template_engine($db, $creatorid, $connid);
                    $sql = $this->_compile_string($_smarty, $value);
                    $query = $db->query($sql);
                    log_message("debug", "[Parameters#create]" . $db->last_query());
                    if ($query) {
                        $fields = $query->list_fields();
                        if ($fields && 0 < count($fields)) {
                            $is_single_value = $query->num_rows() == 1 && count($fields) == 1;
                            if ($is_single_value) {
                                $cached_row = $query->row_array();
                                $cached = $cached_row[$fields[0]];
                            } else {
                                $cached = json_encode($query->result_array());
                            }
                        }
                    }
                } else {
                    if ($type == 2) {
                    } else {
                        if ($type == 3) {
                            $url = $value;
                            $cached = parse_url_parameter($this->db, $url, $cached, 0, $creatorid, $name);
                        }
                    }
                }
            }
            $this->db->insert("dc_parameter", array("creatorid" => $creatorid, "connid" => $connid, "name" => $name, "type" => $type, "value" => $value, "cached" => $cached, "public" => $public, "ttl" => $ttl, "lastupdate" => time()));
        } else {
            if ($type == 0) {
                $connid = 0;
                $ttl = 0;
            } else {
                if ($type == 1) {
                    $db = $this->_get_db($creatorid, $connid);
                    $_smarty = $this->_get_template_engine($db, $creatorid, $connid);
                    $sql = $this->_compile_string($_smarty, $value);
                    $query = $db->query($sql);
                    log_message("debug", "[Parameters#create]" . $db->last_query());
                    if ($query) {
                        $fields = $query->list_fields();
                        if ($fields && 0 < count($fields)) {
                            $is_single_value = $query->num_rows() == 1 && count($fields) == 1;
                            if ($is_single_value) {
                                $cached_row = $query->row_array();
                                $cached = $cached_row[$fields[0]];
                            } else {
                                $cached = json_encode($query->result_array());
                            }
                        }
                    }
                } else {
                    if ($type == 2) {
                        if (function_exists($value)) {
                            $cached = call_user_func_array($value, array());
                        }
                        if ($cached == NULL) {
                            $cached = "";
                        }
                    } else {
                        if ($type == 3) {
                            $url = $value;
                            $cached = parse_url_parameter($this->db, $url, $cached, 0, $creatorid, $name);
                        }
                    }
                }
            }
            $this->db->update("dc_parameter", array("connid" => $connid, "type" => $type, "value" => $value, "cached" => $cached, "ttl" => $ttl, "public" => $public, "lastupdate" => time()), array("creatorid" => $creatorid, "name" => $name));
        }
        $this->load->library("smartyview");
        $this->_assign_parameters($creatorid);
        $this->smartyview->display("new/parameters.table.tpl");
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
    public function view_value()
    {
        $connid = $this->input->post("connid");
        $name = $this->input->post("name");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("cached,type,value")->where(array("creatorid" => $creatorid, "connid" => $connid, "name" => $name))->get("dc_parameter");
        if ($query->num_rows() == 0) {
            echo "";
        } else {
            $type = $query->row()->type;
            $value = $query->row()->value;
            $content = $query->row()->cached;
            $code_language = false;
            $highlight_line = 0;
            if ($type == 2) {
                $func = new ReflectionFunction($value);
                if ($func) {
                    $filename = $func->getFileName();
                    $content = file_get_contents($filename);
                    $code_language = "php";
                    $highlight_line = $func->getStartLine() - 1;
                }
            } else {
                $results = json_decode($content);
                if ($results) {
                    $content = json_encode($results, JSON_PRETTY_PRINT);
                }
            }
            $this->load->library("smartyview");
            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
            if ($ace_editor_theme) {
                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
            }
            if ($code_language) {
                $this->smartyview->assign("code_language", $code_language);
            }
            if (0 < $highlight_line) {
                $this->smartyview->assign("highlight_line", $highlight_line);
            }
            $this->smartyview->assign("content", $content);
            $this->smartyview->display("inc/view_code.tpl");
        }
    }
    public function get_available_parametersJSON()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("error"));
        } else {
            $freeboard = $this->input->post("boardid");
            $term = $this->input->get_post("q");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("name,cached,type,value")->where("creatorid", $creatorid)->like("name", $term)->get("dc_parameter");
            $result = $query->result_array();
            $data = array();
            foreach ($result as $row) {
                $data[$row["name"]] = parse_json_data($row["cached"]);
            }
            echo json_encode(array("result" => "ok", "data" => $data));
        }
    }
    public function get_available_paramaters()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "error"));
        } else {
            $term = $this->input->get_post("q");
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("name")->where("creatorid", $creatorid)->like("name", $term)->get("dc_parameter");
            $result = $query->result_array();
            $data = array();
            foreach ($result as $row) {
                $data[] = "{\$" . $row["name"] . "}";
            }
            echo json_encode($data);
        }
    }
}

?>