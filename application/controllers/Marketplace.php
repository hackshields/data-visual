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
class Marketplace extends BaseController
{
    public function get_html_templates()
    {
        $this->load->library("smartyview");
        $templates = array();
        $this->load->helper("directory");
        $base_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "templates";
        if (file_exists($base_dir)) {
            $dirs = directory_map($base_dir, 1);
            foreach ($dirs as $dir) {
                if (is_dir($base_dir . DIRECTORY_SEPARATOR . $dir)) {
                    $config_json_file = $base_dir . DIRECTORY_SEPARATOR . $dir . "config.json";
                    if (file_exists($config_json_file)) {
                        $json = json_decode(file_get_contents($config_json_file), true);
                        $template = array();
                        $template["dir"] = basename($dir);
                        $template["name"] = $json["name"];
                        $template["description"] = isset($json["description"]) ? $json["description"] : "";
                        $template["thumb"] = isset($json["thumb"]) ? $json["thumb"] : false;
                        $templates[] = $template;
                    }
                }
            }
        }
        $this->smartyview->assign("templates", $templates);
        $this->smartyview->display("marketplace/predefined.htmltemplates.list.tpl");
    }
    public function load_html_template()
    {
        $name = $this->input->post("name", true);
        if (empty($name) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "Error", "code" => 0));
        } else {
            $base_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "templates";
            $dir = $base_dir . DIRECTORY_SEPARATOR . $name;
            if (!file_exists($dir) || !is_dir($dir)) {
                echo json_encode(array("result" => "Error", "code" => 1));
            } else {
                $config_json_file = $dir . DIRECTORY_SEPARATOR . "config.json";
                if (!file_exists($config_json_file)) {
                    echo json_encode(array("result" => "Error", "code" => 2));
                } else {
                    $json = json_decode(file_get_contents($config_json_file), true);
                    $template = $json["template"];
                    $tpl_content = file_get_contents($dir . DIRECTORY_SEPARATOR . $template);
                    echo json_encode(array("tpl" => $tpl_content));
                }
            }
        }
    }
    public function use_html_template()
    {
        $name = $this->input->post("name", true);
        if (empty($name) || !$this->_is_admin_or_developer()) {
            echo json_encode(array("result" => "Error", "code" => 0));
        } else {
            $base_dir = FCPATH . "plugins" . DIRECTORY_SEPARATOR . "templates";
            $dir = $base_dir . DIRECTORY_SEPARATOR . $name;
            if (!file_exists($dir) || !is_dir($dir)) {
                echo json_encode(array("result" => "Error", "code" => 1));
            } else {
                $config_json_file = $dir . DIRECTORY_SEPARATOR . "config.json";
                if (!file_exists($config_json_file)) {
                    echo json_encode(array("result" => "Error", "code" => 2));
                } else {
                    $json = json_decode(file_get_contents($config_json_file), true);
                    $template = $json["template"];
                    $tpl_content = file_get_contents($dir . DIRECTORY_SEPARATOR . $template);
                    $this->load->library("smartyview");
                    $form_fields = $json["fields"];
                    $this->smartyview->assign("form_fields", $form_fields);
                    $form = $this->smartyview->fetch("marketplace/predefined.htmltemplate.form.tpl");
                    echo json_encode(array("tpl" => $tpl_content, "form" => $form));
                }
            }
        }
    }
    public function index()
    {
        $items = array();
        $this->load->library("smartyview");
        $this->smartyview->assign("items", $items);
        $indialog = $this->input->get_post("indialog");
        if ($indialog == "1") {
            $this->smartyview->display("marketplace/marketplace.select.dialog.tpl");
        } else {
            $this->smartyview->display("marketplace/gallery.tpl");
        }
    }
    public function search()
    {
        $items = $this->_call_rest_api("search_market_item", array("owner" => $this->input->get_post("owner"), "type" => $this->input->get_post("type"), "tag" => $this->input->get_post("tag"), "u" => $this->input->get_post("u")));
        $this->load->library("smartyview");
        $this->smartyview->assign("items", $items);
        $this->smartyview->display("marketplace/marketplace.gallery.items.tpl");
    }
    public function publish()
    {
        if (!$this->_is_admin()) {
            echo "Permission Denied";
        } else {
            $do = $this->input->post("do");
            $appid = $this->input->get_post("appid");
            $code = $this->input->get_post("code");
            $variable = $this->input->get_post("variable");
            $pid = $this->input->get_post("pid");
            $tpl = $this->input->get_post("tpl");
            $raw = $this->input->get_post("raw");
            $itemkey = $this->input->get_post("itemkey");
            $indialog = $this->input->get_post("indialog") == "1";
            if ($do == "confirm") {
                $type = $this->input->post("type");
                $target = $this->input->post("target");
                $data = array("do" => "confirm", "appid" => $appid, "code" => $code, "variable" => $variable, "pid" => $pid, "tpl" => $tpl, "raw" => $raw, "itemkey" => $itemkey, "indialog" => $indialog, "name" => $this->input->post("name"), "summary" => $this->input->post("summary"), "description" => $this->input->post("description"), "thumbnail" => $this->input->post("thumbnail"), "regularPrice" => $this->input->post("regularPrice"), "type" => $type, "target" => $target, "param1" => $this->input->post("param1"), "tags" => $this->input->post("tags"), "screenshots" => $this->input->post("screenshots"));
                if ($type == "app") {
                    $query = $this->db->where("appid", $target)->get("dc_app");
                    $data["attach"] = json_encode(array("dc_app" => $query->row_array()));
                } else {
                    if ($type == "code") {
                        $creatorid = $this->session->userdata("login_creatorid");
                        $query = $this->db->where(array("api" => $target, "creatorid" => $creatorid))->get("dc_code");
                        $data["attach"] = json_encode(array("content" => $query->row()->content, "api" => $target));
                    } else {
                        if ($type == "variable") {
                            $creatorid = $this->session->userdata("login_creatorid");
                            $query = $this->db->where(array("name" => $target, "creatorid" => $creatorid))->get("dc_parameter");
                            $data["attach"] = json_encode($query->row_array());
                        } else {
                            if ($type == "template") {
                                $creatorid = $this->session->userdata("login_creatorid");
                                $query = $this->db->select("filename,content")->where(array("filename" => $target, "creatorid" => $creatorid))->get("dc_template");
                                $data["attach"] = json_encode($query->row_array());
                            }
                        }
                    }
                }
                $result = $this->_call_rest_api("publish_market_item", $data);
                echo json_encode($result);
            } else {
                $this->load->library("smartyview");
                $this->smartyview->assign("title", "Publish to Exchange Marketplace");
                $result = $this->smartyview->fetch("marketplace/marketplace.publish.dialog.tpl");
                $this->output->set_output($result);
            }
        }
    }
    public function detail()
    {
        $itemkey = $this->input->post("itemkey");
        $result = $this->_call_rest_api("get_market_item", array("itemkey" => $itemkey), "html");
        echo $result;
    }
    public function view_item()
    {
        $itemkey = $this->input->post("itemkey");
        $type = $this->input->post("type");
        $target = $this->input->post("target");
        $item = $this->_call_rest_api("market_item_detail", array("itemkey" => $itemkey));
        if ($item["type"] == "code") {
            $creatorid = $item["creatorid"];
            $api = $item["target"];
            $attachment = json_decode($item["attachment"], true);
            $this->load->library("smartyview");
            $creatorid = $this->session->userdata("login_creatorid");
            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
            if ($ace_editor_theme) {
                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
            }
            $this->smartyview->assign("content", $attachment["content"]);
            $this->smartyview->display("marketplace/marketplace.code.tpl");
        } else {
            if ($item["type"] == "variable") {
                $creatorid = $item["creatorid"];
                $api = $item["target"];
                $attachment = json_decode($item["attachment"], true);
                $this->load->library("smartyview");
                $creatorid = $this->session->userdata("login_creatorid");
                $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
                if ($ace_editor_theme) {
                    $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
                }
                $this->smartyview->assign("content", $attachment["value"]);
                $this->smartyview->assign("code_language", "sql");
                $this->smartyview->display("marketplace/marketplace.code.tpl");
            } else {
                if ($item["type"] == "product") {
                    $creatorid = $item["creatorid"];
                    $pid = $item["target"];
                    $attachment = json_decode($item["attachment"], true);
                    $name = $attachment["name"];
                    $this->load->library("smartyview");
                    $this->smartyview->assign("title", $name);
                    $this->smartyview->assign("pid", $pid);
                    $this->smartyview->assign("purl", $this->_make_dbface_url("product/" . $attachment["url"]));
                    $this->smartyview->assign("itemkey", $itemkey);
                    $this->smartyview->display("marketplace/marketplace.preview.dialog.tpl");
                } else {
                    if ($item["type"] == "app") {
                        $creatorid = $item["creatorid"];
                        $appid = $item["target"];
                        $attachment = json_decode($item["attachment"], true);
                        $name = $attachment["name"];
                        $this->load->library("smartyview");
                        $this->smartyview->assign("title", $name);
                        $this->smartyview->assign("appid", $appid);
                        $this->smartyview->assign("itemkey", $itemkey);
                        $this->smartyview->display("marketplace/marketplace.preview.dialog.tpl");
                    } else {
                        if ($item["type"] == "template") {
                            $attachment = json_decode($item["attachment"], true);
                            $name = $attachment["filename"];
                            $this->load->library("smartyview");
                            $creatorid = $this->session->userdata("login_creatorid");
                            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
                            if ($ace_editor_theme) {
                                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
                            }
                            $this->smartyview->assign("content", $attachment["content"]);
                            $this->smartyview->assign("code_language", "smarty");
                            $this->smartyview->display("marketplace/marketplace.code.tpl");
                        } else {
                            $creatorid = $item["creatorid"];
                            $type = $item["type"];
                            $this->load->library("smartyview");
                            $creatorid = $this->session->userdata("login_creatorid");
                            $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
                            if ($ace_editor_theme) {
                                $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
                            }
                            $this->smartyview->assign("content", $item["param1"]);
                            if ($type == "script") {
                                $this->smartyview->assign("code_language", "javascript");
                            } else {
                                if ($type == "sql") {
                                    $this->smartyview->assign("code_language", "sql");
                                } else {
                                    $this->smartyview->assign("code_language", "smarty");
                                }
                            }
                            $this->smartyview->display("marketplace/marketplace.code.tpl");
                        }
                    }
                }
            }
        }
    }
    public function import_form()
    {
        $itemkey = $this->input->post("itemkey");
        $result = $this->_call_rest_api("market_item_info", array("itemkey" => $itemkey));
        if (!$result) {
            echo "No Market Item Found!";
        } else {
            $type = $result["type"];
            $this->load->library("smartyview");
            $creatorid = $this->session->userdata("login_creatorid");
            $target = $result["target"];
            if ($type == "app") {
                $target = $result["name"];
            } else {
                if ($type == "variable") {
                    $this->smartyview->assign("title", "Importing Variable...");
                } else {
                    if ($type == "template") {
                        $this->smartyview->assign("title", "Importing Template...");
                    }
                }
            }
            $this->smartyview->assign("name", $target);
            $this->smartyview->assign("itemkey", $itemkey);
            if ($type != "template") {
                $conns = $this->_get_simple_connections($creatorid);
                $this->smartyview->assign("conns", $conns);
            }
            $this->smartyview->display("marketplace/marketplace.import.form.code.tpl");
        }
    }
    public function import()
    {
        $itemkey = $this->input->post("itemkey");
        $result = $this->_call_rest_api("market_item_detail", array("itemkey" => $itemkey));
        if (!$result) {
            echo "No Market Item Found!";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if ($result["type"] == "code") {
                $name = $this->input->post("name");
                $connid = $this->input->post("connid");
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "api" => $name))->get("dc_code");
                if (0 < $query->num_rows()) {
                    echo json_encode(array("status" => 0, "target" => "name", "message" => "The Cloud code name already used, please choose another one."));
                    return NULL;
                }
                $attachment = json_decode($result["attachment"], true);
                $content = $attachment["content"];
                $filename = $this->_write_cloud_code($creatorid, $name, $content);
                $this->db->insert("dc_code", array("creatorid" => $creatorid, "api" => $name, "connid" => $connid, "content" => $content, "public" => 0, "filename" => $filename, "date" => time()));
                echo json_encode(array("status" => 1, "message" => "Cloud Code imported!"));
            } else {
                if ($result["type"] == "variable") {
                    $name = $this->input->post("name");
                    $connid = $this->input->post("connid");
                    $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "name" => $name))->get("dc_parameter");
                    if (0 < $query->num_rows()) {
                        echo json_encode(array("status" => 0, "target" => "name", "message" => "The variable name already used, please choose another one."));
                        return NULL;
                    }
                    $result = json_decode($result["attachment"], true);
                    $content = $result["value"];
                    $type = $result["type"];
                    $cached = $result["cached"];
                    $ttl = $result["ttl"];
                    $public = $result["public"];
                    $this->db->insert("dc_parameter", array("creatorid" => $creatorid, "connid" => $connid, "name" => $name, "type" => $type, "value" => $content, "cached" => $cached, "ttl" => $ttl, "public" => $public, "lastupdate" => time()));
                    echo json_encode(array("status" => 1, "message" => "Variable imported!"));
                } else {
                    if ($result["type"] == "app") {
                        $name = $this->input->post("name");
                        $connid = $this->input->post("connid");
                        $attachment = json_decode($result["attachment"], true);
                        $result = $attachment["dc_app"];
                        unset($result["appid"]);
                        $result["connid"] = $connid;
                        $result["name"] = $name;
                        $result["creatorid"] = $creatorid;
                        $result["embedcode"] = NULL;
                        $result["createdate"] = time();
                        $this->db->insert("dc_app", $result);
                        echo json_encode(array("status" => 1, "message" => "Application imported!"));
                    } else {
                        if ($result["type"] == "template") {
                            $name = $this->input->post("name");
                            $creatorid = $this->session->userdata("login_creatorid");
                            $query = $this->db->select("content")->where(array("creatorid" => $creatorid, "filename" => $name))->get("dc_template");
                            if (0 < $query->num_rows()) {
                                echo json_encode(array("status" => 0, "target" => "name", "message" => "The template name already used in your account, please choose another one."));
                                return NULL;
                            }
                            $result = json_decode($result["attachment"], true);
                            $this->db->insert("dc_template", array("creatorid" => $creatorid, "filename" => $name, "content" => $result["content"], "date" => time()));
                            echo json_encode(array("status" => 1, "message" => "Template imported!"));
                        }
                    }
                }
            }
        }
    }
    public function remove()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0));
        } else {
            $itemkey = $this->input->post("itemkey");
            $result = $this->_call_rest_api("remove_market_item", array("itemkey" => $itemkey));
            echo json_encode($result);
        }
    }
    public function follow()
    {
        $itemkey = $this->input->post("itemkey");
        $result = $this->_call_rest_api("follow_market_item", array("itemkey" => $itemkey));
        echo json_encode($result);
    }
    public function gettags()
    {
        $result = $this->_call_rest_api("get_market_tags");
        echo json_encode($result);
    }
    /**
     * export connection
     * 1. all schema settings for target database
     * 2. all applications
     * 3. category
     * 4. all parameters
     */
    public function export()
    {
        $this->load->helper("download");
        $connid = $this->input->get("connid");
        $data = "Here is some text!";
        $name = "dbface_export_" . $connid . ".dfa";
        force_download($name, $data);
    }
}

?>