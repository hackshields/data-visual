<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

$tcpdf["base_directory"] = APPPATH . "libraries/tcpdf/";
$tcpdf["base_url"] = "http://myaccount.goyoders.dev/app/3rdparty/tcpdf/";
$tcpdf["fonts_directory"] = $tcpdf["base_directory"] . "fonts/";
$tcpdf["enable_disk_cache"] = false;
$tcpdf["cache_directory"] = $tcpdf["base_directory"] . "cache/";
$tcpdf["image_directory"] = $tcpdf["base_directory"] . "images/";
$tcpdf["blank_image"] = $tcpdf["image_directory"] . "_blank.png";
$tcpdf["language_file"] = $tcpdf["base_directory"] . "config/lang/eng.php";
$tcpdf["page_format"] = "LETTER";
$tcpdf["page_orientation"] = "P";
$tcpdf["page_unit"] = "mm";
$tcpdf["page_break_auto"] = true;
$tcpdf["unicode"] = true;
$tcpdf["encoding"] = "UTF-8";
$tcpdf["creator"] = "TCPDF";
$tcpdf["author"] = "TCPDF";
$tcpdf["margin_top"] = 27;
$tcpdf["margin_bottom"] = 27;
$tcpdf["margin_left"] = 15;
$tcpdf["margin_right"] = 15;
$tcpdf["page_font"] = "helvetica";
$tcpdf["page_font_size"] = 10;
$tcpdf["small_font_ratio"] = 2 / 3;
$tcpdf["header_on"] = true;
$tcpdf["header_font"] = $tcpdf["page_font"];
$tcpdf["header_font_size"] = 10;
$tcpdf["header_margin"] = 5;
$tcpdf["header_title"] = "";
$tcpdf["header_string"] = "";
$tcpdf["header_logo"] = "";
$tcpdf["header_logo_width"] = 30;
$tcpdf["footer_on"] = true;
$tcpdf["footer_font"] = $tcpdf["page_font"];
$tcpdf["footer_font_size"] = 8;
$tcpdf["footer_margin"] = 10;
$tcpdf["image_scale"] = 4;
$tcpdf["cell_height_ratio"] = 1.25;
$tcpdf["cell_padding"] = 0;

?>