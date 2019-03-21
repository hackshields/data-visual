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
if (!defined("DATAMATRIXDEFS")) {
    define("DATAMATRIXDEFS", true);
}
define("ENC_ASCII", 0);
define("ENC_C40", 1);
define("ENC_TXT", 2);
define("ENC_X12", 3);
define("ENC_EDF", 4);
define("ENC_BASE256", 5);
define("ENC_ASCII_EXT", 6);
define("ENC_ASCII_NUM", 7);
/**
* @class Datamatrix
* Class to create DataMatrix ECC 200 barcode arrays for TCPDF class.
* DataMatrix (ISO/IEC 16022:2006) is a 2-dimensional bar code.
*
* @package com.tecnick.tcpdf
* @author Nicola Asuni
* @version 1.0.004
*/
class Datamatrix
{
    /**
     * Barcode array to be returned which is readable by TCPDF.
     * @protected
     */
    protected $barcode_array = array();
    /**
     * Store last used encoding for data codewords.
     * @protected
     */
    protected $last_enc = ENC_ASCII;
    /**
     * Table of Data Matrix ECC 200 Symbol Attributes:<ul>
     * <li>total matrix rows (including finder pattern)</li>
     * <li>total matrix cols (including finder pattern)</li>
     * <li>total matrix rows (without finder pattern)</li>
     * <li>total matrix cols (without finder pattern)</li>
     * <li>region data rows (with finder pattern)</li>
     * <li>region data col (with finder pattern)</li>
     * <li>region data rows (without finder pattern)</li>
     * <li>region data col (without finder pattern)</li>
     * <li>horizontal regions</li>
     * <li>vertical regions</li>
     * <li>regions</li>
     * <li>data codewords</li>
     * <li>error codewords</li>
     * <li>blocks</li>
     * <li>data codewords per block</li>
     * <li>error codewords per block</li>
     * </ul>
     * @protected
     */
    protected $symbattr = array(array(10, 10, 8, 8, 10, 10, 8, 8, 1, 1, 1, 3, 5, 1, 3, 5), array(12, 12, 10, 10, 12, 12, 10, 10, 1, 1, 1, 5, 7, 1, 5, 7), array(14, 14, 12, 12, 14, 14, 12, 12, 1, 1, 1, 8, 10, 1, 8, 10), array(16, 16, 14, 14, 16, 16, 14, 14, 1, 1, 1, 12, 12, 1, 12, 12), array(18, 18, 16, 16, 18, 18, 16, 16, 1, 1, 1, 18, 14, 1, 18, 14), array(20, 20, 18, 18, 20, 20, 18, 18, 1, 1, 1, 22, 18, 1, 22, 18), array(22, 22, 20, 20, 22, 22, 20, 20, 1, 1, 1, 30, 20, 1, 30, 20), array(24, 24, 22, 22, 24, 24, 22, 22, 1, 1, 1, 36, 24, 1, 36, 24), array(26, 26, 24, 24, 26, 26, 24, 24, 1, 1, 1, 44, 28, 1, 44, 28), array(32, 32, 28, 28, 16, 16, 14, 14, 2, 2, 4, 62, 36, 1, 62, 36), array(36, 36, 32, 32, 18, 18, 16, 16, 2, 2, 4, 86, 42, 1, 86, 42), array(40, 40, 36, 36, 20, 20, 18, 18, 2, 2, 4, 114, 48, 1, 114, 48), array(44, 44, 40, 40, 22, 22, 20, 20, 2, 2, 4, 144, 56, 1, 144, 56), array(48, 48, 44, 44, 24, 24, 22, 22, 2, 2, 4, 174, 68, 1, 174, 68), array(52, 52, 48, 48, 26, 26, 24, 24, 2, 2, 4, 204, 84, 2, 102, 42), array(64, 64, 56, 56, 16, 16, 14, 14, 4, 4, 16, 280, 112, 2, 140, 56), array(72, 72, 64, 64, 18, 18, 16, 16, 4, 4, 16, 368, 144, 4, 92, 36), array(80, 80, 72, 72, 20, 20, 18, 18, 4, 4, 16, 456, 192, 4, 114, 48), array(88, 88, 80, 80, 22, 22, 20, 20, 4, 4, 16, 576, 224, 4, 144, 56), array(96, 96, 88, 88, 24, 24, 22, 22, 4, 4, 16, 696, 272, 4, 174, 68), array(104, 104, 96, 96, 26, 26, 24, 24, 4, 4, 16, 816, 336, 6, 136, 56), array(120, 120, 108, 108, 20, 20, 18, 18, 6, 6, 36, 1050, 408, 6, 175, 68), array(132, 132, 120, 120, 22, 22, 20, 20, 6, 6, 36, 1304, 496, 8, 163, 62), array(144, 144, 132, 132, 24, 24, 22, 22, 6, 6, 36, 1558, 620, 10, 156, 62), array(8, 18, 6, 16, 8, 18, 6, 16, 1, 1, 1, 5, 7, 1, 5, 7), array(8, 32, 6, 28, 8, 16, 6, 14, 1, 2, 2, 10, 11, 1, 10, 11), array(12, 26, 10, 24, 12, 26, 10, 24, 1, 1, 1, 16, 14, 1, 16, 14), array(12, 36, 10, 32, 12, 18, 10, 16, 1, 2, 2, 12, 18, 1, 12, 18), array(16, 36, 14, 32, 16, 18, 14, 16, 1, 2, 2, 32, 24, 1, 32, 24), array(16, 48, 14, 44, 16, 24, 14, 22, 1, 2, 2, 49, 28, 1, 49, 28));
    /**
     * Map encodation modes whit character sets.
     * @protected
     */
    protected $chset_id = NULL;
    /**
     * Basic set of characters for each encodation mode.
     * @protected
     */
    protected $chset = array("C40" => array("S1" => 0, "S2" => 1, "S3" => 2, "32" => 3, "48" => 4, "49" => 5, "50" => 6, "51" => 7, "52" => 8, "53" => 9, "54" => 10, "55" => 11, "56" => 12, "57" => 13, "65" => 14, "66" => 15, "67" => 16, "68" => 17, "69" => 18, "70" => 19, "71" => 20, "72" => 21, "73" => 22, "74" => 23, "75" => 24, "76" => 25, "77" => 26, "78" => 27, "79" => 28, "80" => 29, "81" => 30, "82" => 31, "83" => 32, "84" => 33, "85" => 34, "86" => 35, "87" => 36, "88" => 37, "89" => 38, "90" => 39), "TXT" => array("S1" => 0, "S2" => 1, "S3" => 2, "32" => 3, "48" => 4, "49" => 5, "50" => 6, "51" => 7, "52" => 8, "53" => 9, "54" => 10, "55" => 11, "56" => 12, "57" => 13, "97" => 14, "98" => 15, "99" => 16, "100" => 17, "101" => 18, "102" => 19, "103" => 20, "104" => 21, "105" => 22, "106" => 23, "107" => 24, "108" => 25, "109" => 26, "110" => 27, "111" => 28, "112" => 29, "113" => 30, "114" => 31, "115" => 32, "116" => 33, "117" => 34, "118" => 35, "119" => 36, "120" => 37, "121" => 38, "122" => 39), "SH1" => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31), "SH2" => array("33" => 0, "34" => 1, "35" => 2, "36" => 3, "37" => 4, "38" => 5, "39" => 6, "40" => 7, "41" => 8, "42" => 9, "43" => 10, "44" => 11, "45" => 12, "46" => 13, "47" => 14, "58" => 15, "59" => 16, "60" => 17, "61" => 18, "62" => 19, "63" => 20, "64" => 21, "91" => 22, "92" => 23, "93" => 24, "94" => 25, "95" => 26, "F1" => 27, "US" => 30), "S3C" => array("96" => 0, "97" => 1, "98" => 2, "99" => 3, "100" => 4, "101" => 5, "102" => 6, "103" => 7, "104" => 8, "105" => 9, "106" => 10, "107" => 11, "108" => 12, "109" => 13, "110" => 14, "111" => 15, "112" => 16, "113" => 17, "114" => 18, "115" => 19, "116" => 20, "117" => 21, "118" => 22, "119" => 23, "120" => 24, "121" => 25, "122" => 26, "123" => 27, "124" => 28, "125" => 29, "126" => 30, "127" => 31), "S3T" => array("96" => 0, "65" => 1, "66" => 2, "67" => 3, "68" => 4, "69" => 5, "70" => 6, "71" => 7, "72" => 8, "73" => 9, "74" => 10, "75" => 11, "76" => 12, "77" => 13, "78" => 14, "79" => 15, "80" => 16, "81" => 17, "82" => 18, "83" => 19, "84" => 20, "85" => 21, "86" => 22, "87" => 23, "88" => 24, "89" => 25, "90" => 26, "123" => 27, "124" => 28, "125" => 29, "126" => 30, "127" => 31), "X12" => array("13" => 0, "42" => 1, "62" => 2, "32" => 3, "48" => 4, "49" => 5, "50" => 6, "51" => 7, "52" => 8, "53" => 9, "54" => 10, "55" => 11, "56" => 12, "57" => 13, "65" => 14, "66" => 15, "67" => 16, "68" => 17, "69" => 18, "70" => 19, "71" => 20, "72" => 21, "73" => 22, "74" => 23, "75" => 24, "76" => 25, "77" => 26, "78" => 27, "79" => 28, "80" => 29, "81" => 30, "82" => 31, "83" => 32, "84" => 33, "85" => 34, "86" => 35, "87" => 36, "88" => 37, "89" => 38, "90" => 39));
    /**
     * This is the class constructor.
     * Creates a datamatrix object
     * @param $code (string) Code to represent using Datamatrix.
     * @public
     */
    public function __construct($code)
    {
        $barcode_array = array();
        if (is_null($code) || $code == "\\0" || $code == "") {
            return false;
        }
        $cw = $this->getHighLevelEncoding($code);
        $nd = count($cw);
        if (1558 < $nd) {
            return false;
        }
        foreach ($this->symbattr as $params) {
            if ($nd <= $params[11]) {
                break;
            }
        }
        if ($params[11] < $nd) {
            return false;
        }
        if ($nd < $params[11]) {
            if (1 < $params[11] - $nd && $cw[$nd - 1] != 254) {
                if ($this->last_enc == ENC_EDF) {
                    $cw[] = 124;
                    $nd++;
                } else {
                    if ($this->last_enc != ENC_ASCII && $this->last_enc != ENC_BASE256) {
                        $cw[] = 254;
                        $nd++;
                    }
                }
            }
            if ($nd < $params[11]) {
                $cw[] = 129;
                $nd++;
                for ($i = $nd; $i < $params[11]; $i++) {
                    $cw[] = $this->get253StateCodeword(129, $i);
                }
            }
        }
        $cw = $this->getErrorCorrection($cw, $params[13], $params[14], $params[15]);
        $grid = array_fill(0, $params[2] * $params[3], 0);
        $places = $this->getPlacementMap($params[2], $params[3]);
        $grid = array();
        $i = 0;
        $rdri = $params[4] - 1;
        $rdci = $params[5] - 1;
        for ($vr = 0; $vr < $params[9]; $vr++) {
            for ($r = 0; $r < $params[4]; $r++) {
                $row = $vr * $params[4] + $r;
                for ($hr = 0; $hr < $params[8]; $hr++) {
                    for ($c = 0; $c < $params[5]; $c++) {
                        $col = $hr * $params[5] + $c;
                        if ($r == 0) {
                            if ($c % 2) {
                                $grid[$row][$col] = 0;
                            } else {
                                $grid[$row][$col] = 1;
                            }
                        } else {
                            if ($r == $rdri) {
                                $grid[$row][$col] = 1;
                            } else {
                                if ($c == 0) {
                                    $grid[$row][$col] = 1;
                                } else {
                                    if ($c == $rdci) {
                                        if ($r % 2) {
                                            $grid[$row][$col] = 1;
                                        } else {
                                            $grid[$row][$col] = 0;
                                        }
                                    } else {
                                        if ($places[$i] < 2) {
                                            $grid[$row][$col] = $places[$i];
                                        } else {
                                            $cw_id = floor($places[$i] / 10) - 1;
                                            $cw_bit = pow(2, 8 - $places[$i] % 10);
                                            $grid[$row][$col] = ($cw[$cw_id] & $cw_bit) == 0 ? 0 : 1;
                                        }
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        list($this->barcode_array["num_rows"], $this->barcode_array["num_cols"]) = $params;
        $this->barcode_array["bcode"] = $grid;
    }
    /**
     * Returns a barcode array which is readable by TCPDF
     * @return array barcode array readable by TCPDF;
     * @public
     */
    public function getBarcodeArray()
    {
        return $this->barcode_array;
    }
    /**
     * Product of two numbers in a Power-of-Two Galois Field
     * @param $a (int) first number to multiply.
     * @param $b (int) second number to multiply.
     * @param $log (array) Log table.
     * @param $alog (array) Anti-Log table.
     * @param $gf (array) Number of Factors of the Reed-Solomon polynomial.
     * @return int product
     * @protected
     */
    protected function getGFProduct($a, $b, $log, $alog, $gf)
    {
        if ($a == 0 || $b == 0) {
            return 0;
        }
        return $alog[($log[$a] + $log[$b]) % ($gf - 1)];
    }
    /**
     * Add error correction codewords to data codewords array (ANNEX E).
     * @param $wd (array) Array of datacodewords.
     * @param $nb (int) Number of blocks.
     * @param $nd (int) Number of data codewords per block.
     * @param $nc (int) Number of correction codewords per block.
     * @param $gf (int) numner of fields on log/antilog table (power of 2).
     * @param $pp (int) The value of its prime modulus polynomial (301 for ECC200).
     * @return array data codewords + error codewords
     * @protected
     */
    protected function getErrorCorrection($wd, $nb, $nd, $nc, $gf = 256, $pp = 301)
    {
        $log[0] = 0;
        $alog[0] = 1;
        for ($i = 1; $i < $gf; $i++) {
            $alog[$i] = $alog[$i - 1] * 2;
            if ($gf <= $alog[$i]) {
                $alog[$i] ^= $pp;
            }
            $log[$alog[$i]] = $i;
        }
        ksort($log);
        $c = array_fill(0, $nc + 1, 0);
        $c[0] = 1;
        for ($i = 1; $i <= $nc; $i++) {
            $c[$i] = $c[$i - 1];
            for ($j = $i - 1; 1 <= $j; $j--) {
                $c[$j] = $c[$j - 1] ^ $this->getGFProduct($c[$j], $alog[$i], $log, $alog, $gf);
            }
            $c[0] = $this->getGFProduct($c[0], $alog[$i], $log, $alog, $gf);
        }
        ksort($c);
        $num_wd = $nb * $nd;
        $num_we = $nb * $nc;
        for ($b = 0; $b < $nb; $b++) {
            $block = array();
            $n = $b;
            while ($n < $num_wd) {
                $block[] = $wd[$n];
                $n += $nb;
            }
            $we = array_fill(0, $nc + 1, 0);
            for ($i = 0; $i < $nd; $i++) {
                $k = $we[0] ^ $block[$i];
                for ($j = 0; $j < $nc; $j++) {
                    $we[$j] = $we[$j + 1] ^ $this->getGFProduct($k, $c[$nc - $j - 1], $log, $alog, $gf);
                }
            }
            $j = 0;
            $i = $b;
            while ($i < $num_we) {
                $wd[$num_wd + $i] = $we[$j];
                $j++;
                $i += $nb;
            }
        }
        ksort($wd);
        return $wd;
    }
    /**
     * Return the 253-state codeword
     * @param $cwpad (int) Pad codeword.
     * @param $cwpos (int) Number of data codewords from the beginning of encoded data.
     * @return pad codeword
     * @protected
     */
    protected function get253StateCodeword($cwpad, $cwpos)
    {
        $pad = $cwpad + 149 * $cwpos % 253 + 1;
        if (254 < $pad) {
            $pad -= 254;
        }
        return $pad;
    }
    /**
     * Return the 255-state codeword
     * @param $cwpad (int) Pad codeword.
     * @param $cwpos (int) Number of data codewords from the beginning of encoded data.
     * @return pad codeword
     * @protected
     */
    protected function get255StateCodeword($cwpad, $cwpos)
    {
        $pad = $cwpad + 149 * $cwpos % 255 + 1;
        if (255 < $pad) {
            $pad -= 256;
        }
        return $pad;
    }
    /**
     * Returns true if the char belongs to the selected mode
     * @param $chr (int) Character (byte) to check.
     * @param $mode (int) Current encoding mode.
     * @return boolean true if the char is of the selected mode.
     * @protected
     */
    protected function isCharMode($chr, $mode)
    {
        $status = false;
        switch ($mode) {
            case ENC_ASCII:
                $status = 0 <= $chr && $chr <= 127;
                break;
            case ENC_C40:
                $status = $chr == 32 || 48 <= $chr && $chr <= 57 || 65 <= $chr && $chr <= 90;
                break;
            case ENC_TXT:
                $status = $chr == 32 || 48 <= $chr && $chr <= 57 || 97 <= $chr && $chr <= 122;
                break;
            case ENC_X12:
                $status = $chr == 13 || $chr == 42 || $chr == 62;
                break;
            case ENC_EDF:
                $status = 32 <= $chr && $chr <= 94;
                break;
            case ENC_BASE256:
                $status = $chr == 232 || $chr == 233 || $chr == 234 || $chr == 241;
                break;
            case ENC_ASCII_EXT:
                $status = 128 <= $chr && $chr <= 255;
                break;
            case ENC_ASCII_NUM:
                $status = 48 <= $chr && $chr <= 57;
                break;
        }
        return $status;
    }
    /**
     * The look-ahead test scans the data to be encoded to find the best mode (Annex P - steps from J to S).
     * @param $data (string) data to encode
     * @param $pos (int) current position
     * @param $mode (int) current encoding mode
     * @return int encoding mode
     * @protected
     */
    protected function lookAheadTest($data, $pos, $mode)
    {
        $data_length = strlen($data);
        if ($data_length <= $pos) {
            return $mode;
        }
        $charscount = 0;
        if ($mode == ENC_ASCII) {
            $numch = array(0, 1, 1, 1, 1, 1.25);
        } else {
            $numch = array(1, 2, 2, 2, 2, 2.25);
            $numch[$mode] = 0;
        }
        while (true) {
            if ($pos + $charscount == $data_length) {
                if ($numch[ENC_ASCII] <= ceil(min($numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256]))) {
                    return ENC_ASCII;
                }
                if ($numch[ENC_BASE256] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF]))) {
                    return ENC_BASE256;
                }
                if ($numch[ENC_EDF] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_BASE256]))) {
                    return ENC_EDF;
                }
                if ($numch[ENC_TXT] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256]))) {
                    return ENC_TXT;
                }
                if ($numch[ENC_X12] < ceil(min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_EDF], $numch[ENC_BASE256]))) {
                    return ENC_X12;
                }
                return ENC_C40;
            }
            $chr = ord($data[$pos + $charscount]);
            $charscount++;
            if ($this->isCharMode($chr, ENC_ASCII_NUM)) {
                $numch[ENC_ASCII] += 1 / 2;
            } else {
                if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                    $numch[ENC_ASCII] = ceil($numch[ENC_ASCII]);
                    $numch[ENC_ASCII] += 2;
                } else {
                    $numch[ENC_ASCII] = ceil($numch[ENC_ASCII]);
                    $numch[ENC_ASCII] += 1;
                }
            }
            if ($this->isCharMode($chr, ENC_C40)) {
                $numch[ENC_C40] += 2 / 3;
            } else {
                if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                    $numch[ENC_C40] += 8 / 3;
                } else {
                    $numch[ENC_C40] += 4 / 3;
                }
            }
            if ($this->isCharMode($chr, ENC_TXT)) {
                $numch[ENC_TXT] += 2 / 3;
            } else {
                if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                    $numch[ENC_TXT] += 8 / 3;
                } else {
                    $numch[ENC_TXT] += 4 / 3;
                }
            }
            if ($this->isCharMode($chr, ENC_X12) || $this->isCharMode($chr, ENC_C40)) {
                $numch[ENC_X12] += 2 / 3;
            } else {
                if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                    $numch[ENC_X12] += 13 / 3;
                } else {
                    $numch[ENC_X12] += 10 / 3;
                }
            }
            if ($this->isCharMode($chr, ENC_EDF)) {
                $numch[ENC_EDF] += 3 / 4;
            } else {
                if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                    $numch[ENC_EDF] += 17 / 4;
                } else {
                    $numch[ENC_EDF] += 13 / 4;
                }
            }
            if ($this->isCharMode($chr, ENC_BASE256)) {
                $numch[ENC_BASE256] += 4;
            } else {
                $numch[ENC_BASE256] += 1;
            }
            if (4 <= $charscount) {
                if ($numch[ENC_ASCII] + 1 <= min($numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    return ENC_ASCII;
                }
                if ($numch[ENC_BASE256] + 1 <= $numch[ENC_ASCII] || $numch[ENC_BASE256] + 1 < min($numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_EDF])) {
                    return ENC_BASE256;
                }
                if ($numch[ENC_EDF] + 1 < min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_X12], $numch[ENC_BASE256])) {
                    return ENC_EDF;
                }
                if ($numch[ENC_TXT] + 1 < min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_X12], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    return ENC_TXT;
                }
                if ($numch[ENC_X12] + 1 < min($numch[ENC_ASCII], $numch[ENC_C40], $numch[ENC_TXT], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    return ENC_X12;
                }
                if ($numch[ENC_C40] + 1 < min($numch[ENC_ASCII], $numch[ENC_TXT], $numch[ENC_EDF], $numch[ENC_BASE256])) {
                    if ($numch[ENC_C40] < $numch[ENC_X12]) {
                        return ENC_C40;
                    }
                    if ($numch[ENC_C40] == $numch[ENC_X12]) {
                        for ($k = $pos + $charscount + 1; $k < $data_length; $k++) {
                            $tmpchr = ord($data[$k]);
                            if ($this->isCharMode($tmpchr, ENC_X12)) {
                                return ENC_X12;
                            }
                            if (!($this->isCharMode($tmpchr, ENC_X12) || $this->isCharMode($tmpchr, ENC_C40))) {
                                break;
                            }
                        }
                        return ENC_C40;
                    }
                }
            }
        }
    }
    /**
     * Get the switching codeword to a new encoding mode (latch codeword)
     * @param $mode (int) New encoding mode.
     * @return (int) Switch codeword.
     * @protected
     */
    protected function getSwitchEncodingCodeword($mode)
    {
        switch ($mode) {
            case ENC_ASCII:
                $cw = 254;
                if ($this->last_enc == ENC_EDF) {
                    $cw = 124;
                }
                break;
            case ENC_C40:
                $cw = 230;
                break;
            case ENC_TXT:
                $cw = 239;
                break;
            case ENC_X12:
                $cw = 238;
                break;
            case ENC_EDF:
                $cw = 240;
                break;
            case ENC_BASE256:
                $cw = 231;
                break;
        }
        return $cw;
    }
    /**
     * Choose the minimum matrix size and return the max number of data codewords.
     * @param $numcw (int) Number of current codewords.
     * @return number of data codewords in matrix
     * @protected
     */
    protected function getMaxDataCodewords($numcw)
    {
        foreach ($this->symbattr as $key => $matrix) {
            if ($numcw <= $matrix[11]) {
                return $matrix[11];
            }
        }
        return 0;
    }
    /**
     * Get high level encoding using the minimum symbol data characters for ECC 200
     * @param $data (string) data to encode
     * @return array of codewords
     * @protected
     */
    protected function getHighLevelEncoding($data)
    {
        $enc = ENC_ASCII;
        $pos = 0;
        $cw = array();
        $cw_num = 0;
        $data_length = strlen($data);
        while ($pos < $data_length) {
            $this->last_enc = $enc;
            switch ($enc) {
                case ENC_ASCII:
                    if (1 < $data_length && $pos < $data_length - 1 && $this->isCharMode(ord($data[$pos]), ENC_ASCII_NUM) && $this->isCharMode(ord($data[$pos + 1]), ENC_ASCII_NUM)) {
                        $cw[] = intval(substr($data, $pos, 2)) + 130;
                        $cw_num++;
                        $pos += 2;
                    } else {
                        $newenc = $this->lookAheadTest($data, $pos, $enc);
                        if ($newenc != $enc) {
                            $enc = $newenc;
                            $cw[] = $this->getSwitchEncodingCodeword($enc);
                            $cw_num++;
                        } else {
                            $chr = ord($data[$pos]);
                            $pos++;
                            if ($this->isCharMode($chr, ENC_ASCII_EXT)) {
                                $cw[] = 235;
                                $cw[] = $chr - 127;
                                $cw_num += 2;
                            } else {
                                $cw[] = $chr + 1;
                                $cw_num++;
                            }
                        }
                    }
                    break;
                case ENC_C40:
                case ENC_TXT:
                case ENC_X12:
                    $temp_cw = array();
                    $p = 0;
                    $epos = $pos;
                    $set_id = $this->chset_id[$enc];
                    $charset = $this->chset[$set_id];
                    do {
                        $chr = ord($data[$epos]);
                        $epos++;
                        if ($chr & 128) {
                            if ($enc == ENC_X12) {
                                return false;
                            }
                            $chr = $chr & 127;
                            $temp_cw[] = 1;
                            $temp_cw[] = 30;
                            $p += 2;
                        }
                        if (isset($charset[$chr])) {
                            $temp_cw[] = $charset[$chr];
                            $p++;
                        } else {
                            if (isset($this->chset["SH1"][$chr])) {
                                $temp_cw[] = 0;
                                $shiftset = $this->chset["SH1"];
                            } else {
                                if (isset($chr) && isset($this->chset["SH2"][$chr])) {
                                    $temp_cw[] = 1;
                                    $shiftset = $this->chset["SH2"];
                                } else {
                                    if ($enc == ENC_C40 && isset($this->chset["S3C"][$chr])) {
                                        $temp_cw[] = 2;
                                        $shiftset = $this->chset["S3C"];
                                    } else {
                                        if ($enc == ENC_TXT && isset($this->chset["S3T"][$chr])) {
                                            $temp_cw[] = 2;
                                            $shiftset = $this->chset["S3T"];
                                        } else {
                                            return false;
                                        }
                                    }
                                }
                            }
                            $temp_cw[] = $shiftset[$chr];
                            $p += 2;
                        }
                        if (3 <= $p) {
                            $c1 = array_shift($temp_cw);
                            $c2 = array_shift($temp_cw);
                            $c3 = array_shift($temp_cw);
                            $p -= 3;
                            $tmp = 1600 * $c1 + 40 * $c2 + $c3 + 1;
                            $cw[] = $tmp >> 8;
                            $cw[] = $tmp % 256;
                            $cw_num += 2;
                            $pos = $epos;
                            $newenc = $this->lookAheadTest($data, $pos, $enc);
                            if ($newenc != $enc) {
                                $enc = $newenc;
                                if ($enc != ENC_ASCII) {
                                    $cw[] = $this->getSwitchEncodingCodeword(ENC_ASCII);
                                    $cw_num++;
                                }
                                $cw[] = $this->getSwitchEncodingCodeword($enc);
                                $cw_num++;
                                $pos -= $p;
                                $p = 0;
                                break;
                            }
                        }
                    } while (0 < $p && $epos < $data_length);
                    if (0 < $p) {
                        $cwr = $this->getMaxDataCodewords($cw_num) - $cw_num;
                        if ($cwr == 1 && $p == 1) {
                            $c1 = array_shift($temp_cw);
                            $p--;
                            $cw[] = $chr + 1;
                            $cw_num++;
                            $pos = $epos;
                            $enc = ENC_ASCII;
                            $this->last_enc = $enc;
                        } else {
                            if ($cwr == 2 && $p == 1) {
                                $c1 = array_shift($temp_cw);
                                $p--;
                                $cw[] = 254;
                                $cw[] = $chr + 1;
                                $cw_num += 2;
                                $pos = $epos;
                                $enc = ENC_ASCII;
                                $this->last_enc = $enc;
                            } else {
                                if ($cwr == 2 && $p == 2) {
                                    $c1 = array_shift($temp_cw);
                                    $c2 = array_shift($temp_cw);
                                    $p -= 2;
                                    $tmp = 1600 * $c1 + 40 * $c2 + 1;
                                    $cw[] = $tmp >> 8;
                                    $cw[] = $tmp % 256;
                                    $cw_num += 2;
                                    $pos = $epos;
                                    $enc = ENC_ASCII;
                                    $this->last_enc = $enc;
                                } else {
                                    if ($enc != ENC_ASCII) {
                                        $enc = ENC_ASCII;
                                        $this->last_enc = $enc;
                                        $cw[] = $this->getSwitchEncodingCodeword($enc);
                                        $cw_num++;
                                        $pos = $epos - $p;
                                    }
                                }
                            }
                        }
                    }
                    break;
                case ENC_EDF:
                    $temp_cw = array();
                    $epos = $pos;
                    $field_length = 0;
                    $newenc = $enc;
                    do {
                        $chr = ord($data[$epos]);
                        if ($this->isCharMode($chr, ENC_EDF)) {
                            $epos++;
                            $temp_cw[] = $chr;
                            $field_length++;
                        }
                        if ($field_length == 4 || $epos == $data_length || !$this->isCharMode($chr, ENC_EDF)) {
                            if ($epos == $data_length && $field_length < 3) {
                                $enc = ENC_ASCII;
                                $cw[] = $this->getSwitchEncodingCodeword($enc);
                                $cw_num++;
                                break;
                            }
                            if ($field_length < 4) {
                                $temp_cw[] = 31;
                                $field_length++;
                                for ($i = $field_length; $i < 4; $i++) {
                                    $temp_cw[] = 0;
                                }
                                $enc = ENC_ASCII;
                                $this->last_enc = $enc;
                            }
                            $tcw = (($temp_cw[0] & 63) << 2) + (($temp_cw[1] & 48) >> 4);
                            if (0 < $tcw) {
                                $cw[] = $tcw;
                                $cw_num++;
                            }
                            $tcw = (($temp_cw[1] & 15) << 4) + (($temp_cw[2] & 60) >> 2);
                            if (0 < $tcw) {
                                $cw[] = $tcw;
                                $cw_num++;
                            }
                            $tcw = (($temp_cw[2] & 3) << 6) + ($temp_cw[3] & 63);
                            if (0 < $tcw) {
                                $cw[] = $tcw;
                                $cw_num++;
                            }
                            $temp_cw = array();
                            $pos = $epos;
                            $field_length = 0;
                            if ($enc == ENC_ASCII) {
                                break;
                            }
                        }
                    } while ($epos < $data_length);
                    break;
                case ENC_BASE256:
                    $temp_cw = array();
                    for ($field_length = 0; $pos < $data_length && $field_length <= 1555; $field_length++) {
                        $newenc = $this->lookAheadTest($data, $pos, $enc);
                        if ($newenc != $enc) {
                            $enc = $newenc;
                            break;
                        }
                        $chr = ord($data[$pos]);
                        $pos++;
                        $temp_cw[] = $chr;
                    }
                    if ($field_length <= 249) {
                        $cw[] = $this->get255StateCodeword($field_length, $cw_num + 1);
                        $cw_num++;
                    } else {
                        $cw[] = $this->get255StateCodeword(floor($field_length / 250) + 249, $cw_num + 1);
                        $cw[] = $this->get255StateCodeword($field_length % 250, $cw_num + 2);
                        $cw_num += 2;
                    }
                    if (!empty($temp_cw)) {
                        foreach ($temp_cw as $p => $cht) {
                            $cw[] = $this->get255StateCodeword($cht, $cw_num + $p + 1);
                        }
                    }
                    break;
            }
        }
        return $cw;
    }
    /**
     * Places "chr+bit" with appropriate wrapping within array[].
     * (Annex F - ECC 200 symbol character placement)
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $row (int) Row number.
     * @param $col (int) Column number.
     * @param $chr (int) Char byte.
     * @param $bit (int) Bit.
     * @return array
     * @protected
     */
    protected function placeModule($marr, $nrow, $ncol, $row, $col, $chr, $bit)
    {
        if ($row < 0) {
            $row += $nrow;
            $col += 4 - ($nrow + 4) % 8;
        }
        if ($col < 0) {
            $col += $ncol;
            $row += 4 - ($ncol + 4) % 8;
        }
        $marr[$row * $ncol + $col] = 10 * $chr + $bit;
        return $marr;
    }
    /**
     * Places the 8 bits of a utah-shaped symbol character.
     * (Annex F - ECC 200 symbol character placement)
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $row (int) Row number.
     * @param $col (int) Column number.
     * @param $chr (int) Char byte.
     * @return array
     * @protected
     */
    protected function placeUtah($marr, $nrow, $ncol, $row, $col, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 2, $col - 2, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 2, $col - 1, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 1, $col - 2, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 1, $col - 1, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row - 1, $col, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row, $col - 2, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row, $col - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, $row, $col, $chr, 8);
        return $marr;
    }
    /**
     * Places the 8 bits of the first special corner case.
     * (Annex F - ECC 200 symbol character placement)
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     * @return array
     * @protected
     */
    protected function placeCornerA($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 1, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 2, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 2, $ncol - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 3, $ncol - 1, $chr, 8);
        return $marr;
    }
    /**
     * Places the 8 bits of the second special corner case.
     * (Annex F - ECC 200 symbol character placement)
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     * @return array
     * @protected
     */
    protected function placeCornerB($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 3, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 2, 0, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 4, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 3, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 8);
        return $marr;
    }
    /**
     * Places the 8 bits of the third special corner case.
     * (Annex F - ECC 200 symbol character placement)
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     * @return array
     * @protected
     */
    protected function placeCornerC($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 3, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 2, 0, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 2, $ncol - 1, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 3, $ncol - 1, $chr, 8);
        return $marr;
    }
    /**
     * Places the 8 bits of the fourth special corner case.
     * (Annex F - ECC 200 symbol character placement)
     * @param $marr (array) Array of symbols.
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @param $chr (int) Char byte.
     * @return array
     * @protected
     */
    protected function placeCornerD($marr, $nrow, $ncol, $chr)
    {
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, 0, $chr, 1);
        $marr = $this->placeModule($marr, $nrow, $ncol, $nrow - 1, $ncol - 1, $chr, 2);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 3, $chr, 3);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 2, $chr, 4);
        $marr = $this->placeModule($marr, $nrow, $ncol, 0, $ncol - 1, $chr, 5);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 3, $chr, 6);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 2, $chr, 7);
        $marr = $this->placeModule($marr, $nrow, $ncol, 1, $ncol - 1, $chr, 8);
        return $marr;
    }
    /**
     * Build a placement map.
     * (Annex F - ECC 200 symbol character placement)
     * @param $nrow (int) Number of rows.
     * @param $ncol (int) Number of columns.
     * @return array
     * @protected
     */
    protected function getPlacementMap($nrow, $ncol)
    {
        $marr = array_fill(0, $nrow * $ncol, 0);
        $chr = 1;
        $row = 4;
        $col = 0;
        do {
            if ($row == $nrow && $col == 0) {
                $marr = $this->placeCornerA($marr, $nrow, $ncol, $chr);
                $chr++;
            }
            if ($row == $nrow - 2 && $col == 0 && $ncol % 4) {
                $marr = $this->placeCornerB($marr, $nrow, $ncol, $chr);
                $chr++;
            }
            if ($row == $nrow - 2 && $col == 0 && $ncol % 8 == 4) {
                $marr = $this->placeCornerC($marr, $nrow, $ncol, $chr);
                $chr++;
            }
            if ($row == $nrow + 4 && $col == 2 && !($ncol % 8)) {
                $marr = $this->placeCornerD($marr, $nrow, $ncol, $chr);
                $chr++;
            }
            do {
                if ($row < $nrow && 0 <= $col && !$marr[$row * $ncol + $col]) {
                    $marr = $this->placeUtah($marr, $nrow, $ncol, $row, $col, $chr);
                    $chr++;
                }
                $row -= 2;
                $col += 2;
            } while (0 <= $row && $col < $ncol);
            $row++;
            $col += 3;
            do {
                if (0 <= $row && $col < $ncol && !$marr[$row * $ncol + $col]) {
                    $marr = $this->placeUtah($marr, $nrow, $ncol, $row, $col, $chr);
                    $chr++;
                }
                $row += 2;
                $col -= 2;
            } while ($row < $nrow && 0 <= $col);
            $row += 3;
            $col++;
        } while ($row < $nrow || $col < $ncol);
        if (!$marr[$nrow * $ncol - 1]) {
            $marr[$nrow * $ncol - 1] = 1;
            $marr[$nrow * $ncol - $ncol - 2] = 1;
        }
        return $marr;
    }
}

?>