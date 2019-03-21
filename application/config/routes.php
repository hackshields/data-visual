<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$route["default_controller"] = "Login";
$route["user/(:any)/(:any)"] = "CloudCode/index";
$route["user/(:any)/value/(:any)"] = "CloudCode/value_api";
$route["team/(:any)/(:any)"] = "CloudCode/index";
$route["team/(:any)/value/(:any)"] = "CloudCode/value_api";
$route["api/v8/(.+)"] = "APIV8/index/\$1";
$route["api/(:any)"] = "DbFaceAPI/\$1";
$route["cron/(:any)"] = "Cron/index/\$1";
$route["files/(:any)/(:any)"] = "Files/index/\$1";
$route["filecache"] = "Files/cache";
$route["cmd/(:any)"] = "Command/\$1";
$route["sync/(:any)"] = "Sync/index";
$route["docker/create/(:any)"] = "DockerService/create";
$route["docker/stop/(:any)"] = "DockerService/stop";
$route["docker/all"] = "DockerService/findall";
$route["product/(:any)"] = "Product/index";
$route["version"] = "License/get_latest_version";
$route["create_subaccount"] = "Register/create_subaccount";
$route["translate_uri_dashes"] = false;
$route["(:any)/iframe"] = "SSOLogin/index/\$1";
$route["ipn"] = "License/ipn";
$route["Checkupdate/errorReport"] = "License/errorReport";
$route["malogout"] = "Ma/logout";
$route["ma/(:any)"] = function ($id) {
    return "Embed/ma/" . $id;
};
$route["shared/(:any)"] = function ($id) {
    return "Embed/app/" . $id;
};

?>