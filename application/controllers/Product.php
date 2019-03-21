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
class Product extends CI_Controller
{
    public function index()
    {
        $root = $this->uri->segment(1);
        $url = $this->uri->segment(2);
        if (empty($root) || $root != "product") {
            echo "Error Code: 20001";
        } else {
            $query = $this->db->where("url", $url)->get("dc_product");
            if (!$query || $query->num_rows() != 1) {
                echo "Error Code: 20002";
            } else {
                $product_info = $query->row_array();
                if ($product_info["active"] != 1) {
                    echo "Product not actived";
                } else {
                    $creatorid = $product_info["creatorid"];
                    $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "ipwhitelist"));
                    if (0 < $query->num_rows()) {
                        $white_str = trim($query->row()->value);
                        if (!empty($white_str)) {
                            $ip_address = $this->input->ip_address();
                            if (!check_ip_in_whitelist($ip_address, $white_str)) {
                                echo "You are not allowed to access this resource! If you think this is wrong, please send your IP Address: " . $ip_address . " to your administrator.";
                                return NULL;
                            }
                        }
                    }
                    if (!$this->config->item("production")) {
                        if (!module_rewrite_enabled()) {
                            $this->config->set_item("df.static", "../../static");
                        } else {
                            $this->config->set_item("df.static", "../static");
                        }
                    }
                    $init_appid = $this->input->get("ta");
                    $this->load->library("smartyview");
                    if (!empty($init_appid)) {
                        $this->smartyview->assign("init_appid", $init_appid);
                    }
                    $this->load->helper("url");
                    $pid = $product_info["pid"];
                    $this->smartyview->assign("pid", $product_info["pid"]);
                    $product_base_url = get_url_base();
                    $this->smartyview->assign("product_base_url", $product_base_url);
                    $this->smartyview->assign("base_url", base_url());
                    $this->smartyview->assign("name", $product_info["name"]);
                    $this->smartyview->assign("url", $product_info["url"]);
                    $this->smartyview->assign("description", $product_info["description"]);
                    $this->smartyview->assign("brand", $product_info["brand"]);
                    if (!empty($product_info["brandurl"]) && $product_info["brandurl"] != "#") {
                        $this->smartyview->assign("brandurl", $product_info["brandurl"]);
                    }
                    $this->smartyview->assign("mtype", $product_info["menutype"]);
                    $this->smartyview->assign("theme", $product_info["theme"]);
                    $this->smartyview->assign("mpos", $product_info["menuposition"]);
                    $settings = json_decode($product_info["settings"], true);
                    $containertype = isset($settings["containertype"]) ? $settings["containertype"] : "container";
                    $this->smartyview->assign("containertype", $containertype);
                    $users = isset($settings["users"]) ? $settings["users"] : false;
                    $login_status = false;
                    if ($product_info["logintype"] == 0) {
                        $action = $this->input->get_post("do");
                        if (empty($action)) {
                            $this->smartyview->assign("require_login", true);
                        } else {
                            if ($action == "login") {
                                $username = $this->input->post("username");
                                $password = $this->input->post("password");
                                $encrypt_password = md5($password . $this->config->item("password_encrypt"));
                                $query = $this->db->query("select userid, creatorid, name, email, permission, plan, status from dc_user where (name=? and password=?) or (email=? and password=?)", array($username, $encrypt_password, $username, $encrypt_password));
                                $check_login_success = false;
                                $login_uid = false;
                                $user_info = $query->row();
                                if ($query->num_rows() == 1) {
                                    $login_uid = $user_info->userid;
                                    $check_login_success = $users && in_array($login_uid, $users);
                                }
                                if ($check_login_success) {
                                    $this->session->set_userdata("login_p_" . $pid . "_userid", $login_uid);
                                    $this->session->set_userdata("login_userid", $user_info->userid);
                                    $this->session->set_userdata("login_username", $user_info->name);
                                    $this->session->set_userdata("login_email", $user_info->email);
                                    $this->session->set_userdata("login_permission", $user_info->permission);
                                    $this->session->set_userdata("login_plan", $user_info->plan);
                                    $this->session->set_userdata("login_creatorid", $user_info->creatorid);
                                    redirect($product_base_url . "?do=main");
                                    return NULL;
                                }
                                $this->smartyview->assign("require_login", true);
                                $this->smartyview->assign("message", array("title" => "Error", "content" => "Invalid password"));
                            }
                        }
                        $user_login = $this->session->userdata("login_p_" . $pid . "_userid");
                        $creatorid = $this->session->userdata("login_creatorid");
                        if (empty($user_login) && empty($creatorid)) {
                            $login_status = true;
                            $this->load->helper("url");
                            $this->smartyview->assign("current_url", current_url() . "product");
                            $this->smartyview->assign("require_login", true);
                        }
                    }
                    $this->smartyview->assign("settings", $settings);
                    if (!$login_status) {
                        $menu_apps = json_decode($product_info["apps"], true);
                        $this->smartyview->assign("menu_apps", $menu_apps);
                    }
                    $this->smartyview->display("product/product.index.tpl");
                }
            }
        }
    }
}

?>