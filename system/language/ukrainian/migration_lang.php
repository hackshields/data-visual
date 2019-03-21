<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Не знайдено жодної міграції.";
$lang["migration_not_found"] = "Не знайдено жодної міграції з версією: %s.";
$lang["migration_sequence_gap"] = "Виявлено розрив в послідовності біля версії: %s.";
$lang["migration_multiple_version"] = "Існує кілька міграцій з однією версією: %s.";
$lang["migration_class_doesnt_exist"] = "Неможливо знайти клас міграції \"%s\".";
$lang["migration_missing_up_method"] = "В класі міграції \"%s\" відсунтій \"up\" метод.";
$lang["migration_missing_down_method"] = "В класі міграції \"%s\" відсутній \"down\" метод.";
$lang["migration_invalid_filename"] = "Міграція \"%s\" має недопустиме ім’я файлу.";

?>