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
class CssEditor extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $self_host = $this->config->item("self_host");
        if (!$self_host) {
            echo "";
        }
        $this->load->database();
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select css from dc_customcss where creatorid=?", array($creatorid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $this->smartyview->assign("custom_css", $row["css"]);
        }
        $query = $this->db->query("select js from dc_customjs where creatorid=?", array($creatorid));
        if (0 < $query->num_rows()) {
            $js = $query->row()->js;
            $this->smartyview->assign("custom_js", $js);
        }
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $this->_load_db_config($creatorid);
        $items = $this->_load_js_config($creatorid);
        $items = array_merge($items, $this->config->config);
        $this->smartyview->assign("items", $items);
        $this->smartyview->display("new/box.csseditor.tpl");
    }
    public function load_global_user_config()
    {
        $self_host = $this->config->item("self_host");
        if (!$self_host) {
            echo "No permission to access global config settings.";
        } else {
            if (!$this->_is_admin()) {
                echo "No permission to access global config settings.";
            } else {
                if (file_exists(USERPATH . "data" . DIRECTORY_SEPARATOR . "config.php")) {
                    echo file_get_contents(USERPATH . "data" . DIRECTORY_SEPARATOR . "config.php");
                } else {
                    $result = file_get_contents(FCPATH . "config" . DIRECTORY_SEPARATOR . "config.inc.php");
                    echo $result;
                }
            }
        }
    }
    public function save_global_user_config()
    {
        $self_host = $this->config->item("self_host");
        if (!$self_host) {
            echo json_encode(array("status" => 0, "result" => "No permission to access global config settings."));
        } else {
            if (!$this->_is_admin()) {
                echo json_encode(array("status" => 0, "result" => "No permission to access global config settings."));
            } else {
                $data = $this->input->post("data");
                file_put_contents(USERPATH . "data" . DIRECTORY_SEPARATOR . "config.php", $data);
                echo json_encode(array("status" => 1, "result" => "Config settings saved"));
            }
        }
    }
    public function update_sys_tpl()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $tpl_file = $this->input->post("tpl");
        $content = $this->input->post("content");
        $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "filename" => $tpl_file))->get("dc_sys_template");
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_sys_template", array("creatorid" => $creatorid, "filename" => $tpl_file, "content" => $content, "date" => time()));
        } else {
            $this->db->update("dc_sys_template", array("content" => $content, "date" => time()), array("creatorid" => $creatorid, "filename" => $tpl_file));
        }
        $systpl_path = FCPATH . DIRECTORY_SEPARATOR . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "public";
        if (!file_exists($systpl_path)) {
            @mkdir($systpl_path, 511, true);
        }
        $file_path = $systpl_path . DIRECTORY_SEPARATOR . $tpl_file;
        $this->load->helper("file");
        write_file($file_path, $content);
        echo json_encode(array("status" => 1));
    }
    public function revert_tpl()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $tpl_file = $this->input->post("tpl");
        $systpl_path = FCPATH . DIRECTORY_SEPARATOR . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "public";
        if (file_exists($systpl_path)) {
            $tpl_file_path = $systpl_path . DIRECTORY_SEPARATOR . $tpl_file;
            if (file_exists($tpl_file_path)) {
                @unlink($tpl_file_path);
                $systpl_path = FCPATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "public";
                if (file_exists($systpl_path)) {
                    $tpl_file_path = $systpl_path . DIRECTORY_SEPARATOR . $tpl_file;
                    if (file_exists($tpl_file_path)) {
                        $content = @file_get_contents($tpl_file_path);
                        echo json_encode(array("status" => 1, "tpl" => $content));
                        return NULL;
                    }
                }
            }
        }
        echo json_encode(array("status" => 1));
    }
    public function open_tpl()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $tpl_file = $this->input->post("tpl");
        $systpl_path = FCPATH . DIRECTORY_SEPARATOR . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "public";
        if (file_exists($systpl_path)) {
            $tpl_file_path = $systpl_path . DIRECTORY_SEPARATOR . $tpl_file;
            if (file_exists($tpl_file_path)) {
                $content = @file_get_contents($tpl_file_path);
                $this->output->set_output($content);
                return NULL;
            }
        }
        $systpl_path = FCPATH . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "public";
        if (file_exists($systpl_path)) {
            $tpl_file_path = $systpl_path . DIRECTORY_SEPARATOR . $tpl_file;
            if (file_exists($tpl_file_path)) {
                $content = @file_get_contents($tpl_file_path);
                $this->output->set_output($content);
                return NULL;
            }
        }
        $this->output->set_output("");
    }
    public function save_func()
    {
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $content = $this->input->post("content");
        if (empty($content)) {
            $this->db->delete("dc_customjs", array("creatorid" => $creatorid));
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
        } else {
            $query = $this->db->query("select 1 from dc_customjs where creatorid=?", array($creatorid));
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_customjs", array("creatorid" => $creatorid, "js" => $content, "date" => time()));
            } else {
                $this->db->update("dc_customjs", array("js" => $content, "date" => time()), array("creatorid" => $creatorid));
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
        }
    }
    public function save_footer()
    {
    }
    public function _get_variable_type($var)
    {
        return "config";
    }
    public function save_variables()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0));
        } else {
            $cfgs = $this->config->item("property_to_config_key");
            $data = $this->input->post("data");
            foreach ($data as $name => $value) {
                $type = $this->_get_variable_type($name);
                $arr = array("creatorid" => $creatorid, "name" => $name, "type" => $type, "value" => $value);
                $query = $this->db->select("value,type")->where(array("creatorid" => $creatorid, "name" => $name))->get("dc_user_options");
                if ($query->num_rows() == 0) {
                    if (!empty($value)) {
                        $this->db->insert("dc_user_options", $arr);
                    }
                } else {
                    $old_type = $query->row()->type;
                    $old_value = $query->row()->value;
                    if ($old_value != $value || $old_type != $arr["type"]) {
                        $should_removed = false;
                        if (empty($value)) {
                            $should_removed = true;
                        } else {
                            if ($type == "config" && $cfgs && isset($cfgs[$name])) {
                                $k = $cfgs[$name];
                                $default_v = $this->config->item($k);
                                if ($value == $default_v) {
                                    $should_removed = true;
                                }
                            }
                        }
                        if ($should_removed) {
                            $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => $name));
                        } else {
                            $this->db->update("dc_user_options", array("value" => $value, "type" => $arr["type"]), array("creatorid" => $creatorid, "name" => $name));
                        }
                    }
                }
            }
            echo json_encode(array("status" => 1));
        }
    }
    public function save()
    {
        $this->load->database();
        $this->load->helper("json");
        $creatorid = $this->session->userdata("login_creatorid");
        $content = $this->input->post("content");
        if (empty($content)) {
            $this->db->delete("dc_customcss", array("creatorid" => $creatorid));
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
        } else {
            $query = $this->db->query("select 1 from dc_customcss where creatorid=?", array($creatorid));
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_customcss", array("creatorid" => $creatorid, "css" => $content, "date" => time()));
            } else {
                $this->db->update("dc_customcss", array("css" => $content, "date" => time()), array("creatorid" => $creatorid));
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
        }
    }
    public function save_template()
    {
        $creatorid = $this->input->post("login_creatorid");
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0));
        } else {
            $tpl = $this->input->post("tpl");
            $content = $this->input->post("data");
        }
    }
}

?>