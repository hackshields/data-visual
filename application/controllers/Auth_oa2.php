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
class Auth_oa2 extends CI_Controller
{
    public function _update_facebook_info($fbID, $userid, $name)
    {
        $this->db->update("dc_user", array("name" => $name), array("userid" => $userid));
        $query = $this->db->query("select 1 from dc_user_options where creatorid=? and name=?", array($userid, "useravatar"));
        if ($query->num_rows() == 0) {
            $this->db->insert("dc_user_options", array("creatorid" => $userid, "name" => "useravatar", "type" => "string", "value" => "fb:" . $fbID));
        } else {
            $this->db->update("dc_user_options", array("value" => "fb:" . $fbID), array("creatorid" => $userid, "name" => "useravatar"));
        }
    }
    public function facebook()
    {
        $email = $this->input->get("email");
        $name = $this->input->get("name");
        $fbID = $this->input->get("id");
        $this->load->database();
        $username = $name;
        $password = md5("facebook" . $fbID . $email . $this->config->item("password_encrypt"));
        $query = $this->db->query("select userid, creatorid, name, email, permission, plan from dc_user where email = ? and name=? and password=?", array($email, $username, $password));
        if (0 < $query->num_rows()) {
            $row = $query->row();
            $this->_update_facebook_info($fbID, $row->userid, $name);
            $this->_login_user($row);
        } else {
            $query = $this->db->query("select 1 from dc_user where email = ?", array($email));
            if ($query->num_rows() == 0) {
                $this->db->insert("dc_user", array("email" => $email, "name" => $username, "password" => $password, "permission" => 0, "status" => 0, "plan" => "level1", "regip" => $this->input->ip_address(), "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")));
                $uid = $this->db->insert_id();
                $this->_update_facebook_info($fbID, $uid, $name);
                if ($this->db->affected_rows() == 1) {
                    $this->load->library("email");
                    $this->email->from("support@dbface.com", "DbFace");
                    $this->email->to($email);
                    $this->email->subject("Welcome to DbFace");
                    $this->load->library("smartyview");
                    $this->smartyview->assign("name", $username);
                    $this->email->message($this->smartyview->fetch("email/register.tpl"));
                    $this->email->send();
                    $this->load->helper("url");
                    $query = $this->db->query("select userid, creatorid, name, email, permission, plan from dc_user where email = ?", array($email));
                    $this->_login_user($query->row());
                    return NULL;
                }
                $this->_display_login($email, "Sorry, Can not login, please try again.");
            } else {
                $this->_display_login($email, "Email already used, forgot password?");
            }
        }
    }
    public function session()
    {
        $provider_name = "google";
        $this->load->config("oauth2", true);
        $client_id = $this->config->item($provider_name . "_id", "oauth2");
        $client_secret = $this->config->item($provider_name . "_secret", "oauth2");
        $redirect_uri = "https://dashboard.dbface.com/?module=Auth_oa2&action=session";
        require APPPATH . "third_party/google-api-php-client/src/Google/Client.php";
        $client = new Google_Client();
        $client->setClientId($client_id);
        $client->setClientSecret($client_secret);
        $client->setRedirectUri($redirect_uri);
        $client->setScopes("email");
        if (isset($_REQUEST["logout"])) {
            $this->session->unset_userdata("access_token");
        }
        $code = $this->input->get("code");
        if (!empty($code)) {
            $client->authenticate($code);
            $this->session->set_userdata("access_token", $client->getAccessToken());
            $this->load->helper("url");
            redirect($redirect_uri);
        } else {
            $access_token = $this->session->userdata("access_token");
            if (!empty($access_token)) {
                $client->setAccessToken($access_token);
                if ($client->getAccessToken()) {
                    $token_data = $client->verifyIdToken()->getAttributes();
                    $userID = $token_data["payload"]["sub"];
                    $email = $token_data["payload"]["email"];
                    $this->load->database();
                    $username = "google." . $userID;
                    $password = md5("google" . $userID . $email . $this->config->item("password_encrypt"));
                    $query = $this->db->query("select userid, creatorid, name, email, permission, plan from dc_user where email = ? and name=? and password=?", array($email, $username, $password));
                    if (0 < $query->num_rows()) {
                        $row = $query->row();
                        $this->_login_user($row);
                        return NULL;
                    }
                    $query = $this->db->query("select 1 from dc_user where email = ? ", array($email));
                    if ($query->num_rows() == 0) {
                        $this->db->insert("dc_user", array("email" => $email, "name" => $username, "password" => $password, "permission" => 0, "status" => 0, "plan" => "level1", "regip" => $this->input->ip_address(), "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")));
                        if ($this->db->affected_rows() == 1) {
                            $this->load->library("email");
                            $this->email->from("support@dbface.com", "DbFace");
                            $this->email->to($email);
                            $this->email->subject("Welcome to DbFace");
                            $this->load->library("smartyview");
                            $this->smartyview->assign("name", $username);
                            $this->email->message($this->smartyview->fetch("email/register.tpl"));
                            $this->email->send();
                            $this->load->helper("url");
                            $query = $this->db->query("select userid, creatorid, name, email, permission, plan from dc_user where email = ?", array($email));
                            $this->_login_user($query->row());
                            return NULL;
                        }
                        $this->_display_login($email, "Sorry, Can not login, please try again.");
                    } else {
                        $this->_display_login($email, "Email already used, forgot password?");
                    }
                }
            } else {
                $authUrl = $client->createAuthUrl();
                $this->load->helper("url");
                redirect($authUrl);
            }
        }
    }
    public function _display_login($email, $message)
    {
        $this->load->library("smartyview");
        $this->smartyview->assign("email", $email);
        $this->smartyview->assign("message", array("title" => "Error", "content" => $message));
        $this->smartyview->display("login/login.tpl");
    }
    public function _login_user($row)
    {
        $this->session->set_userdata("login_userid", $row->userid);
        $this->session->set_userdata("login_username", $row->name);
        $this->session->set_userdata("login_permission", $row->permission);
        $this->session->set_userdata("login_plan", $row->plan);
        $creatorid = $row->creatorid;
        if (!$creatorid) {
            $creatorid = $row->userid;
        }
        $this->session->set_userdata("login_thirdpart", true);
        $this->session->set_userdata("login_creatorid", $creatorid);
        $this->load->helper("url");
        redirect("?module=CoreHome#module=Dashboard");
    }
}

?>