<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Ingen migrasjoner ble funnet.";
$lang["migration_not_found"] = "Ingen migrasjon funner med følgende versjonsnummer: %s.";
$lang["migration_sequence_gap"] = "Det er en glippe i migrasjonssekvensen nær versjonsnummer: %s.";
$lang["migration_multiple_version"] = "Det er flere migrasjoner med samme versjonsnummer: %s.";
$lang["migration_class_doesnt_exist"] = "Fant ikke migrasjonsklassen «%s».";
$lang["migration_missing_up_method"] = "Migrasjonsklassen «%s» mangler en «up»-metode.";
$lang["migration_missing_down_method"] = "Migrasjonsklassen «%s» mangler en «down»-metode.";
$lang["migration_invalid_filename"] = "Migrasjon «%s» har et ugyldig filnavn.";

?>