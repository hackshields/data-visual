<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "沒有發現任何遷移";
$lang["migration_not_found"] = "無法根據版本號碼 %s 找到遷移方法";
$lang["migration_sequence_gap"] = "版本遷移存在間隙：%s";
$lang["migration_multiple_version"] = "有多個遷移對應到同一版本號：%s";
$lang["migration_class_doesnt_exist"] = "無法找到遷移類別 \"%s\"";
$lang["migration_missing_up_method"] = "無法找到遷移類別 \"%s\" 中的 \"up\" 方法";
$lang["migration_missing_down_method"] = "無法找到遷移類別 \"%s\" 中的 \" 方法";
$lang["migration_invalid_filename"] = "無效的遷移檔名：\"%s\"";

?>