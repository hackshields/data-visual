<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Nie znaleziono żadnych migracji.";
$lang["migration_not_found"] = "Nie można znaleźć migracji o numerze wersji: %s.";
$lang["migration_sequence_gap"] = "Istnieje luka w wersjach migracji, koło numeru: %s.";
$lang["migration_multiple_version"] = "Jest wiele migracji o tym samym numerze wersji: %d.";
$lang["migration_class_doesnt_exist"] = "Klasa migracji %s nie mogła zostać znaleziona.";
$lang["migration_missing_up_method"] = "Klasie migracji %s brakuje metody \"up\".";
$lang["migration_missing_down_method"] = "Klasie migracji %s brakuje metody \"down\".";
$lang["migration_invalid_filename"] = "Migracja %s ma niepoprawną nazwę pliku.";

?>