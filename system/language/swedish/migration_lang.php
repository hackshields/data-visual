<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Det finns inga migrationer.";
$lang["migration_not_found"] = "Det finns ingen migration med versionsnummer: %s.";
$lang["migration_sequence_gap"] = "Det finns ett glapp i sekvensen av migrationer vid versionsnummer: %s.";
$lang["migration_multiple_version"] = "Det finns flera migrationer med samma versionsnummer: %s.";
$lang["migration_class_doesnt_exist"] = "Migrations-klassen \"%s\" finns inte.";
$lang["migration_missing_up_method"] = "Migrations-klassen \"%s\" saknar en \"up\"-metod.";
$lang["migration_missing_down_method"] = "Migrations-klassen \"%s\" saknar en \"down\"-metod.";
$lang["migration_invalid_filename"] = "Migrationen \"%s\" har ett ogiltigt filnamn.";

?>