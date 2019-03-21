<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["email_must_be_array"] = "Az email validációs metódusnak tömböt kell átadni.";
$lang["email_invalid_address"] = "Érvénytelen email cím: %s";
$lang["email_attachment_missing"] = "Nem találhatóak a következő email csatolmányok: %s";
$lang["email_attachment_unreadable"] = "A következő csatolmányt nem sikerült megnyitni: %s";
$lang["email_no_from"] = "Nem lehetséges emailt küldeni a \"From\" fejléc megadása nélkül.";
$lang["email_no_recipients"] = "Címzetteket meg kell adni: To, Cc, vagy Bcc";
$lang["email_send_failure_phpmail"] = "Nem sikerült emailt küldeni a PHP mail() függvénnyel.  Lehetséges, hogy a szerver nincs ennek a módszernek a használatára beállítva.";
$lang["email_send_failure_sendmail"] = "Nem sikerült emailt küldeni a PHP Sendmail használatával.  Lehetséges, hogy a szerver nincs ennek a módszernek a használatára beállítva.";
$lang["email_send_failure_smtp"] = "Nem sikerült emailt küldeni a PHP SMTP használatával.  Lehetséges, hogy a szerver nincs ennek a módszernek a használatára beállítva.";
$lang["email_sent"] = "Az üzenet sikeresen elküldésre került a következő protokoll használatával: %s";
$lang["email_no_socket"] = "Nem sikerült socketet nyitni a Sendmailhez. Kérjük ellenőrizze a beállításokat!";
$lang["email_no_hostname"] = "Nem adott meg SMTP kiszolgálónevet.";
$lang["email_smtp_error"] = "A következő SMTP hiba következett be: %s";
$lang["email_no_smtp_unpw"] = "Hiba: Az SMTP felhasználónév és jelszó megadása kötelező.";
$lang["email_failed_smtp_login"] = "Nem sikerült az AUTH LOGIN parancs küldése. Hiba: %s";
$lang["email_smtp_auth_un"] = "A felhasználónév hitelesítése sikertelen. Hiba: %s";
$lang["email_smtp_auth_pw"] = "A jelszó hitelesítése sikertelen. Hiba: %s";
$lang["email_smtp_data_failure"] = "Az adatküldés nem lehetséges: %s";
$lang["email_exit_status"] = "Kilépési kód: %s";

?>