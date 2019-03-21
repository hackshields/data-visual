<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Jokių migracijos instrukcijų neaptikta.";
$lang["migration_not_found"] = "Nerasta migracijos instrukcijų su versijos numeriu: %s.";
$lang["migration_sequence_gap"] = "Migracijos instrukcijų sekoje yra tarptas netoli versijos numerio: %s.";
$lang["migration_multiple_version"] = "Yra keletas migracijos instrukcijų su tuo pačiu versijos numeriu: %s.";
$lang["migration_class_doesnt_exist"] = "Nepavyko rasti migracijos klasės „%s“.";
$lang["migration_missing_up_method"] = "Migracijos klasei „%s“ trūksta „aukštyn“ (up) metodo.";
$lang["migration_missing_down_method"] = "Migracijos klasei „%s“ trūksta „žemyn“ (down) metodo.";
$lang["migration_invalid_filename"] = "Migracijos instrukcija „%s“ turi neteisingą failo pavadinimą.";

?>