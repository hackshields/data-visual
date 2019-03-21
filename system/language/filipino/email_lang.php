<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["email_must_be_array"] = "Ang email validation method ay dapat ipasa bilang array.";
$lang["email_invalid_address"] = "Hindi wastong email address: %s";
$lang["email_attachment_missing"] = "Hindi mahanap ang sumusunod na mga email attachment: %s";
$lang["email_attachment_unreadable"] = "Hindi mabuksan ang attachment na ito: %s";
$lang["email_no_from"] = "Hindi maaaring magpadala ng mail na walang \"From\" header.";
$lang["email_no_recipients"] = "Dapat mong isama ang mga tatanggap: To, Cc, or Bcc";
$lang["email_send_failure_phpmail"] = "Hindi makapagpadala ng email gamit ang PHP mail(). Ang iyong server ay maaaring hindi naka-configure upang magpadala ng mail gamit ang pamamaraang ito.";
$lang["email_send_failure_sendmail"] = "Hindi makapagpadala ng email gamit ang PHP Sendmail. Ang iyong server ay maaaring hindi naka-configure upang magpadala ng mail gamit ang pamamaraang ito.";
$lang["email_send_failure_smtp"] = "Hindi makapagpadala ng email gamit ang PHP SMTP. Ang iyong server ay maaaring hindi naka-configure upang magpadala ng mail gamit ang pamamaraang ito.";
$lang["email_sent"] = "Ang iyong mensahe ay matagumpay na naipadala gamit ang sumusunod na protocol: %s";
$lang["email_no_socket"] = "Hindi mabuksan ang isang socket sa Sendmail. Pakisuri ang mga setting.";
$lang["email_no_hostname"] = "Hindi ka tumukoy ng isang SMTP hostname.";
$lang["email_smtp_error"] = "Ang sumusunod na SMTP error na naganap: %s";
$lang["email_no_smtp_unpw"] = "Error: Dapat kang magtalaga ng isang SMTP username at password.";
$lang["email_failed_smtp_login"] = "Nabigong ipadala ang AUTH LOGIN command. Error: %s";
$lang["email_smtp_auth_un"] = "Nabigong patotohanan username. Error: %s";
$lang["email_smtp_auth_pw"] = "Nabigong patotohanan password. Error: %s";
$lang["email_smtp_data_failure"] = "Hindi makapagpadala ng data: %s";
$lang["email_exit_status"] = "Exit status code: %s";

?>