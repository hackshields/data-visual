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
class MY_Config extends CI_Config
{
    public function __construct()
    {
        $base_url = getenv("base_url");
        if (!empty($base_url)) {
            $this->config =& get_config();
            $this->config["base_url"] = $base_url;
        } else {
            $forward_prefix = isset($_SERVER["X-Forwarded-Prefix"]) ? $_SERVER["X-Forwarded-Prefix"] : false;
            if (empty($forward_prefix) && function_exists("getallheaders")) {
                $headers = getallheaders();
                $forward_prefix = isset($headers["X-Forwarded-Prefix"]) ? $headers["X-Forwarded-Prefix"] : false;
            }
            if (!empty($forward_prefix)) {
                $this->config =& get_config();
                $this->config["base_url"] = $this->base_url($forward_prefix);
                $this->config["dbface_app_url_base"] = $this->base_url($forward_prefix);
            }
        }
        parent::__construct();
    }
}

?>