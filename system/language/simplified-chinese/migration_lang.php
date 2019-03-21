<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "不需迁移。  ";
$lang["migration_not_found"] = "无法根据版本号找到迁移方法： %s。";
$lang["migration_sequence_gap"] = "版本迁移存在鸿沟：%s。";
$lang["migration_multiple_version"] = "多个迁移对应同一版本号：%s。";
$lang["migration_class_doesnt_exist"] = "无法找到迁移类 \"%s\"。";
$lang["migration_missing_up_method"] = "无法找到迁移类 \"%s\" 中的 \"up\" 方法。";
$lang["migration_missing_down_method"] = "无法找到迁移类 \"%s\" 中的 \" 方法。";
$lang["migration_invalid_filename"] = "无效的迁移文件名：\"%s\"。";

?>