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
class Productbuilder extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_assign_product_list($creatorid);
        $url_base = $this->_get_url_base();
        $this->smartyview->assign("url_base", $url_base);
        $enable_marketplace = $this->config->item("enable_marketplace");
        if ($enable_marketplace) {
            $this->smartyview->assign("enable_marketplace", $enable_marketplace);
        }
        $this->smartyview->display("product/product.list.tpl");
    }
    public function _assign_product_list($creatorid)
    {
        $query = $this->db->query("select pid, name,url, description from dc_product where creatorid = ?", array($creatorid));
        $products = $query->result_array();
        $this->smartyview->assign("products", $products);
    }
    public function create($newCreate = true)
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $ace_editor_theme = $this->_get_ace_editor_theme($creatorid);
        if ($ace_editor_theme) {
            $this->smartyview->assign("ace_editor_theme", $ace_editor_theme);
        }
        $sub_accounts = $this->_get_sub_user_account($creatorid);
        $this->smartyview->assign("subaccounts", $sub_accounts);
        $apps = $this->_get_app_by_category($creatorid);
        $this->smartyview->assign("apps", $apps);
        $url_base = $this->_get_url_base();
        $this->smartyview->assign("url_base", $url_base);
        if ($newCreate) {
            $url = $this->_gen_product_url_suffix();
            $this->smartyview->assign("url", $url);
        }
        $this->smartyview->display("product/product.create.tpl");
    }
    public function edit()
    {
        $this->load->library("smartyview");
        $pid = $this->input->get("pid");
        $this->smartyview->assign("pid", $pid);
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "pid" => $pid))->get("dc_product");
        if ($query && $query->num_rows() == 1) {
            $product_info = $query->row_array();
            $name = $product_info["name"];
            $this->smartyview->assign("name", $name);
            $this->smartyview->assign("pid", $product_info["pid"]);
            $this->smartyview->assign("url", $product_info["url"]);
            $this->smartyview->assign("description", $product_info["description"]);
            $this->smartyview->assign("brand", $product_info["brand"]);
            $this->smartyview->assign("brandurl", $product_info["brandurl"]);
            $this->smartyview->assign("mtype", $product_info["menutype"]);
            $this->smartyview->assign("theme", $product_info["theme"]);
            $this->smartyview->assign("active", $product_info["active"]);
            $this->smartyview->assign("logintype", $product_info["logintype"]);
            $this->smartyview->assign("mpos", $product_info["menuposition"]);
            $settings = json_decode($product_info["settings"], true);
            $this->smartyview->assign("settings", $settings);
            if ($settings && isset($settings["users"])) {
                $this->smartyview->assign("permission_accounts", $settings["users"]);
            }
            if (isset($settings["css"]) && !empty($settings["css"])) {
                $this->smartyview->assign("css", $settings["css"]);
            }
            if (isset($settings["js"]) && !empty($settings["js"])) {
                $this->smartyview->assign("js", $settings["js"]);
            }
            if (isset($settings["containertype"]) && !empty($settings["containertype"])) {
                $this->smartyview->assign("containertype", $settings["containertype"]);
            }
            $menu_apps = json_decode($product_info["apps"], true);
            $this->smartyview->assign("menu_apps", $menu_apps);
        }
        $this->create(false);
    }
    public function save_item()
    {
        $pid = $this->input->post("pid");
        if (!$this->_is_admin() || empty($pid)) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $k = $this->input->post("k");
            if ($k == "apps") {
                $apps = json_encode($this->input->post("apps"));
                if (!$this->_check_apps_owner($apps)) {
                    echo json_encode(array("status" => 0, "code" => 100));
                } else {
                    $this->db->update("dc_product", array("apps" => $apps), array("creatorid" => $creatorid, "pid" => $pid));
                    echo json_encode(array("status" => 1));
                }
            } else {
                if ($k == "logintype" || $k == "brand" || $k == "brandurl" || $k == "menutype" || $k == "menuposition" || $k == "name" || $k == "description" || $k == "theme" || $k == "active") {
                    $val = $this->input->post("val");
                    $this->db->update("dc_product", array($k => $val), array("creatorid" => $creatorid, "pid" => $pid));
                    echo json_encode(array("status" => 1));
                } else {
                    echo json_encode(array("status" => 0, "code" => 0));
                }
            }
        }
    }
    public function save()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0, "code" => 999));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $pid = $this->input->post("pid");
            $pname = $this->input->post("pname");
            $purl = $this->input->post("purl");
            $pdesc = $this->input->post("pdesc");
            $pbrand = $this->input->post("pbrand");
            $pbrandurl = $this->input->post("pbrandurl");
            $mtype = $this->input->post("mtype");
            $mpos = $this->input->post("mpos");
            $theme = $this->input->post("theme");
            $logintype = $this->input->post("logintype");
            $is_created = empty($pid) || $pid == "0";
            $settings = json_encode($this->input->post("settings"));
            $apps = json_encode($this->input->post("apps"));
            if (!$this->_check_apps_owner($apps)) {
                echo json_encode(array("status" => 0, "code" => 100));
            } else {
                if ($is_created) {
                    $query = $this->db->query("select 1 from dc_product where url = ?", array($purl));
                    if (0 < $query->num_rows()) {
                        echo json_encode(array("status" => 0, "message" => "The URL has been used by other product."));
                        return NULL;
                    }
                    $this->db->insert("dc_product", array("creatorid" => $creatorid, "name" => $pname, "url" => $purl, "description" => $pdesc, "logintype" => $logintype, "brand" => empty($pbrand) ? $pname : $pbrand, "brandurl" => $pbrandurl, "menutype" => $mtype, "menuposition" => $mpos, "theme" => $theme, "settings" => $settings, "apps" => $apps, "active" => 1));
                    $pid = $this->db->insert_id();
                } else {
                    $query = $this->db->query("select 1 from dc_product where url = ? and pid != ?", array($purl, $pid));
                    if (0 < $query->num_rows()) {
                        echo json_encode(array("status" => 0, "message" => "The URL has been used by other product."));
                        return NULL;
                    }
                    $this->db->update("dc_product", array("name" => $pname, "url" => $purl, "description" => $pdesc, "logintype" => $logintype, "brand" => empty($pbrand) ? $pname : $pbrand, "brandurl" => $pbrandurl, "menutype" => $mtype, "menuposition" => $mpos, "theme" => $theme, "settings" => $settings, "apps" => $apps), array("pid" => $pid, "creatorid" => $creatorid));
                }
                echo json_encode(array("status" => 1, "pid" => $pid));
            }
        }
    }
    public function _gen_product_url_suffix()
    {
        return uniqid("p");
    }
    public function iframe_preview()
    {
        $pid = $this->input->get_post("pid");
        $query = $this->db->where("pid", $pid)->get("dc_product");
        if (!$query || $query->num_rows() != 1) {
            echo "Loading...";
        } else {
            $product_info = $query->row_array();
            $this->load->library("smartyview");
            $this->smartyview->assign("pid", $product_info["pid"]);
            $this->smartyview->assign("name", $product_info["name"]);
            $this->smartyview->assign("url", $product_info["url"]);
            $this->smartyview->assign("description", $product_info["description"]);
            $this->smartyview->assign("brand", $product_info["brand"]);
            $this->smartyview->assign("brandurl", $product_info["brandurl"]);
            $this->smartyview->assign("mtype", $product_info["menutype"]);
            $this->smartyview->assign("theme", $product_info["theme"]);
            $this->smartyview->assign("mpos", $product_info["menuposition"]);
            $this->smartyview->assign("logintype", $product_info["logintype"]);
            $settings = json_decode($product_info["settings"], true);
            $this->smartyview->assign("settings", $settings);
            if (isset($settings["css"]) && !empty($settings["css"])) {
                $this->smartyview->assign("css", $settings["css"]);
            }
            if (isset($settings["js"]) && !empty($settings["js"])) {
                $this->smartyview->assign("js", $settings["js"]);
            }
            if (isset($settings["containertype"]) && !empty($settings["containertype"])) {
                $this->smartyview->assign("containertype", $settings["containertype"]);
            }
            $menu_apps = json_decode($product_info["apps"], true);
            $this->smartyview->assign("menu_apps", $menu_apps);
            $this->smartyview->display("product/product.index.tpl");
        }
    }
    public function preview()
    {
        $pid = $this->input->post("pid");
        if (!$this->_is_admin() || empty($pid)) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "pid" => $pid))->get("dc_product");
        if ($query && $query->num_rows() == 1) {
            $product_info = $query->row_array();
            $name = $product_info["name"];
            $this->load->library("smartyview");
            $this->smartyview->assign("name", $name);
            $this->smartyview->assign("url", $product_info["url"]);
            $this->smartyview->assign("description", $product_info["description"]);
            $this->smartyview->assign("brand", $product_info["brand"]);
            $this->smartyview->assign("brandurl", $product_info["brandurl"]);
            $this->smartyview->assign("mtype", $product_info["menutype"]);
            $this->smartyview->assign("theme", $product_info["theme"]);
            $this->smartyview->assign("mpos", $product_info["menuposition"]);
            $this->smartyview->assign("logintype", $product_info["logintype"]);
            $settings = json_decode($product_info["settings"], true);
            $this->smartyview->assign("settings", $settings);
            $menu_apps = json_decode($product_info["apps"], true);
            $this->smartyview->assign("menu_apps", $menu_apps);
            $this->smartyview->display("product/product.preview.tpl");
        }
    }
    public function delp()
    {
        $pid = $this->input->post("pid");
        if (!$this->_is_admin() || empty($pid)) {
            return NULL;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->where(array("creatorid" => $creatorid, "pid" => $pid))->get("dc_product");
        if (!$query || $query->num_rows() == 0) {
            return NULL;
        }
        $this->load->library("smartyview");
        $this->db->delete("dc_product", array("creatorid" => $creatorid, "pid" => $pid));
        $this->_assign_product_list($creatorid);
        $url_base = $this->_get_url_base();
        $this->smartyview->assign("url_base", $url_base);
        $this->smartyview->display("product/product.inc.list.tpl");
    }
    public function _check_app_is_under_account($appid, $creatorid)
    {
        if ($appid == "M" || $appid == "Menu") {
            return true;
        }
        $query = $this->db->query("select 1 from dc_app where creatorid=? and appid=?", array($creatorid, $appid));
        if ($query && $query->num_rows() == 1) {
            return true;
        }
        return false;
    }
    public function update_status()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $pid = $this->input->post("pid");
        $status = $this->input->post("status");
        if (empty($creatorid) || empty($pid)) {
            echo json_encode(array("status" => 0));
        } else {
            $this->db->update("dc_product", array("active" => $status), array("pid" => $pid, "creatorid" => $creatorid));
            echo json_encode(array("status" => 1));
        }
    }
    public function update_theme()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $pid = $this->input->post("pid");
        $theme = $this->input->post("theme");
        if (empty($creatorid) || empty($pid) || empty($theme)) {
            echo json_encode(array("status" => 0));
        } else {
            $this->db->update("dc_product", array("theme" => $theme), array("pid" => $pid, "creatorid" => $creatorid));
            echo json_encode(array("status" => 1));
        }
    }
    public function _check_apps_owner($apps)
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if ($apps && is_array($apps)) {
            foreach ($apps as $app) {
                $appid = $app["data-url"];
                $result = $this->_check_app_is_under_account($appid, $creatorid);
                if (!$result) {
                    return false;
                }
                if (isset($app["subapps"])) {
                    foreach ($app["subapps"] as $sa) {
                        $appid = $sa["data-url"];
                        $result = $this->_check_app_is_under_account($appid, $creatorid);
                        if (!$result) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
}

?>