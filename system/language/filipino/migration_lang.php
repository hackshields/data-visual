<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Walang mga migration ang natagpuan.";
$lang["migration_not_found"] = "Walang migration ang mahanap na may numero-bersyon: %s.";
$lang["migration_sequence_gap"] = "May puwang sa migration sequence malapit sa numero-bersyon: %s.";
$lang["migration_multiple_version"] = "Mayroong maraming migration na parehong numero-bersyon: %s.";
$lang["migration_class_doesnt_exist"] = "Ang migration class na \"%s\" ay hindi matagpuan.";
$lang["migration_missing_up_method"] = "Ang migration class na \"%s\" ay kulang ng \"up\" method.";
$lang["migration_missing_down_method"] = "Ang migration class na \"%s\" ay kulang ng \"down\" method.";
$lang["migration_invalid_filename"] = "Migration \"%s\" ay mayroong di wastong pangalan ng file.";

?>