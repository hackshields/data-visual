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
class Filter extends BaseController
{
    public function index()
    {
    }
    public function remove()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $fid = $this->input->post("fid");
        }
    }
    public function refresh()
    {
    }
    public function get_app_filters()
    {
        $appid = $this->input->get_post("appid");
        $query = $this->db->select("key, value")->where(array("appid" => $appid, "type" => "inline_filter"))->get("dc_app_options");
    }
    public function get_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $fid = $this->input->post("fid");
            $query = $this->db->where(array("creatorid" => $creatorid, "filterid" => $fid))->get("dc_filter");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "code" => 998));
            } else {
                $filter_info = $query->row_array();
                if ($filter_info["type"] == 0 || $filter_info["type"] == 1) {
                    $filter_info["value"] = json_decode($filter_info["value"], true);
                }
                echo json_encode(array("status" => 1, "filter" => $filter_info));
            }
        }
    }
    public function del_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $fid = $this->input->post("fid");
            if (empty($fid)) {
                echo json_encode(array("status" => 0, "code" => 998));
            } else {
                $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "key" => $fid, "type" => "inline_filter"));
                $this->db->delete("dc_filter", array("creatorid" => $creatorid, "filterid" => $fid));
                echo json_encode(array("status" => 1));
            }
        }
    }
    public function toggle_app_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $appid = $this->input->post("appid");
            if (empty($appid)) {
                echo json_encode(array("status" => 0, "code" => 998));
            } else {
                $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "appid" => $appid, "type" => "inline_filter_status"))->get("dc_app_options");
                $cur_status = 1;
                if ($query->num_rows() == 0) {
                    $cur_status = 1;
                    $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "key" => time(), "type" => "inline_filter_status", "value" => $cur_status));
                } else {
                    $row = $query->row_array();
                    $cur_status = $row["value"] == "0" ? "1" : "0";
                    $this->db->update("dc_app_options", array("key" => time(), "value" => $cur_status), array("creatorid" => $creatorid, "appid" => $appid, "type" => "inline_filter_status"));
                    if ($cur_status == 0) {
                        $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "type" => "inline_filter"));
                    }
                }
                echo json_encode(array("status" => 1, "filter_status" => $cur_status));
            }
        }
    }
    public function del_app_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $appid = $this->input->post("appid");
            $fid = $this->input->post("fid");
            $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "key" => $fid, "type" => "inline_filter"));
            echo json_encode(array("status" => 1));
        }
    }
    public function add_app_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $appid = $this->input->post("appid");
            $fid = $this->input->post("fid");
            $del = $this->input->post("del") == 1;
            if ($del) {
                $this->db->delete("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "key" => $fid, "type" => "inline_filter"));
                echo json_encode(array("status" => 1));
            } else {
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "type" => "inline_filter", "appid" => $appid, "key" => $fid))->get("dc_app_options");
                if ($query->num_rows() == 0 && !$del) {
                    $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "appid" => $appid, "key" => $fid, "type" => "inline_filter", "value" => json_encode(array())));
                }
                $query = $this->db->where(array("creatorid" => $creatorid, "filterid" => $fid))->get("dc_filter");
                $filter_info = $query->row_array();
                if ($filter_info["type"] == 0 || $filter_info["type"] == 1) {
                    $filter_info["value"] = json_decode($filter_info["value"], true);
                }
                $this->load->library("smartyview");
                $this->smartyview->assign("filter", $filter_info);
                $html = $this->smartyview->fetch("inc/inc.app.filter.dimension.tpl");
                echo json_encode(array("status" => 1, "dimension" => $html));
            }
        }
    }
    public function create()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $filterid = trim($this->input->post("fid"));
            $name = trim($this->input->post("name"));
            if (empty($name)) {
                echo json_encode(array("status" => 0, "code" => 996));
            } else {
                $type = $this->input->post("type");
                $connid = $this->input->post("connid");
                $sql = $this->input->post("sql");
                $names = $this->input->post("names[]");
                $values = $this->input->post("values[]");
                $single = $this->input->post("use_radio");
                $isdefault = $this->input->post("is_default");
                $is_expression = $this->input->post("is_expression");
                $expression = $this->input->post("expression");
                $appid = $this->input->post("appid");
                $value = array();
                $cached = NULL;
                if ($type != 0 && $type != 1) {
                    echo json_encode(array("status" => 0, "code" => 998));
                } else {
                    if ($type == 0) {
                        $db = $this->_get_db($creatorid, $connid);
                        $value = json_encode(array("connid" => $connid, "sql" => $sql));
                        if ($db) {
                            $query = $db->query($sql);
                            if ($query && 0 < $query->num_rows()) {
                                $result = $query->result_array();
                                $fields = $query->list_fields();
                                $is_nv = 2 <= count($fields);
                                foreach ($result as $row) {
                                    $n = $row[$fields[0]];
                                    $v = $is_nv ? $row[$fields[1]] : $n;
                                    $value[] = array("name" => $n, "value" => $v);
                                }
                                $cached = json_encode($value);
                            }
                        }
                    } else {
                        if ($type == 1) {
                            $size = count($names);
                            for ($i = 0; $i < $size; $i++) {
                                $n = $names[$i];
                                $v = isset($values[$i]) ? $values[$i] : "";
                                $value[] = array("name" => $n, "value" => $v);
                            }
                            $value = json_encode($value);
                            $cached = NULL;
                        }
                    }
                    if (empty($filterid)) {
                        $this->db->insert("dc_filter", array("creatorid" => $creatorid, "connid" => $connid, "name" => $name, "type" => $type, "value" => $value, "cached" => $cached, "single" => $single, "isdefault" => $isdefault, "expression" => $is_expression == 0 ? NULL : $expression, "ttl" => 0, "lastupdate" => time()));
                        $filterid = $this->db->insert_id();
                    } else {
                        $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "filterid" => $filterid))->get("dc_filter");
                        if ($query->num_rows() == 1) {
                            $this->db->update("dc_filter", array("connid" => $connid, "name" => $name, "type" => $type, "value" => $value, "cached" => $cached, "single" => $single, "isdefault" => $isdefault, "expression" => $is_expression == 0 ? NULL : $expression, "ttl" => 0, "lastupdate" => time()), array("creatorid" => $creatorid, "filterid" => $filterid));
                        } else {
                            echo json_encode(array("status" => 0, "code" => 997));
                            return NULL;
                        }
                    }
                    echo json_encode(array("status" => 1, "filter" => array("filterid" => $filterid, "name" => $name)));
                }
            }
        }
    }
    public function get_manage_filter()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $appid = $this->input->post("appid");
        if (empty($creatorid) || empty($appid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $this->_load_predefined_filters();
            $this->load->library("smartyview");
            $query = $this->db->where(array("creatorid" => $creatorid))->order_by("lastupdate", "desc")->get("dc_filter");
            $result = $query->result_array();
            $all_filters = array();
            $all_filters_by_name = array();
            foreach ($result as $row) {
                $all_filters[] = $row;
                $row["value"] = json_decode($row["value"], true);
                $all_filters_by_name[$row["filterid"]] = $row;
            }
            $this->smartyview->assign("all_filters", $all_filters);
            $this->smartyview->assign("all_filters_by_name", $all_filters_by_name);
            $query = $this->db->select("key, value")->where(array("creatorid" => $creatorid, "appid" => $appid, "type" => "inline_filter"))->get("dc_app_options");
            $result = $query->result_array();
            $app_filters = array();
            $app_filter_ids = array();
            foreach ($result as $row) {
                $f = array();
                $filterinfo = $all_filters_by_name[$row["key"]];
                $f["filterid"] = $filterinfo["filterid"];
                $f["name"] = $filterinfo["name"];
                $f["value"] = !empty($row["value"]) ? json_decode($row["value"], true) : false;
                $app_filters[] = $f;
                $app_filter_ids[] = $row["key"];
            }
            $this->smartyview->assign("app_filters", $app_filters);
            $this->smartyview->assign("app_filter_ids", $app_filter_ids);
            $this->smartyview->assign("appid", $appid);
            $this->smartyview->display("inc/inc.filter.manage.content.tpl");
        }
    }
    public function save_filter()
    {
        $appid = $this->input->post("appid");
        $values = $this->input->post("values");
        $fs = $this->input->post("fs");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($appid)) {
            echo json_encode(array("status" => 0));
        } else {
            $filters = array();
            foreach ($fs as $f) {
                $info = array();
                if ($values) {
                    foreach ($values as $row) {
                        if ($row["fid"] == $f) {
                            $a = array();
                            $a["name"] = $row["name"];
                            $a["value"] = $row["value"];
                            $info[] = $a;
                        }
                    }
                }
                $filters[$f] = $info;
            }
            foreach ($filters as $filterid => $value) {
                $query = $this->db->select("1")->where(array("appid" => $appid, "key" => $filterid, "type" => "inline_filter"))->get("dc_app_options");
                if (0 < $query->num_rows()) {
                    $this->db->update("dc_app_options", array("value" => json_encode($value)), array("creatorid" => $creatorid, "appid" => $appid, "key" => $filterid, "type" => "inline_filter"));
                } else {
                    $this->db->insert("dc_app_options", array("creatorid" => $creatorid, "connid" => 0, "appid" => $appid, "key" => $filterid, "type" => "inline_filter", "value" => json_encode($value)));
                }
            }
            echo json_encode(array("status" => 1));
        }
    }
}

?>