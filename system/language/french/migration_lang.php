<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["migration_none_found"] = "Aucune migration trouvée.";
$lang["migration_not_found"] = "Aucune migration n'a été trouvée avec le numéro de version : %d.";
$lang["migration_sequence_gap"] = "Il y a un trou dans la séquence de migration près de la version numéro : %s.";
$lang["migration_multiple_version"] = "Il y a plusieurs migrations avec le même numéro de version : %d.";
$lang["migration_class_doesnt_exist"] = "La classe de migration \"%s\" n'a pas pu être trouvée.";
$lang["migration_missing_up_method"] = "La classe de migration \"%s\" ne dispose pas d'une méthode 'up'.";
$lang["migration_missing_down_method"] = "La classe de migration \"%s\" ne dispose pas d'une méthode 'down'.";
$lang["migration_invalid_filename"] = "Le nom de fichier de la migration \"%s\" n'est pas valide.";

?>