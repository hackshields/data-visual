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
defined("BASEPATH") or exit("No direct script access allowed");
$config["migration_enabled"] = false;
$config["migration_type"] = "timestamp";
$config["migration_table"] = "migrations";
$config["migration_auto_latest"] = true;
$config["migration_version"] = 0;
$config["migration_path"] = APPPATH . "migrations/";

?>