<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "ไม่พบการโยกย้าย";
$lang["migration_not_found"] = "ไม่พบข้อมูลการโยกย้าย หมายเลขเวอร์ชั่น: %s.";
$lang["migration_sequence_gap"] = "มีช่องว่างในลำดับการโยกย้ายที่อยู่ใกล้หมายเลขรุ่นคือ: %s.";
$lang["migration_multiple_version"] = "มีการโยกย้ายหลายรายการที่มีหมายเลขรุ่นเดียวกัน: %s.";
$lang["migration_class_doesnt_exist"] = "ไม่พบ Migration Class \"%s\"";
$lang["migration_missing_up_method"] = "Migration class \"%s\" ไม่มีเมธอด \"up\"";
$lang["migration_missing_down_method"] = "Migration class \"%s\" ไม่มีเมธอด \"down\"";
$lang["migration_invalid_filename"] = "การโยกย้าย \"%s\" มีไฟล์ที่ไม่ถูกต้อง";

?>