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
class SSOLogin extends BaseController
{
    public function index($username)
    {
        $token = $this->input->get_post("token");
        if (empty($token)) {
            if (empty($username)) {
                exit("Invalid sso login URL. Code: 10001");
            }
            $query = $this->db->select("userid")->where("name", $username)->get("dc_user");
            if ($query->num_rows() != 1) {
                exit("Invalid sso login. Code: 10002");
            }
            $userid = $query->row()->userid;
            $query = $this->db->select("value")->where("creatorid", $userid)->where("name", "sso_login_url")->get("dc_user_options");
            if ($query->num_rows() == 0) {
                exit("Invalid sso login. Code: 10003");
            }
            $sso_iframe_url = get_url_base();
            $sso_login_url = $query->row()->value;
            $this->session->set_userdata("sso_login_creatorid", $userid);
            $this->load->helper("url");
            $sso_login_url = $sso_login_url . (parse_url($sso_login_url, PHP_URL_QUERY) ? "&" : "?") . "&ssocallback=" . $sso_iframe_url;
            redirect($sso_login_url);
        } else {
            $userid = $this->session->userdata("sso_login_creatorid");
            if (empty($userid)) {
                exit("Invalid sso login. Code: 10002");
            }
            require APPPATH . "third_party/php-jwt/vendor/autoload.php";
            $query = $this->db->select("value")->where("creatorid", $userid)->where("name", "sso_secret_token")->get("dc_user_options");
            if ($query->num_rows() == 0) {
                exit("Invalid sso login. Code: 10004");
            }
            $key = $query->row()->value;
            try {
                $decoded = Firebase\JWT\JWT::decode(urldecode($token), $key, array("HS256"));
                $email = $decoded->email;
                $name = $decoded->name;
            } catch (Exception $exception) {
                exit("Invalid token. Code: 10007");
            }
            $query = $this->db->select("userid, creatorid, name, permission, plan")->where(array("email" => $email, "name" => $name))->get("dc_user");
            if ($query->num_rows() == 0) {
                $sso_autocreate_account = $this->config->item("sso_autocreate_account");
                if (!$sso_autocreate_account) {
                    exit("Invalid sso login. Code: 10005");
                }
                $permission = isset($decoded->permission) ? $decoded->permission : false;
                if ($permission != "user" && $permission != "developer") {
                    $permission = $this->config->item("sso_autocreate_account_permission");
                }
                $result_permission = $permission == "developer" ? 1 : 9;
                $gen_password = isset($decoded->password) && !empty($decoded->password) ? $decoded->password : md5(time() . $this->config->item("password_encrypt"));
                $this->db->insert("dc_user", array("creatorid" => $userid, "email" => $email, "name" => $name, "password" => $gen_password, "permission" => $result_permission, "status" => 1, "regip" => $this->input->ip_address(), "regdate" => time(), "plan" => "", "expiredate" => time() + $this->config->item("trial_period_secs")));
                $result_id = $this->db->insert_id();
                $query = $this->db->select("userid, creatorid, name, permission, plan")->where(array("userid" => $result_id))->get("dc_user");
            }
            $row = $query->row();
            $sso_userid = $row->userid;
            $sso_creatorid = $row->creatorid;
            if ($sso_userid != $userid && $sso_creatorid != $userid) {
                exit("Invalid sso login. Code: 10006");
            }
            if (isset($decoded->group) && !empty($decoded->group)) {
                $group_name = $decoded->group;
                $s_query = $this->db->select("groupid")->where(array("name" => $group_name, "creatorid" => $sso_creatorid))->get("dc_usergroup");
                if (0 < $s_query->num_rows()) {
                    $groupid = $s_query->row()->groupid;
                    $this->db->update("dc_user", array("groupid" => $groupid), array("userid" => $sso_userid));
                }
            }
            $this->session->set_userdata("login_userid", $sso_userid);
            $this->session->set_userdata("login_username", $row->name);
            $this->session->set_userdata("login_email", $row->email);
            $this->session->set_userdata("login_permission", $row->permission);
            $this->session->set_userdata("login_plan", $row->plan);
            $this->session->set_userdata("login_creatorid", $userid);
            $this->session->set_userdata("login_sso", true);
            $this->load->helper("url");
            redirect("?module=CoreHome#module=Dashboard");
        }
    }
}

?>