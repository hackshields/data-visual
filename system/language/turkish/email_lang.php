<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
$lang["email_must_be_array"] = "Email doğrulama metoduna bir dizi verilmeli.";
$lang["email_invalid_address"] = "Geçersiz email adresi: %s";
$lang["email_attachment_missing"] = "Email eklentisi bulunamıyor: %s";
$lang["email_attachment_unreadable"] = "Email eklentisi açılamıyor: %s";
$lang["email_no_from"] = "\"From\" başlığı olmadan email gönderilemez.";
$lang["email_no_recipients"] = "Alıcıları yazmalısınız: To, Cc, or Bcc";
$lang["email_send_failure_phpmail"] = "PHP mail() fonksiyonu ile email gönderilemiyor. Sunucunuz bu metod ile email göndermeye ayarlanmamış olabilir.";
$lang["email_send_failure_sendmail"] = "PHP Sendmail ile email gönderilemiyor. Sunucunuz bu metod ile email göndermeye ayarlanmamış olabilir.";
$lang["email_send_failure_smtp"] = "PHP SMTP ile email gönderilemiyor. Sunucunuz bu metod ile email göndermeye ayarlanmamış olabilir.";
$lang["email_sent"] = "Mesajınız %s protokolü kullanılarak başarıyla gönderildi.";
$lang["email_no_socket"] = "Sendmail soketi açılamıyor. Lütfen ayarlarınızı kontrol ediniz.";
$lang["email_no_hostname"] = "SMTP sunucu adı belirtmelisiniz.";
$lang["email_smtp_error"] = "SMTP hatası: %s";
$lang["email_no_smtp_unpw"] = "Hata: SMTP kullanıcı adı ve şifresi belirtilmeli.";
$lang["email_failed_smtp_login"] = "AUTH LOGIN komutu gönderilemedi. Hata: %s";
$lang["email_smtp_auth_un"] = "Kullanıcı adı geçersiz. Hata: %s";
$lang["email_smtp_auth_pw"] = "Şifre geçersiz. Hata: %s";
$lang["email_smtp_data_failure"] = "Veriler gönderilemedi: %s";
$lang["email_exit_status"] = "Çıkış durum kodu: %s";

?>