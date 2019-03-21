<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Nem találhatóak migrációk.";
$lang["migration_not_found"] = "A megadott verziószámú migráció nem található: %s.";
$lang["migration_sequence_gap"] = "A migrációk verziószámainak sorrendjében kihagyás található, a következő verziónál: %s.";
$lang["migration_multiple_version"] = "Különöböző migrációk egyező verziószámmal: %s.";
$lang["migration_class_doesnt_exist"] = "A(z) \"%s\" migrációs osztály nem található.";
$lang["migration_missing_up_method"] = "A(z) \"%s\" migrációs osztály \"up\" metódusa nem található.";
$lang["migration_missing_down_method"] = "A(z) \"%s\" migrációs osztály \"down\" metódusa nem található.";
$lang["migration_invalid_filename"] = "A(z) \"%s\" migráció hibás fájlnévvel rendelkezik.";

?>