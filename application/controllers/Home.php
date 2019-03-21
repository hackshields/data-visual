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
class Home extends CI_Controller
{
    public function index()
    {
        $this->load->library("smartyview");
        $lang = $this->input->get("lang");
        if ($lang == "zh") {
            $this->config->set_item("language", "zh-CN");
        }
        $this->smartyview->display("sites/" . $this->config->item("language") . "/index.tpl");
    }
    public function features()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("sites/" . $this->config->item("language") . "/features.tpl");
    }
    public function service()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("sites/" . $this->config->item("language") . "/service.tpl");
    }
    public function pricing()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("sites/" . $this->config->item("language") . "/pricing.tpl");
    }
    public function documents()
    {
        $this->load->library("smartyview");
        $this->smartyview->display("sites/documents.tpl");
    }
    public function support()
    {
        $this->load->library("smartyview");
        $u = $this->input->get("u");
        if ($u == "newticket") {
            $this->smartyview->assign("target_url", "https://support.zoho.com/portal/dbface/newticket");
        }
        $this->smartyview->display("sites/" . $this->config->item("language") . "/support.tpl");
    }
}

?>