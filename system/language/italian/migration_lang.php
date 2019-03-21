<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Non ci sono migrazioni disponibili.";
$lang["migration_not_found"] = "Non è stato possibile trovare la migrazione numero: %s.";
$lang["migration_sequence_gap"] = "C'è un divario nella sequenza di migrazione nei pressi del numero di versione: %s.";
$lang["migration_multiple_version"] = "Ci sono differenti migrazioni con lo stesso numero di versione: %s.";
$lang["migration_class_doesnt_exist"] = "Non è possibile trovare la classe migrazione \"%s\".";
$lang["migration_missing_up_method"] = "La classe migrazione \"%s\" è sprovvista del metodo \"up\".";
$lang["migration_missing_down_method"] = "La classe migrazione \"%s\" è sprovvista del metodo \"down\".";
$lang["migration_invalid_filename"] = "La migrazione \"%s\" ha un nome file non valido.";

?>