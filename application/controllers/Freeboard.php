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
class Freeboard extends BaseController
{
    public function index()
    {
        $this->load->library("smartyview");
        $appid = $this->input->get_post("appid");
        $this->smartyview->assign("appid", $appid);
        $mode = $this->input->get_post("mode");
        if (!empty($mode)) {
            $this->smartyview->assign("freeboard_mode", $mode);
        }
        $this->smartyview->display("freeboard/freeboard.tpl");
    }
}

?>