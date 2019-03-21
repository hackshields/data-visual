<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "No s'ha trobat cap migració.";
$lang["migration_not_found"] = "No s'ha trobat cap migració amb versió: %s.";
$lang["migration_sequence_gap"] = "Hi ha un forat en les seqüencies de migració prop de la versió: %s.";
$lang["migration_multiple_version"] = "Hi ha multiples migracions amb el mateix número de versió: %s.";
$lang["migration_class_doesnt_exist"] = "La classe de migració \"%s\" no s'ha trobat.";
$lang["migration_missing_up_method"] = "La classe de migració \"%s\" no conté el mètode \"up\".";
$lang["migration_missing_down_method"] = "La classe de migració \"%s\" no conté el mètode \"down\".";
$lang["migration_invalid_filename"] = "Migració \"%s\" conté un nom de fitxer invàlid.";

?>