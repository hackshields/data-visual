<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Δεν βρέθηκαν migrations.";
$lang["migration_not_found"] = "Όχι migrations, θα μπορούσε να βρεθεί με τον αριθμό έκδοσης: %s.";
$lang["migration_sequence_gap"] = "Υπάρχει ένα κενό στην ακολουθία του migration στους κοντινούς αριθμούς έκδοσης: %s.";
$lang["migration_multiple_version"] = "Υπάρχουν πολλαπλά migrations με τον ίδιο αριθμό έκδοσης: %s.";
$lang["migration_class_doesnt_exist"] = "Η migration class \"%s\" δεν μπορεί να βρεθεί.";
$lang["migration_missing_up_method"] = "Απο την migration class \"%s\" λείπει η \"up\" μέθοδος.";
$lang["migration_missing_down_method"] = "Απο την migration class \"%s\" λείπει η \"down\" μέθοδος.";
$lang["migration_invalid_filename"] = "Migration \"%s\" έχει ένα μη εγκυρο όνομα.";

?>