<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("Directe toegang tot scripts is niet toegestaan");
$lang["migration_none_found"] = "Er is geen enkele migratie gevonden.";
$lang["migration_not_found"] = "Een migratie met dit versienummer is onvindbaar. %s.";
$lang["migration_sequence_gap"] = "Er ontbreekt een deel in de migratiereeks omstreeks dit versienummer. %s.";
$lang["migration_multiple_version"] = "Er zijn meerdere migraties met hetzelfde versienummer: %s.";
$lang["migration_class_doesnt_exist"] = "De migratie-class \"%s\" kon niet worden gevonden.";
$lang["migration_missing_up_method"] = "De migratie-class \"%s\" mist een \"up\"-methode";
$lang["migration_missing_down_method"] = "De migratie-class \"%s\" mist een \"down\"-methode.";
$lang["migration_invalid_filename"] = "De migratie \"%s\" heeft een ongeldige bestandsnaam.";

?>