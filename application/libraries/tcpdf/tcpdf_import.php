<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */
require_once dirname(__FILE__) . "/tcpdf.php";
require_once dirname(__FILE__) . "/tcpdf_parser.php";
/**
 * @class TCPDF_IMPORT
 * !!! THIS CLASS IS UNDER DEVELOPMENT !!!
 * PHP class extension of the TCPDF (http://www.tcpdf.org) library to import existing PDF documents.<br>
 * @package com.tecnick.tcpdf
 * @brief PHP class extension of the TCPDF library to import existing PDF documents.
 * @version 1.0.001
 * @author Nicola Asuni - info@tecnick.com
 */
class TCPDF_IMPORT extends TCPDF
{
    /**
     * Import an existing PDF document
     * @param $filename (string) Filename of the PDF document to import.
     * @return true in case of success, false otherwise
     * @public
     * @since 1.0.000 (2011-05-24)
     */
    public function importPDF($filename)
    {
        $rawdata = file_get_contents($filename);
        if ($rawdata === false) {
            $this->Error("Unable to get the content of the file: " . $filename);
        }
        $cfg = array("die_for_errors" => false, "ignore_filter_decoding_errors" => true, "ignore_missing_filter_decoders" => true);
        try {
            $pdf = new TCPDF_PARSER($rawdata, $cfg);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        $data = $pdf->getParsedData();
        unset($rawdata);
        print_r($data);
        unset($pdf);
    }
}

?>