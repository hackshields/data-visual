<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Nu s-au găsit migrații.";
$lang["migration_not_found"] = "Nu s-au putut găsi migrații cu numărul: %s.";
$lang["migration_sequence_gap"] = "Există un decalaj în secvența de migrare aproape de versiunea nr.: %s.";
$lang["migration_multiple_version"] = "Există mai multe migrații cu aceleași versiune de număr: %s.";
$lang["migration_class_doesnt_exist"] = "Clasa migrației \"%s\" nu a fost găsită.";
$lang["migration_missing_up_method"] = "Clasei migrației \"%s\" îi lipsește o metodă de tip \"up\".";
$lang["migration_missing_down_method"] = "Clasei migrației \"%s\" îi lipsește o metodă de tip \"down\".";
$lang["migration_invalid_filename"] = "Migrația \"%s\" are un nume invalid..";

?>