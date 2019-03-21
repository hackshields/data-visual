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
class Entry extends BaseController
{
    public function _remap($method_in, $params = array())
    {
        array_unshift($params, $method_in);
        $this->_execute($params);
    }
    public function _execute($params)
    {
        $self_host = $this->config->item("self_host");
        $enable_cloudcode = $this->config->item("enable_cloudcode");
        if (!$self_host || $enable_cloudcode) {
            echo json_encode(array("message" => "Cloud code not enabled for the account"));
        } else {
            $creatorid = $this->session->userdata("login_creatorid");
            if (empty($creatorid)) {
                echo json_encode(array("message" => "Permission Denied"));
            } else {
                $path = implode(DIRECTORY_SEPARATOR, $params);
                $file_path = "user" . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . $creatorid . DIRECTORY_SEPARATOR . $path . ".php";
                if (file_exists($file_path)) {
                    $file_name = basename($file_path);
                    if (substr($file_name, 0, 1) === "_") {
                        echo json_encode(array("message" => "Permission Denied"));
                    } else {
                        define("__CLOUD_CODE__", "__CLOUD_CODE__");
                        include $file_path;
                    }
                } else {
                    echo json_encode(array("message" => "Code not found"));
                }
            }
        }
    }
}

?>