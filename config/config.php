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
$config["production"] = true;
$config["enable_cloudcode"] = true;
$config["reserved_instance"] = true;
$config["self_host"] = true;
$config["strict_email_check"] = false;
$config["dbface_master_host"] = false;
include "config.inc.php";
if (file_exists(USERPATH . "data" . DIRECTORY_SEPARATOR . "config.php")) {
    require_once USERPATH . "data" . DIRECTORY_SEPARATOR . "config.php";
}

?>