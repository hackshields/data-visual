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
class Account extends BaseController
{
    public function index()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->smartyview->assign("accounts", $this->_get_accounts($creatorid));
        $usergroups = $this->_get_usergroups($creatorid);
        $this->smartyview->assign("usergroups", $usergroups);
        $this->smartyview->display("new/account.list.tpl");
    }
    public function save_api_whitelist()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $ip = $this->input->post("ip");
            if (!empty($ip)) {
                $this->_save_user_option($creatorid, "api_whitelist", $ip);
            } else {
                $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "api_whitelist", "type" => "string"));
            }
            echo json_encode(array("status" => 1, "result" => $ip));
        }
    }
    public function enable_public_dbapi()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $val = $this->input->post("v");
            if ($val == "1") {
                $this->_save_user_option($creatorid, "enable_public_dbapi", 1);
            } else {
                $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "enable_public_dbapi", "type" => "string"));
            }
            echo json_encode(array("status" => 1, "result" => $val));
        }
    }
    public function enable_dbapi()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $val = $this->input->post("v");
            if ($val == "1") {
                $this->_save_user_option($creatorid, "enable_databaseapi", 1);
            } else {
                $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "enable_databaseapi", "type" => "string"));
            }
            echo json_encode(array("status" => 1, "result" => $val));
        }
    }
    public function generateapimasterkey()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $sso_secret_token = $this->_generateSSOToken($creatorid);
        $this->_save_user_option($creatorid, "dbapi_master_key", $sso_secret_token);
        echo $sso_secret_token;
    }
    public function generateSSOToken()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $sso_secret_token = $this->_generateSSOToken($creatorid);
        $this->_save_user_option($creatorid, "sso_secret_token", $sso_secret_token);
        echo $sso_secret_token;
    }
    public function _generateSSOToken($creatorid)
    {
        $token = md5($creatorid . uniqid("st_"));
        return $token;
    }
    public function createusergroup()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $tag = $this->input->post("tag");
        if ($tag == "__confirm__") {
            $this->_create_user_group();
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("userid, name, groupid")->where(array("creatorid" => $creatorid, "permission" => 9))->get("dc_user");
            $users = $query->result_array();
            $this->smartyview->assign("users", $users);
            $this->smartyview->display("usergroup/usergroup.create.tpl");
        }
    }
    public function create()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $tag = $this->input->post("tag");
        if ($tag == "__confirm__") {
            $this->_create_account();
        } else {
            $this->smartyview->display("new/account.create.tpl");
        }
    }
    public function suspend()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        if (empty($creatorid) || empty($userid) || $creatorid != $userid) {
            return NULL;
        }
        $query = $this->db->select("email")->where("userid", $creatorid)->get("dc_user");
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $email = $query->row()->email;
        $feedback = $this->input->post("feedback");
        $this->db->update("dc_user", array("status" => USER_STATUS_DELETED), array("userid" => $creatorid));
        $this->load->library("email");
        $this->_init_email_settings();
        $this->email->from("support@dbface.com", "DbFace");
        $this->email->to("support@dbface.com");
        $this->email->subject("Request Cancel Account");
        $this->email->message("userid: " . $email . "<p/>" . $feedback);
        $this->email->send();
        $this->load->helper("url");
        redirect("?module=Logout");
    }
    public function editaccess()
    {
        if (!$this->_is_admin()) {
            echo "Permission Denied.";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $userid = $this->input->get_post("userid");
            $groupid = $this->input->get_post("groupid");
            if (empty($userid) && empty($groupid)) {
                echo "Missing Account ID.";
            } else {
                if (!empty($userid)) {
                    $query = $this->db->select("1")->where(array("userid" => $userid, "creatorid" => $creatorid, "permission" => 9))->get("dc_user");
                    if ($query->num_rows() == 0) {
                        echo "Permission Denied.";
                        return NULL;
                    }
                }
                if (!empty($groupid)) {
                    $query = $this->db->select("1")->where(array("groupid" => $groupid, "creatorid" => $creatorid))->get("dc_usergroup");
                    if ($query->num_rows() == 0) {
                        echo "Permission Denied.";
                        return NULL;
                    }
                }
                $query = $this->db->select("connid,name")->where("creatorid", $creatorid)->get("dc_conn");
                $result_array = $query->result_array();
                $conn_names = array();
                foreach ($result_array as $row) {
                    $conn_names[$row["connid"]] = $row["name"];
                }
                if (!empty($userid)) {
                    $query = $this->db->select("appid")->where("userid", $userid)->get("dc_app_permission");
                    $result_array = $query->result_array();
                    $app_permissions = array();
                    foreach ($result_array as $row) {
                        $app_permissions[] = $row["appid"];
                    }
                } else {
                    if (!empty($groupid)) {
                        $query = $this->db->select("appid")->where("groupid", $groupid)->get("dc_usergroup_permission");
                        $result_array = $query->result_array();
                        $app_permissions = array();
                        foreach ($result_array as $row) {
                            $app_permissions[] = $row["appid"];
                        }
                    }
                }
                $query = $this->db->select("appid,connid,type,name,title,desc")->where(array("creatorid" => $creatorid, "status" => "publish"))->order_by("createdate", "desc")->get("dc_app");
                $result_array = $query->result_array();
                $apps = array();
                foreach ($result_array as $row) {
                    $appid = $row["appid"];
                    $connid = $row["connid"];
                    if (!isset($conn_names[$connid])) {
                        continue;
                    }
                    $name = $row["name"];
                    $desc = empty($row["desc"]) ? $row["title"] : $row["desc"];
                    $connname = $conn_names[$connid];
                    if (!isset($apps[$connname])) {
                        $apps[$connname] = array();
                    }
                    $has_permission = in_array($appid, $app_permissions) ? true : false;
                    $apps[$connname][] = array("appid" => $appid, "type" => $row["type"], "name" => $name, "desc" => $desc, "has_permission" => $has_permission);
                }
                $this->load->library("smartyview");
                $this->smartyview->assign("db_apps", $apps);
                if (!empty($userid)) {
                    $this->smartyview->assign("userid", $userid);
                }
                if (!empty($groupid)) {
                    $this->smartyview->assign("groupid", $groupid);
                }
                $this->smartyview->display("new/account.edit.app.permission.tpl");
            }
        }
    }
    public function update_app_permission()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $userid = $this->input->post("userid");
            $groupid = $this->input->post("groupid");
            $appid = $this->input->post("appid");
            if (empty($appid)) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "appid" => $appid))->get("dc_app");
                if ($query->num_rows() == 0) {
                    echo json_encode(array("status" => 0, "message" => "Permission Denied"));
                } else {
                    if (!empty($userid)) {
                        $query = $this->db->select("1")->where(array("userid" => $userid, "creatorid" => $creatorid, "permission" => 9))->get("dc_user");
                        if ($query->num_rows() == 0) {
                            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
                            return NULL;
                        }
                        $checked = $this->input->post("checked");
                        $query = $this->db->select("1")->where(array("userid" => $userid, "appid" => $appid))->get("dc_app_permission");
                        $exists = $query->num_rows() == 1 ? true : false;
                        if ($checked == "1") {
                            if (!$exists) {
                                $this->db->insert("dc_app_permission", array("userid" => $userid, "appid" => $appid));
                            }
                        } else {
                            if ($exists) {
                                $this->db->delete("dc_app_permission", array("userid" => $userid, "appid" => $appid));
                            }
                        }
                    } else {
                        if (!empty($groupid)) {
                            $query = $this->db->select("1")->where(array("groupid" => $groupid, "creatorid" => $creatorid))->get("dc_usergroup");
                            if ($query->num_rows() == 0) {
                                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
                                return NULL;
                            }
                            $checked = $this->input->post("checked");
                            $query = $this->db->select("1")->where(array("groupid" => $groupid, "appid" => $appid))->get("dc_usergroup_permission");
                            $exists = $query->num_rows() == 1 ? true : false;
                            if ($checked == "1") {
                                if (!$exists) {
                                    $this->db->insert("dc_usergroup_permission", array("groupid" => $groupid, "appid" => $appid, "date" => time()));
                                }
                            } else {
                                if ($exists) {
                                    $this->db->delete("dc_usergroup_permission", array("groupid" => $groupid, "appid" => $appid));
                                }
                            }
                        }
                    }
                    echo json_encode(array("status" => 1));
                }
            }
        }
    }
    public function editusergroup()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $this->load->library("smartyview");
        $groupid = $this->input->get("groupid");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("groupid,name")->where(array("groupid" => $groupid, "creatorid" => $creatorid))->get("dc_usergroup");
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $this->smartyview->assign("group", $row);
            $query = $this->db->select("userid, name, groupid")->where(array("creatorid" => $creatorid, "permission" => 9))->get("dc_user");
            $users = $query->result_array();
            $this->smartyview->assign("users", $users);
        }
        $this->smartyview->display("usergroup/usergroup.create.tpl");
    }
    public function edit()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $userid = $this->input->get_post("userid");
        $this->load->database();
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select * from dc_user where userid=? and creatorid=?", array($userid, $creatorid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $this->smartyview->assign("expiredate", date("Y-m-d", $row["expiredate"]));
            $this->smartyview->assign("account", $row);
        }
        $theme = $this->_get_ace_editor_theme($creatorid);
        if ($theme) {
            $this->smartyview->assign("ace_editor_theme", $theme);
        }
        $this->smartyview->display("new/account.create.tpl");
    }
    public function billing()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $creatorid = $this->session->userdata("login_userid");
        $thirdpartlogin = $this->session->userdata("login_thirdpart");
        if ($thirdpartlogin) {
            $this->smartyview->assign("login_thirdpart", true);
        }
        $this->smartyview->assign("check_expired", $this->config->item("check_expired"));
        $this->smartyview->assign("reserved_instance", $this->config->item("reserved_instance"));
        $this->smartyview->assign("self_host", $this->config->item("self_host"));
        $this->smartyview->assign("on_premise_host", $this->config->item("self_host"));
        $query = $this->db->query("select * from dc_user where userid=?", array($creatorid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $permission = $row["permission"];
            $this->smartyview->assign("expiredate", date("Y-m-d", $row["expiredate"]));
            $this->smartyview->assign("account", $row);
            $plan = $row["plan"];
            $quote = $this->config->item("plan_quote");
            if (!array_key_exists($plan, $quote)) {
                $quote = "level0";
            }
            $this->smartyview->assign("plan_name", $quote[$plan]["name"]);
            $user_options = $this->_get_user_options($creatorid);
            $license_email = false;
            $license_code = false;
            $license_name = false;
            if ($user_options) {
                foreach ($user_options as $user_option) {
                    $name = $user_option["name"];
                    if ($name == "userwelcome") {
                        $hasSetWelcome = true;
                        $user_option["value"] = $this->_get_user_template_code($creatorid, "system.userwelcome");
                    }
                    if ($name == "license_email") {
                        $license_email = $user_option["value"];
                    }
                    if ($name == "license_code") {
                        $license_code = $user_option["value"];
                    }
                    if ($name == "license_name") {
                        $license_name = $user_option["value"];
                    }
                    $this->smartyview->assign($name, $user_option["value"]);
                }
            }
            if ($license_email && $license_code) {
                $expired = $this->session->userdata("_EXPIRED_");
                if (!$expired && ce1($license_email, $license_code)) {
                    $this->smartyview->assign("valid_license", true);
                }
            }
            $this->smartyview->display("new/account.billing.tpl");
        }
    }
    public function editprofile()
    {
        $this->load->library("smartyview");
        $this->load->database();
        $userid = $this->session->userdata("login_userid");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid) || empty($userid)) {
            return NULL;
        }
        $thirdpartlogin = $this->session->userdata("login_thirdpart");
        if ($thirdpartlogin) {
            $this->smartyview->assign("login_thirdpart", true);
        }
        $this->smartyview->assign("check_expired", $this->config->item("check_expired"));
        $this->smartyview->assign("reserved_instance", $this->config->item("reserved_instance"));
        $this->smartyview->assign("self_host", $this->config->item("self_host"));
        $this->smartyview->assign("on_premise_host", $this->config->item("self_host"));
        $query = $this->db->query("select * from dc_user where userid=?", array($userid));
        if (0 < $query->num_rows()) {
            $row = $query->row_array();
            $permission = $row["permission"];
            $this->smartyview->assign("expiredate", date("Y-m-d", $row["expiredate"]));
            $this->smartyview->assign("account", $row);
            $plan = isset($row["plan"]) ? $row["plan"] : "level0";
            $quote = $this->config->item("plan_quote");
            if (!array_key_exists($plan, $quote)) {
                $plan = "level0";
            }
            $this->smartyview->assign("plan_name", $quote[$plan]["name"]);
            $cur_ipaddress = $this->input->ip_address();
            $this->smartyview->assign("cur_ipaddress", $cur_ipaddress);
            $hasSetWelcome = false;
            $user_options = $this->_get_user_options($creatorid);
            $license_email = false;
            $license_code = false;
            $welcome_app_id = 0;
            if ($user_options) {
                $sso_secret_token = false;
                foreach ($user_options as $user_option) {
                    $name = $user_option["name"];
                    if ($name == "userwelcome") {
                        $hasSetWelcome = true;
                        $user_option["value"] = $this->_get_user_template_code($creatorid, "system.userwelcome");
                    }
                    if ($name == "welcome_appid") {
                        $welcome_app_id = $user_option["value"];
                    }
                    if ($name == "license_email") {
                        $license_email = $user_option["value"];
                    }
                    if ($name == "license_code") {
                        $license_code = $user_option["value"];
                    }
                    if ($name == "sso_secret_token") {
                        $sso_secret_token = $user_option["value"];
                    }
                    $this->smartyview->assign($name, $user_option["value"]);
                }
                if (!$sso_secret_token) {
                    $sso_secret_token = $this->_generateSSOToken($creatorid);
                    $this->smartyview->assign("sso_secret_token", $sso_secret_token);
                    $this->_save_user_option($creatorid, "sso_secret_token", $sso_secret_token);
                }
            }
            if ($license_email && $license_code) {
                $expired = $this->session->userdata("_EXPIRED_");
                if (!$expired && ce1($license_email, $license_code)) {
                    $this->smartyview->assign("valid_license", true);
                }
            }
            if (!$hasSetWelcome) {
                $userwelcome = $this->_compile_tpl(false, false, false, "new/userwelcome.tpl");
                $this->smartyview->assign("userwelcome", $userwelcome);
            }
            $this->smartyview->assign("welcome_app_id", $welcome_app_id);
            if ($permission == 0) {
                $apps = $this->_get_apps_by_status($creatorid, "publish", "appid, name");
                $this->smartyview->assign("apps", $apps);
            }
            $userlanguage = $this->session->userdata("userlanguage");
            $this->smartyview->assign("userlanguage", $userlanguage);
            $this->smartyview->assign("useravatar", $this->_get_user_avatar());
            $sso_url_path = $this->config->item("sso_url_path");
            if (empty($sso_url_path)) {
                $sso_url_path = "iframe";
            }
            $query = $this->db->select("name")->where("userid", $creatorid)->get("dc_user");
            $username = $query->row()->name;
            $sso_iframe_url = $this->_make_dbface_url($username . "/iframe");
            $this->smartyview->assign("sso_iframe_url", $sso_iframe_url);
            $pass_days = $this->session->userdata("__license_pass_days__");
            $require_upgrade = $this->session->userdata("__require_upgrade__");
            if (!empty($pass_days) && !empty($require_upgrade)) {
                $this->smartyview->assign("require_upgrade", $require_upgrade);
                $this->smartyview->assign("pass_days", $pass_days);
            }
            $tab = $this->input->get("tab");
            if (!empty($tab)) {
                $this->smartyview->assign("tab", $tab);
            }
            $login_permission = $this->session->userdata("login_permission");
            if ($login_permission == 0) {
                $this->smartyview->display("new/account.editprofile.tpl");
            } else {
                $this->smartyview->display("new/subaccount.editprofile.tpl");
            }
        }
    }
    public function delgroup()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $gid = $this->input->post("gid");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->db->where(array("groupid" => $gid, "creatorid" => $creatorid))->delete("dc_usergroup");
        $this->db->update("dc_user", array("groupid" => ""), array("creatorid" => $creatorid, "groupid" => $gid, "permission" => 9));
        $this->db->delete("dc_usergroup_permission", array("groupid" => $gid));
        $accounts = $this->_get_accounts($creatorid);
        $this->smartyview->assign("accounts", $accounts);
        $usergroups = $this->_get_usergroups($creatorid);
        $this->smartyview->assign("usergroups", $usergroups);
        $this->smartyview->display("new/account.inc.list.tpl");
    }
    public function del()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->library("smartyview");
        $userid = $this->input->post("u");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->load->database();
        $this->db->where(array("userid" => $userid, "creatorid" => $creatorid))->delete("dc_user");
        $accounts = $this->_get_accounts($creatorid);
        $this->smartyview->assign("accounts", $accounts);
        $usergroups = $this->_get_usergroups($creatorid);
        $this->smartyview->assign("usergroups", $usergroups);
        $this->smartyview->display("new/account.inc.list.tpl");
    }
    public function changepsd()
    {
        $uid = $this->input->post("u");
        $name = $this->input->post("name");
        $email = $this->input->post("email");
        $oldpassword = trim($this->input->post("oldpassword"));
        $password = trim($this->input->post("password"));
        $password2 = trim($this->input->post("password2"));
        $hasError = false;
        $message = "";
        $login_uid = $this->session->userdata("login_userid");
        if (!$this->_is_admin() && $login_uid != $uid) {
            exit;
        }
        if (!empty($password) && $password != $password2) {
            $hasError = true;
            $message = "Password not matched";
        }
        if (!empty($password) && strlen($password) < 6) {
            $message = "Password is too short, at least 6 characters";
            $hasError = true;
        }
        $update_array = array();
        $query = $this->db->query("select name, email from dc_user where userid=? and password=?", array($uid, md5($oldpassword . $this->config->item("password_encrypt"))));
        if ($query->num_rows() == 0) {
            $message = "The current password is wrong.";
            $hasError = true;
        } else {
            $row = $query->row();
            $org_name = $row->name;
            $org_email = $row->email;
            if (!empty($name) && $org_name != $name) {
                $q = $this->db->query("select 1 from dc_user where name = ?", array($name));
                if (0 < $q->num_rows()) {
                    $message = "The new username has been used.";
                    $hasError = true;
                } else {
                    $update_array["name"] = $name;
                }
            }
            if (!empty($email) && $org_email != $email) {
                $q = $this->db->query("select 1 from dc_user where email = ?", array($email));
                if (0 < $q->num_rows()) {
                    $message = "The new email has been used.";
                    $hasError = true;
                } else {
                    $update_array["email"] = $email;
                }
            }
        }
        $this->load->helper("json");
        if ($hasError) {
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => $message)));
        } else {
            $this->load->database();
            $creatorid = $this->session->userdata("login_creatorid");
            if (!empty($password)) {
                $update_array["password"] = md5($password . $this->config->item("password_encrypt"));
            }
            if (!empty($update_array)) {
                $result = $this->db->update("dc_user", $update_array, array("userid" => $uid));
                if ($result && isset($update_array["email"])) {
                    $this->session->set_userdata("login_email", $update_array["email"]);
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Account profile has been updated.")));
        }
    }
    public function _create_user_group()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $groupname = trim($this->input->post("groupname"));
        $users = $this->input->post("users");
        $groupid = $this->input->post("groupid");
        if (!empty($users) && is_string($users)) {
            $users = array($users);
        }
        if (empty($groupname)) {
            $hasError = true;
            $message = "Group name can not be empty, please choose another one.";
        }
        if (!empty($groupname) && in_array($groupname, $this->config->item("reserved_username"))) {
            $hasError = true;
            $message = "Group name is reserved, please choose another one.";
        }
        if (empty($groupid)) {
            $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "name" => $groupname))->get("dc_usergroup");
            if (0 < $query->num_rows()) {
                $hasError = true;
                $message = "A groul with this name already exists, please choose another one.";
            } else {
                $gen_groupid = uniqid("ug");
                $this->db->insert("dc_usergroup", array("groupid" => $gen_groupid, "name" => $groupname, "creatorid" => $creatorid, "date" => time()));
                if (is_array($users) && 0 < count($users)) {
                    foreach ($users as $userid) {
                        $this->db->update("dc_user", array("groupid" => $gen_groupid), array("userid" => $userid, "creatorid" => $creatorid));
                    }
                }
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
                return NULL;
            }
        } else {
            $query = $this->db->select("1")->where("groupid != ", $groupid)->where(array("creatorid" => $creatorid, "name" => $groupname))->get("dc_usergroup");
            if (0 < $query->num_rows()) {
                $hasError = true;
                $message = "A groul with this name already exists, please choose another one.";
            } else {
                $this->db->update("dc_usergroup", array("name" => $groupname, "date" => time()), array("groupid" => $groupid, "creatorid" => $creatorid));
                $this->db->update("dc_user", array("groupid" => ""), array("creatorid" => $creatorid, "groupid" => $groupid));
                if (is_array($users) && 0 < count($users)) {
                    foreach ($users as $userid) {
                        $this->db->update("dc_user", array("groupid" => $groupid), array("userid" => $userid, "creatorid" => $creatorid));
                    }
                }
                $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true)));
                return NULL;
            }
        }
        if ($hasError) {
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => $message)));
        }
    }
    public function _create_account()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $username = trim($this->input->post("username"));
        $email = trim($this->input->post("email"));
        $password = trim($this->input->post("password"));
        $password2 = trim($this->input->post("password2"));
        $permission = $this->input->post("permission");
        $userid = $this->input->post("userid");
        $hasError = false;
        $message = "";
        if (64 < strlen($username)) {
            $hasError = true;
            $message = "Username is too long, less then 64 characters";
        }
        if (!preg_match("#^[a-zA-Z0-9_@.]+\$#", $username)) {
            $hasError = true;
            $message = "Username is not allowd, please remove the special characters.";
        }
        if (in_array($username, $this->config->item("reserved_username"))) {
            $hasError = true;
            $message = "Username is reserved, please choose another one.";
        }
        if ($password != $password2) {
            $hasError = true;
            $message = "Password not matched";
        }
        if (strlen($password) < 6) {
            $messages = "Password is too short, at least 6 characters";
            $hasError = true;
        }
        $this->load->database();
        $this->load->helper("json");
        $query = $this->db->select("plan")->where("userid", $creatorid)->get("dc_user");
        if ($query->num_rows() == 0) {
            $hasError = true;
            $message = "Unexpected error";
        }
        $login_plan = $query->row()->plan;
        $update_array = NULL;
        if (empty($userid)) {
            $usernameInUse = $this->_checkItemUsedInDb("dc_user", "name", $username);
            if ($usernameInUse) {
                $hasError = true;
                $message = "Username is used";
            }
            $emailInUse = $this->_checkItemUsedInDb("dc_user", "email", $email);
            if ($emailInUse) {
                $hasError = true;
                $message = "Email is used";
            }
        } else {
            $update_array = array("password" => md5($password . $this->config->item("password_encrypt")), "plan" => $login_plan);
            $query = $this->db->query("select name, email, permission from dc_user where userid = ?", array($userid));
            if ($query->num_rows() == 1) {
                $row = $query->row_array();
                $org_username = $row["name"];
                $org_email = $row["email"];
                $org_permission = $row["permission"];
                if ($org_username != $username) {
                    $usernameInUse = $this->_checkItemUsedInDb("dc_user", "name", $username);
                    if ($usernameInUse) {
                        $hasError = true;
                        $message = "Username is used";
                    } else {
                        $update_array["name"] = $username;
                    }
                }
                if ($org_email != $email) {
                    $emailInUse = $this->_checkItemUsedInDb("dc_user", "email", $email);
                    if ($emailInUse) {
                        $hasError = true;
                        $message = "Email is used";
                    } else {
                        $update_array["email"] = $email;
                    }
                }
                if ($org_permission != $permission) {
                    $update_array["permission"] = $permission;
                }
            } else {
                $hasError = true;
                $message = "Can not find the user in database.";
            }
        }
        if ($hasError) {
            $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => $message)));
        } else {
            if (empty($userid)) {
                $query = $this->db->query("select count(userid) as numuser from dc_user where creatorid = ?", array($creatorid));
                $row = $query->row_array();
                $nowsubNum = $row["numuser"];
                $quote = $this->_check_quote("max_subaccount", $nowsubNum);
                if ($quote) {
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Please upgrade your plan to allow saving new subaccounts.")));
                    return NULL;
                }
                $this->db->insert("dc_user", array("creatorid" => $creatorid, "email" => $email, "name" => $username, "password" => md5($password . $this->config->item("password_encrypt")), "permission" => $permission, "status" => 1, "regip" => $this->input->ip_address(), "regdate" => time(), "plan" => $login_plan, "expiredate" => time() + $this->config->item("trial_period_secs")));
                if ($this->db->affected_rows() == 1) {
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Account has been created.")));
                } else {
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "Can not save the account, please try again.")));
                }
            } else {
                $this->db->update("dc_user", $update_array, array("userid" => $userid));
                if ($this->db->affected_rows() == 1) {
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => true, "message" => "Account has been updated.")));
                } else {
                    $this->output->set_content_type("application/json")->set_output(json_encode(array("result" => false, "message" => "No information updated.")));
                }
            }
        }
    }
    public function flushcache()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $creatorid = $this->session->userdata("login_creatorid");
        $result = 0;
        $cached_dir = FCPATH . "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "cache" . DIRECTORY_SEPARATOR;
        if (file_exists($cached_dir)) {
            $di = new RecursiveDirectoryIterator($cached_dir, FilesystemIterator::SKIP_DOTS);
            $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($ri as $file) {
                $file->isDir();
                $file->isDir() ? rmdir($file) : unlink($file);
                $result++;
            }
        }
        $this->db->delete("dc_cache", array("creatorid" => $creatorid));
        $affected = $this->db->affected_rows();
        $result += $affected;
        $this->load->helper("json");
        $this->output->set_content_type("application/json")->set_output(json_encode(array("affected" => $result)));
    }
    public function save_sso()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $sso_login_url = $this->input->post("sso_login_url");
        $sso_logout_url = $this->input->post("sso_logout_url");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!empty($sso_login_url)) {
            $this->_save_user_option($creatorid, "sso_login_url", $sso_login_url);
        } else {
            $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "sso_login_url"));
        }
        if (!empty($sso_logout_url)) {
            $this->_save_user_option($creatorid, "sso_logout_url", $sso_logout_url);
        } else {
            $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "sso_logout_url"));
        }
        $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 1)));
    }
    public function save_default_language()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $userlanguage = $this->input->post("language");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_save_user_option($creatorid, "userlanguage", $userlanguage);
        $this->session->set_userdata("userlanguage", $userlanguage);
        $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 1)));
    }
    public function save_welcome_app()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $welcome_app = $this->input->post("welcome_app");
        $creatorid = $this->session->userdata("login_creatorid");
        $this->_save_user_option($creatorid, "welcome_appid", $welcome_app);
        $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 1)));
    }
    public function save_editor_theme()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->helper("json");
        $ace_editor_theme = $this->input->post("ace_editor_theme");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!empty($ace_editor_theme)) {
            $this->_save_user_option($creatorid, "ace_editor_theme", $ace_editor_theme);
        } else {
            $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "ace_editor_theme"));
        }
        $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 1)));
    }
    public function saveskin()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->helper("json");
        $skin = $this->input->post("skin");
        $creatorid = $this->session->userdata("login_creatorid");
        if (!empty($skin)) {
            $this->_save_user_option($creatorid, "skin", $skin);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode(array("status" => 1)));
    }
    public function savesystemsettings()
    {
        if (!$this->_is_admin()) {
            exit;
        }
        $this->load->helper("json");
        $ipwhitelist = $this->input->post("ipwhitelist");
        $customlogo = $this->input->post("customlogo");
        $userwelcome = trim($this->input->post("userwelcome"));
        $userlanguage = $this->input->post("userlanguage");
        $skin = $this->input->post("skin");
        $creatorid = $this->session->userdata("login_creatorid");
        $datamodule = $this->input->post("datamodule");
        $onlydefaultapps = $this->input->post("onlydefaultapps");
        $ace_editor_theme = $this->input->post("ace_editor_theme");
        $this->load->database();
        if (!empty($ipwhitelist)) {
            $this->_save_user_option($creatorid, "ipwhitelist", $ipwhitelist);
        } else {
            $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "ipwhitelist"));
        }
        $this->_save_user_option($creatorid, "customlogo", $customlogo);
        $this->_save_user_option($creatorid, "userwelcome", $userwelcome);
        $this->_save_user_option($creatorid, "userlanguage", $userlanguage);
        if (!empty($skin)) {
            $this->_save_user_option($creatorid, "skin", $skin);
        }
        if (empty($ace_editor_theme)) {
            $this->db->delete("dc_user_options", array("creatorid" => $creatorid, "name" => "ace_editor_theme"));
        } else {
            $this->_save_user_option($creatorid, "ace_editor_theme", $ace_editor_theme);
        }
        $this->_save_user_option($creatorid, "datamodule", $datamodule);
        $this->_save_user_option($creatorid, "onlydefaultapps", $onlydefaultapps);
        $this->session->set_userdata("userlanguage", $userlanguage);
        $this->session->set_userdata("onlydefaultapps", $onlydefaultapps);
        $affected = $this->db->affected_rows();
        $this->output->set_content_type("application/json")->set_output(json_encode(array("affected" => $affected)));
    }
    public function _get_user_options($creatorid)
    {
        $query = $this->db->query("select name, value from dc_user_options where creatorid = ?", array($creatorid));
        if (0 < $query->num_rows()) {
            return $query->result_array();
        }
        return false;
    }
    public function extend_upgrade()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $code = $this->input->post("cd");
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_email"));
        if (0 < $query->num_rows()) {
            $license_email = $query->row()->value;
        }
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_code"));
        if (0 < $query->num_rows()) {
            $license_code = $query->row()->value;
        }
        $query = $this->db->query("select value from dc_user_options where creatorid = ? and name=?", array($creatorid, "license_date"));
        if (0 < $query->num_rows()) {
            $license_date = $query->row()->value;
        }
        $query = $this->db->query("select 1 from dc_user_options where creatorid = ? and value=?", array($creatorid, $code));
        if (0 < $query->num_rows()) {
            echo json_encode(array("status" => 3));
        } else {
            if (empty($license_email) || empty($code)) {
                echo json_encode(array("status" => 0));
            } else {
                if ($code != "UP_" . strtoupper(md5("dbface17" . $license_email . "jk!"))) {
                    echo json_encode(array("status" => 0));
                } else {
                    $now = time();
                    $this->_save_user_option($creatorid, "license_date", $now);
                    $this->_save_user_option($creatorid, "upgrades_" . $now, $code);
                    $this->session->unset_userdata("__require_upgrade__");
                    $this->session->unset_userdata("__license_pass_days__");
                    echo json_encode(array("status" => 1));
                }
            }
        }
    }
    public function license()
    {
        $result = $this->_license();
        if ($result) {
            $creatorid = $this->session->userdata("login_creatorid");
            $email = $this->input->post("ce");
            $code = $this->input->post("cd");
            $name = $this->input->post("cn");
            $this->_save_user_option($creatorid, "license_email", $email);
            $this->_save_user_option($creatorid, "license_code", $code);
            $this->_save_user_option($creatorid, "license_name", $name);
            $this->_save_user_option($creatorid, "license_date", time());
            $this->db->update("dc_user", array("expiredate" => time() + 24 * 3600 * 360), array("creatorid" => "0"));
            $this->session->unset_userdata("_EXPIRED_");
            $this->_update_signature();
        }
    }
    public function ml()
    {
        $self_host = $this->config->item("self_host");
        if (!$self_host) {
            echo "Only valid for On-premises installation";
        } else {
            $email = $this->input->get("e");
            $ps = $this->input->get("p");
            $p = $this->config->item("password_encrypt");
            if ($ps != $p) {
                return NULL;
            }
            if (empty($email)) {
                echo "Empty Email";
            } else {
                $l = md5("dbfacephp15pro" . $email . "!@");
                $code = "EXC_" . strtoupper($l);
                $query = $this->db->where("name", "admin")->get("dc_user");
                if ($query->num_rows() == 0) {
                    echo "Can not found admin account";
                } else {
                    $creatorid = $query->row()->userid;
                    $this->db->update("dc_user", array("email" => $email), array("userid" => $creatorid));
                    $this->_save_user_option($creatorid, "license_email", $email);
                    $this->_save_user_option($creatorid, "license_code", $code);
                    $this->db->update("dc_user", array("expiredate" => time() + 24 * 3600 * 360), array("userid" => $creatorid));
                    $this->session->unset_userdata("_EXPIRED_");
                    $query = $this->db->query("select expiredate from dc_user where creatorid = 0 limit 1");
                    $expiredate = $query->row()->expiredate;
                    $signature = md5("dbfacephp.#" . $expiredate . "a!");
                    $this->_save_user_option($creatorid, "signature", $signature);
                }
            }
        }
    }
    public function upload_bigquery_serviceaccount()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "error" => "not login"));
        } else {
            if (!file_exists(USERPATH . "files")) {
                mkdir(USERPATH . "files", 493);
            }
            $useruploaddir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR;
            if (!file_exists($useruploaddir)) {
                mkdir($useruploaddir, 493);
            }
            $certdir = $useruploaddir . "certs";
            if (!file_exists($certdir)) {
                mkdir($certdir, 493);
            }
            $config["upload_path"] = $certdir;
            $config["allowed_types"] = "*";
            $config["max_size"] = 100;
            $config["overwrite"] = true;
            $this->load->library("upload", $config);
            if ($this->upload->do_upload("userfile")) {
                $data = $this->upload->data();
                $file_name = $data["file_name"];
                echo json_encode(array("status" => 1, "filename" => $file_name));
            } else {
                echo json_encode(array("status" => 0, "error" => $this->upload->display_errors()));
            }
        }
    }
    public function uploadcerts()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            echo json_encode(array("status" => 0, "error" => "not login"));
        } else {
            if (!file_exists(USERPATH . "files")) {
                mkdir(USERPATH . "files", 493);
            }
            $useruploaddir = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR;
            if (!file_exists($useruploaddir)) {
                mkdir($useruploaddir, 493);
            }
            $certdir = $useruploaddir . "certs";
            if (!file_exists($certdir)) {
                mkdir($certdir, 493);
            }
            $config["upload_path"] = $certdir;
            $config["allowed_types"] = "*";
            $config["max_size"] = 100;
            $config["overwrite"] = true;
            $this->load->library("upload", $config);
            if ($this->upload->do_upload("userfile")) {
                $data = $this->upload->data();
                $file_name = $data["file_name"];
                echo json_encode(array("status" => 1, "filename" => $file_name));
            } else {
                echo json_encode(array("status" => 0, "error" => $this->upload->display_errors()));
            }
        }
    }
    public function uploadavatar()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->session->userdata("login_userid");
        if (!file_exists(FCPATH . "user/files")) {
            mkdir(FCPATH . "user/files");
        }
        $useruploaddir = FCPATH . "user/files/" . $userid;
        if (!file_exists($useruploaddir)) {
            mkdir($useruploaddir);
        }
        $config["upload_path"] = $useruploaddir;
        $config["allowed_types"] = "*";
        $config["max_size"] = 100;
        $config["file_name"] = "avatar_" . $userid;
        $config["overwrite"] = true;
        $this->load->library("upload", $config);
        if (!$this->upload->do_upload("userfile")) {
            $error = array("error" => $this->upload->display_errors());
            echo "!message!" . $this->upload->display_errors();
        } else {
            $data = $this->upload->data();
            $fullpath = $data["full_path"];
            $file_name = $data["file_name"];
            $query = $this->db->query("select 1 from dc_user_options where creatorid=? and name=?", array($userid, "useravatar"));
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_user_options", array("creatorid" => $userid, "name" => "useravatar", "type" => "string", "value" => "user/files/" . $userid . "/" . $file_name));
            } else {
                $this->db->update("dc_user_options", array("value" => "user/files/" . $userid . "/" . $file_name), array("creatorid" => $userid, "name" => "useravatar"));
            }
            echo "user/files/" . $userid . "/" . $file_name;
        }
    }
    public function backup()
    {
        if (!$this->_is_admin() || !$this->config->item("self_host")) {
            return NULL;
        }
        $dir_backup = FCPATH . "user" . DIRECTORY_SEPARATOR . "cache";
        if (!file_exists($dir_backup)) {
            mkdir($dir_backup);
        }
        $this->load->library("zip");
        $filename = "dbface_backup_" . date("YmdHis") . ".zip";
        $zip_filename = $dir_backup . DIRECTORY_SEPARATOR . $filename;
        $this->zip->read_dir(USERPATH . "files", false, FCPATH);
        $this->zip->read_dir(USERPATH . "data", false, FCPATH);
        $this->zip->archive($zip_filename);
        $this->zip->download($filename);
    }
    public function restore()
    {
        if (!$this->_is_admin() || !$this->config->item("self_host")) {
            echo json_encode(array("status" => 0, "message" => "There is an error, that's all we know."));
        } else {
            if (!file_exists(USERPATH . "cache" . DIRECTORY_SEPARATOR . "upload")) {
                @mkdir(USERPATH . "cache" . DIRECTORY_SEPARATOR . "upload");
            }
            $options = array("upload_dir" => USERPATH . "cache" . DIRECTORY_SEPARATOR . "upload" . DIRECTORY_SEPARATOR, "param_name" => "userfile", "print_response" => false);
            $this->load->library("zip");
            $this->load->helper("file");
            $this->load->helper("directory");
            require APPPATH . "libraries" . DIRECTORY_SEPARATOR . "UploadHandler.php";
            $upload_handler = new UploadHandler($options, false);
            $result = $upload_handler->post(false);
            if ($result && isset($result["userfile"]) && is_array($result["userfile"]) && 0 < count($result["userfile"])) {
                $uploaded_filename = $result["userfile"][0]->name;
                $file_path = $options["upload_dir"] . $uploaded_filename;
                if (file_exists($file_path)) {
                    $zip = new ZipArchive();
                    if ($zip->open($file_path) === true) {
                        $extract_dir = $options["upload_dir"] . time();
                        mkdir($extract_dir);
                        $zip->extractTo($extract_dir);
                        $zip->close();
                        $filename = "dbface_internal_backup_" . date("YmdHis") . ".zip";
                        $zip_filename = $options["upload_dir"] . $filename;
                        $this->zip->read_dir(USERPATH . "files", false, FCPATH);
                        $success = $this->zip->archive($zip_filename);
                        if ($success) {
                            directory_copy($extract_dir . DIRECTORY_SEPARATOR . "user" . DIRECTORY_SEPARATOR . "files", FCPATH . "user" . DIRECTORY_SEPARATOR . "files");
                            @unlink($file_path);
                            @delete_files($extract_dir, true);
                            @rmdir($extract_dir);
                            echo json_encode(array("status" => 1, "message" => "DbFace has been restored by the backup file"));
                        }
                    }
                }
            }
            echo json_encode(array("status" => 0, "message" => "Uploaded failed, please check more information from the log."));
        }
    }
    public function importdemo()
    {
        if (!$this->_is_admin()) {
            return NULL;
        }
        $current_creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->query("select userid from dc_user where email = ?", array("demo@dbface.com"));
        if ($query->num_rows() == 0) {
            return NULL;
        }
        $demo_userid = $query->row()->userid;
        if ($current_creatorid == $demo_userid) {
            return NULL;
        }
        $query = $this->db->query("select * from dc_conn where creatorid = ?", array($demo_userid));
        if (0 < $query->num_rows()) {
            $conns = $query->result_array();
            foreach ($conns as $conn) {
                $connid = $conn["connid"];
                unset($conn["connid"]);
                $conn["creatorid"] = $current_creatorid;
                $conn["createdate"] = time();
                $this->db->insert("dc_conn", $conn);
                $created_connid = $this->db->insert_id();
                $query = $this->db->query("select * from dc_app where creatoid=? and connid =?", array($demo_userid, $connid));
                if (0 < $query->num_rows()) {
                    $apps = $query->result_array();
                    foreach ($apps as $app) {
                        $appid = $app["appid"];
                        $app["creatorid"] = $current_creatorid;
                        $app["connid"] = $created_connid;
                        unset($app["appid"]);
                        unset($app["embedcode"]);
                        $this->db->insert("dc_app", $app);
                        $created_appid = $this->db->insert_id();
                        $query = $this->db->query("select * from dc_app_options where creatorid=? and connid=? and appid = ?", array($demo_userid, $connid, $appid));
                        if (0 < $query->num_rows()) {
                            $app_options = $query->result_array();
                            foreach ($app_options as $app_option) {
                                $app_option["creatorid"] = $current_creatorid;
                                $app_option["connid"] = $created_connid;
                                $app_option["appid"] = $created_appid;
                                $this->db->insert("dc_app_options", $app_option);
                            }
                        }
                    }
                }
                $query = $this->db->query("select * from dc_tablelinks where creatorid=? and connid = ?", array($demo_userid, $connid));
                if (0 < $query->num_rows()) {
                    $tablelinks = $query->result_array();
                    foreach ($tablelinks as $tablelink) {
                        $tablelink["creatorid"] = $current_creatorid;
                        $tablelink["connid"] = $created_connid;
                        $this->db->insert("dc_tablelinks", $tablelink);
                    }
                }
            }
        }
        $query = $this->db->query("select * from dc_category where creatorid=?", array($demo_userid));
        if (0 < $query->num_rows()) {
            $categories = $query->result_array();
            foreach ($categories as $category) {
                $org_categoryid = $category["categoryid"];
                unset($category["categoryid"]);
                $category["creatorid"] = $current_creatorid;
                $this->db->insert("dc_category", $category);
                $new_categoryid = $this->db->insert_id();
                $this->db->query("update dc_app set categoryid = ? where creatorid = ? and categoryid = ?", array($new_categoryid, $current_creatorid, $org_categoryid));
            }
        }
        $query = $this->db->query("select * from dc_customcss where creatorid=?", array($demo_userid));
        if (0 < $query->num_rows()) {
            $customcsses = $query->result_array();
            foreach ($customcsses as $customcss) {
                $customcss["creatorid"] = $current_creatorid;
                $customcss["date"] = time();
                $this->db->insert("dc_customcss", $customcss);
            }
        }
        $query = $this->db->query("select * from dc_customjs where creatorid=?", array($demo_userid));
        if (0 < $query->num_rows()) {
            $customjses = $query->result_array();
            foreach ($customjses as $customjs) {
                $customjs["creatorid"] = $current_creatorid;
                $customjs["date"] = time();
                $this->db->insert("dc_customjs", $customjs);
            }
        }
    }
    public function startbilling()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $plan = $this->input->post("plan");
        $this->_log_uservisit($creatorid, "billing", $plan);
    }
    public function paypal_create()
    {
        echo json_encode(array("paymentID" => "testpaymentID"));
    }
    public function paypal_execute()
    {
        echo json_encode(array("paymentID" => "testpaymentID", "payerID" => "testpayerId"));
    }
    /**
     * TODO: 清除系统不再活跃的死账号及相关数据。
     *
     * 删除整理目标
     *
     * 1. 账号在非订阅状态并且已经过期或用户手动设置为删除的账号。
     *
     */
    public function purge()
    {
    }
    /**
     * 通过邮件邀请用户注册为子账号。
     */
    public function invite_colleages()
    {
        if (!$this->_is_admin()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied!"));
        } else {
            $gid = $this->input->post("gid");
            $email_str = $this->input->post("emails");
            $emails = preg_split("/(,|;)/", $email_str);
            if (count($emails) == 0) {
                echo json_encode(array("status" => 0, "message" => "No valid emails selected!"));
            } else {
                $email_invite_encrypt = $this->config->item("email_invite_encrypt");
                $creatorid = $this->session->userdata("login_creatorid");
                $base_url = $this->_get_url_base();
                $this->load->library("smartyview");
                $this->load->library("email");
                $this->_init_email_settings();
                $email_title = $this->config->item("invite_colleages_subject");
                if (empty($email_title)) {
                    $email_title = "Welcome to DbFace - It is amazing to have you on board!";
                }
                foreach ($emails as $email) {
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        continue;
                    }
                    $key = md5($email_invite_encrypt . "|" . $creatorid . "|" . $email);
                    $url = $base_url . "?module=Register&action=create_subaccount&creator=" . $creatorid . "&email=" . $email;
                    if (!empty($gid)) {
                        $url .= "&gid=" . $gid;
                    }
                    $url .= "&sign=" . $key;
                    $this->email->to($email);
                    $this->email->subject($email_title);
                    $this->smartyview->assign("url", $url);
                    $this->email->message($this->smartyview->fetch("email/create_subaccount.tpl"));
                    $result = $this->email->send();
                    dbface_log("debug", "Sent invite email", array("email" => $email, "result" => $result));
                }
                echo json_encode(array("status" => 1, "message" => "Invite emails sent!"));
            }
        }
    }
    public function get_global_account_userdata()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $query = $this->db->select("value")->where(array("creatorid" => $creatorid, "name" => "global_account_userdata"))->get("dc_user_options");
            if ($query->num_rows() == 1) {
                $data = $query->row()->value;
                echo $data;
            } else {
                echo "{}";
            }
        }
    }
    public function save_global_account_userdata()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $data = $this->input->post("data");
            $json_data = json_decode($data, true);
            if (json_last_error() != JSON_ERROR_NONE) {
                echo json_encode(array("result" => 0, "message" => json_last_error_msg()));
            } else {
                $query = $this->db->select("1")->where(array("creatorid" => $creatorid, "name" => "global_account_userdata"))->get("dc_user_options");
                if ($query->num_rows() == 0) {
                    $this->db->insert("dc_user_options", array("creatorid" => $creatorid, "name" => "global_account_userdata", "type" => "string", "value" => json_encode($json_data)));
                } else {
                    $this->db->update("dc_user_options", array("value" => json_encode($json_data)), array("creatorid" => $creatorid, "name" => "global_account_userdata"));
                }
                echo json_encode(array("status" => 1));
            }
        }
    }
    public function get_account_userdata()
    {
        if (!$this->_is_admin_or_developer()) {
            echo "";
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $userid = $this->input->post("userid");
            if (empty($creatorid) || empty($userid) || !$this->_contains_subaccount($creatorid, $userid)) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                $query = $this->db->select("value")->where(array("creatorid" => $userid, "name" => "account_userdata"))->get("dc_user_options");
                if ($query->num_rows() == 1) {
                    $data = $query->row()->value;
                    echo $data;
                } else {
                    echo "{}";
                }
            }
        }
    }
    public function save_account_userdata()
    {
        if (!$this->_is_admin_or_developer()) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            $userid = $this->input->post("userid");
            if (empty($creatorid) || empty($userid) || !$this->_contains_subaccount($creatorid, $userid)) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                $data = $this->input->post("data");
                $json_data = json_decode($data, true);
                if (json_last_error() != JSON_ERROR_NONE) {
                    echo json_encode(array("result" => 0, "message" => json_last_error_msg()));
                } else {
                    $query = $this->db->select("1")->where(array("creatorid" => $userid, "name" => "account_userdata"))->get("dc_user_options");
                    if ($query->num_rows() == 0) {
                        $this->db->insert("dc_user_options", array("creatorid" => $userid, "name" => "account_userdata", "type" => "string", "value" => json_encode($json_data)));
                    } else {
                        $this->db->update("dc_user_options", array("value" => json_encode($json_data)), array("creatorid" => $userid, "name" => "account_userdata"));
                    }
                    echo json_encode(array("status" => 1));
                }
            }
        }
    }
    public function api_swagger_json()
    {
        $json_path = FCPATH . "config" . DIRECTORY_SEPARATOR . "swagger.json";
        $content = file_get_contents($json_path);
        echo $content;
    }
    public function api_service()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        $user_options = $this->_get_user_options($creatorid);
        if ($user_options) {
            foreach ($user_options as $user_option) {
                $name = $user_option["name"];
                $this->smartyview->assign($name, $user_option["value"]);
            }
        }
        $this->smartyview->display("api/service.main.tpl");
    }
    public function qrcode_url()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $userid = $this->input->post("userid");
        if (empty($creatorid) || empty($userid)) {
            echo json_encode(array("status" => 0, "message" => "Permission Denied"));
        } else {
            $query = $this->db->select("1")->where(array("userid" => $userid, "creatorid" => $creatorid))->get("dc_user");
            if ($query->num_rows() == 0) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
                $token = array("creatorid" => $creatorid, "userid" => $userid, "action" => "account_login", "date" => time());
                $access_key = $this->config->item("app_access_key");
                $jwt = Firebase\JWT\JWT::encode($token, $access_key);
                $this->load->helper("url");
                $url_base = $this->_get_url_base();
                $url = $url_base . "?token=" . urlencode($jwt);
                echo json_encode(array("status" => 1, "url" => $url));
            }
        }
    }
    public function disable_license()
    {
        $self_host = $this->config->item("self_host");
        if (!$self_host) {
            echo json_encode(array("result" => "fail"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if (empty($creatorid) || !$this->_is_admin()) {
                echo json_encode(array("status" => 0, "message" => "Permission Denied"));
            } else {
                $this->db->delete("dc_user_options", array("name" => "license_code"));
                $this->db->delete("dc_user_options", array("name" => "license_email"));
                $this->db->delete("dc_user_options", array("name" => "license_name"));
                $this->db->update("dc_user", array("expiredate" => time(), "plan" => "level0"), array("creatorid" => 0));
                echo json_encode(array("result" => "ok"));
            }
        }
    }
}

?>