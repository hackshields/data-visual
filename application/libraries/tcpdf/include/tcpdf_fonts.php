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
/**
 * @class TCPDF_FONTS
 * Font methods for TCPDF library.
 * @package com.tecnick.tcpdf
 * @version 1.1.0
 * @author Nicola Asuni - info@tecnick.com
 */
class TCPDF_FONTS
{
    /**
     * Static cache used for speed up uniord performances
     * @protected
     */
    protected static $cache_uniord = array();
    /**
     * Convert and add the selected TrueType or Type1 font to the fonts folder (that must be writeable).
     * @param $fontfile (string) Font file (full path).
     * @param $fonttype (string) Font type. Leave empty for autodetect mode. Valid values are: TrueTypeUnicode, TrueType, Type1, CID0JP = CID-0 Japanese, CID0KR = CID-0 Korean, CID0CS = CID-0 Chinese Simplified, CID0CT = CID-0 Chinese Traditional.
     * @param $enc (string) Name of the encoding table to use. Leave empty for default mode. Omit this parameter for TrueType Unicode and symbolic fonts like Symbol or ZapfDingBats.
     * @param $flags (int) Unsigned 32-bit integer containing flags specifying various characteristics of the font (PDF32000:2008 - 9.8.2 Font Descriptor Flags): +1 for fixed font; +4 for symbol or +32 for non-symbol; +64 for italic. Fixed and Italic mode are generally autodetected so you have to set it to 32 = non-symbolic font (default) or 4 = symbolic font.
     * @param $outpath (string) Output path for generated font files (must be writeable by the web server). Leave empty for default font folder.
     * @param $platid (int) Platform ID for CMAP table to extract (when building a Unicode font for Windows this value should be 3, for Macintosh should be 1).
     * @param $encid (int) Encoding ID for CMAP table to extract (when building a Unicode font for Windows this value should be 1, for Macintosh should be 0). When Platform ID is 3, legal values for Encoding ID are: 0=Symbol, 1=Unicode, 2=ShiftJIS, 3=PRC, 4=Big5, 5=Wansung, 6=Johab, 7=Reserved, 8=Reserved, 9=Reserved, 10=UCS-4.
     * @param $addcbbox (boolean) If true includes the character bounding box information on the php font file.
     * @param $link (boolean) If true link to system font instead of copying the font data (not transportable) - Note: do not work with Type1 fonts.
     * @return (string) TCPDF font name or boolean false in case of error.
     * @author Nicola Asuni
     * @since 5.9.123 (2010-09-30)
     * @public static
     */
    public static function addTTFfont($fontfile, $fonttype = "", $enc = "", $flags = 32, $outpath = "", $platid = 3, $encid = 1, $addcbbox = false, $link = false)
    {
        if (!file_exists($fontfile)) {
            return false;
        }
        $fmetric = array();
        $font_path_parts = pathinfo($fontfile);
        if (!isset($font_path_parts["filename"])) {
            $font_path_parts["filename"] = substr($font_path_parts["basename"], 0, 0 - (strlen($font_path_parts["extension"]) + 1));
        }
        $font_name = strtolower($font_path_parts["filename"]);
        $font_name = preg_replace("/[^a-z0-9_]/", "", $font_name);
        $search = array("bold", "oblique", "italic", "regular");
        $replace = array("b", "i", "i", "");
        $font_name = str_replace($search, $replace, $font_name);
        if (empty($font_name)) {
            $font_name = "tcpdffont";
        }
        if (empty($outpath)) {
            $outpath = self::_getfontpath();
        }
        if (@file_exists($outpath . $font_name . ".php")) {
            return $font_name;
        }
        $fmetric["file"] = $font_name;
        $fmetric["ctg"] = $font_name . ".ctg.z";
        $font = file_get_contents($fontfile);
        $fmetric["originalsize"] = strlen($font);
        if (empty($fonttype)) {
            if (TCPDF_STATIC::_getULONG($font, 0) == 65536) {
                $fonttype = "TrueTypeUnicode";
            } else {
                if (substr($font, 0, 4) == "OTTO") {
                    return false;
                }
                $fonttype = "Type1";
            }
        }
        switch ($fonttype) {
            case "CID0CT":
            case "CID0CS":
            case "CID0KR":
            case "CID0JP":
                $fmetric["type"] = "cidfont0";
                break;
            case "Type1":
                $fmetric["type"] = "Type1";
                if (empty($enc) && ($flags & 4) == 0) {
                    $enc = "cp1252";
                }
                break;
            case "TrueType":
                $fmetric["type"] = "TrueType";
                break;
            case "TrueTypeUnicode":
            default:
                $fmetric["type"] = "TrueTypeUnicode";
                break;
        }
        $fmetric["enc"] = preg_replace("/[^A-Za-z0-9_\\-]/", "", $enc);
        $fmetric["diff"] = "";
        if (($fmetric["type"] == "TrueType" || $fmetric["type"] == "Type1") && !empty($enc) && $enc != "cp1252" && isset(TCPDF_FONT_DATA::$encmap[$enc])) {
            $enc_ref = TCPDF_FONT_DATA::$encmap["cp1252"];
            $enc_target = TCPDF_FONT_DATA::$encmap[$enc];
            $last = 0;
            for ($i = 32; $i <= 255; $i++) {
                if ($enc_target[$i] != $enc_ref[$i]) {
                    if ($i != $last + 1) {
                        $fmetric["diff"] .= $i . " ";
                    }
                    $last = $i;
                    $fmetric["diff"] .= "/" . $enc_target[$i] . " ";
                }
            }
        }
        if ($fmetric["type"] == "Type1") {
            $a = unpack("Cmarker/Ctype/Vsize", substr($font, 0, 6));
            if ($a["marker"] != 128) {
                return false;
            }
            $fmetric["size1"] = $a["size"];
            $data = substr($font, 6, $fmetric["size1"]);
            $a = unpack("Cmarker/Ctype/Vsize", substr($font, 6 + $fmetric["size1"], 6));
            if ($a["marker"] != 128) {
                return false;
            }
            $fmetric["size2"] = $a["size"];
            $encrypted = substr($font, 12 + $fmetric["size1"], $fmetric["size2"]);
            $data .= $encrypted;
            $fmetric["file"] .= ".z";
            $fp = TCPDF_STATIC::fopenLocal($outpath . $fmetric["file"], "wb");
            fwrite($fp, gzcompress($data));
            fclose($fp);
            $fmetric["Flags"] = $flags;
            preg_match("#/FullName[\\s]*\\(([^\\)]*)#", $font, $matches);
            $fmetric["name"] = preg_replace("/[^a-zA-Z0-9_\\-]/", "", $matches[1]);
            preg_match("#/FontBBox[\\s]*{([^}]*)#", $font, $matches);
            $fmetric["bbox"] = trim($matches[1]);
            $bv = explode(" ", $fmetric["bbox"]);
            $fmetric["Ascent"] = intval($bv[3]);
            $fmetric["Descent"] = intval($bv[1]);
            preg_match("#/ItalicAngle[\\s]*([0-9\\+\\-]*)#", $font, $matches);
            $fmetric["italicAngle"] = intval($matches[1]);
            if ($fmetric["italicAngle"] != 0) {
                $fmetric["Flags"] |= 64;
            }
            preg_match("#/UnderlinePosition[\\s]*([0-9\\+\\-]*)#", $font, $matches);
            $fmetric["underlinePosition"] = intval($matches[1]);
            preg_match("#/UnderlineThickness[\\s]*([0-9\\+\\-]*)#", $font, $matches);
            $fmetric["underlineThickness"] = intval($matches[1]);
            preg_match("#/isFixedPitch[\\s]*([^\\s]*)#", $font, $matches);
            if ($matches[1] == "true") {
                $fmetric["Flags"] |= 1;
            }
            $imap = array();
            if (0 < preg_match_all("#dup[\\s]([0-9]+)[\\s]*/([^\\s]*)[\\s]put#sU", $font, $fmap, PREG_SET_ORDER)) {
                foreach ($fmap as $v) {
                    $imap[$v[2]] = $v[1];
                }
            }
            $r = 55665;
            $c1 = 52845;
            $c2 = 22719;
            $elen = strlen($encrypted);
            $eplain = "";
            for ($i = 0; $i < $elen; $i++) {
                $chr = ord($encrypted[$i]);
                $eplain .= chr($chr ^ $r >> 8);
                $r = (($chr + $r) * $c1 + $c2) % 65536;
            }
            if (0 < preg_match("#/ForceBold[\\s]*([^\\s]*)#", $eplain, $matches) && $matches[1] == "true") {
                $fmetric["Flags"] |= 262144;
            }
            if (0 < preg_match("#/StdVW[\\s]*\\[([^\\]]*)#", $eplain, $matches)) {
                $fmetric["StemV"] = intval($matches[1]);
            } else {
                $fmetric["StemV"] = 70;
            }
            if (0 < preg_match("#/StdHW[\\s]*\\[([^\\]]*)#", $eplain, $matches)) {
                $fmetric["StemH"] = intval($matches[1]);
            } else {
                $fmetric["StemH"] = 30;
            }
            if (0 < preg_match("#/BlueValues[\\s]*\\[([^\\]]*)#", $eplain, $matches)) {
                $bv = explode(" ", $matches[1]);
                if (6 <= count($bv)) {
                    $v1 = intval($bv[2]);
                    $v2 = intval($bv[4]);
                    if ($v1 <= $v2) {
                        $fmetric["XHeight"] = $v1;
                        $fmetric["CapHeight"] = $v2;
                    } else {
                        $fmetric["XHeight"] = $v2;
                        $fmetric["CapHeight"] = $v1;
                    }
                } else {
                    $fmetric["XHeight"] = 450;
                    $fmetric["CapHeight"] = 700;
                }
            } else {
                $fmetric["XHeight"] = 450;
                $fmetric["CapHeight"] = 700;
            }
            if (0 < preg_match("#/lenIV[\\s]*([0-9]*)#", $eplain, $matches)) {
                $lenIV = intval($matches[1]);
            } else {
                $lenIV = 4;
            }
            $fmetric["Leading"] = 0;
            $eplain = substr($eplain, strpos($eplain, "/CharStrings") + 1);
            preg_match_all("#/([A-Za-z0-9\\.]*)[\\s][0-9]+[\\s]RD[\\s](.*)[\\s]ND#sU", $eplain, $matches, PREG_SET_ORDER);
            if (!empty($enc) && isset(TCPDF_FONT_DATA::$encmap[$enc])) {
                $enc_map = TCPDF_FONT_DATA::$encmap[$enc];
            } else {
                $enc_map = false;
            }
            $fmetric["cw"] = "";
            $fmetric["MaxWidth"] = 0;
            $cwidths = array();
            foreach ($matches as $k => $v) {
                $cid = 0;
                if (isset($imap[$v[1]])) {
                    $cid = $imap[$v[1]];
                } else {
                    if ($enc_map !== false) {
                        $cid = array_search($v[1], $enc_map);
                        if ($cid === false) {
                            $cid = 0;
                        } else {
                            if (1000 < $cid) {
                                $cid -= 1000;
                            }
                        }
                    }
                }
                $r = 4330;
                $c1 = 52845;
                $c2 = 22719;
                $cd = $v[2];
                $clen = strlen($cd);
                $ccom = array();
                for ($i = 0; $i < $clen; $i++) {
                    $chr = ord($cd[$i]);
                    $ccom[] = $chr ^ $r >> 8;
                    $r = (($chr + $r) * $c1 + $c2) % 65536;
                }
                $cdec = array();
                $ck = 0;
                for ($i = $lenIV; $i < $clen; $ck++) {
                    if ($ccom[$i] < 32) {
                        $cdec[$ck] = $ccom[$i];
                        if (0 < $ck && $cdec[$ck] == 13) {
                            $cwidths[$cid] = $cdec[$ck - 1];
                        }
                        $i++;
                    } else {
                        if (32 <= $ccom[$i] && $ccom[$i] <= 246) {
                            $cdec[$ck] = $ccom[$i] - 139;
                            $i++;
                        } else {
                            if (247 <= $ccom[$i] && $ccom[$i] <= 250) {
                                $cdec[$ck] = ($ccom[$i] - 247) * 256 + $ccom[$i + 1] + 108;
                                $i += 2;
                            } else {
                                if (251 <= $ccom[$i] && $ccom[$i] <= 254) {
                                    $cdec[$ck] = (0 - ($ccom[$i] - 251)) * 256 - $ccom[$i + 1] - 108;
                                    $i += 2;
                                } else {
                                    if ($ccom[$i] == 255) {
                                        $sval = chr($ccom[$i + 1]) . chr($ccom[$i + 2]) . chr($ccom[$i + 3]) . chr($ccom[$i + 4]);
                                        $vsval = unpack("li", $sval);
                                        $cdec[$ck] = $vsval["i"];
                                        $i += 5;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $fmetric["MissingWidth"] = $cwidths[0];
            $fmetric["MaxWidth"] = $fmetric["MissingWidth"];
            $fmetric["AvgWidth"] = 0;
            for ($cid = 0; $cid <= 255; $cid++) {
                if (isset($cwidths[$cid])) {
                    if ($fmetric["MaxWidth"] < $cwidths[$cid]) {
                        $fmetric["MaxWidth"] = $cwidths[$cid];
                    }
                    $fmetric["AvgWidth"] += $cwidths[$cid];
                    $fmetric["cw"] .= "," . $cid . "=>" . $cwidths[$cid];
                } else {
                    $fmetric["cw"] .= "," . $cid . "=>" . $fmetric["MissingWidth"];
                }
            }
            $fmetric["AvgWidth"] = round($fmetric["AvgWidth"] / count($cwidths));
        } else {
            $offset = 0;
            if (TCPDF_STATIC::_getULONG($font, $offset) != 65536) {
                return false;
            }
            if ($fmetric["type"] != "cidfont0") {
                if ($link) {
                    symlink($fontfile, $outpath . $fmetric["file"]);
                } else {
                    $fmetric["file"] .= ".z";
                    $fp = TCPDF_STATIC::fopenLocal($outpath . $fmetric["file"], "wb");
                    fwrite($fp, gzcompress($font));
                    fclose($fp);
                }
            }
            $offset += 4;
            $numTables = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $offset += 6;
            $table = array();
            for ($i = 0; $i < $numTables; $i++) {
                $tag = substr($font, $offset, 4);
                $offset += 4;
                $table[$tag] = array();
                $table[$tag]["checkSum"] = TCPDF_STATIC::_getULONG($font, $offset);
                $offset += 4;
                $table[$tag]["offset"] = TCPDF_STATIC::_getULONG($font, $offset);
                $offset += 4;
                $table[$tag]["length"] = TCPDF_STATIC::_getULONG($font, $offset);
                $offset += 4;
            }
            $offset = $table["head"]["offset"] + 12;
            if (TCPDF_STATIC::_getULONG($font, $offset) != 1594834165) {
                return false;
            }
            $offset += 4;
            $offset += 2;
            $fmetric["unitsPerEm"] = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $urk = 1000 / $fmetric["unitsPerEm"];
            $offset += 16;
            $xMin = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $yMin = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $xMax = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $yMax = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $fmetric["bbox"] = "" . $xMin . " " . $yMin . " " . $xMax . " " . $yMax . "";
            $macStyle = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $fmetric["Flags"] = $flags;
            if (($macStyle & 2) == 2) {
                $fmetric["Flags"] |= 64;
            }
            $offset = $table["head"]["offset"] + 50;
            $short_offset = TCPDF_STATIC::_getSHORT($font, $offset) == 0;
            $offset += 2;
            $indexToLoc = array();
            $offset = $table["loca"]["offset"];
            if ($short_offset) {
                $tot_num_glyphs = floor($table["loca"]["length"] / 2);
                for ($i = 0; $i < $tot_num_glyphs; $i++) {
                    $indexToLoc[$i] = TCPDF_STATIC::_getUSHORT($font, $offset) * 2;
                    if (isset($indexToLoc[$i - 1]) && $indexToLoc[$i] == $indexToLoc[$i - 1]) {
                        unset($indexToLoc[$i - 1]);
                    }
                    $offset += 2;
                }
            } else {
                $tot_num_glyphs = floor($table["loca"]["length"] / 4);
                for ($i = 0; $i < $tot_num_glyphs; $i++) {
                    $indexToLoc[$i] = TCPDF_STATIC::_getULONG($font, $offset);
                    if (isset($indexToLoc[$i - 1]) && $indexToLoc[$i] == $indexToLoc[$i - 1]) {
                        unset($indexToLoc[$i - 1]);
                    }
                    $offset += 4;
                }
            }
            $offset = $table["cmap"]["offset"] + 2;
            $numEncodingTables = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $encodingTables = array();
            for ($i = 0; $i < $numEncodingTables; $i++) {
                $encodingTables[$i]["platformID"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                $offset += 2;
                $encodingTables[$i]["encodingID"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                $offset += 2;
                $encodingTables[$i]["offset"] = TCPDF_STATIC::_getULONG($font, $offset);
                $offset += 4;
            }
            $offset = $table["OS/2"]["offset"];
            $offset += 2;
            $fmetric["AvgWidth"] = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $usWeightClass = round(TCPDF_STATIC::_getUFWORD($font, $offset) * $urk);
            $fmetric["StemV"] = round(70 * $usWeightClass / 400);
            $fmetric["StemH"] = round(30 * $usWeightClass / 400);
            $offset += 2;
            $offset += 2;
            $fsType = TCPDF_STATIC::_getSHORT($font, $offset);
            $offset += 2;
            if ($fsType == 2) {
                return false;
            }
            $fmetric["name"] = "";
            $offset = $table["name"]["offset"];
            $offset += 2;
            $numNameRecords = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $stringStorageOffset = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            for ($i = 0; $i < $numNameRecords; $i++) {
                $offset += 6;
                $nameID = TCPDF_STATIC::_getUSHORT($font, $offset);
                $offset += 2;
                if ($nameID == 6) {
                    $stringLength = TCPDF_STATIC::_getUSHORT($font, $offset);
                    $offset += 2;
                    $stringOffset = TCPDF_STATIC::_getUSHORT($font, $offset);
                    $offset += 2;
                    $offset = $table["name"]["offset"] + $stringStorageOffset + $stringOffset;
                    $fmetric["name"] = substr($font, $offset, $stringLength);
                    $fmetric["name"] = preg_replace("/[^a-zA-Z0-9_\\-]/", "", $fmetric["name"]);
                    break;
                }
                $offset += 4;
            }
            if (empty($fmetric["name"])) {
                $fmetric["name"] = $font_name;
            }
            $offset = $table["post"]["offset"];
            $offset += 4;
            $fmetric["italicAngle"] = TCPDF_STATIC::_getFIXED($font, $offset);
            $offset += 4;
            $fmetric["underlinePosition"] = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $fmetric["underlineThickness"] = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $isFixedPitch = TCPDF_STATIC::_getULONG($font, $offset) == 0 ? false : true;
            $offset += 2;
            if ($isFixedPitch) {
                $fmetric["Flags"] |= 1;
            }
            $offset = $table["hhea"]["offset"];
            $offset += 4;
            $fmetric["Ascent"] = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $fmetric["Descent"] = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $fmetric["Leading"] = round(TCPDF_STATIC::_getFWORD($font, $offset) * $urk);
            $offset += 2;
            $fmetric["MaxWidth"] = round(TCPDF_STATIC::_getUFWORD($font, $offset) * $urk);
            $offset += 2;
            $offset += 22;
            $numberOfHMetrics = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset = $table["maxp"]["offset"];
            $offset += 4;
            $numGlyphs = TCPDF_STATIC::_getUSHORT($font, $offset);
            $ctg = array();
            foreach ($encodingTables as $enctable) {
                if ($enctable["platformID"] == $platid && $enctable["encodingID"] == $encid) {
                    $offset = $table["cmap"]["offset"] + $enctable["offset"];
                    $format = TCPDF_STATIC::_getUSHORT($font, $offset);
                    $offset += 2;
                    switch ($format) {
                        case 0:
                            $offset += 4;
                            for ($c = 0; $c < 256; $c++) {
                                $g = TCPDF_STATIC::_getBYTE($font, $offset);
                                $ctg[$c] = $g;
                                $offset++;
                            }
                            break;
                        case 2:
                            $offset += 4;
                            $numSubHeaders = 0;
                            for ($i = 0; $i < 256; $i++) {
                                $subHeaderKeys[$i] = TCPDF_STATIC::_getUSHORT($font, $offset) / 8;
                                $offset += 2;
                                if ($numSubHeaders < $subHeaderKeys[$i]) {
                                    $numSubHeaders = $subHeaderKeys[$i];
                                }
                            }
                            $numSubHeaders++;
                            $subHeaders = array();
                            $numGlyphIndexArray = 0;
                            for ($k = 0; $k < $numSubHeaders; $k++) {
                                $subHeaders[$k]["firstCode"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                                $subHeaders[$k]["entryCount"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                                $subHeaders[$k]["idDelta"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                                $subHeaders[$k]["idRangeOffset"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                                $subHeaders[$k]["idRangeOffset"] -= 2 + ($numSubHeaders - $k - 1) * 8;
                                $subHeaders[$k]["idRangeOffset"] /= 2;
                                $numGlyphIndexArray += $subHeaders[$k]["entryCount"];
                            }
                            for ($k = 0; $k < $numGlyphIndexArray; $k++) {
                                $glyphIndexArray[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                            }
                            for ($i = 0; $i < 256; $i++) {
                                $k = $subHeaderKeys[$i];
                                if ($k == 0) {
                                    $c = $i;
                                    $g = $glyphIndexArray[0];
                                    $ctg[$c] = $g;
                                } else {
                                    $start_byte = $subHeaders[$k]["firstCode"];
                                    $end_byte = $start_byte + $subHeaders[$k]["entryCount"];
                                    for ($j = $start_byte; $j < $end_byte; $j++) {
                                        $c = ($i << 8) + $j;
                                        $idRangeOffset = $subHeaders[$k]["idRangeOffset"] + $j - $subHeaders[$k]["firstCode"];
                                        $g = ($glyphIndexArray[$idRangeOffset] + $subHeaders[$k]["idDelta"]) % 65536;
                                        if ($g < 0) {
                                            $g = 0;
                                        }
                                        $ctg[$c] = $g;
                                    }
                                }
                            }
                            break;
                        case 4:
                            $length = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $offset += 2;
                            $offset += 2;
                            $segCount = floor(TCPDF_STATIC::_getUSHORT($font, $offset) / 2);
                            $offset += 2;
                            $offset += 6;
                            $endCount = array();
                            for ($k = 0; $k < $segCount; $k++) {
                                $endCount[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                            }
                            $offset += 2;
                            $startCount = array();
                            for ($k = 0; $k < $segCount; $k++) {
                                $startCount[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                            }
                            $idDelta = array();
                            for ($k = 0; $k < $segCount; $k++) {
                                $idDelta[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                            }
                            $idRangeOffset = array();
                            for ($k = 0; $k < $segCount; $k++) {
                                $idRangeOffset[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                            }
                            $gidlen = floor($length / 2) - 8 - 4 * $segCount;
                            $glyphIdArray = array();
                            for ($k = 0; $k < $gidlen; $k++) {
                                $glyphIdArray[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                            }
                            for ($k = 0; $k < $segCount; $k++) {
                                for ($c = $startCount[$k]; $c <= $endCount[$k]; $c++) {
                                    if ($idRangeOffset[$k] == 0) {
                                        $g = ($idDelta[$k] + $c) % 65536;
                                    } else {
                                        $gid = floor($idRangeOffset[$k] / 2) + $c - $startCount[$k] - ($segCount - $k);
                                        $g = ($glyphIdArray[$gid] + $idDelta[$k]) % 65536;
                                    }
                                    if ($g < 0) {
                                        $g = 0;
                                    }
                                    $ctg[$c] = $g;
                                }
                            }
                            break;
                        case 6:
                            $offset += 4;
                            $firstCode = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $offset += 2;
                            $entryCount = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $offset += 2;
                            for ($k = 0; $k < $entryCount; $k++) {
                                $c = $k + $firstCode;
                                $g = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $offset += 2;
                                $ctg[$c] = $g;
                            }
                            break;
                        case 8:
                            $offset += 10;
                            for ($k = 0; $k < 8192; $k++) {
                                $is32[$k] = TCPDF_STATIC::_getBYTE($font, $offset);
                                $offset++;
                            }
                            $nGroups = TCPDF_STATIC::_getULONG($font, $offset);
                            $offset += 4;
                            for ($i = 0; $i < $nGroups; $i++) {
                                $startCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                                $offset += 4;
                                $endCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                                $offset += 4;
                                $startGlyphID = TCPDF_STATIC::_getULONG($font, $offset);
                                $offset += 4;
                                for ($k = $startCharCode; $k <= $endCharCode; $k++) {
                                    $is32idx = floor($c / 8);
                                    if (isset($is32[$is32idx]) && ($is32[$is32idx] & 1 << 7 - $c % 8) == 0) {
                                        $c = $k;
                                    } else {
                                        $c = (55232 + ($k >> 10) << 10) + 56320 + ($k & 1023) - 56613888;
                                    }
                                    $ctg[$c] = 0;
                                    $startGlyphID++;
                                }
                            }
                            break;
                        case 10:
                            $offset += 10;
                            $startCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                            $offset += 4;
                            $numChars = TCPDF_STATIC::_getULONG($font, $offset);
                            $offset += 4;
                            for ($k = 0; $k < $numChars; $k++) {
                                $c = $k + $startCharCode;
                                $g = TCPDF_STATIC::_getUSHORT($font, $offset);
                                $ctg[$c] = $g;
                                $offset += 2;
                            }
                            break;
                        case 12:
                            $offset += 10;
                            $nGroups = TCPDF_STATIC::_getULONG($font, $offset);
                            $offset += 4;
                            for ($k = 0; $k < $nGroups; $k++) {
                                $startCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                                $offset += 4;
                                $endCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                                $offset += 4;
                                $startGlyphCode = TCPDF_STATIC::_getULONG($font, $offset);
                                $offset += 4;
                                for ($c = $startCharCode; $c <= $endCharCode; $c++) {
                                    $ctg[$c] = $startGlyphCode;
                                    $startGlyphCode++;
                                }
                            }
                            break;
                        case 13:
                            break;
                        case 14:
                            break;
                    }
                }
            }
            if (!isset($ctg[0])) {
                $ctg[0] = 0;
            }
            $offset = $table["glyf"]["offset"] + $indexToLoc[$ctg[120]] + 4;
            $yMin = TCPDF_STATIC::_getFWORD($font, $offset);
            $offset += 4;
            $yMax = TCPDF_STATIC::_getFWORD($font, $offset);
            $offset += 2;
            $fmetric["XHeight"] = round(($yMax - $yMin) * $urk);
            $offset = $table["glyf"]["offset"] + $indexToLoc[$ctg[72]] + 4;
            $yMin = TCPDF_STATIC::_getFWORD($font, $offset);
            $offset += 4;
            $yMax = TCPDF_STATIC::_getFWORD($font, $offset);
            $offset += 2;
            $fmetric["CapHeight"] = round(($yMax - $yMin) * $urk);
            $cw = array();
            $offset = $table["hmtx"]["offset"];
            for ($i = 0; $i < $numberOfHMetrics; $i++) {
                $cw[$i] = round(TCPDF_STATIC::_getUFWORD($font, $offset) * $urk);
                $offset += 4;
            }
            if ($numberOfHMetrics < $numGlyphs) {
                $cw = array_pad($cw, $numGlyphs, $cw[$numberOfHMetrics - 1]);
            }
            $fmetric["MissingWidth"] = $cw[0];
            $fmetric["cw"] = "";
            $fmetric["cbbox"] = "";
            for ($cid = 0; $cid <= 65535; $cid++) {
                if (isset($ctg[$cid])) {
                    if (isset($cw[$ctg[$cid]])) {
                        $fmetric["cw"] .= "," . $cid . "=>" . $cw[$ctg[$cid]];
                    }
                    if ($addcbbox && isset($indexToLoc[$ctg[$cid]])) {
                        $offset = $table["glyf"]["offset"] + $indexToLoc[$ctg[$cid]];
                        $xMin = round(TCPDF_STATIC::_getFWORD($font, $offset + 2) * $urk);
                        $yMin = round(TCPDF_STATIC::_getFWORD($font, $offset + 4) * $urk);
                        $xMax = round(TCPDF_STATIC::_getFWORD($font, $offset + 6) * $urk);
                        $yMax = round(TCPDF_STATIC::_getFWORD($font, $offset + 8) * $urk);
                        $fmetric["cbbox"] .= "," . $cid . "=>array(" . $xMin . "," . $yMin . "," . $xMax . "," . $yMax . ")";
                    }
                }
            }
        }
        if ($fmetric["type"] == "TrueTypeUnicode" && count($ctg) == 256) {
            $fmetric["type"] = "TrueType";
        }
        $pfile = "<" . "?" . "php" . "\n";
        $pfile .= "// TCPDF FONT FILE DESCRIPTION" . "\n";
        $pfile .= "\$type='" . $fmetric["type"] . "';" . "\n";
        $pfile .= "\$name='" . $fmetric["name"] . "';" . "\n";
        $pfile .= "\$up=" . $fmetric["underlinePosition"] . ";" . "\n";
        $pfile .= "\$ut=" . $fmetric["underlineThickness"] . ";" . "\n";
        if (0 < $fmetric["MissingWidth"]) {
            $pfile .= "\$dw=" . $fmetric["MissingWidth"] . ";" . "\n";
        } else {
            $pfile .= "\$dw=" . $fmetric["AvgWidth"] . ";" . "\n";
        }
        $pfile .= "\$diff='" . $fmetric["diff"] . "';" . "\n";
        if ($fmetric["type"] == "Type1") {
            $pfile .= "\$enc='" . $fmetric["enc"] . "';" . "\n";
            $pfile .= "\$file='" . $fmetric["file"] . "';" . "\n";
            $pfile .= "\$size1=" . $fmetric["size1"] . ";" . "\n";
            $pfile .= "\$size2=" . $fmetric["size2"] . ";" . "\n";
        } else {
            $pfile .= "\$originalsize=" . $fmetric["originalsize"] . ";" . "\n";
            if ($fmetric["type"] == "cidfont0") {
                switch ($fonttype) {
                    case "CID0JP":
                        $pfile .= "// Japanese" . "\n";
                        $pfile .= "\$enc='UniJIS-UTF16-H';" . "\n";
                        $pfile .= "\$cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'Japan1','Supplement'=>5);" . "\n";
                        $pfile .= "include(dirname(__FILE__).'/uni2cid_aj16.php');" . "\n";
                        break;
                    case "CID0KR":
                        $pfile .= "// Korean" . "\n";
                        $pfile .= "\$enc='UniKS-UTF16-H';" . "\n";
                        $pfile .= "\$cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'Korea1','Supplement'=>0);" . "\n";
                        $pfile .= "include(dirname(__FILE__).'/uni2cid_ak12.php');" . "\n";
                        break;
                    case "CID0CS":
                        $pfile .= "// Chinese Simplified" . "\n";
                        $pfile .= "\$enc='UniGB-UTF16-H';" . "\n";
                        $pfile .= "\$cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'GB1','Supplement'=>2);" . "\n";
                        $pfile .= "include(dirname(__FILE__).'/uni2cid_ag15.php');" . "\n";
                        break;
                    case "CID0CT":
                    default:
                        $pfile .= "// Chinese Traditional" . "\n";
                        $pfile .= "\$enc='UniCNS-UTF16-H';" . "\n";
                        $pfile .= "\$cidinfo=array('Registry'=>'Adobe', 'Ordering'=>'CNS1','Supplement'=>0);" . "\n";
                        $pfile .= "include(dirname(__FILE__).'/uni2cid_aj16.php');" . "\n";
                        break;
                }
            } else {
                $pfile .= "\$enc='" . $fmetric["enc"] . "';" . "\n";
                $pfile .= "\$file='" . $fmetric["file"] . "';" . "\n";
                $pfile .= "\$ctg='" . $fmetric["ctg"] . "';" . "\n";
                $cidtogidmap = str_pad("", 131072, "");
                foreach ($ctg as $cid => $gid) {
                    $cidtogidmap = self::updateCIDtoGIDmap($cidtogidmap, $cid, $ctg[$cid]);
                }
                $fp = TCPDF_STATIC::fopenLocal($outpath . $fmetric["ctg"], "wb");
                fwrite($fp, gzcompress($cidtogidmap));
                fclose($fp);
            }
        }
        $pfile .= "\$desc=array(";
        $pfile .= "'Flags'=>" . $fmetric["Flags"] . ",";
        $pfile .= "'FontBBox'=>'[" . $fmetric["bbox"] . "]',";
        $pfile .= "'ItalicAngle'=>" . $fmetric["italicAngle"] . ",";
        $pfile .= "'Ascent'=>" . $fmetric["Ascent"] . ",";
        $pfile .= "'Descent'=>" . $fmetric["Descent"] . ",";
        $pfile .= "'Leading'=>" . $fmetric["Leading"] . ",";
        $pfile .= "'CapHeight'=>" . $fmetric["CapHeight"] . ",";
        $pfile .= "'XHeight'=>" . $fmetric["XHeight"] . ",";
        $pfile .= "'StemV'=>" . $fmetric["StemV"] . ",";
        $pfile .= "'StemH'=>" . $fmetric["StemH"] . ",";
        $pfile .= "'AvgWidth'=>" . $fmetric["AvgWidth"] . ",";
        $pfile .= "'MaxWidth'=>" . $fmetric["MaxWidth"] . ",";
        $pfile .= "'MissingWidth'=>" . $fmetric["MissingWidth"] . "";
        $pfile .= ");" . "\n";
        if (!empty($fmetric["cbbox"])) {
            $pfile .= "\$cbbox=array(" . substr($fmetric["cbbox"], 1) . ");" . "\n";
        }
        $pfile .= "\$cw=array(" . substr($fmetric["cw"], 1) . ");" . "\n";
        $pfile .= "// --- EOF ---" . "\n";
        $fp = TCPDF_STATIC::fopenLocal($outpath . $font_name . ".php", "w");
        fwrite($fp, $pfile);
        fclose($fp);
        return $font_name;
    }
    /**
     * Returs the checksum of a TTF table.
     * @param $table (string) table to check
     * @param $length (int) length of table in bytes
     * @return int checksum
     * @author Nicola Asuni
     * @since 5.2.000 (2010-06-02)
     * @public static
     */
    public static function _getTTFtableChecksum($table, $length)
    {
        $sum = 0;
        $tlen = $length / 4;
        $offset = 0;
        for ($i = 0; $i < $tlen; $i++) {
            $v = unpack("Ni", substr($table, $offset, 4));
            $sum += $v["i"];
            $offset += 4;
        }
        $sum = unpack("Ni", pack("N", $sum));
        return $sum["i"];
    }
    /**
     * Returns a subset of the TrueType font data without the unused glyphs.
     * @param $font (string) TrueType font data.
     * @param $subsetchars (array) Array of used characters (the glyphs to keep).
     * @return (string) A subset of TrueType font data without the unused glyphs.
     * @author Nicola Asuni
     * @since 5.2.000 (2010-06-02)
     * @public static
     */
    public static function _getTrueTypeFontSubset($font, $subsetchars)
    {
        ksort($subsetchars);
        $offset = 0;
        if (TCPDF_STATIC::_getULONG($font, $offset) != 65536) {
            return $font;
        }
        $offset += 4;
        $numTables = TCPDF_STATIC::_getUSHORT($font, $offset);
        $offset += 2;
        $offset += 6;
        $table = array();
        for ($i = 0; $i < $numTables; $i++) {
            $tag = substr($font, $offset, 4);
            $offset += 4;
            $table[$tag] = array();
            $table[$tag]["checkSum"] = TCPDF_STATIC::_getULONG($font, $offset);
            $offset += 4;
            $table[$tag]["offset"] = TCPDF_STATIC::_getULONG($font, $offset);
            $offset += 4;
            $table[$tag]["length"] = TCPDF_STATIC::_getULONG($font, $offset);
            $offset += 4;
        }
        $offset = $table["head"]["offset"] + 12;
        if (TCPDF_STATIC::_getULONG($font, $offset) != 1594834165) {
            return $font;
        }
        $offset += 4;
        $offset = $table["head"]["offset"] + 50;
        $short_offset = TCPDF_STATIC::_getSHORT($font, $offset) == 0;
        $offset += 2;
        $indexToLoc = array();
        $offset = $table["loca"]["offset"];
        if ($short_offset) {
            $tot_num_glyphs = floor($table["loca"]["length"] / 2);
            for ($i = 0; $i < $tot_num_glyphs; $i++) {
                $indexToLoc[$i] = TCPDF_STATIC::_getUSHORT($font, $offset) * 2;
                $offset += 2;
            }
        } else {
            $tot_num_glyphs = $table["loca"]["length"] / 4;
            for ($i = 0; $i < $tot_num_glyphs; $i++) {
                $indexToLoc[$i] = TCPDF_STATIC::_getULONG($font, $offset);
                $offset += 4;
            }
        }
        $subsetglyphs = array();
        $subsetglyphs[0] = true;
        $offset = $table["cmap"]["offset"] + 2;
        $numEncodingTables = TCPDF_STATIC::_getUSHORT($font, $offset);
        $offset += 2;
        $encodingTables = array();
        for ($i = 0; $i < $numEncodingTables; $i++) {
            $encodingTables[$i]["platformID"] = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $encodingTables[$i]["encodingID"] = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            $encodingTables[$i]["offset"] = TCPDF_STATIC::_getULONG($font, $offset);
            $offset += 4;
        }
        foreach ($encodingTables as $enctable) {
            $offset = $table["cmap"]["offset"] + $enctable["offset"];
            $format = TCPDF_STATIC::_getUSHORT($font, $offset);
            $offset += 2;
            switch ($format) {
                case 0:
                    $offset += 4;
                    for ($c = 0; $c < 256; $c++) {
                        if (isset($subsetchars[$c])) {
                            $g = TCPDF_STATIC::_getBYTE($font, $offset);
                            $subsetglyphs[$g] = true;
                        }
                        $offset++;
                    }
                    break;
                case 2:
                    $offset += 4;
                    $numSubHeaders = 0;
                    for ($i = 0; $i < 256; $i++) {
                        $subHeaderKeys[$i] = TCPDF_STATIC::_getUSHORT($font, $offset) / 8;
                        $offset += 2;
                        if ($numSubHeaders < $subHeaderKeys[$i]) {
                            $numSubHeaders = $subHeaderKeys[$i];
                        }
                    }
                    $numSubHeaders++;
                    $subHeaders = array();
                    $numGlyphIndexArray = 0;
                    for ($k = 0; $k < $numSubHeaders; $k++) {
                        $subHeaders[$k]["firstCode"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]["entryCount"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]["idDelta"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]["idRangeOffset"] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                        $subHeaders[$k]["idRangeOffset"] -= 2 + ($numSubHeaders - $k - 1) * 8;
                        $subHeaders[$k]["idRangeOffset"] /= 2;
                        $numGlyphIndexArray += $subHeaders[$k]["entryCount"];
                    }
                    for ($k = 0; $k < $numGlyphIndexArray; $k++) {
                        $glyphIndexArray[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    for ($i = 0; $i < 256; $i++) {
                        $k = $subHeaderKeys[$i];
                        if ($k == 0) {
                            $c = $i;
                            if (isset($subsetchars[$c])) {
                                $g = $glyphIndexArray[0];
                                $subsetglyphs[$g] = true;
                            }
                        } else {
                            $start_byte = $subHeaders[$k]["firstCode"];
                            $end_byte = $start_byte + $subHeaders[$k]["entryCount"];
                            for ($j = $start_byte; $j < $end_byte; $j++) {
                                $c = ($i << 8) + $j;
                                if (isset($subsetchars[$c])) {
                                    $idRangeOffset = $subHeaders[$k]["idRangeOffset"] + $j - $subHeaders[$k]["firstCode"];
                                    $g = ($glyphIndexArray[$idRangeOffset] + $subHeaders[$k]["idDelta"]) % 65536;
                                    if ($g < 0) {
                                        $g = 0;
                                    }
                                    $subsetglyphs[$g] = true;
                                }
                            }
                        }
                    }
                    break;
                case 4:
                    $length = TCPDF_STATIC::_getUSHORT($font, $offset);
                    $offset += 2;
                    $offset += 2;
                    $segCount = floor(TCPDF_STATIC::_getUSHORT($font, $offset) / 2);
                    $offset += 2;
                    $offset += 6;
                    $endCount = array();
                    for ($k = 0; $k < $segCount; $k++) {
                        $endCount[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $offset += 2;
                    $startCount = array();
                    for ($k = 0; $k < $segCount; $k++) {
                        $startCount[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $idDelta = array();
                    for ($k = 0; $k < $segCount; $k++) {
                        $idDelta[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $idRangeOffset = array();
                    for ($k = 0; $k < $segCount; $k++) {
                        $idRangeOffset[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    $gidlen = floor($length / 2) - 8 - 4 * $segCount;
                    $glyphIdArray = array();
                    for ($k = 0; $k < $gidlen; $k++) {
                        $glyphIdArray[$k] = TCPDF_STATIC::_getUSHORT($font, $offset);
                        $offset += 2;
                    }
                    for ($k = 0; $k < $segCount; $k++) {
                        for ($c = $startCount[$k]; $c <= $endCount[$k]; $c++) {
                            if (isset($subsetchars[$c])) {
                                if ($idRangeOffset[$k] == 0) {
                                    $g = ($idDelta[$k] + $c) % 65536;
                                } else {
                                    $gid = floor($idRangeOffset[$k] / 2) + $c - $startCount[$k] - ($segCount - $k);
                                    $g = ($glyphIdArray[$gid] + $idDelta[$k]) % 65536;
                                }
                                if ($g < 0) {
                                    $g = 0;
                                }
                                $subsetglyphs[$g] = true;
                            }
                        }
                    }
                    break;
                case 6:
                    $offset += 4;
                    $firstCode = TCPDF_STATIC::_getUSHORT($font, $offset);
                    $offset += 2;
                    $entryCount = TCPDF_STATIC::_getUSHORT($font, $offset);
                    $offset += 2;
                    for ($k = 0; $k < $entryCount; $k++) {
                        $c = $k + $firstCode;
                        if (isset($subsetchars[$c])) {
                            $g = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $subsetglyphs[$g] = true;
                        }
                        $offset += 2;
                    }
                    break;
                case 8:
                    $offset += 10;
                    for ($k = 0; $k < 8192; $k++) {
                        $is32[$k] = TCPDF_STATIC::_getBYTE($font, $offset);
                        $offset++;
                    }
                    $nGroups = TCPDF_STATIC::_getULONG($font, $offset);
                    $offset += 4;
                    for ($i = 0; $i < $nGroups; $i++) {
                        $startCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                        $offset += 4;
                        $endCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                        $offset += 4;
                        $startGlyphID = TCPDF_STATIC::_getULONG($font, $offset);
                        $offset += 4;
                        for ($k = $startCharCode; $k <= $endCharCode; $k++) {
                            $is32idx = floor($c / 8);
                            if (isset($is32[$is32idx]) && ($is32[$is32idx] & 1 << 7 - $c % 8) == 0) {
                                $c = $k;
                            } else {
                                $c = (55232 + ($k >> 10) << 10) + 56320 + ($k & 1023) - 56613888;
                            }
                            if (isset($subsetchars[$c])) {
                                $subsetglyphs[$startGlyphID] = true;
                            }
                            $startGlyphID++;
                        }
                    }
                    break;
                case 10:
                    $offset += 10;
                    $startCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                    $offset += 4;
                    $numChars = TCPDF_STATIC::_getULONG($font, $offset);
                    $offset += 4;
                    for ($k = 0; $k < $numChars; $k++) {
                        $c = $k + $startCharCode;
                        if (isset($subsetchars[$c])) {
                            $g = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $subsetglyphs[$g] = true;
                        }
                        $offset += 2;
                    }
                    break;
                case 12:
                    $offset += 10;
                    $nGroups = TCPDF_STATIC::_getULONG($font, $offset);
                    $offset += 4;
                    for ($k = 0; $k < $nGroups; $k++) {
                        $startCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                        $offset += 4;
                        $endCharCode = TCPDF_STATIC::_getULONG($font, $offset);
                        $offset += 4;
                        $startGlyphCode = TCPDF_STATIC::_getULONG($font, $offset);
                        $offset += 4;
                        for ($c = $startCharCode; $c <= $endCharCode; $c++) {
                            if (isset($subsetchars[$c])) {
                                $subsetglyphs[$startGlyphCode] = true;
                            }
                            $startGlyphCode++;
                        }
                    }
                    break;
                case 13:
                    break;
                case 14:
                    break;
            }
        }
        $new_sga = $subsetglyphs;
        while (!empty($new_sga)) {
            $sga = $new_sga;
            $new_sga = array();
            foreach ($sga as $key => $val) {
                if (isset($indexToLoc[$key])) {
                    $offset = $table["glyf"]["offset"] + $indexToLoc[$key];
                    $numberOfContours = TCPDF_STATIC::_getSHORT($font, $offset);
                    $offset += 2;
                    if ($numberOfContours < 0) {
                        $offset += 8;
                        do {
                            $flags = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $offset += 2;
                            $glyphIndex = TCPDF_STATIC::_getUSHORT($font, $offset);
                            $offset += 2;
                            if (!isset($subsetglyphs[$glyphIndex])) {
                                $new_sga[$glyphIndex] = true;
                            }
                            if ($flags & 1) {
                                $offset += 4;
                            } else {
                                $offset += 2;
                            }
                            if ($flags & 8) {
                                $offset += 2;
                            } else {
                                if ($flags & 64) {
                                    $offset += 4;
                                } else {
                                    if ($flags & 128) {
                                        $offset += 8;
                                    }
                                }
                            }
                        } while ($flags & 32);
                    }
                }
            }
            $subsetglyphs += $new_sga;
        }
        ksort($subsetglyphs);
        $glyf = "";
        $loca = "";
        $offset = 0;
        $glyf_offset = $table["glyf"]["offset"];
        for ($i = 0; $i < $tot_num_glyphs; $i++) {
            if (isset($subsetglyphs[$i])) {
                $length = $indexToLoc[$i + 1] - $indexToLoc[$i];
                $glyf .= substr($font, $glyf_offset + $indexToLoc[$i], $length);
            } else {
                $length = 0;
            }
            if ($short_offset) {
                $loca .= pack("n", floor($offset / 2));
            } else {
                $loca .= pack("N", $offset);
            }
            $offset += $length;
        }
        $table_names = array("head", "hhea", "hmtx", "maxp", "cvt ", "fpgm", "prep");
        $offset = 12;
        foreach ($table as $tag => $val) {
            if (in_array($tag, $table_names)) {
                $table[$tag]["data"] = substr($font, $table[$tag]["offset"], $table[$tag]["length"]);
                if ($tag == "head") {
                    $table[$tag]["data"] = substr($table[$tag]["data"], 0, 8) . "" . substr($table[$tag]["data"], 12);
                }
                $pad = 4 - $table[$tag]["length"] % 4;
                if ($pad != 4) {
                    $table[$tag]["length"] += $pad;
                    $table[$tag]["data"] .= str_repeat("", $pad);
                }
                $table[$tag]["offset"] = $offset;
                $offset += $table[$tag]["length"];
            } else {
                unset($table[$tag]);
            }
        }
        $table["loca"]["data"] = $loca;
        $table["loca"]["length"] = strlen($loca);
        $pad = 4 - $table["loca"]["length"] % 4;
        if ($pad != 4) {
            $table["loca"]["length"] += $pad;
            $table["loca"]["data"] .= str_repeat("", $pad);
        }
        $table["loca"]["offset"] = $offset;
        $table["loca"]["checkSum"] = self::_getTTFtableChecksum($table["loca"]["data"], $table["loca"]["length"]);
        $offset += $table["loca"]["length"];
        $table["glyf"]["data"] = $glyf;
        $table["glyf"]["length"] = strlen($glyf);
        $pad = 4 - $table["glyf"]["length"] % 4;
        if ($pad != 4) {
            $table["glyf"]["length"] += $pad;
            $table["glyf"]["data"] .= str_repeat("", $pad);
        }
        $table["glyf"]["offset"] = $offset;
        $table["glyf"]["checkSum"] = self::_getTTFtableChecksum($table["glyf"]["data"], $table["glyf"]["length"]);
        $font = "";
        $font .= pack("N", 65536);
        $numTables = count($table);
        $font .= pack("n", $numTables);
        $entrySelector = floor(log($numTables, 2));
        $searchRange = pow(2, $entrySelector) * 16;
        $rangeShift = $numTables * 16 - $searchRange;
        $font .= pack("n", $searchRange);
        $font .= pack("n", $entrySelector);
        $font .= pack("n", $rangeShift);
        $offset = $numTables * 16;
        foreach ($table as $tag => $data) {
            $font .= $tag;
            $font .= pack("N", $data["checkSum"]);
            $font .= pack("N", $data["offset"] + $offset);
            $font .= pack("N", $data["length"]);
        }
        foreach ($table as $data) {
            $font .= $data["data"];
        }
        $checkSumAdjustment = 2981146554.0 - self::_getTTFtableChecksum($font, strlen($font));
        $font = substr($font, 0, $table["head"]["offset"] + 8) . pack("N", $checkSumAdjustment) . substr($font, $table["head"]["offset"] + 12);
        return $font;
    }
    /**
     * Outputs font widths
     * @param $font (array) font data
     * @param $cidoffset (int) offset for CID values
     * @return PDF command string for font widths
     * @author Nicola Asuni
     * @since 4.4.000 (2008-12-07)
     * @public static
     */
    public static function _putfontwidths($font, $cidoffset = 0)
    {
        ksort($font["cw"]);
        $rangeid = 0;
        $range = array();
        $prevcid = -2;
        $prevwidth = -1;
        $interval = false;
        foreach ($font["cw"] as $cid => $width) {
            $cid -= $cidoffset;
            if ($font["subset"] && !isset($font["subsetchars"][$cid])) {
                continue;
            }
            if ($width != $font["dw"]) {
                if ($cid == $prevcid + 1) {
                    if ($width == $prevwidth) {
                        if ($width == $range[$rangeid][0]) {
                            $range[$rangeid][] = $width;
                        } else {
                            array_pop($range[$rangeid]);
                            $rangeid = $prevcid;
                            $range[$rangeid] = array();
                            $range[$rangeid][] = $prevwidth;
                            $range[$rangeid][] = $width;
                        }
                        $interval = true;
                        $range[$rangeid]["interval"] = true;
                    } else {
                        if ($interval) {
                            $rangeid = $cid;
                            $range[$rangeid] = array();
                            $range[$rangeid][] = $width;
                        } else {
                            $range[$rangeid][] = $width;
                        }
                        $interval = false;
                    }
                } else {
                    $rangeid = $cid;
                    $range[$rangeid] = array();
                    $range[$rangeid][] = $width;
                    $interval = false;
                }
                $prevcid = $cid;
                $prevwidth = $width;
            }
        }
        $prevk = -1;
        $nextk = -1;
        $prevint = false;
        foreach ($range as $k => $ws) {
            $cws = count($ws);
            if ($k == $nextk && !$prevint && (!isset($ws["interval"]) || $cws < 4)) {
                if (isset($range[$k]["interval"])) {
                    unset($range[$k]["interval"]);
                }
                $range[$prevk] = array_merge($range[$prevk], $range[$k]);
                unset($range[$k]);
            } else {
                $prevk = $k;
            }
            $nextk = $k + $cws;
            if (isset($ws["interval"])) {
                if (3 < $cws) {
                    $prevint = true;
                } else {
                    $prevint = false;
                }
                if (isset($range[$k]["interval"])) {
                    unset($range[$k]["interval"]);
                }
                $nextk--;
            } else {
                $prevint = false;
            }
        }
        $w = "";
        foreach ($range as $k => $ws) {
            if (count(array_count_values($ws)) == 1) {
                $w .= " " . $k . " " . ($k + count($ws) - 1) . " " . $ws[0];
            } else {
                $w .= " " . $k . " [ " . implode(" ", $ws) . " ]";
            }
        }
        return "/W [" . $w . " ]";
    }
    /**
     * Update the CIDToGIDMap string with a new value.
     * @param $map (string) CIDToGIDMap.
     * @param $cid (int) CID value.
     * @param $gid (int) GID value.
     * @return (string) CIDToGIDMap.
     * @author Nicola Asuni
     * @since 5.9.123 (2011-09-29)
     * @public static
     */
    public static function updateCIDtoGIDmap($map, $cid, $gid)
    {
        if (0 <= $cid && $cid <= 65535 && 0 <= $gid) {
            if (65535 < $gid) {
                $gid -= 65536;
            }
            $map[$cid * 2] = chr($gid >> 8);
            $map[$cid * 2 + 1] = chr($gid & 255);
        }
        return $map;
    }
    /**
     * Return fonts path
     * @return string
     * @public static
     */
    public static function _getfontpath()
    {
        if (!defined("K_PATH_FONTS") && is_dir($fdir = realpath(dirname(__FILE__) . "/../fonts"))) {
            if (substr($fdir, -1) != "/") {
                $fdir .= "/";
            }
            define("K_PATH_FONTS", $fdir);
        }
        return defined("K_PATH_FONTS") ? K_PATH_FONTS : "";
    }
    /**
     * Return font full path
     * @param $file (string) Font file name.
     * @param $fontdir (string) Font directory (set to false fto search on default directories)
     * @return string Font full path or empty string
     * @author Nicola Asuni
     * @since 6.0.025
     * @public static
     */
    public static function getFontFullPath($file, $fontdir = false)
    {
        $fontfile = "";
        if ($fontdir !== false && @file_exists($fontdir . $file)) {
            $fontfile = $fontdir . $file;
        } else {
            if (@file_exists(@self::_getfontpath() . $file)) {
                $fontfile = self::_getfontpath() . $file;
            } else {
                if (@file_exists($file)) {
                    $fontfile = $file;
                }
            }
        }
        return $fontfile;
    }
    /**
     * Get a reference font size.
     * @param $size (string) String containing font size value.
     * @param $refsize (float) Reference font size in points.
     * @return float value in points
     * @public static
     */
    public static function getFontRefSize($size, $refsize = 12)
    {
        switch ($size) {
            case "xx-small":
                $size = $refsize - 4;
                break;
            case "x-small":
                $size = $refsize - 3;
                break;
            case "small":
                $size = $refsize - 2;
                break;
            case "medium":
                $size = $refsize;
                break;
            case "large":
                $size = $refsize + 2;
                break;
            case "x-large":
                $size = $refsize + 4;
                break;
            case "xx-large":
                $size = $refsize + 6;
                break;
            case "smaller":
                $size = $refsize - 3;
                break;
            case "larger":
                $size = $refsize + 3;
                break;
        }
        return $size;
    }
    /**
     * Returns the unicode caracter specified by the value
     * @param $c (int) UTF-8 value
     * @param $unicode (boolean) True if we are in unicode mode, false otherwise.
     * @return Returns the specified character.
     * @since 2.3.000 (2008-03-05)
     * @public static
     */
    public static function unichr($c, $unicode = true)
    {
        if (!$unicode) {
            return chr($c);
        }
        if ($c <= 127) {
            return chr($c);
        }
        if ($c <= 2047) {
            return chr(192 | $c >> 6) . chr(128 | $c & 63);
        }
        if ($c <= 65535) {
            return chr(224 | $c >> 12) . chr(128 | $c >> 6 & 63) . chr(128 | $c & 63);
        }
        if ($c <= 1114111) {
            return chr(240 | $c >> 18) . chr(128 | $c >> 12 & 63) . chr(128 | $c >> 6 & 63) . chr(128 | $c & 63);
        }
        return "";
    }
    /**
     * Returns the unicode caracter specified by UTF-8 value
     * @param $c (int) UTF-8 value
     * @return Returns the specified character.
     * @public static
     */
    public static function unichrUnicode($c)
    {
        return self::unichr($c, true);
    }
    /**
     * Returns the unicode caracter specified by ASCII value
     * @param $c (int) UTF-8 value
     * @return Returns the specified character.
     * @public static
     */
    public static function unichrASCII($c)
    {
        return self::unichr($c, false);
    }
    /**
     * Converts array of UTF-8 characters to UTF16-BE string.<br>
     * Based on: http://www.faqs.org/rfcs/rfc2781.html
     * <pre>
     *   Encoding UTF-16:
     *
     *   Encoding of a single character from an ISO 10646 character value to
     *    UTF-16 proceeds as follows. Let U be the character number, no greater
     *    than 0x10FFFF.
     *
     *    1) If U < 0x10000, encode U as a 16-bit unsigned integer and
     *       terminate.
     *
     *    2) Let U' = U - 0x10000. Because U is less than or equal to 0x10FFFF,
     *       U' must be less than or equal to 0xFFFFF. That is, U' can be
     *       represented in 20 bits.
     *
     *    3) Initialize two 16-bit unsigned integers, W1 and W2, to 0xD800 and
     *       0xDC00, respectively. These integers each have 10 bits free to
     *       encode the character value, for a total of 20 bits.
     *
     *    4) Assign the 10 high-order bits of the 20-bit U' to the 10 low-order
     *       bits of W1 and the 10 low-order bits of U' to the 10 low-order
     *       bits of W2. Terminate.
     *
     *    Graphically, steps 2 through 4 look like:
     *    U' = yyyyyyyyyyxxxxxxxxxx
     *    W1 = 110110yyyyyyyyyy
     *    W2 = 110111xxxxxxxxxx
     * </pre>
     * @param $unicode (array) array containing UTF-8 unicode values
     * @param $setbom (boolean) if true set the Byte Order Mark (BOM = 0xFEFF)
     * @return string
     * @protected
     * @author Nicola Asuni
     * @since 2.1.000 (2008-01-08)
     * @public static
     */
    public static function arrUTF8ToUTF16BE($unicode, $setbom = false)
    {
        $outstr = "";
        if ($setbom) {
            $outstr .= "þÿ";
        }
        foreach ($unicode as $char) {
            if ($char == 8203) {
            } else {
                if ($char == 65533) {
                    $outstr .= "ÿý";
                } else {
                    if ($char < 65536) {
                        $outstr .= chr($char >> 8);
                        $outstr .= chr($char & 255);
                    } else {
                        $char -= 65536;
                        $w1 = 55296 | $char >> 10;
                        $w2 = 56320 | $char & 1023;
                        $outstr .= chr($w1 >> 8);
                        $outstr .= chr($w1 & 255);
                        $outstr .= chr($w2 >> 8);
                        $outstr .= chr($w2 & 255);
                    }
                }
            }
        }
        return $outstr;
    }
    /**
     * Convert an array of UTF8 values to array of unicode characters
     * @param $ta (array) The input array of UTF8 values.
     * @param $isunicode (boolean) True for Unicode mode, false otherwise.
     * @return Return array of unicode characters
     * @since 4.5.037 (2009-04-07)
     * @public static
     */
    public static function UTF8ArrayToUniArray($ta, $isunicode = true)
    {
        if ($isunicode) {
            return array_map(array("TCPDF_FONTS", "unichrUnicode"), $ta);
        }
        return array_map(array("TCPDF_FONTS", "unichrASCII"), $ta);
    }
    /**
     * Extract a slice of the $strarr array and return it as string.
     * @param $strarr (string) The input array of characters.
     * @param $start (int) the starting element of $strarr.
     * @param $end (int) first element that will not be returned.
     * @param $unicode (boolean) True if we are in unicode mode, false otherwise.
     * @return Return part of a string
     * @public static
     */
    public static function UTF8ArrSubString($strarr, $start = "", $end = "", $unicode = true)
    {
        if (strlen($start) == 0) {
            $start = 0;
        }
        if (strlen($end) == 0) {
            $end = count($strarr);
        }
        $string = "";
        for ($i = $start; $i < $end; $i++) {
            $string .= self::unichr($strarr[$i], $unicode);
        }
        return $string;
    }
    /**
     * Extract a slice of the $uniarr array and return it as string.
     * @param $uniarr (string) The input array of characters.
     * @param $start (int) the starting element of $strarr.
     * @param $end (int) first element that will not be returned.
     * @return Return part of a string
     * @since 4.5.037 (2009-04-07)
     * @public static
     */
    public static function UniArrSubString($uniarr, $start = "", $end = "")
    {
        if (strlen($start) == 0) {
            $start = 0;
        }
        if (strlen($end) == 0) {
            $end = count($uniarr);
        }
        $string = "";
        for ($i = $start; $i < $end; $i++) {
            $string .= $uniarr[$i];
        }
        return $string;
    }
    /**
     * Converts UTF-8 characters array to array of Latin1 characters array<br>
     * @param $unicode (array) array containing UTF-8 unicode values
     * @return array
     * @author Nicola Asuni
     * @since 4.8.023 (2010-01-15)
     * @public static
     */
    public static function UTF8ArrToLatin1Arr($unicode)
    {
        $outarr = array();
        foreach ($unicode as $char) {
            if ($char < 256) {
                $outarr[] = $char;
            } else {
                if (array_key_exists($char, TCPDF_FONT_DATA::$uni_utf8tolatin)) {
                    $outarr[] = TCPDF_FONT_DATA::$uni_utf8tolatin[$char];
                } else {
                    if ($char == 65533) {
                    } else {
                        $outarr[] = 63;
                    }
                }
            }
        }
        return $outarr;
    }
    /**
     * Converts UTF-8 characters array to array of Latin1 string<br>
     * @param $unicode (array) array containing UTF-8 unicode values
     * @return array
     * @author Nicola Asuni
     * @since 4.8.023 (2010-01-15)
     * @public static
     */
    public static function UTF8ArrToLatin1($unicode)
    {
        $outstr = "";
        foreach ($unicode as $char) {
            if ($char < 256) {
                $outstr .= chr($char);
            } else {
                if (array_key_exists($char, TCPDF_FONT_DATA::$uni_utf8tolatin)) {
                    $outstr .= chr(TCPDF_FONT_DATA::$uni_utf8tolatin[$char]);
                } else {
                    if ($char == 65533) {
                    } else {
                        $outstr .= "?";
                    }
                }
            }
        }
        return $outstr;
    }
    /**
     * Converts UTF-8 character to integer value.<br>
     * Uses the getUniord() method if the value is not cached.
     * @param $uch (string) character string to process.
     * @return integer Unicode value
     * @public static
     */
    public static function uniord($uch)
    {
        if (!isset(self::$cache_uniord[$uch])) {
            self::$cache_uniord[$uch] = self::getUniord($uch);
        }
        return self::$cache_uniord[$uch];
    }
    /**
     * Converts UTF-8 character to integer value.<br>
     * Invalid byte sequences will be replaced with 0xFFFD (replacement character)<br>
     * Based on: http://www.faqs.org/rfcs/rfc3629.html
     * <pre>
     *    Char. number range  |        UTF-8 octet sequence
     *       (hexadecimal)    |              (binary)
     *    --------------------+-----------------------------------------------
     *    0000 0000-0000 007F | 0xxxxxxx
     *    0000 0080-0000 07FF | 110xxxxx 10xxxxxx
     *    0000 0800-0000 FFFF | 1110xxxx 10xxxxxx 10xxxxxx
     *    0001 0000-0010 FFFF | 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx
     *    ---------------------------------------------------------------------
     *
     *   ABFN notation:
     *   ---------------------------------------------------------------------
     *   UTF8-octets = *( UTF8-char )
     *   UTF8-char   = UTF8-1 / UTF8-2 / UTF8-3 / UTF8-4
     *   UTF8-1      = %x00-7F
     *   UTF8-2      = %xC2-DF UTF8-tail
     *
     *   UTF8-3      = %xE0 %xA0-BF UTF8-tail / %xE1-EC 2( UTF8-tail ) /
     *                 %xED %x80-9F UTF8-tail / %xEE-EF 2( UTF8-tail )
     *   UTF8-4      = %xF0 %x90-BF 2( UTF8-tail ) / %xF1-F3 3( UTF8-tail ) /
     *                 %xF4 %x80-8F 2( UTF8-tail )
     *   UTF8-tail   = %x80-BF
     *   ---------------------------------------------------------------------
     * </pre>
     * @param $uch (string) character string to process.
     * @return integer Unicode value
     * @author Nicola Asuni
     * @public static
     */
    public static function getUniord($uch)
    {
        if (function_exists("mb_convert_encoding")) {
            list(, $char) = @unpack("N", @mb_convert_encoding($uch, "UCS-4BE", "UTF-8"));
            if (0 <= $char) {
                return $char;
            }
        }
        $bytes = array();
        $countbytes = 0;
        $numbytes = 1;
        $length = strlen($uch);
        for ($i = 0; $i < $length; $i++) {
            $char = ord($uch[$i]);
            if ($countbytes == 0) {
                if ($char <= 127) {
                    return $char;
                }
                if ($char >> 5 == 6) {
                    $bytes[] = $char - 192 << 6;
                    $countbytes++;
                    $numbytes = 2;
                } else {
                    if ($char >> 4 == 14) {
                        $bytes[] = $char - 224 << 12;
                        $countbytes++;
                        $numbytes = 3;
                    } else {
                        if ($char >> 3 == 30) {
                            $bytes[] = $char - 240 << 18;
                            $countbytes++;
                            $numbytes = 4;
                        } else {
                            return 65533;
                        }
                    }
                }
            } else {
                if ($char >> 6 == 2) {
                    $bytes[] = $char - 128;
                    $countbytes++;
                    if ($countbytes == $numbytes) {
                        $char = $bytes[0];
                        for ($j = 1; $j < $numbytes; $j++) {
                            $char += $bytes[$j] << ($numbytes - $j - 1) * 6;
                        }
                        if (55296 <= $char && $char <= 57343 || 1114111 <= $char) {
                            return 65533;
                        }
                        return $char;
                    }
                } else {
                    return 65533;
                }
            }
        }
        return 65533;
    }
    /**
     * Converts UTF-8 strings to codepoints array.<br>
     * Invalid byte sequences will be replaced with 0xFFFD (replacement character)<br>
     * @param $str (string) string to process.
     * @param $isunicode (boolean) True when the documetn is in Unicode mode, false otherwise.
     * @param $currentfont (array) Reference to current font array.
     * @return array containing codepoints (UTF-8 characters values)
     * @author Nicola Asuni
     * @public static
     */
    public static function UTF8StringToArray($str, $isunicode = true, &$currentfont)
    {
        if ($isunicode) {
            $chars = TCPDF_STATIC::pregSplit("//", "u", $str, -1, PREG_SPLIT_NO_EMPTY);
            $carr = array_map(array("TCPDF_FONTS", "uniord"), $chars);
        } else {
            $chars = str_split($str);
            $carr = array_map("ord", $chars);
        }
        $currentfont["subsetchars"] += array_fill_keys($carr, true);
        return $carr;
    }
    /**
     * Converts UTF-8 strings to Latin1 when using the standard 14 core fonts.<br>
     * @param $str (string) string to process.
     * @param $isunicode (boolean) True when the documetn is in Unicode mode, false otherwise.
     * @param $currentfont (array) Reference to current font array.
     * @return string
     * @since 3.2.000 (2008-06-23)
     * @public static
     */
    public static function UTF8ToLatin1($str, $isunicode = true, &$currentfont)
    {
        $unicode = self::UTF8StringToArray($str, $isunicode, $currentfont);
        return self::UTF8ArrToLatin1($unicode);
    }
    /**
     * Converts UTF-8 strings to UTF16-BE.<br>
     * @param $str (string) string to process.
     * @param $setbom (boolean) if true set the Byte Order Mark (BOM = 0xFEFF)
     * @param $isunicode (boolean) True when the documetn is in Unicode mode, false otherwise.
     * @param $currentfont (array) Reference to current font array.
     * @return string
     * @author Nicola Asuni
     * @since 1.53.0.TC005 (2005-01-05)
     * @public static
     */
    public static function UTF8ToUTF16BE($str, $setbom = false, $isunicode = true, &$currentfont)
    {
        if (!$isunicode) {
            return $str;
        }
        $unicode = self::UTF8StringToArray($str, $isunicode, $currentfont);
        return self::arrUTF8ToUTF16BE($unicode, $setbom);
    }
    /**
     * Reverse the RLT substrings using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
     * @param $str (string) string to manipulate.
     * @param $setbom (bool) if true set the Byte Order Mark (BOM = 0xFEFF)
     * @param $forcertl (bool) if true forces RTL text direction
     * @param $isunicode (boolean) True if the document is in Unicode mode, false otherwise.
     * @param $currentfont (array) Reference to current font array.
     * @return string
     * @author Nicola Asuni
     * @since 2.1.000 (2008-01-08)
     * @public static
     */
    public static function utf8StrRev($str, $setbom = false, $forcertl = false, $isunicode = true, &$currentfont)
    {
        return self::utf8StrArrRev(self::UTF8StringToArray($str, $isunicode, $currentfont), $str, $setbom, $forcertl, $isunicode, $currentfont);
    }
    /**
     * Reverse the RLT substrings array using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
     * @param $arr (array) array of unicode values.
     * @param $str (string) string to manipulate (or empty value).
     * @param $setbom (bool) if true set the Byte Order Mark (BOM = 0xFEFF)
     * @param $forcertl (bool) if true forces RTL text direction
     * @param $isunicode (boolean) True if the document is in Unicode mode, false otherwise.
     * @param $currentfont (array) Reference to current font array.
     * @return string
     * @author Nicola Asuni
     * @since 4.9.000 (2010-03-27)
     * @public static
     */
    public static function utf8StrArrRev($arr, $str = "", $setbom = false, $forcertl = false, $isunicode = true, &$currentfont)
    {
        return self::arrUTF8ToUTF16BE(self::utf8Bidi($arr, $str, $forcertl, $isunicode, $currentfont), $setbom);
    }
    /**
     * Reverse the RLT substrings using the Bidirectional Algorithm (http://unicode.org/reports/tr9/).
     * @param $ta (array) array of characters composing the string.
     * @param $str (string) string to process
     * @param $forcertl (bool) if 'R' forces RTL, if 'L' forces LTR
     * @param $isunicode (boolean) True if the document is in Unicode mode, false otherwise.
     * @param $currentfont (array) Reference to current font array.
     * @return array of unicode chars
     * @author Nicola Asuni
     * @since 2.4.000 (2008-03-06)
     * @public static
     */
    public static function utf8Bidi($ta, $str = "", $forcertl = false, $isunicode = true, &$currentfont)
    {
        $pel = 0;
        $maxlevel = 0;
        if (TCPDF_STATIC::empty_string($str)) {
            $str = self::UTF8ArrSubString($ta, "", "", $isunicode);
        }
        if (preg_match(TCPDF_FONT_DATA::$uni_RE_PATTERN_ARABIC, $str)) {
            $arabic = true;
        } else {
            $arabic = false;
        }
        if (!($forcertl || $arabic || preg_match(TCPDF_FONT_DATA::$uni_RE_PATTERN_RTL, $str))) {
            return $ta;
        }
        $numchars = count($ta);
        if ($forcertl == "R") {
            $pel = 1;
        } else {
            if ($forcertl == "L") {
                $pel = 0;
            } else {
                for ($i = 0; $i < $numchars; $i++) {
                    $type = TCPDF_FONT_DATA::$uni_type[$ta[$i]];
                    if ($type == "L") {
                        $pel = 0;
                        break;
                    }
                    if ($type == "AL" || $type == "R") {
                        $pel = 1;
                        break;
                    }
                }
            }
        }
        $cel = $pel;
        $dos = "N";
        $remember = array();
        $sor = $pel % 2 ? "R" : "L";
        $eor = $sor;
        $chardata = array();
        for ($i = 0; $i < $numchars; $i++) {
            if ($ta[$i] == TCPDF_FONT_DATA::$uni_RLE) {
                $next_level = $cel + $cel % 2 + 1;
                if ($next_level < 62) {
                    $remember[] = array("num" => TCPDF_FONT_DATA::$uni_RLE, "cel" => $cel, "dos" => $dos);
                    $cel = $next_level;
                    $dos = "N";
                    $sor = $eor;
                    $eor = $cel % 2 ? "R" : "L";
                }
            } else {
                if ($ta[$i] == TCPDF_FONT_DATA::$uni_LRE) {
                    $next_level = $cel + 2 - $cel % 2;
                    if ($next_level < 62) {
                        $remember[] = array("num" => TCPDF_FONT_DATA::$uni_LRE, "cel" => $cel, "dos" => $dos);
                        $cel = $next_level;
                        $dos = "N";
                        $sor = $eor;
                        $eor = $cel % 2 ? "R" : "L";
                    }
                } else {
                    if ($ta[$i] == TCPDF_FONT_DATA::$uni_RLO) {
                        $next_level = $cel + $cel % 2 + 1;
                        if ($next_level < 62) {
                            $remember[] = array("num" => TCPDF_FONT_DATA::$uni_RLO, "cel" => $cel, "dos" => $dos);
                            $cel = $next_level;
                            $dos = "R";
                            $sor = $eor;
                            $eor = $cel % 2 ? "R" : "L";
                        }
                    } else {
                        if ($ta[$i] == TCPDF_FONT_DATA::$uni_LRO) {
                            $next_level = $cel + 2 - $cel % 2;
                            if ($next_level < 62) {
                                $remember[] = array("num" => TCPDF_FONT_DATA::$uni_LRO, "cel" => $cel, "dos" => $dos);
                                $cel = $next_level;
                                $dos = "L";
                                $sor = $eor;
                                $eor = $cel % 2 ? "R" : "L";
                            }
                        } else {
                            if ($ta[$i] == TCPDF_FONT_DATA::$uni_PDF) {
                                if (count($remember)) {
                                    $last = count($remember) - 1;
                                    if ($remember[$last]["num"] == TCPDF_FONT_DATA::$uni_RLE || $remember[$last]["num"] == TCPDF_FONT_DATA::$uni_LRE || $remember[$last]["num"] == TCPDF_FONT_DATA::$uni_RLO || $remember[$last]["num"] == TCPDF_FONT_DATA::$uni_LRO) {
                                        $match = array_pop($remember);
                                        $cel = $match["cel"];
                                        $dos = $match["dos"];
                                        $sor = $eor;
                                        $eor = ($match["cel"] < $cel ? $cel : $match["cel"]) % 2 ? "R" : "L";
                                    }
                                }
                            } else {
                                if ($ta[$i] != TCPDF_FONT_DATA::$uni_RLE && $ta[$i] != TCPDF_FONT_DATA::$uni_LRE && $ta[$i] != TCPDF_FONT_DATA::$uni_RLO && $ta[$i] != TCPDF_FONT_DATA::$uni_LRO && $ta[$i] != TCPDF_FONT_DATA::$uni_PDF) {
                                    if ($dos != "N") {
                                        $chardir = $dos;
                                    } else {
                                        if (isset(TCPDF_FONT_DATA::$uni_type[$ta[$i]])) {
                                            $chardir = TCPDF_FONT_DATA::$uni_type[$ta[$i]];
                                        } else {
                                            $chardir = "L";
                                        }
                                    }
                                    $chardata[] = array("char" => $ta[$i], "level" => $cel, "type" => $chardir, "sor" => $sor, "eor" => $eor);
                                }
                            }
                        }
                    }
                }
            }
        }
        $numchars = count($chardata);
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["type"] == "NSM") {
                if ($levcount) {
                    $chardata[$i]["type"] = $chardata[$i]["sor"];
                } else {
                    if (0 < $i) {
                        $chardata[$i]["type"] = $chardata[$i - 1]["type"];
                    }
                }
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["char"] == "EN") {
                for ($j = $levcount; 0 <= $j; $j--) {
                    if ($chardata[$j]["type"] == "AL") {
                        $chardata[$i]["type"] = "AN";
                    } else {
                        if ($chardata[$j]["type"] == "L" || $chardata[$j]["type"] == "R") {
                            break;
                        }
                    }
                }
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["type"] == "AL") {
                $chardata[$i]["type"] = "R";
            }
        }
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if (0 < $levcount && $i + 1 < $numchars && $chardata[$i + 1]["level"] == $prevlevel) {
                if ($chardata[$i]["type"] == "ES" && $chardata[$i - 1]["type"] == "EN" && $chardata[$i + 1]["type"] == "EN") {
                    $chardata[$i]["type"] = "EN";
                } else {
                    if ($chardata[$i]["type"] == "CS" && $chardata[$i - 1]["type"] == "EN" && $chardata[$i + 1]["type"] == "EN") {
                        $chardata[$i]["type"] = "EN";
                    } else {
                        if ($chardata[$i]["type"] == "CS" && $chardata[$i - 1]["type"] == "AN" && $chardata[$i + 1]["type"] == "AN") {
                            $chardata[$i]["type"] = "AN";
                        }
                    }
                }
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["type"] == "ET") {
                if (0 < $levcount && $chardata[$i - 1]["type"] == "EN") {
                    $chardata[$i]["type"] = "EN";
                } else {
                    for ($j = $i + 1; $j < $numchars && $chardata[$j]["level"] == $prevlevel; $j++) {
                        if ($chardata[$j]["type"] == "EN") {
                            $chardata[$i]["type"] = "EN";
                            break;
                        }
                        if ($chardata[$j]["type"] != "ET") {
                            break;
                        }
                    }
                }
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["type"] == "ET" || $chardata[$i]["type"] == "ES" || $chardata[$i]["type"] == "CS") {
                $chardata[$i]["type"] = "ON";
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["char"] == "EN") {
                for ($j = $levcount; 0 <= $j; $j--) {
                    if ($chardata[$j]["type"] == "L") {
                        $chardata[$i]["type"] = "L";
                    } else {
                        if ($chardata[$j]["type"] == "R") {
                            break;
                        }
                    }
                }
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        $prevlevel = -1;
        $levcount = 0;
        for ($i = 0; $i < $numchars; $i++) {
            if (0 < $levcount && $i + 1 < $numchars && $chardata[$i + 1]["level"] == $prevlevel) {
                if ($chardata[$i]["type"] == "N" && $chardata[$i - 1]["type"] == "L" && $chardata[$i + 1]["type"] == "L") {
                    $chardata[$i]["type"] = "L";
                } else {
                    if ($chardata[$i]["type"] == "N" && ($chardata[$i - 1]["type"] == "R" || $chardata[$i - 1]["type"] == "EN" || $chardata[$i - 1]["type"] == "AN") && ($chardata[$i + 1]["type"] == "R" || $chardata[$i + 1]["type"] == "EN" || $chardata[$i + 1]["type"] == "AN")) {
                        $chardata[$i]["type"] = "R";
                    } else {
                        if ($chardata[$i]["type"] == "N") {
                            $chardata[$i]["type"] = $chardata[$i]["sor"];
                        }
                    }
                }
            } else {
                if ($levcount == 0 && $i + 1 < $numchars && $chardata[$i + 1]["level"] == $prevlevel) {
                    if ($chardata[$i]["type"] == "N" && $chardata[$i]["sor"] == "L" && $chardata[$i + 1]["type"] == "L") {
                        $chardata[$i]["type"] = "L";
                    } else {
                        if ($chardata[$i]["type"] == "N" && ($chardata[$i]["sor"] == "R" || $chardata[$i]["sor"] == "EN" || $chardata[$i]["sor"] == "AN") && ($chardata[$i + 1]["type"] == "R" || $chardata[$i + 1]["type"] == "EN" || $chardata[$i + 1]["type"] == "AN")) {
                            $chardata[$i]["type"] = "R";
                        } else {
                            if ($chardata[$i]["type"] == "N") {
                                $chardata[$i]["type"] = $chardata[$i]["sor"];
                            }
                        }
                    }
                } else {
                    if (0 < $levcount && ($i + 1 == $numchars || $i + 1 < $numchars && $chardata[$i + 1]["level"] != $prevlevel)) {
                        if ($chardata[$i]["type"] == "N" && $chardata[$i - 1]["type"] == "L" && $chardata[$i]["eor"] == "L") {
                            $chardata[$i]["type"] = "L";
                        } else {
                            if ($chardata[$i]["type"] == "N" && ($chardata[$i - 1]["type"] == "R" || $chardata[$i - 1]["type"] == "EN" || $chardata[$i - 1]["type"] == "AN") && ($chardata[$i]["eor"] == "R" || $chardata[$i]["eor"] == "EN" || $chardata[$i]["eor"] == "AN")) {
                                $chardata[$i]["type"] = "R";
                            } else {
                                if ($chardata[$i]["type"] == "N") {
                                    $chardata[$i]["type"] = $chardata[$i]["sor"];
                                }
                            }
                        }
                    } else {
                        if ($chardata[$i]["type"] == "N") {
                            $chardata[$i]["type"] = $chardata[$i]["sor"];
                        }
                    }
                }
            }
            if ($chardata[$i]["level"] != $prevlevel) {
                $levcount = 0;
            } else {
                $levcount++;
            }
            $prevlevel = $chardata[$i]["level"];
        }
        for ($i = 0; $i < $numchars; $i++) {
            $odd = $chardata[$i]["level"] % 2;
            if ($odd) {
                if ($chardata[$i]["type"] == "L" || $chardata[$i]["type"] == "AN" || $chardata[$i]["type"] == "EN") {
                    $chardata[$i]["level"] += 1;
                }
            } else {
                if ($chardata[$i]["type"] == "R") {
                    $chardata[$i]["level"] += 1;
                } else {
                    if ($chardata[$i]["type"] == "AN" || $chardata[$i]["type"] == "EN") {
                        $chardata[$i]["level"] += 2;
                    }
                }
            }
            $maxlevel = max($chardata[$i]["level"], $maxlevel);
        }
        for ($i = 0; $i < $numchars; $i++) {
            if ($chardata[$i]["type"] == "B" || $chardata[$i]["type"] == "S") {
                $chardata[$i]["level"] = $pel;
            } else {
                if ($chardata[$i]["type"] == "WS") {
                    for ($j = $i + 1; $j < $numchars; $j++) {
                        if ($chardata[$j]["type"] == "B" || $chardata[$j]["type"] == "S" || $j == $numchars - 1 && $chardata[$j]["type"] == "WS") {
                            $chardata[$i]["level"] = $pel;
                            break;
                        }
                        if ($chardata[$j]["type"] != "WS") {
                            break;
                        }
                    }
                }
            }
        }
        if ($arabic) {
            $endedletter = array(1569, 1570, 1571, 1572, 1573, 1575, 1577, 1583, 1584, 1585, 1586, 1608, 1688);
            $alfletter = array(1570, 1571, 1573, 1575);
            $chardata2 = $chardata;
            $laaletter = false;
            $charAL = array();
            $x = 0;
            for ($i = 0; $i < $numchars; $i++) {
                if (TCPDF_FONT_DATA::$uni_type[$chardata[$i]["char"]] == "AL" || $chardata[$i]["char"] == 32 || $chardata[$i]["char"] == 8204) {
                    $charAL[$x] = $chardata[$i];
                    $charAL[$x]["i"] = $i;
                    $chardata[$i]["x"] = $x;
                    $x++;
                }
            }
            $numAL = $x;
            for ($i = 0; $i < $numchars; $i++) {
                $thischar = $chardata[$i];
                if (0 < $i) {
                    $prevchar = $chardata[$i - 1];
                } else {
                    $prevchar = false;
                }
                if ($i + 1 < $numchars) {
                    $nextchar = $chardata[$i + 1];
                } else {
                    $nextchar = false;
                }
                if (TCPDF_FONT_DATA::$uni_type[$thischar["char"]] == "AL") {
                    $x = $thischar["x"];
                    if (0 < $x) {
                        $prevchar = $charAL[$x - 1];
                    } else {
                        $prevchar = false;
                    }
                    if ($x + 1 < $numAL) {
                        $nextchar = $charAL[$x + 1];
                    } else {
                        $nextchar = false;
                    }
                    if ($prevchar !== false && $prevchar["char"] == 1604 && in_array($thischar["char"], $alfletter)) {
                        $arabicarr = TCPDF_FONT_DATA::$uni_laa_array;
                        $laaletter = true;
                        if (1 < $x) {
                            $prevchar = $charAL[$x - 2];
                        } else {
                            $prevchar = false;
                        }
                    } else {
                        $arabicarr = TCPDF_FONT_DATA::$uni_arabicsubst;
                        $laaletter = false;
                    }
                    if ($prevchar !== false && $nextchar !== false && (TCPDF_FONT_DATA::$uni_type[$prevchar["char"]] == "AL" || TCPDF_FONT_DATA::$uni_type[$prevchar["char"]] == "NSM") && (TCPDF_FONT_DATA::$uni_type[$nextchar["char"]] == "AL" || TCPDF_FONT_DATA::$uni_type[$nextchar["char"]] == "NSM") && $prevchar["type"] == $thischar["type"] && $nextchar["type"] == $thischar["type"] && $nextchar["char"] != 1567) {
                        if (in_array($prevchar["char"], $endedletter)) {
                            if (isset($arabicarr[$thischar["char"]][2])) {
                                $chardata2[$i]["char"] = $arabicarr[$thischar["char"]][2];
                            }
                        } else {
                            if (isset($arabicarr[$thischar["char"]][3])) {
                                $chardata2[$i]["char"] = $arabicarr[$thischar["char"]][3];
                            }
                        }
                    } else {
                        if ($nextchar !== false && (TCPDF_FONT_DATA::$uni_type[$nextchar["char"]] == "AL" || TCPDF_FONT_DATA::$uni_type[$nextchar["char"]] == "NSM") && $nextchar["type"] == $thischar["type"] && $nextchar["char"] != 1567) {
                            if (isset($arabicarr[$chardata[$i]["char"]][2])) {
                                $chardata2[$i]["char"] = $arabicarr[$thischar["char"]][2];
                            }
                        } else {
                            if ($prevchar !== false && (TCPDF_FONT_DATA::$uni_type[$prevchar["char"]] == "AL" || TCPDF_FONT_DATA::$uni_type[$prevchar["char"]] == "NSM") && $prevchar["type"] == $thischar["type"] || $nextchar !== false && $nextchar["char"] == 1567) {
                                if (1 < $i && $thischar["char"] == 1607 && $chardata[$i - 1]["char"] == 1604 && $chardata[$i - 2]["char"] == 1604) {
                                    $chardata2[$i - 2]["char"] = false;
                                    $chardata2[$i - 1]["char"] = false;
                                    $chardata2[$i]["char"] = 65010;
                                } else {
                                    if ($prevchar !== false && in_array($prevchar["char"], $endedletter)) {
                                        if (isset($arabicarr[$thischar["char"]][0])) {
                                            $chardata2[$i]["char"] = $arabicarr[$thischar["char"]][0];
                                        }
                                    } else {
                                        if (isset($arabicarr[$thischar["char"]][1])) {
                                            $chardata2[$i]["char"] = $arabicarr[$thischar["char"]][1];
                                        }
                                    }
                                }
                            } else {
                                if (isset($arabicarr[$thischar["char"]][0])) {
                                    $chardata2[$i]["char"] = $arabicarr[$thischar["char"]][0];
                                }
                            }
                        }
                    }
                    if ($laaletter) {
                        $chardata2[$charAL[$x - 1]["i"]]["char"] = false;
                    }
                }
            }
            for ($i = 0; $i < $numchars - 1; $i++) {
                if ($chardata2[$i]["char"] == 1617 && isset(TCPDF_FONT_DATA::$uni_diacritics[$chardata2[$i + 1]["char"]]) && isset($currentfont["cw"][TCPDF_FONT_DATA::$uni_diacritics[$chardata2[$i + 1]["char"]]])) {
                    $chardata2[$i]["char"] = false;
                    $chardata2[$i + 1]["char"] = TCPDF_FONT_DATA::$uni_diacritics[$chardata2[$i + 1]["char"]];
                }
            }
            foreach ($chardata2 as $key => $value) {
                if ($value["char"] === false) {
                    unset($chardata2[$key]);
                }
            }
            $chardata = array_values($chardata2);
            $numchars = count($chardata);
            unset($chardata2);
            unset($arabicarr);
            unset($laaletter);
            unset($charAL);
        }
        for ($j = $maxlevel; 0 < $j; $j--) {
            $ordarray = array();
            $revarr = array();
            $onlevel = false;
            for ($i = 0; $i < $numchars; $i++) {
                if ($j <= $chardata[$i]["level"]) {
                    $onlevel = true;
                    if (isset(TCPDF_FONT_DATA::$uni_mirror[$chardata[$i]["char"]])) {
                        $chardata[$i]["char"] = TCPDF_FONT_DATA::$uni_mirror[$chardata[$i]["char"]];
                    }
                    $revarr[] = $chardata[$i];
                } else {
                    if ($onlevel) {
                        $revarr = array_reverse($revarr);
                        $ordarray = array_merge($ordarray, $revarr);
                        $revarr = array();
                        $onlevel = false;
                    }
                    $ordarray[] = $chardata[$i];
                }
            }
            if ($onlevel) {
                $revarr = array_reverse($revarr);
                $ordarray = array_merge($ordarray, $revarr);
            }
            $chardata = $ordarray;
        }
        $ordarray = array();
        foreach ($chardata as $cd) {
            $ordarray[] = $cd["char"];
            $currentfont["subsetchars"][$cd["char"]] = true;
        }
        return $ordarray;
    }
}

?>