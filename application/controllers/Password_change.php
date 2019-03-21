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
class Password_change extends BaseController
{
    public function test()
    {
        $this->load->library("smartyview");
        $this->smartyview->assign("message", "This is a test page");
        $this->smartyview->assign("KEY", "dsfafdsfds");
        $this->smartyview->assign("e", "jsding2006@gmail.com");
        $this->smartyview->display("new/resetpassword.tpl");
    }
    public function index()
    {
        $key = $this->input->get_post("KEY");
        $email = $this->input->get_post("e");
        if (!empty($key) && !empty($email) && $this->_verify_password_change_request($email, $key)) {
            $tag = $this->input->post("tag");
            if ($tag == "confirm") {
                $password = $this->input->post("password");
                $password2 = $this->input->post("password2");
                $hasError = false;
                $message = "";
                if ($password != $password2) {
                    $message = "Password is not match";
                    $hasError = true;
                }
                if (!$hasError) {
                    $this->load->database();
                    $this->db->update("dc_user", array("password" => md5($password . $this->config->item("password_encrypt"))), array("email" => $email));
                    $this->_remove_password_change_key($email);
                    $this->load->helper("url");
                    redirect("?module=Login");
                } else {
                    $this->load->library("smartyview");
                    $this->smartyview->assign("message", $message);
                    $this->smartyview->assign("KEY", $key);
                    $this->smartyview->assign("e", $email);
                    $this->smartyview->display("new/resetpassword.tpl");
                }
            } else {
                $this->load->library("smartyview");
                $this->smartyview->assign("KEY", $key);
                $this->smartyview->assign("e", $email);
                $this->smartyview->display("new/resetpassword.tpl");
            }
        } else {
            $this->load->helper("url");
            redirect("?module=Login");
        }
    }
}

?>