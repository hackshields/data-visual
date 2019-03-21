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
class Logout extends CI_Controller
{
    public function index()
    {
        $this->load->helper("url");
        $is_sso = $this->session->userdata("login_sso");
        $creatorid = $this->session->userdata("login_creatorid");
        $login_session_id = $this->session->userdata("_login_session_id");
        if (!empty($login_session_id)) {
            $this->db->update("dc_loginsessions", array("logout_at" => time()), array("id" => $login_session_id));
        }
        $this->load->helper("cookie");
        $this->load->helper("clientdata");
        delete_data(KEY_COOKIE);
        $this->session->sess_destroy();
        if (!empty($is_sso) && !empty($creatorid)) {
            $query = $this->db->select("value")->where("creatorid", $creatorid)->where("name", "sso_logout_url")->get("dc_user_options");
            if (0 < $query->num_rows()) {
                $sso_logout_url = $query->row()->value;
                if (!empty($sso_logout_url)) {
                    redirect($sso_logout_url);
                    return NULL;
                }
            }
        }
        redirect("?module=Login&prev=logout");
    }
}

?>