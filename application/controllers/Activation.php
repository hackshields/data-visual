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
class Activation extends BaseController
{
    public function index()
    {
        $key = $this->input->get_post("KEY");
        $email = $this->input->get_post("e");
        if (!empty($email) && !empty($key) && $this->_verify_email_activation($email, $key)) {
            $this->load->database();
            $this->db->update("dc_user", array("status" => 0), array("email" => $email));
            $this->load->library("smartyview");
            $this->smartyview->assign("email", $email);
            if ($this->db->affected_rows() == 1) {
                $this->smartyview->assign("message", array("status" => 1, "title" => "Success", "content" => "Your account has been activated."));
                $this->session->unset_userdata("email_not_activation");
            }
            $this->smartyview->display("login/login.tpl");
        }
    }
    public function resend()
    {
        $this->load->library("email");
        $this->_init_email_settings();
        $userid = $this->session->userdata("login_userid");
        $query = $this->db->query("select email from dc_user where userid=?", array($userid));
        if ($query->num_rows() == 1) {
            $email = $query->row()->email;
            $this->session->unset_userdata("email_not_activation");
            $this->email->from("support@dbface.com", "DbFace");
            $this->email->to($email);
            $this->email->subject("Activate your DbFace account");
            $this->load->library("smartyview");
            $token = $this->_get_email_activation_encrypt($userid, $email);
            $activation_url = $this->config->item("base_url") . "?module=Activation&KEY=" . $token . "&e=" . $email;
            $this->smartyview->assign("activation_url", $activation_url);
            $this->email->message($this->smartyview->fetch("email/emailactivation.tpl"));
            $this->email->send();
        }
        echo "1";
    }
}

?>