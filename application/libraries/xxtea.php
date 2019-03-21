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
if (!extension_loaded("xxtea")) {
    class XXTEA
    {
        const DELTA = 2654435769.0;
        private static function long2str($v, $w)
        {
            $len = count($v);
            $n = $len << 2;
            if ($w) {
                $m = $v[$len - 1];
                $n -= 4;
                if ($m < $n - 3 || $n < $m) {
                    return false;
                }
                $n = $m;
            }
            $s = array();
            for ($i = 0; $i < $len; $i++) {
                $s[$i] = pack("V", $v[$i]);
            }
            if ($w) {
                return substr(join("", $s), 0, $n);
            }
            return join("", $s);
        }
        private static function str2long($s, $w)
        {
            $v = unpack("V*", $s . str_repeat("", 4 - strlen($s) % 4 & 3));
            $v = array_values($v);
            if ($w) {
                $v[count($v)] = strlen($s);
            }
            return $v;
        }
        private static function int32($n)
        {
            return $n & 4294967295.0;
        }
        private static function mx($sum, $y, $z, $p, $e, $k)
        {
            return ($z >> 5 & 134217727 ^ $y << 2) + ($y >> 3 & 536870911 ^ $z << 4) ^ ($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z);
        }
        private static function fixk($k)
        {
            if (count($k) < 4) {
                for ($i = count($k); $i < 4; $i++) {
                    $k[$i] = 0;
                }
            }
            return $k;
        }
        public static function encrypt($str, $key)
        {
            if ($str == "") {
                return "";
            }
            $v = self::str2long($str, true);
            $k = self::fixk(self::str2long($key, false));
            $n = count($v) - 1;
            $z = $v[$n];
            $q = floor(6 + 52 / ($n + 1));
            $sum = 0;
            while (0 < $q--) {
                $sum = self::int32($sum + self::DELTA);
                $e = $sum >> 2 & 3;
                for ($p = 0; $p < $n; $p++) {
                    $y = $v[$p + 1];
                    $v[$p] = self::int32($v[$p] + self::mx($sum, $y, $z, $p, $e, $k));
                    $z = $v[$p];
                }
                $y = $v[0];
                $v[$n] = self::int32($v[$n] + self::mx($sum, $y, $z, $p, $e, $k));
                $z = $v[$n];
            }
            return self::long2str($v, false);
        }
        public static function decrypt($str, $key)
        {
            if ($str == "") {
                return "";
            }
            $v = self::str2long($str, false);
            $k = self::fixk(self::str2long($key, false));
            $n = count($v) - 1;
            $y = $v[0];
            $q = floor(6 + 52 / ($n + 1));
            $sum = self::int32($q * self::DELTA);
            while ($sum != 0) {
                $e = $sum >> 2 & 3;
                for ($p = $n; 0 < $p; $p--) {
                    $z = $v[$p - 1];
                    $v[$p] = self::int32($v[$p] - self::mx($sum, $y, $z, $p, $e, $k));
                    $y = $v[$p];
                }
                $z = $v[$n];
                $v[0] = self::int32($v[0] - self::mx($sum, $y, $z, $p, $e, $k));
                $y = $v[0];
                $sum = self::int32($sum - self::DELTA);
            }
            return self::long2str($v, true);
        }
    }
    function xxtea_encrypt($str, $key)
    {
        return XXTEA::encrypt($str, $key);
    }
    function xxtea_decrypt($str, $key)
    {
        return XXTEA::decrypt($str, $key);
    }
}

?>