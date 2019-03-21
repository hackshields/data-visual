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
 * @class TCPDF_IMAGES
 * Static image methods used by the TCPDF class.
 * @package com.tecnick.tcpdf
 * @brief PHP class for generating PDF documents without requiring external extensions.
 * @version 1.0.005
 * @author Nicola Asuni - info@tecnick.com
 */
class TCPDF_IMAGES
{
    /**
     * Array of hinheritable SVG properties.
     * @since 5.0.000 (2010-05-02)
     * @public static
     */
    public static $svginheritprop = array("clip-rule", "color", "color-interpolation", "color-interpolation-filters", "color-profile", "color-rendering", "cursor", "direction", "display", "fill", "fill-opacity", "fill-rule", "font", "font-family", "font-size", "font-size-adjust", "font-stretch", "font-style", "font-variant", "font-weight", "glyph-orientation-horizontal", "glyph-orientation-vertical", "image-rendering", "kerning", "letter-spacing", "marker", "marker-end", "marker-mid", "marker-start", "pointer-events", "shape-rendering", "stroke", "stroke-dasharray", "stroke-dashoffset", "stroke-linecap", "stroke-linejoin", "stroke-miterlimit", "stroke-opacity", "stroke-width", "text-anchor", "text-rendering", "visibility", "word-spacing", "writing-mode");
    /**
     * Return the image type given the file name or array returned by getimagesize() function.
     * @param $imgfile (string) image file name
     * @param $iminfo (array) array of image information returned by getimagesize() function.
     * @return string image type
     * @since 4.8.017 (2009-11-27)
     * @public static
     */
    public static function getImageFileType($imgfile, $iminfo = array())
    {
        $type = "";
        if (isset($iminfo["mime"]) && !empty($iminfo["mime"])) {
            $mime = explode("/", $iminfo["mime"]);
            if (1 < count($mime) && $mime[0] == "image" && !empty($mime[1])) {
                $type = strtolower(trim($mime[1]));
            }
        }
        if (empty($type)) {
            $fileinfo = pathinfo($imgfile);
            if (isset($fileinfo["extension"]) && !TCPDF_STATIC::empty_string($fileinfo["extension"])) {
                $type = strtolower(trim($fileinfo["extension"]));
            }
        }
        if ($type == "jpg") {
            $type = "jpeg";
        }
        return $type;
    }
    /**
     * Set the transparency for the given GD image.
     * @param $new_image (image) GD image object
     * @param $image (image) GD image object.
     * return GD image object.
     * @since 4.9.016 (2010-04-20)
     * @public static
     */
    public static function setGDImageTransparency($new_image, $image)
    {
        $tcol = array("red" => 255, "green" => 255, "blue" => 255);
        $tid = imagecolortransparent($image);
        $palletsize = imagecolorstotal($image);
        if (0 <= $tid && $tid < $palletsize) {
            $tcol = imagecolorsforindex($image, $tid);
        }
        $tid = imagecolorallocate($new_image, $tcol["red"], $tcol["green"], $tcol["blue"]);
        imagefill($new_image, 0, 0, $tid);
        imagecolortransparent($new_image, $tid);
        return $new_image;
    }
    /**
     * Convert the loaded image to a PNG and then return a structure for the PDF creator.
     * This function requires GD library and write access to the directory defined on K_PATH_CACHE constant.
     * @param $image (image) Image object.
     * @param $tempfile (string) Temporary file name.
     * return image PNG image object.
     * @since 4.9.016 (2010-04-20)
     * @public static
     */
    public static function _toPNG($image, $tempfile)
    {
        imageinterlace($image, 0);
        imagepng($image, $tempfile);
        imagedestroy($image);
        $retvars = self::_parsepng($tempfile);
        unlink($tempfile);
        return $retvars;
    }
    /**
     * Convert the loaded image to a JPEG and then return a structure for the PDF creator.
     * This function requires GD library and write access to the directory defined on K_PATH_CACHE constant.
     * @param $image (image) Image object.
     * @param $quality (int) JPEG quality.
     * @param $tempfile (string) Temporary file name.
     * return image JPEG image object.
     * @public static
     */
    public static function _toJPEG($image, $quality, $tempfile)
    {
        imagejpeg($image, $tempfile, $quality);
        imagedestroy($image);
        $retvars = self::_parsejpeg($tempfile);
        unlink($tempfile);
        return $retvars;
    }
    /**
     * Extract info from a JPEG file without using the GD library.
     * @param $file (string) image file to parse
     * @return array structure containing the image data
     * @public static
     */
    public static function _parsejpeg($file)
    {
        if (!@file_exists($file)) {
            $tfile = str_replace(" ", "%20", $file);
            if (@file_exists($tfile)) {
                $file = $tfile;
            }
        }
        $a = getimagesize($file);
        if (empty($a)) {
            return false;
        }
        if ($a[2] != 2) {
            return false;
        }
        $bpc = isset($a["bits"]) ? intval($a["bits"]) : 8;
        if (!isset($a["channels"])) {
            $channels = 3;
        } else {
            $channels = intval($a["channels"]);
        }
        switch ($channels) {
            case 1:
                $colspace = "DeviceGray";
                break;
            case 3:
                $colspace = "DeviceRGB";
                break;
            case 4:
                $colspace = "DeviceCMYK";
                break;
            default:
                $channels = 3;
                $colspace = "DeviceRGB";
                break;
        }
        $data = file_get_contents($file);
        $icc = array();
        $offset = 0;
        while (($pos = strpos($data, "ICC_PROFILE", $offset)) !== false) {
            $length = TCPDF_STATIC::_getUSHORT($data, $pos - 2) - 16;
            $msn = max(1, ord($data[$pos + 12]));
            $nom = max(1, ord($data[$pos + 13]));
            $icc[$msn - 1] = substr($data, $pos + 14, $length);
            $offset = $pos + 14 + $length;
        }
        if (0 < count($icc)) {
            ksort($icc);
            $icc = implode("", $icc);
            if (ord($icc[36]) != 97 || ord($icc[37]) != 99 || ord($icc[38]) != 115 || ord($icc[39]) != 112) {
                $icc = false;
            }
        } else {
            $icc = false;
        }
        return array("w" => $a[0], "h" => $a[1], "ch" => $channels, "icc" => $icc, "cs" => $colspace, "bpc" => $bpc, "f" => "DCTDecode", "data" => $data);
    }
    /**
     * Extract info from a PNG file without using the GD library.
     * @param $file (string) image file to parse
     * @return array structure containing the image data
     * @public static
     */
    public static function _parsepng($file)
    {
        $f = @fopen($file, "rb");
        if ($f === false) {
            return false;
        }
        if (fread($f, 8) != chr(137) . "PNG" . chr(13) . chr(10) . chr(26) . chr(10)) {
            return false;
        }
        fread($f, 4);
        if (fread($f, 4) != "IHDR") {
            return false;
        }
        $w = TCPDF_STATIC::_freadint($f);
        $h = TCPDF_STATIC::_freadint($f);
        $bpc = ord(fread($f, 1));
        $ct = ord(fread($f, 1));
        if ($ct == 0) {
            $colspace = "DeviceGray";
        } else {
            if ($ct == 2) {
                $colspace = "DeviceRGB";
            } else {
                if ($ct == 3) {
                    $colspace = "Indexed";
                } else {
                    fclose($f);
                    return "pngalpha";
                }
            }
        }
        if (ord(fread($f, 1)) != 0) {
            fclose($f);
            return false;
        }
        if (ord(fread($f, 1)) != 0) {
            fclose($f);
            return false;
        }
        if (ord(fread($f, 1)) != 0) {
            fclose($f);
            return false;
        }
        fread($f, 4);
        $channels = $ct == 2 ? 3 : 1;
        $parms = "/DecodeParms << /Predictor 15 /Colors " . $channels . " /BitsPerComponent " . $bpc . " /Columns " . $w . " >>";
        $pal = "";
        $trns = "";
        $data = "";
        $icc = false;
        $n = TCPDF_STATIC::_freadint($f);
        do {
            $type = fread($f, 4);
            if ($type == "PLTE") {
                $pal = TCPDF_STATIC::rfread($f, $n);
                fread($f, 4);
            } else {
                if ($type == "tRNS") {
                    $t = TCPDF_STATIC::rfread($f, $n);
                    if ($ct == 0) {
                        $trns = array(ord($t[1]));
                    } else {
                        if ($ct == 2) {
                            $trns = array(ord($t[1]), ord($t[3]), ord($t[5]));
                        } else {
                            if (0 < $n) {
                                $trns = array();
                                for ($i = 0; $i < $n; $i++) {
                                    $trns[] = ord($t[$i]);
                                }
                            }
                        }
                    }
                    fread($f, 4);
                } else {
                    if ($type == "IDAT") {
                        $data .= TCPDF_STATIC::rfread($f, $n);
                        fread($f, 4);
                    } else {
                        if ($type == "iCCP") {
                            for ($len = 0; ord(fread($f, 1)) != 0 && $len < 80; $len++) {
                            }
                            if (ord(fread($f, 1)) != 0) {
                                fclose($f);
                                return false;
                            }
                            $icc = TCPDF_STATIC::rfread($f, $n - $len - 2);
                            $icc = gzuncompress($icc);
                            fread($f, 4);
                        } else {
                            if ($type == "IEND") {
                                break;
                            }
                            TCPDF_STATIC::rfread($f, $n + 4);
                        }
                    }
                }
            }
            $n = TCPDF_STATIC::_freadint($f);
        } while ($n);
        if ($colspace == "Indexed" && empty($pal)) {
            fclose($f);
            return false;
        }
        fclose($f);
        return array("w" => $w, "h" => $h, "ch" => $channels, "icc" => $icc, "cs" => $colspace, "bpc" => $bpc, "f" => "FlateDecode", "parms" => $parms, "pal" => $pal, "trns" => $trns, "data" => $data);
    }
}

?>