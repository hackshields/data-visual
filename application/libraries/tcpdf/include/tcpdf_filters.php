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
 * @class TCPDF_FILTERS
 * This is a PHP class for decoding common PDF filters (PDF 32000-2008 - 7.4 Filters).<br>
 * @package com.tecnick.tcpdf
 * @brief This is a PHP class for decoding common PDF filters.
 * @version 1.0.001
 * @author Nicola Asuni - info@tecnick.com
 */
class TCPDF_FILTERS
{
    /**
     * Define a list of available filter decoders.
     * @private static
     */
    private static $available_filters = array("ASCIIHexDecode", "ASCII85Decode", "LZWDecode", "FlateDecode", "RunLengthDecode");
    /**
     * Get a list of available decoding filters.
     * @return (array) Array of available filter decoders.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function getAvailableFilters()
    {
        return self::$available_filters;
    }
    /**
     * Decode data using the specified filter type.
     * @param $filter (string) Filter name.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilter($filter, $data)
    {
        switch ($filter) {
            case "ASCIIHexDecode":
                return self::decodeFilterASCIIHexDecode($data);
            case "ASCII85Decode":
                return self::decodeFilterASCII85Decode($data);
            case "LZWDecode":
                return self::decodeFilterLZWDecode($data);
            case "FlateDecode":
                return self::decodeFilterFlateDecode($data);
            case "RunLengthDecode":
                return self::decodeFilterRunLengthDecode($data);
            case "CCITTFaxDecode":
                return self::decodeFilterCCITTFaxDecode($data);
            case "JBIG2Decode":
                return self::decodeFilterJBIG2Decode($data);
            case "DCTDecode":
                return self::decodeFilterDCTDecode($data);
            case "JPXDecode":
                return self::decodeFilterJPXDecode($data);
            case "Crypt":
                return self::decodeFilterCrypt($data);
        }
        return self::decodeFilterStandard($data);
    }
    /**
     * Standard
     * Default decoding filter (leaves data unchanged).
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterStandard($data)
    {
        return $data;
    }
    /**
     * ASCIIHexDecode
     * Decodes data encoded in an ASCII hexadecimal representation, reproducing the original binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterASCIIHexDecode($data)
    {
        $decoded = "";
        $data = preg_replace("/[\\s]/", "", $data);
        $eod = strpos($data, ">");
        if ($eod !== false) {
            $data = substr($data, 0, $eod);
            $eod = true;
        }
        $data_length = strlen($data);
        if ($data_length % 2 != 0) {
            if ($eod) {
                $data = substr($data, 0, -1) . "0" . substr($data, -1);
            } else {
                self::Error("decodeFilterASCIIHexDecode: invalid code");
            }
        }
        if (0 < preg_match("/[^a-fA-F\\d]/", $data)) {
            self::Error("decodeFilterASCIIHexDecode: invalid code");
        }
        $decoded = pack("H*", $data);
        return $decoded;
    }
    /**
     * ASCII85Decode
     * Decodes data encoded in an ASCII base-85 representation, reproducing the original binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterASCII85Decode($data)
    {
        $decoded = "";
        $data = preg_replace("/[\\s]/", "", $data);
        if (strpos($data, "<~") !== false) {
            $data = substr($data, 2);
        }
        $eod = strpos($data, "~>");
        if ($eod !== false) {
            $data = substr($data, 0, $eod);
        }
        $data_length = strlen($data);
        if (0 < preg_match("/[^\\x21-\\x75,\\x74]/", $data)) {
            self::Error("decodeFilterASCII85Decode: invalid code");
        }
        $zseq = chr(0) . chr(0) . chr(0) . chr(0);
        $group_pos = 0;
        $tuple = 0;
        $pow85 = array(85 * 85 * 85 * 85, 85 * 85 * 85, 85 * 85, 85, 1);
        $last_pos = $data_length - 1;
        for ($i = 0; $i < $data_length; $i++) {
            $char = ord($data[$i]);
            if ($char == 122) {
                if ($group_pos == 0) {
                    $decoded .= $zseq;
                } else {
                    self::Error("decodeFilterASCII85Decode: invalid code");
                }
            } else {
                $tuple += ($char - 33) * $pow85[$group_pos];
                if ($group_pos == 4) {
                    $decoded .= chr($tuple >> 24) . chr($tuple >> 16) . chr($tuple >> 8) . chr($tuple);
                    $tuple = 0;
                    $group_pos = 0;
                } else {
                    $group_pos++;
                }
            }
        }
        if (1 < $group_pos) {
            $tuple += $pow85[$group_pos - 1];
        }
        switch ($group_pos) {
            case 4:
                $decoded .= chr($tuple >> 24) . chr($tuple >> 16) . chr($tuple >> 8);
                break;
            case 3:
                $decoded .= chr($tuple >> 24) . chr($tuple >> 16);
                break;
            case 2:
                $decoded .= chr($tuple >> 24);
                break;
            case 1:
                self::Error("decodeFilterASCII85Decode: invalid code");
                break;
        }
        return $decoded;
    }
    /**
     * LZWDecode
     * Decompresses data encoded using the LZW (Lempel-Ziv-Welch) adaptive compression method, reproducing the original text or binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterLZWDecode($data)
    {
        $decoded = "";
        $data_length = strlen($data);
        $bitstring = "";
        for ($i = 0; $i < $data_length; $i++) {
            $bitstring .= sprintf("%08b", ord($data[$i]));
        }
        $data_length = strlen($bitstring);
        $bitlen = 9;
        $dix = 258;
        $dictionary = array();
        for ($i = 0; $i < 256; $i++) {
            $dictionary[$i] = chr($i);
        }
        $prev_index = 0;
        while (0 < $data_length && ($index = bindec(substr($bitstring, 0, $bitlen))) != 257) {
            $bitstring = substr($bitstring, $bitlen);
            $data_length -= $bitlen;
            if ($index == 256) {
                $bitlen = 9;
                $dix = 258;
                $prev_index = 256;
                $dictionary = array();
                for ($i = 0; $i < 256; $i++) {
                    $dictionary[$i] = chr($i);
                }
            } else {
                if ($prev_index == 256) {
                    $decoded .= $dictionary[$index];
                    $prev_index = $index;
                } else {
                    if ($index < $dix) {
                        $decoded .= $dictionary[$index];
                        $dic_val = $dictionary[$prev_index] . $dictionary[$index][0];
                        $prev_index = $index;
                    } else {
                        $dic_val = $dictionary[$prev_index] . $dictionary[$prev_index][0];
                        $decoded .= $dic_val;
                    }
                    $dictionary[$dix] = $dic_val;
                    $dix++;
                    if ($dix == 2047) {
                        $bitlen = 12;
                    } else {
                        if ($dix == 1023) {
                            $bitlen = 11;
                        } else {
                            if ($dix == 511) {
                                $bitlen = 10;
                            }
                        }
                    }
                }
            }
        }
        return $decoded;
    }
    /**
     * FlateDecode
     * Decompresses data encoded using the zlib/deflate compression method, reproducing the original text or binary data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterFlateDecode($data)
    {
        $decoded = @gzuncompress($data);
        if ($decoded === false) {
            self::Error("decodeFilterFlateDecode: invalid code");
        }
        return $decoded;
    }
    /**
     * RunLengthDecode
     * Decompresses data encoded using a byte-oriented run-length encoding algorithm.
     * @param $data (string) Data to decode.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterRunLengthDecode($data)
    {
        $decoded = "";
        $data_length = strlen($data);
        $i = 0;
        while ($i < $data_length) {
            $byte = ord($data[$i]);
            if ($byte == 128) {
                break;
            }
            if ($byte < 128) {
                $decoded .= substr($data, $i + 1, $byte + 1);
                $i += $byte + 2;
            } else {
                $decoded .= str_repeat($data[$i + 1], 257 - $byte);
                $i += 2;
            }
        }
        return $decoded;
    }
    /**
     * CCITTFaxDecode (NOT IMPLEMETED - RETURN AN EXCEPTION)
     * Decompresses data encoded using the CCITT facsimile standard, reproducing the original data (typically monochrome image data at 1 bit per pixel).
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterCCITTFaxDecode($data)
    {
        self::Error("~decodeFilterCCITTFaxDecode: this method has not been yet implemented");
    }
    /**
     * JBIG2Decode (NOT IMPLEMETED - RETURN AN EXCEPTION)
     * Decompresses data encoded using the JBIG2 standard, reproducing the original monochrome (1 bit per pixel) image data (or an approximation of that data).
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterJBIG2Decode($data)
    {
        self::Error("~decodeFilterJBIG2Decode: this method has not been yet implemented");
    }
    /**
     * DCTDecode (NOT IMPLEMETED - RETURN AN EXCEPTION)
     * Decompresses data encoded using a DCT (discrete cosine transform) technique based on the JPEG standard, reproducing image sample data that approximates the original data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterDCTDecode($data)
    {
        self::Error("~decodeFilterDCTDecode: this method has not been yet implemented");
    }
    /**
     * JPXDecode (NOT IMPLEMETED - RETURN AN EXCEPTION)
     * Decompresses data encoded using the wavelet-based JPEG2000 standard, reproducing the original image data.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterJPXDecode($data)
    {
        self::Error("~decodeFilterJPXDecode: this method has not been yet implemented");
    }
    /**
     * Crypt (NOT IMPLEMETED - RETURN AN EXCEPTION)
     * Decrypts data encrypted by a security handler, reproducing the data as it was before encryption.
     * @param $data (string) Data to decode.
     * @return Decoded data string.
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function decodeFilterCrypt($data)
    {
        self::Error("~decodeFilterCrypt: this method has not been yet implemented");
    }
    /**
     * Throw an exception.
     * @param $msg (string) The error message
     * @since 1.0.000 (2011-05-23)
     * @public static
     */
    public static function Error($msg)
    {
        throw new Exception("TCPDF_PARSER ERROR: " . $msg);
    }
}

?>