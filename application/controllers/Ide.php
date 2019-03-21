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
class Ide extends CI_Controller
{
    public function index()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            $this->load->helper("url");
            redirect("?module=Logout");
        } else {
            $ide_settings_path = USERPATH . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . "ide" . DIRECTORY_SEPARATOR;
            if (!file_exists($ide_settings_path)) {
                mkdir($ide_settings_path);
            }
            $query = $this->db->select("userid, email, name")->where("userid", $creatorid)->get("dc_user");
            $row = $query->row();
            $email = $row->email;
            $name = $row->name;
            $uid = $row->userid;
            $query = $this->db->query("select value from dc_user_options where creatorid = ? and name = ?", array($creatorid, "ace_editor_theme"));
            $theme = "default";
            if (0 < $query->num_rows()) {
                $theme = $query->row()->value;
            }
            require_once APPPATH . "third_party/php-jwt/vendor/autoload.php";
            $token = array("email" => $email, "name" => $name, "userid" => $uid, "theme" => $theme, "date" => time());
            $jwt = Firebase\JWT\JWT::encode($token, "jsding@dbface");
            $this->load->helper("url");
            redirect("plugins/ide/" . "?token=" . urlencode($jwt));
        }
    }
}

?>