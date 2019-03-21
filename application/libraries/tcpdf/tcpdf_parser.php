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
require_once dirname(__FILE__) . "/include/tcpdf_filters.php";
/**
 * @class TCPDF_PARSER
 * This is a PHP class for parsing PDF documents.<br>
 * @package com.tecnick.tcpdf
 * @brief This is a PHP class for parsing PDF documents..
 * @version 1.0.15
 * @author Nicola Asuni - info@tecnick.com
 */
class TCPDF_PARSER
{
    /**
     * Raw content of the PDF document.
     * @private
     */
    private $pdfdata = "";
    /**
     * XREF data.
     * @protected
     */
    protected $xref = array();
    /**
     * Array of PDF objects.
     * @protected
     */
    protected $objects = array();
    /**
     * Class object for decoding filters.
     * @private
     */
    private $FilterDecoders = NULL;
    /**
     * Array of configuration parameters.
     * @private
     */
    private $cfg = array("die_for_errors" => false, "ignore_filter_decoding_errors" => true, "ignore_missing_filter_decoders" => true);
    /**
     * Parse a PDF document an return an array of objects.
     * @param $data (string) PDF data to parse.
     * @param $cfg (array) Array of configuration parameters:
     * 			'die_for_errors' : if true termitate the program execution in case of error, otherwise thows an exception;
     * 			'ignore_filter_decoding_errors' : if true ignore filter decoding errors;
     * 			'ignore_missing_filter_decoders' : if true ignore missing filter decoding errors.
     * @public
     * @since 1.0.000 (2011-05-24)
     */
    public function __construct($data, $cfg = array())
    {
        if (empty($data)) {
            $this->Error("Empty PDF data.");
        }
        if (($trimpos = strpos($data, "%PDF-")) === false) {
            $this->Error("Invalid PDF data: missing %PDF header.");
        }
        $this->pdfdata = substr($data, $trimpos);
        $pdflen = strlen($this->pdfdata);
        $this->setConfig($cfg);
        $this->xref = $this->getXrefData();
        $this->objects = array();
        foreach ($this->xref["xref"] as $obj => $offset) {
            if (!isset($this->objects[$obj]) && 0 < $offset) {
                $this->objects[$obj] = $this->getIndirectObject($obj, $offset, true);
            }
        }
        unset($this->pdfdata);
        $this->pdfdata = "";
    }
    /**
     * Set the configuration parameters.
     * @param $cfg (array) Array of configuration parameters:
     * 			'die_for_errors' : if true termitate the program execution in case of error, otherwise thows an exception;
     * 			'ignore_filter_decoding_errors' : if true ignore filter decoding errors;
     * 			'ignore_missing_filter_decoders' : if true ignore missing filter decoding errors.
     * @public
     */
    protected function setConfig($cfg)
    {
        if (isset($cfg["die_for_errors"])) {
            $this->cfg["die_for_errors"] = $cfg["die_for_errors"];
        }
        if (isset($cfg["ignore_filter_decoding_errors"])) {
            $this->cfg["ignore_filter_decoding_errors"] = $cfg["ignore_filter_decoding_errors"];
        }
        if (isset($cfg["ignore_missing_filter_decoders"])) {
            $this->cfg["ignore_missing_filter_decoders"] = $cfg["ignore_missing_filter_decoders"];
        }
    }
    /**
     * Return an array of parsed PDF document objects.
     * @return (array) Array of parsed PDF document objects.
     * @public
     * @since 1.0.000 (2011-06-26)
     */
    public function getParsedData()
    {
        return array($this->xref, $this->objects);
    }
    /**
     * Get Cross-Reference (xref) table and trailer data from PDF document data.
     * @param $offset (int) xref offset (if know).
     * @param $xref (array) previous xref array (if any).
     * @return Array containing xref and trailer data.
     * @protected
     * @since 1.0.000 (2011-05-24)
     */
    protected function getXrefData($offset = 0, $xref = array())
    {
        if ($offset == 0) {
            if (preg_match_all("/[\\r\\n]startxref[\\s]*[\\r\\n]+([0-9]+)[\\s]*[\\r\\n]+%%EOF/i", $this->pdfdata, $matches, PREG_SET_ORDER, $offset) == 0) {
                $this->Error("Unable to find startxref");
            }
            $matches = array_pop($matches);
            $startxref = $matches[1];
        } else {
            if (strpos($this->pdfdata, "xref", $offset) == $offset) {
                $startxref = $offset;
            } else {
                if (preg_match("/([0-9]+[\\s][0-9]+[\\s]obj)/i", $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                    $startxref = $offset;
                } else {
                    if (preg_match("/[\\r\\n]startxref[\\s]*[\\r\\n]+([0-9]+)[\\s]*[\\r\\n]+%%EOF/i", $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset)) {
                        $startxref = $matches[1][0];
                    } else {
                        $this->Error("Unable to find startxref");
                    }
                }
            }
        }
        if (strpos($this->pdfdata, "xref", $startxref) == $startxref) {
            $xref = $this->decodeXref($startxref, $xref);
        } else {
            $xref = $this->decodeXrefStream($startxref, $xref);
        }
        if (empty($xref)) {
            $this->Error("Unable to find xref");
        }
        return $xref;
    }
    /**
     * Decode the Cross-Reference section
     * @param $startxref (int) Offset at which the xref section starts (position of the 'xref' keyword).
     * @param $xref (array) Previous xref array (if any).
     * @return Array containing xref and trailer data.
     * @protected
     * @since 1.0.000 (2011-06-20)
     */
    protected function decodeXref($startxref, $xref = array())
    {
        $startxref += 4;
        $offset = $startxref + strspn($this->pdfdata, "", $startxref);
        $obj_num = 0;
        while (0 < preg_match("/([0-9]+)[\\x20]([0-9]+)[\\x20]?([nf]?)(\\r\\n|[\\x20]?[\\r\\n])/", $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            if ($matches[0][1] != $offset) {
                break;
            }
            $offset += strlen($matches[0][0]);
            if ($matches[3][0] == "n") {
                $index = $obj_num . "_" . intval($matches[2][0]);
                if (!isset($xref["xref"][$index])) {
                    $xref["xref"][$index] = intval($matches[1][0]);
                }
                $obj_num++;
            } else {
                if ($matches[3][0] == "f") {
                    $obj_num++;
                } else {
                    $obj_num = intval($matches[1][0]);
                }
            }
        }
        if (0 < preg_match("/trailer[\\s]*<<(.*)>>/isU", $this->pdfdata, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            $trailer_data = $matches[1][0];
            if (!isset($xref["trailer"]) || empty($xref["trailer"])) {
                $xref["trailer"] = array();
                if (0 < preg_match("/Size[\\s]+([0-9]+)/i", $trailer_data, $matches)) {
                    $xref["trailer"]["size"] = intval($matches[1]);
                }
                if (0 < preg_match("/Root[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+R/i", $trailer_data, $matches)) {
                    $xref["trailer"]["root"] = intval($matches[1]) . "_" . intval($matches[2]);
                }
                if (0 < preg_match("/Encrypt[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+R/i", $trailer_data, $matches)) {
                    $xref["trailer"]["encrypt"] = intval($matches[1]) . "_" . intval($matches[2]);
                }
                if (0 < preg_match("/Info[\\s]+([0-9]+)[\\s]+([0-9]+)[\\s]+R/i", $trailer_data, $matches)) {
                    $xref["trailer"]["info"] = intval($matches[1]) . "_" . intval($matches[2]);
                }
                if (0 < preg_match("/ID[\\s]*[\\[][\\s]*[<]([^>]*)[>][\\s]*[<]([^>]*)[>]/i", $trailer_data, $matches)) {
                    $xref["trailer"]["id"] = array();
                    list(, $xref["trailer"]["id"][0], $xref["trailer"]["id"][1]) = $matches;
                }
            }
            if (0 < preg_match("/Prev[\\s]+([0-9]+)/i", $trailer_data, $matches)) {
                $xref = $this->getXrefData(intval($matches[1]), $xref);
            }
        } else {
            $this->Error("Unable to find trailer");
        }
        return $xref;
    }
    /**
     * Decode the Cross-Reference Stream section
     * @param $startxref (int) Offset at which the xref section starts.
     * @param $xref (array) Previous xref array (if any).
     * @return Array containing xref and trailer data.
     * @protected
     * @since 1.0.003 (2013-03-16)
     */
    protected function decodeXrefStream($startxref, $xref = array())
    {
        $xrefobj = $this->getRawObject($startxref);
        $xrefcrs = $this->getIndirectObject($xrefobj[1], $startxref, true);
        if (!isset($xref["trailer"]) || empty($xref["trailer"])) {
            $xref["trailer"] = array();
            $filltrailer = true;
        } else {
            $filltrailer = false;
        }
        if (!isset($xref["xref"])) {
            $xref["xref"] = array();
        }
        $valid_crs = false;
        $columns = 0;
        $sarr = $xrefcrs[0][1];
        if (!is_array($sarr)) {
            $sarr = array();
        }
        foreach ($sarr as $k => $v) {
            if ($v[0] == "/" && $v[1] == "Type" && isset($sarr[$k + 1]) && $sarr[$k + 1][0] == "/" && $sarr[$k + 1][1] == "XRef") {
                $valid_crs = true;
            } else {
                if ($v[0] == "/" && $v[1] == "Index" && isset($sarr[$k + 1])) {
                    $index_first = intval($sarr[$k + 1][1][0][1]);
                    $index_entries = intval($sarr[$k + 1][1][1][1]);
                } else {
                    if ($v[0] == "/" && $v[1] == "Prev" && isset($sarr[$k + 1]) && $sarr[$k + 1][0] == "numeric") {
                        $prevxref = intval($sarr[$k + 1][1]);
                    } else {
                        if ($v[0] == "/" && $v[1] == "W" && isset($sarr[$k + 1])) {
                            $wb = array();
                            $wb[0] = intval($sarr[$k + 1][1][0][1]);
                            $wb[1] = intval($sarr[$k + 1][1][1][1]);
                            $wb[2] = intval($sarr[$k + 1][1][2][1]);
                        } else {
                            if ($v[0] == "/" && $v[1] == "DecodeParms" && isset($sarr[$k + 1][1])) {
                                $decpar = $sarr[$k + 1][1];
                                foreach ($decpar as $kdc => $vdc) {
                                    if ($vdc[0] == "/" && $vdc[1] == "Columns" && isset($decpar[$kdc + 1]) && $decpar[$kdc + 1][0] == "numeric") {
                                        $columns = intval($decpar[$kdc + 1][1]);
                                    } else {
                                        if ($vdc[0] == "/" && $vdc[1] == "Predictor" && isset($decpar[$kdc + 1]) && $decpar[$kdc + 1][0] == "numeric") {
                                            $predictor = intval($decpar[$kdc + 1][1]);
                                        }
                                    }
                                }
                            } else {
                                if ($filltrailer) {
                                    if ($v[0] == "/" && $v[1] == "Size" && isset($sarr[$k + 1]) && $sarr[$k + 1][0] == "numeric") {
                                        $xref["trailer"]["size"] = $sarr[$k + 1][1];
                                    } else {
                                        if ($v[0] == "/" && $v[1] == "Root" && isset($sarr[$k + 1]) && $sarr[$k + 1][0] == "objref") {
                                            $xref["trailer"]["root"] = $sarr[$k + 1][1];
                                        } else {
                                            if ($v[0] == "/" && $v[1] == "Info" && isset($sarr[$k + 1]) && $sarr[$k + 1][0] == "objref") {
                                                $xref["trailer"]["info"] = $sarr[$k + 1][1];
                                            } else {
                                                if ($v[0] == "/" && $v[1] == "Encrypt" && isset($sarr[$k + 1]) && $sarr[$k + 1][0] == "objref") {
                                                    $xref["trailer"]["encrypt"] = $sarr[$k + 1][1];
                                                } else {
                                                    if ($v[0] == "/" && $v[1] == "ID" && isset($sarr[$k + 1])) {
                                                        $xref["trailer"]["id"] = array();
                                                        $xref["trailer"]["id"][0] = $sarr[$k + 1][1][0][1];
                                                        $xref["trailer"]["id"][1] = $sarr[$k + 1][1][1][1];
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($valid_crs && isset($xrefcrs[1][3][0])) {
            $rowlen = $columns + 1;
            $sdata = unpack("C*", $xrefcrs[1][3][0]);
            $sdata = array_chunk($sdata, $rowlen);
            $ddata = array();
            $prev_row = array_fill(0, $rowlen, 0);
            foreach ($sdata as $k => $row) {
                $ddata[$k] = array();
                $predictor = 10 + $row[0];
                for ($i = 1; $i <= $columns; $i++) {
                    $j = $i - 1;
                    $row_up = $prev_row[$j];
                    if ($i == 1) {
                        $row_left = 0;
                        $row_upleft = 0;
                    } else {
                        $row_left = $row[$i - 1];
                        $row_upleft = $prev_row[$j - 1];
                    }
                    switch ($predictor) {
                        case 10:
                            $ddata[$k][$j] = $row[$i];
                            break;
                        case 11:
                            $ddata[$k][$j] = $row[$i] + $row_left & 255;
                            break;
                        case 12:
                            $ddata[$k][$j] = $row[$i] + $row_up & 255;
                            break;
                        case 13:
                            $ddata[$k][$j] = $row[$i] + ($row_left + $row_up) / 2 & 255;
                            break;
                        case 14:
                            $p = $row_left + $row_up - $row_upleft;
                            $pa = abs($p - $row_left);
                            $pb = abs($p - $row_up);
                            $pc = abs($p - $row_upleft);
                            $pmin = min($pa, $pb, $pc);
                            switch ($pmin) {
                                case $pa:
                                    $ddata[$k][$j] = $row[$i] + $row_left & 255;
                                    break;
                                case $pb:
                                    $ddata[$k][$j] = $row[$i] + $row_up & 255;
                                    break;
                                case $pc:
                                    $ddata[$k][$j] = $row[$i] + $row_upleft & 255;
                                    break;
                            }
                            break;
                        default:
                            $this->Error("Unknown PNG predictor");
                            break;
                    }
                }
                $prev_row = $ddata[$k];
            }
            $sdata = array();
            foreach ($ddata as $k => $row) {
                $sdata[$k] = array(0, 0, 0);
                if ($wb[0] == 0) {
                    $sdata[$k][0] = 1;
                }
                $i = 0;
                for ($c = 0; $c < 3; $c++) {
                    for ($b = 0; $b < $wb[$c]; $b++) {
                        if (isset($row[$i])) {
                            $sdata[$k][$c] += $row[$i] << ($wb[$c] - 1 - $b) * 8;
                        }
                        $i++;
                    }
                }
            }
            $ddata = array();
            if (isset($index_first)) {
                $obj_num = $index_first;
            } else {
                $obj_num = 0;
            }
            foreach ($sdata as $k => $row) {
                switch ($row[0]) {
                    case 0:
                        break;
                    case 1:
                        $index = $obj_num . "_" . $row[2];
                        if (!isset($xref["xref"][$index])) {
                            $xref["xref"][$index] = $row[1];
                        }
                        break;
                    case 2:
                        $index = $row[1] . "_0_" . $row[2];
                        $xref["xref"][$index] = -1;
                        break;
                    default:
                        break;
                }
                $obj_num++;
            }
        }
        if (isset($prevxref)) {
            $xref = $this->getXrefData($prevxref, $xref);
        }
        return $xref;
    }
    /**
     * Get object type, raw value and offset to next object
     * @param $offset (int) Object offset.
     * @return array containing object type, raw value and offset to next object
     * @protected
     * @since 1.0.000 (2011-06-20)
     */
    protected function getRawObject($offset = 0)
    {
        $objtype = "";
        $objval = "";
        $offset += strspn($this->pdfdata, "", $offset);
        $char = $this->pdfdata[$offset];
        switch ($char) {
            case "%":
                $next = strcspn($this->pdfdata, "\r\n", $offset);
                if (0 < $next) {
                    $offset += $next;
                    return $this->getRawObject($offset);
                }
                break;
            case "/":
                $objtype = $char;
                $offset++;
                if (preg_match("/^([^\\x00\\x09\\x0a\\x0c\\x0d\\x20\\s\\x28\\x29\\x3c\\x3e\\x5b\\x5d\\x7b\\x7d\\x2f\\x25]+)/", substr($this->pdfdata, $offset, 256), $matches) == 1) {
                    $objval = $matches[1];
                    $offset += strlen($objval);
                }
                break;
            case "(":
            case ")":
                $objtype = $char;
                $offset++;
                $strpos = $offset;
                if ($char == "(") {
                    for ($open_bracket = 1; 0 < $open_bracket; $strpos++) {
                        if (!isset($this->pdfdata[$strpos])) {
                            break;
                        }
                        $ch = $this->pdfdata[$strpos];
                        switch ($ch) {
                            case "\\":
                                $strpos++;
                                break;
                            case "(":
                                $open_bracket++;
                                break;
                            case ")":
                                $open_bracket--;
                                break;
                        }
                    }
                    $objval = substr($this->pdfdata, $offset, $strpos - $offset - 1);
                    $offset = $strpos;
                }
                break;
            case "[":
            case "]":
                $objtype = $char;
                $offset++;
                if ($char == "[") {
                    $objval = array();
                    do {
                        $element = $this->getRawObject($offset);
                        $offset = $element[2];
                        $objval[] = $element;
                    } while ($element[0] != "]");
                    array_pop($objval);
                }
                break;
            case "<":
            case ">":
                if (isset($this->pdfdata[$offset + 1]) && $this->pdfdata[$offset + 1] == $char) {
                    $objtype = $char . $char;
                    $offset += 2;
                    if ($char == "<") {
                        $objval = array();
                        do {
                            $element = $this->getRawObject($offset);
                            $offset = $element[2];
                            $objval[] = $element;
                        } while ($element[0] != ">>");
                        array_pop($objval);
                    }
                } else {
                    $objtype = $char;
                    $offset++;
                    if ($char == "<" && preg_match("/^([0-9A-Fa-f\\x09\\x0a\\x0c\\x0d\\x20]+)>/iU", substr($this->pdfdata, $offset), $matches) == 1) {
                        $objval = strtr($matches[1], "\t\n\f\r ", "");
                        $offset += strlen($matches[0]);
                    } else {
                        if (($endpos = strpos($this->pdfdata, ">", $offset)) !== false) {
                            $offset = $endpos + 1;
                        }
                    }
                }
                break;
            default:
                if (substr($this->pdfdata, $offset, 6) == "endobj") {
                    $objtype = "endobj";
                    $offset += 6;
                } else {
                    if (substr($this->pdfdata, $offset, 4) == "null") {
                        $objtype = "null";
                        $offset += 4;
                        $objval = "null";
                    } else {
                        if (substr($this->pdfdata, $offset, 4) == "true") {
                            $objtype = "boolean";
                            $offset += 4;
                            $objval = "true";
                        } else {
                            if (substr($this->pdfdata, $offset, 5) == "false") {
                                $objtype = "boolean";
                                $offset += 5;
                                $objval = "false";
                            } else {
                                if (substr($this->pdfdata, $offset, 6) == "stream") {
                                    $objtype = "stream";
                                    $offset += 6;
                                    if (preg_match("/^([\\r]?[\\n])/isU", substr($this->pdfdata, $offset), $matches) == 1) {
                                        $offset += strlen($matches[0]);
                                        if (preg_match("/(endstream)[\\x09\\x0a\\x0c\\x0d\\x20]/isU", substr($this->pdfdata, $offset), $matches, PREG_OFFSET_CAPTURE) == 1) {
                                            $objval = substr($this->pdfdata, $offset, $matches[0][1]);
                                            $offset += $matches[1][1];
                                        }
                                    }
                                } else {
                                    if (substr($this->pdfdata, $offset, 9) == "endstream") {
                                        $objtype = "endstream";
                                        $offset += 9;
                                    } else {
                                        if (preg_match("/^([0-9]+)[\\s]+([0-9]+)[\\s]+R/iU", substr($this->pdfdata, $offset, 33), $matches) == 1) {
                                            $objtype = "objref";
                                            $offset += strlen($matches[0]);
                                            $objval = intval($matches[1]) . "_" . intval($matches[2]);
                                        } else {
                                            if (preg_match("/^([0-9]+)[\\s]+([0-9]+)[\\s]+obj/iU", substr($this->pdfdata, $offset, 33), $matches) == 1) {
                                                $objtype = "obj";
                                                $objval = intval($matches[1]) . "_" . intval($matches[2]);
                                                $offset += strlen($matches[0]);
                                            } else {
                                                if (0 < ($numlen = strspn($this->pdfdata, "+-.0123456789", $offset))) {
                                                    $objtype = "numeric";
                                                    $objval = substr($this->pdfdata, $offset, $numlen);
                                                    $offset += $numlen;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                break;
        }
        return array($objtype, $objval, $offset);
    }
    /**
     * Get content of indirect object.
     * @param $obj_ref (string) Object number and generation number separated by underscore character.
     * @param $offset (int) Object offset.
     * @param $decoding (boolean) If true decode streams.
     * @return array containing object data.
     * @protected
     * @since 1.0.000 (2011-05-24)
     */
    protected function getIndirectObject($obj_ref, $offset = 0, $decoding = true)
    {
        $obj = explode("_", $obj_ref);
        if ($obj === false || count($obj) != 2) {
            $this->Error("Invalid object reference: " . $obj);
        } else {
            $objref = $obj[0] . " " . $obj[1] . " obj";
            $offset += strspn($this->pdfdata, "0", $offset);
            if (strpos($this->pdfdata, $objref, $offset) != $offset) {
                return array("null", "null", $offset);
            }
            $offset += strlen($objref);
            $objdata = array();
            $i = 0;
            do {
                $oldoffset = $offset;
                $element = $this->getRawObject($offset);
                $offset = $element[2];
                if ($decoding && $element[0] == "stream" && isset($objdata[$i - 1][0]) && $objdata[$i - 1][0] == "<<") {
                    $element[3] = $this->decodeStream($objdata[$i - 1][1], $element[1]);
                }
                $objdata[$i] = $element;
                $i++;
            } while ($element[0] != "endobj" && $offset != $oldoffset);
            array_pop($objdata);
            return $objdata;
        }
    }
    /**
     * Get the content of object, resolving indect object reference if necessary.
     * @param $obj (string) Object value.
     * @return array containing object data.
     * @protected
     * @since 1.0.000 (2011-06-26)
     */
    protected function getObjectVal($obj)
    {
        if ($obj[0] == "objref") {
            if (isset($this->objects[$obj[1]])) {
                return $this->objects[$obj[1]];
            }
            if (isset($this->xref[$obj[1]])) {
                $this->objects[$obj[1]] = $this->getIndirectObject($obj[1], $this->xref[$obj[1]], false);
                return $this->objects[$obj[1]];
            }
        }
        return $obj;
    }
    /**
     * Decode the specified stream.
     * @param $sdic (array) Stream's dictionary array.
     * @param $stream (string) Stream to decode.
     * @return array containing decoded stream data and remaining filters.
     * @protected
     * @since 1.0.000 (2011-06-22)
     */
    protected function decodeStream($sdic, $stream)
    {
        $slength = strlen($stream);
        if ($slength <= 0) {
            return array("", array());
        }
        $filters = array();
        foreach ($sdic as $k => $v) {
            if ($v[0] == "/") {
                if ($v[1] == "Length" && isset($sdic[$k + 1]) && $sdic[$k + 1][0] == "numeric") {
                    $declength = intval($sdic[$k + 1][1]);
                    if ($declength < $slength) {
                        $stream = substr($stream, 0, $declength);
                        $slength = $declength;
                    }
                } else {
                    if ($v[1] == "Filter" && isset($sdic[$k + 1])) {
                        $objval = $this->getObjectVal($sdic[$k + 1]);
                        if ($objval[0] == "/") {
                            $filters[] = $objval[1];
                        } else {
                            if ($objval[0] == "[") {
                                foreach ($objval[1] as $flt) {
                                    if ($flt[0] == "/") {
                                        $filters[] = $flt[1];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $remaining_filters = array();
        foreach ($filters as $filter) {
            if (in_array($filter, TCPDF_FILTERS::getAvailableFilters())) {
                try {
                    $stream = TCPDF_FILTERS::decodeFilter($filter, $stream);
                } catch (Exception $e) {
                    $emsg = $e->getMessage();
                    if ($emsg[0] == "~" && !$this->cfg["ignore_missing_filter_decoders"] || $emsg[0] != "~" && !$this->cfg["ignore_filter_decoding_errors"]) {
                        $this->Error($e->getMessage());
                    }
                }
            } else {
                $remaining_filters[] = $filter;
            }
        }
        return array($stream, $remaining_filters);
    }
    /**
     * Throw an exception or print an error message and die if the K_TCPDF_PARSER_THROW_EXCEPTION_ERROR constant is set to true.
     * @param $msg (string) The error message
     * @public
     * @since 1.0.000 (2011-05-23)
     */
    public function Error($msg)
    {
        if ($this->cfg["die_for_errors"]) {
            exit("<strong>TCPDF_PARSER ERROR: </strong>" . $msg);
        }
        throw new Exception("TCPDF_PARSER ERROR: " . $msg);
    }
}

?>