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
class Register extends BaseController
{
    public function index()
    {
        $tag = $this->input->post("tag");
        if ($tag == "confirm") {
            $this->_do_register();
        } else {
            $this->load->library("smartyview");
            $logo_settings = $this->config->item("login_logo_settings");
            if ($logo_settings) {
                $this->smartyview->assign("login_logo_settings", $logo_settings);
            }
            $this->smartyview->display("new/register.tpl");
        }
    }
    /**
     * display create subaccount from email link
     */
    public function create_subaccount()
    {
        $creator = $this->input->get_post("creator");
        $email = $this->input->get_post("email");
        $sign = $this->input->get_post("sign");
        $gid = $this->input->get_post("gid");
        if (empty($creator) || empty($email) || empty($sign)) {
            echo "Page Not Found";
        } else {
            $email_invite_encrypt = $this->config->item("email_invite_encrypt");
            $valid_sign = md5($email_invite_encrypt . "|" . $creator . "|" . $email);
            if ($valid_sign != $sign) {
                echo "Page Not Found";
            } else {
                $this->load->library("smartyview");
                $this->smartyview->assign("creator", $creator);
                $this->smartyview->assign("sign", $sign);
                $this->smartyview->assign("gid", $gid);
                $tag = $this->input->post("tag");
                if ($tag == "confirm") {
                    $this->_do_register_subaccount();
                } else {
                    $logo_settings = $this->config->item("login_logo_settings");
                    if ($logo_settings) {
                        $this->smartyview->assign("login_logo_settings", $logo_settings);
                    }
                    $this->smartyview->assign("email", $email);
                    $this->smartyview->display("new/register.subaccount.tpl");
                }
            }
        }
    }
    public function r()
    {
    }
    public function _display_page($email, $name, $messages)
    {
        $this->load->library("smartyview");
        $this->smartyview->assign("email", $email);
        $this->smartyview->assign("username", $name);
        $this->smartyview->assign("message", array("title" => "Error", "content" => $messages));
        $logo_settings = $this->config->item("login_logo_settings");
        if ($logo_settings) {
            $this->smartyview->assign("login_logo_settings", $logo_settings);
        }
        $this->smartyview->display("new/register.tpl");
    }
    public function _display_subaccount_page($email, $name, $messages)
    {
        $this->smartyview->assign("email", $email);
        $this->smartyview->assign("username", $name);
        $this->smartyview->assign("message", array("title" => "Error", "content" => $messages));
        $logo_settings = $this->config->item("login_logo_settings");
        if ($logo_settings) {
            $this->smartyview->assign("login_logo_settings", $logo_settings);
        }
        $this->smartyview->display("new/register.subaccount.tpl");
    }
    public function _do_register_subaccount()
    {
        $creator = trim($this->input->post("creator"));
        $name = trim($this->input->post("username"));
        $email = trim($this->input->post("email"));
        $password = trim($this->input->post("password"));
        $password2 = trim($this->input->post("password2"));
        $query = $this->db->select("1")->where(array("creatorid" => 0, "userid" => $creator, "permission" => 0, "status" => 0))->get("dc_user");
        if ($query->num_rows() == 0) {
            $messages = "Expired invitation email, creator does not exists.";
            $this->_display_subaccount_page($email, $name, $messages);
        }
        if (empty($name) || empty($email) || empty($password) || empty($password2)) {
            $messages = "Please fill the required information";
            $this->_display_subaccount_page($email, $name, $messages);
        } else {
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $messages = "Please enter a valid email address.";
                $this->_display_subaccount_page($email, $name, $messages);
            } else {
                if ($name == $password) {
                    $messages = "Having the same password as your username will make you a high risk for hacking";
                    $this->_display_subaccount_page($email, $name, $messages);
                } else {
                    if ($password != $password2) {
                        $messages = "Password is not match";
                        $this->_display_subaccount_page($email, $name, $messages);
                    } else {
                        if (strlen($password) < 6) {
                            $messages = "Password is too short, at least 6 characters";
                            $this->_display_subaccount_page($email, $name, $messages);
                        } else {
                            if (64 < strlen($name)) {
                                $messages = "Username is too long, less then 64 characters";
                                $this->_display_subaccount_page($email, $name, $messages);
                            } else {
                                if (!preg_match("#^[a-zA-Z0-9_@.]+\$#", $name)) {
                                    $messages = "Username is not allowd, please remove the special characters.";
                                    $this->_display_subaccount_page($email, $name, $messages);
                                } else {
                                    if (in_array($name, $this->config->item("reserved_username"))) {
                                        $messages = "Username is reserved, please choose another one.";
                                        $this->_display_subaccount_page($email, $name, $messages);
                                    } else {
                                        if (!empty($email)) {
                                            $this->config->load("disallowed_domain", true, true);
                                            $ban_email_domains = $this->config->item("ban_email_domains", "disallowed_domain");
                                            foreach ($ban_email_domains as $ban_email) {
                                                if (preg_match("/\\b" . $ban_email . "\\b/i", $email)) {
                                                    $messages = "Sorry, that email address is not available.";
                                                    $this->_display_subaccount_page($email, $name, $messages);
                                                    return NULL;
                                                }
                                            }
                                        }
                                        $query = $this->db->query("select 1 from dc_user where email=?", array($email));
                                        if (0 < $query->num_rows()) {
                                            $messages = "Email has been used, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                            $this->_display_subaccount_page($email, $name, $messages);
                                            return NULL;
                                        }
                                        $query = $this->db->query("select 1 from dc_user where name=?", array($name));
                                        if (0 < $query->num_rows()) {
                                            $messages = "Username is used, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                            $this->_display_subaccount_page($email, $name, $messages);
                                            return NULL;
                                        }
                                        $creator_info = $this->db->select("plan")->where("userid", $creator)->get("dc_user");
                                        $plan = $creator_info->row()->plan;
                                        $gid = $this->input->post("gid");
                                        if (!empty($gid)) {
                                            $query = $this->db->select("1")->where(array("creatorid" => $creator, "groupid" => $gid))->get("dc_usergroup");
                                            if ($query->num_rows() == 0) {
                                                $gid = "";
                                            }
                                        }
                                        $this->db->insert("dc_user", array("creatorid" => $creator, "email" => $email, "name" => $name, "password" => md5($password . $this->config->item("password_encrypt")), "permission" => 9, "status" => 1, "regip" => $this->input->ip_address(), "regdate" => time(), "plan" => $plan, "groupid" => $gid, "expiredate" => time() + $this->config->item("trial_period_secs")));
                                        $this->load->helper("url");
                                        redirect("?module=Login");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function _do_register()
    {
        $name = trim($this->input->post("username"));
        $email = trim($this->input->post("email"));
        $password = trim($this->input->post("password"));
        $password2 = trim($this->input->post("password2"));
        $messages = "";
        if (empty($name) || empty($email) || empty($password) || empty($password2)) {
            $messages = "Please fill the required information";
            $this->_display_page($email, $name, $messages);
        } else {
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $messages = "Please enter a valid email address.";
                $this->_display_page($email, $name, $messages);
            } else {
                if ($name == $password) {
                    $messages = "Having the same password as your username will make you a high risk for hacking";
                    $this->_display_page($email, $name, $messages);
                } else {
                    if ($password != $password2) {
                        $messages = "Password is not match";
                        $this->_display_page($email, $name, $messages);
                    } else {
                        if (strlen($password) < 6) {
                            $messages = "Password is too short, at least 6 characters";
                            $this->_display_page($email, $name, $messages);
                        } else {
                            if (64 < strlen($name)) {
                                $messages = "Username is too long, less then 64 characters";
                                $this->_display_page($email, $name, $messages);
                            } else {
                                if (!preg_match("#^[a-zA-Z0-9_@.]+\$#", $name)) {
                                    $messages = "Username is not allowd, please remove the special characters.";
                                    $this->_display_page($email, $name, $messages);
                                } else {
                                    if (in_array($name, $this->config->item("reserved_username"))) {
                                        $messages = "Username is reserved, please choose another one.";
                                        $this->_display_page($email, $name, $messages);
                                    } else {
                                        if (!empty($email)) {
                                            $this->config->load("disallowed_domain", true, true);
                                            $ban_email_domains = $this->config->item("ban_email_domains", "disallowed_domain");
                                            foreach ($ban_email_domains as $ban_email) {
                                                if (preg_match("/\\b" . $ban_email . "\\b/i", $email)) {
                                                    $messages = "Sorry, that email address is not available.";
                                                    $this->_display_page($email, $name, $messages);
                                                    return NULL;
                                                }
                                            }
                                        }
                                        $query = $this->db->query("select 1 from dc_user where email=?", array($email));
                                        if (0 < $query->num_rows()) {
                                            $messages = "Email has been used, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                            $this->_display_page($email, $name, $messages);
                                            return NULL;
                                        }
                                        $query = $this->db->query("select 1 from dc_user where name=?", array($name));
                                        if (0 < $query->num_rows()) {
                                            $messages = "Username is used, <a href='?module=Login&action=forgotpassword'>Forgot the password?</a>";
                                            $this->_display_page($email, $name, $messages);
                                            return NULL;
                                        }
                                        $cooltime = time() - 3 * 60;
                                        $ip_address = $this->input->ip_address();
                                        $query = $this->db->query("select 1 from dc_user where regip = ? and regdate > " . $cooltime . " and creatorid is null", array($ip_address));
                                        if (0 < $query->num_rows()) {
                                            $messages = "400. That’s an error.";
                                            $this->_display_page($email, $name, $messages);
                                            return NULL;
                                        }
                                        $this->db->insert("dc_user", array("email" => $email, "name" => $name, "password" => md5($password . $this->config->item("password_encrypt")), "permission" => 0, "status" => 9, "regip" => $ip_address, "plan" => "level1", "regdate" => time(), "expiredate" => time() + $this->config->item("trial_period_secs")));
                                        $userid = $this->db->insert_id();
                                        if ($this->db->affected_rows() == 1) {
                                            $this->load->library("email");
                                            $this->_init_email_settings();
                                            $this->email->from("support@dbface.com", "DbFace");
                                            $this->email->to($email);
                                            $this->email->cc("ding.jiansheng@dbface.com");
                                            $this->email->subject("Welcome to DbFace - It is amazing to have you on board!");
                                            $this->load->library("smartyview");
                                            $token = $this->_get_email_activation_encrypt($userid, $email);
                                            $activation_url = $this->config->item("base_url") . "?module=Activation&KEY=" . $token . "&e=" . $email;
                                            $this->smartyview->assign("activation_url", $activation_url);
                                            $this->smartyview->assign("name", $name);
                                            $this->email->message($this->smartyview->fetch("email/register.tpl"));
                                            $this->email->send();
                                            $this->load->helper("url");
                                            redirect("?module=Login");
                                            return NULL;
                                        }
                                        $messages = "401. That’s an error.";
                                        $this->_display_page($email, $name, $messages);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>