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
class Share extends BaseController
{
    public function table()
    {
        $segments = $this->uri->segment_array();
        $segments_size = count($segments);
        if ($segments_size < 3) {
            echo "10001: Invalid URL";
        } else {
            $idx = array_search("table", $segments);
            $share_key = isset($segments[$idx + 1]) ? $segments[$idx + 1] : false;
            if (!$share_key) {
                echo "10002: Invalid URL";
            } else {
                $action = isset($segments[$idx + 2]) ? $segments[$idx + 2] : "list";
                $query = $this->db->select("creatorid,appid,connid,value")->where(array("key" => $share_key, "type" => "tableshare"))->get("dc_app_options");
                if ($query->num_rows() != 1) {
                    echo "10003: Invalid URL";
                } else {
                    $settings = json_decode($query->row()->value, true);
                    $enable = isset($settings["enable"]) ? $settings["enable"] : 0;
                    if ($enable != "1") {
                        echo "10004: The table sharing URL disabled by administrator.";
                    } else {
                        $appid = $query->row()->appid;
                        $creatorid = $query->row()->creatorid;
                        $connid = $query->row()->connid;
                        $this->load->helper("url");
                        if (!$this->config->item("production")) {
                            $base_url = base_url("static");
                            $this->config->set_item("df.static", $base_url);
                        }
                        $this->load->library("smartyview");
                        $this->_assign_table_alias($creatorid, $appid);
                        $this->_assign_table_editor_settings($creatorid, $appid);
                        if ($action == "new") {
                            $this->_create_table_item($share_key, $appid, $connid, $creatorid, $settings);
                        } else {
                            if ($action == "edit") {
                                $viewkey = isset($segments[$idx + 3]) ? $segments[$idx + 3] : false;
                                $this->_edit_table_item($share_key, $appid, $connid, $creatorid, $settings, $viewkey);
                            } else {
                                if ($action == "view") {
                                    $viewkey = isset($segments[$idx + 3]) ? $segments[$idx + 3] : false;
                                    $this->_view_table_item($share_key, $appid, $connid, $creatorid, $settings, $viewkey);
                                } else {
                                    $this->_list_table($share_key, $appid, $connid, $creatorid, $settings);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function _list_table($key, $appid, $connid, $creatorid, $settings)
    {
        $query = $this->db->select("title, script")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
        $result = $query->row_array();
        $title = $result["title"];
        $script = json_decode($result["script"], true);
        $tablename = is_string($script["tablename"]) ? $script["tablename"] : $script["tablename"][0];
        $fields = $settings["fields"];
        $features = $settings["features"];
        $filter = $settings["filter"];
        $cur_page = $this->input->get("p");
        if (empty($cur_page)) {
            $cur_page = 0;
        }
        $db = $this->_get_db($creatorid, $connid);
        $query = $db->select($fields)->limit(10, $cur_page * 10)->get($tablename);
        $data = $query->result_array();
        $field_data = field_data($db, $tablename);
        $pks = array();
        foreach ($field_data as $f) {
            if ($f->primary_key == "1") {
                $pks[] = $f->name;
            }
        }
        $itemkeys = array();
        $i = 0;
        foreach ($data as $row) {
            $item = array();
            foreach ($pks as $pk) {
                $item[] = $pk . ":" . $data[$i][$pk];
            }
            $itemkeys[] = implode("|", $item);
            $i++;
        }
        $show_num = min(4, count($fields));
        $this->smartyview->assign("tablename", $title);
        $this->smartyview->assign("show_num", $show_num);
        $this->smartyview->assign("split_num", intval(12 / $show_num));
        $this->smartyview->assign("data", $data);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("itemkeys", $itemkeys);
        $this->smartyview->assign("features", $features);
        $this->smartyview->assign("cur_page", $cur_page);
        $this->smartyview->assign("current_url", $this->_get_url_base());
        $this->smartyview->display("public/table_share_list.tpl");
    }
    public function _view_table_item($key, $appid, $connid, $creatorid, $settings, $viewkey)
    {
        $query = $this->db->select("title, script")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
        $result = $query->row_array();
        $title = $result["title"];
        $script = json_decode($result["script"], true);
        $tablename = is_string($script["tablename"]) ? $script["tablename"] : $script["tablename"][0];
        $db = $this->_get_db($creatorid, $connid);
        $db->from($tablename);
        $search_keys = explode("|", $viewkey);
        foreach ($search_keys as $condition) {
            $cks = explode(":", $condition);
            if (count($cks) == 2) {
                $db->where($cks[0], $cks[1]);
            }
        }
        $query = $db->get();
        $data = $query->row_array();
        $fields = $settings["fields"];
        $features = $settings["features"];
        $filter = $settings["filter"];
        $this->smartyview->assign("tablename", $title);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("data", $data);
        $url_base = base_url();
        $back_url = $url_base . "share/table/" . $key;
        $this->smartyview->assign("back_url", $back_url);
        $this->smartyview->assign("root_url", $back_url);
        $this->smartyview->assign("search_key", $viewkey);
        $this->smartyview->assign("features", $settings["features"]);
        $this->smartyview->display("public/table_share_view.tpl");
    }
    public function _edit_table_item($key, $appid, $connid, $creatorid, $settings, $viewkey)
    {
        $query = $this->db->select("title, script")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
        $result = $query->row_array();
        $title = $result["title"];
        $script = json_decode($result["script"], true);
        $tablename = is_string($script["tablename"]) ? $script["tablename"] : $script["tablename"][0];
        $fields = $settings["fields"];
        $features = $settings["features"];
        $filter = $settings["filter"];
        $db = $this->_get_db($creatorid, $connid);
        $db->from($tablename);
        $search_keys = explode("|", $viewkey);
        foreach ($search_keys as $condition) {
            $cks = explode(":", $condition);
            if (count($cks) == 2) {
                $db->where($cks[0], $cks[1]);
            }
        }
        $query = $db->get();
        $data = $query->row_array();
        $this->smartyview->assign("tablename", $title);
        $this->smartyview->assign("fields", $fields);
        $this->smartyview->assign("data", $data);
        $field_data = $this->_review_field_data(field_data($db, $tablename));
        $pks = array();
        foreach ($field_data as $f) {
            if ($f["primary"]) {
                $pks[] = $f->name;
            }
        }
        $this->smartyview->assign("field_data", $field_data);
        $this->smartyview->assign("pks", $pks);
        $url_base = base_url();
        $back_url = $url_base . "share/table/" . $key;
        $this->smartyview->assign("back_url", $back_url);
        $this->smartyview->display("public/table_share_edit.tpl");
    }
    public function _create_table_item($key, $appid, $connid, $creatorid, $settings)
    {
        $query = $this->db->select("title, script")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
        $result = $query->row_array();
        $title = $result["title"];
        $script = json_decode($result["script"], true);
        $tablename = is_string($script["tablename"]) ? $script["tablename"] : $script["tablename"][0];
        $fields = $settings["fields"];
        $features = $settings["features"];
        $filter = $settings["filter"];
        $this->smartyview->assign("tablename", $title);
        $this->smartyview->assign("fields", $fields);
        $db = $this->_get_db($creatorid, $connid);
        $field_data = $this->_review_field_data(field_data($db, $tablename));
        $pks = array();
        foreach ($field_data as $f) {
            if ($f["primary"]) {
                $pks[] = $f->name;
            }
        }
        $this->smartyview->assign("field_data", $field_data);
        $this->smartyview->assign("pks", $pks);
        $url_base = base_url();
        $back_url = $url_base . "share/table/" . $key;
        $this->smartyview->assign("back_url", $back_url);
        $this->smartyview->display("public/table_share_new.tpl");
    }
    public function _review_field_data($field_data)
    {
        $fields = array();
        foreach ($field_data as $field) {
            $field_array = array();
            $field_array["type"] = $this->get_input_type($field->type, $field->max_length);
            $field_array["name"] = $field->name;
            $field_array["len"] = $field->max_length;
            if (property_exists($field, "primary_key") && $field->primary_key == 1) {
                $pkColumnNames[] = $field->name;
                $field_array["primary"] = true;
            } else {
                $field_array["primary"] = false;
            }
            $fields[] = $field_array;
        }
        return $fields;
    }
}

?>