<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("Nije dozvoljen direktan pristup");
$lang["migration_none_found"] = "Migracije nisu pronađene.";
$lang["migration_not_found"] = "Nisu pronađene migracije sa verzijom: %s.";
$lang["migration_sequence_gap"] = "Postoji praznina u migracijionoj sekvenci blizu verzije: %s.";
$lang["migration_multiple_version"] = "Postoji više migracija sa istom verzijom broj: %s.";
$lang["migration_class_doesnt_exist"] = "Migraciona klasa \"%s\" ne može biti pronađena.";
$lang["migration_missing_up_method"] = "Migracionoj klasi \"%s\" nedostaje \"up\" metod.";
$lang["migration_missing_down_method"] = "Migracionoj klasi \"%s\" nedostaje \"down\" metod.";
$lang["migration_invalid_filename"] = "Migracija \"%s\" ima nepostojeće ime fajla.";

?>