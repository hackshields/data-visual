<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "No se ha encontrado ninguna migración.";
$lang["migration_not_found"] = "No se ha encontrado ninguna migración con el número de versión: %s.";
$lang["migration_sequence_gap"] = "Hay un vacío en la migración, cerca del número de versión: %s.";
$lang["migration_multiple_version"] = "Hay múltiples migraciones con el mismo número de versión: %s.";
$lang["migration_class_doesnt_exist"] = "La clase de migración \"%s\" no ha podido ser encontrada.";
$lang["migration_missing_up_method"] = "A la clase de migración \"%s\" le falta el método \"up\".";
$lang["migration_missing_down_method"] = "A la clase de migración \"%s\" le falta el método \"down\".";
$lang["migration_invalid_filename"] = "La migración \"%s\" tiene un nombre de archivo no válido.";

?>