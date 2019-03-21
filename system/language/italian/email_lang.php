<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["email_must_be_array"] = "Il metodo di validazione delle email deve essere inviato come array.";
$lang["email_invalid_address"] = "Indirizzo email non valido: %s";
$lang["email_attachment_missing"] = "Impossibile trovare il seguente allegato dell'email: %s";
$lang["email_attachment_unreadable"] = "Impossibile aprire il seguente alleato: %s";
$lang["email_no_from"] = "Impossibile inviare l'email senza il campo header \"Da\".";
$lang["email_no_recipients"] = "E' necessario includere le informazioni: A, Cc, or Ccn";
$lang["email_send_failure_phpmail"] = "Impossibile inviare una mail utilizzando la funzione PHP mail(). Il server sembra non essere configurato per inviare mail utilizzando questo metodo.";
$lang["email_send_failure_sendmail"] = "Impossibile inviare una mail utilizzando la funzione Sendmail(). Il server sembra non essere configurato per inviare mail utilizzando questo metodo.";
$lang["email_send_failure_smtp"] = "Impossibile inviare una mail utilizzando la funzione PHP SMTP. Il server sembra non essere configurato per inviare mail utilizzando questo metodo.";
$lang["email_sent"] = "Il tuo messaggio è stato inviato con successo utilizzando il seguente protocollo: %s";
$lang["email_no_socket"] = "Impossibile aprire un socket con Sendmail. Controllare i settaggi.";
$lang["email_no_hostname"] = "Non è stato specificato un hostname SMTP.";
$lang["email_smtp_error"] = "E' stato riscontrato il seguente errore SMTP: %s";
$lang["email_no_smtp_unpw"] = "Errore: occorre assegnare un SMTP username e password.";
$lang["email_failed_smtp_login"] = "Invio del comando AUTH LOGIN fallito. Errore: %s";
$lang["email_smtp_auth_un"] = "Autenticazione dell'username fallita. Errore: %s";
$lang["email_smtp_auth_pw"] = "Autenticazione della password fallita. Errore: %s";
$lang["email_smtp_data_failure"] = "Impossibile inviare i dati: %s";
$lang["email_exit_status"] = "Codice di status di uscita: %s";

?>