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
class Swagger extends CI_Controller
{
    public function index()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("swagger/index.tpl");
    }
    public function json()
    {
        $json_content = file_get_contents(FCPATH . "config" . DIRECTORY_SEPARATOR . "swagger.json");
        $creatorid = $this->session->userdata("login_creatorid");
        $query = $this->db->select("name")->where("userid", $creatorid)->get("dc_user");
        $creatorname = $query->row()->name;
        $json_content = str_replace("{creatorid}", $creatorname, $json_content);
        $swagger_json = json_decode($json_content, true);
        $PHP_SELF = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : (isset($_SERVER["SCRIPT_NAME"]) ? $_SERVER["SCRIPT_NAME"] : $_SERVER["ORIG_PATH_INFO"]);
        $PHP_SELF = str_replace("index.php", "", $PHP_SELF);
        $PHP_SELF = str_replace("//", "/", $PHP_SELF);
        $PHP_DOMAIN = $_SERVER["SERVER_NAME"];
        $PHP_PORT = $_SERVER["SERVER_PORT"] == "80" ? "" : ":" . $_SERVER["SERVER_PORT"];
        $url_base = rtrim($PHP_DOMAIN . $PHP_PORT . $PHP_SELF, "/");
        $swagger_json["host"] = $url_base;
        $this->output->set_content_type("application/json")->set_output(json_encode($swagger_json));
    }
}

?>