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
class Ma extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $creatorid = $this->session->userdata("login_creatorid");
        if (empty($creatorid)) {
            $this->smartyview->display("mobile/login.tpl");
        } else {
            $query = $this->db->query("select value from dc_user_options where creatorid=? and name=?", array($creatorid, "customlogo"));
            if (0 < $query->num_rows()) {
                $customlogo = $query->row()->value;
                $this->smartyview->assign("customlogo", $customlogo);
            }
            $this->smartyview->display("mobile/index.tpl");
        }
    }
    public function nav()
    {
        $creatorid = $this->session->userdata("login_creatorid");
        $categories = $this->_get_categories($creatorid);
        $category_by_key = array();
        $category_icons = array();
        $category_sorts = array();
        foreach ($categories as $category) {
            $category_by_key[$category["categoryid"]] = $category["name"];
            $category_icons[$category["name"]] = $category["icon"];
            $category_sorts[$category["name"]] = $category["sort"];
        }
        $permission = $this->session->userdata("login_permission");
        if ($permission == 9) {
            $apps = $this->_get_user_apps($creatorid, $this->session->userdata("login_userid"));
        } else {
            $apps = $this->_get_apps_by_status($creatorid, "publish");
        }
        $categoryapps = array();
        foreach ($apps as $app) {
            $categoryid = $app["categoryid"];
            if ($categoryid == 65535) {
                continue;
            }
            $categoryname = $this->config->item("default_category_name");
            if (array_key_exists($categoryid, $category_by_key)) {
                $categoryname = $category_by_key[$categoryid];
            }
            if (!array_key_exists($categoryname, $categoryapps)) {
                $categoryapps[$categoryname] = array();
            }
            $app["categoryname"] = $categoryname;
            $categoryapps[$categoryname][] = $app;
        }
        uksort($categoryapps, function ($a, $b) use($category_sorts) {
            return $category_sorts[$a] - $category_sorts[$b];
        });
        if (function_exists("sort_sidemenu")) {
            $categoryapps = sort_sidemenu($categoryapps);
        }
        $mobile_apps = array();
        foreach ($categoryapps as $category => $apps) {
            foreach ($apps as $app) {
                if (!empty($app["name"])) {
                    $item = array();
                    $item["section"] = $category;
                    if (isset($category_icons[$category])) {
                        $item["icon"] = "fa " . $category_icons[$category];
                    } else {
                        $item["icon"] = "fa fa-area-chart";
                    }
                    $item["text"] = $app["name"];
                    $item["url"] = "ma/" . $app["appid"];
                    $item["title"] = array("kendo-ui" => $app["title"]);
                    $item["meta"] = array("kendo-ui" => "");
                    $mobile_apps[] = $item;
                }
            }
        }
        echo json_encode($mobile_apps);
    }
    public function logout()
    {
        $this->load->helper("cookie");
        $this->load->helper("clientdata");
        delete_data(KEY_COOKIE);
        $this->session->sess_destroy();
        $this->load->library("smartyview");
        $this->smartyview->display("mobile/login.part.tpl");
    }
    public function get_qrcode_url()
    {
        $base_url = $this->_get_url_base();
        $qr_url = $base_url . "/ma";
        echo json_encode(array("status" => 1, "url" => $qr_url));
    }
}

?>