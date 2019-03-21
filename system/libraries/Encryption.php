<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * CodeIgniter Encryption Class
 *
 * Provides two-way keyed encryption via PHP's MCrypt and/or OpenSSL extensions.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Andrey Andreev
 * @link		https://codeigniter.com/user_guide/libraries/encryption.html
 */
class CI_Encryption
{
    /**
     * Encryption cipher
     *
     * @var	string
     */
    protected $_cipher = "aes-128";
    /**
     * Cipher mode
     *
     * @var	string
     */
    protected $_mode = "cbc";
    /**
     * Cipher handle
     *
     * @var	mixed
     */
    protected $_handle = NULL;
    /**
     * Encryption key
     *
     * @var	string
     */
    protected $_key = NULL;
    /**
     * PHP extension to be used
     *
     * @var	string
     */
    protected $_driver = NULL;
    /**
     * List of usable drivers (PHP extensions)
     *
     * @var	array
     */
    protected $_drivers = array();
    /**
     * List of available modes
     *
     * @var	array
     */
    protected $_modes = array("mcrypt" => array("cbc" => "cbc", "ecb" => "ecb", "ofb" => "nofb", "ofb8" => "ofb", "cfb" => "ncfb", "cfb8" => "cfb", "ctr" => "ctr", "stream" => "stream"), "openssl" => array("cbc" => "cbc", "ecb" => "ecb", "ofb" => "ofb", "cfb" => "cfb", "cfb8" => "cfb8", "ctr" => "ctr", "stream" => "", "xts" => "xts"));
    /**
     * List of supported HMAC algorithms
     *
     * name => digest size pairs
     *
     * @var	array
     */
    protected $_digests = array("sha224" => 28, "sha256" => 32, "sha384" => 48, "sha512" => 64);
    /**
     * mbstring.func_overload flag
     *
     * @var	bool
     */
    protected static $func_overload = NULL;
    /**
     * Class constructor
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    public function __construct(array $params = array())
    {
        $this->_drivers = array("mcrypt" => defined("MCRYPT_DEV_URANDOM"), "openssl" => extension_loaded("openssl"));
        if (!$this->_drivers["mcrypt"] && !$this->_drivers["openssl"]) {
            show_error("Encryption: Unable to find an available encryption driver.");
        }
        isset($func_overload) or ini_get("mbstring.func_overload");
        $this->initialize($params);
        if (!isset($this->_key) && 0 < self::strlen($key = config_item("encryption_key"))) {
            $this->_key = $key;
        }
        log_message("info", "Encryption Class Initialized");
    }
    /**
     * Initialize
     *
     * @param	array	$params	Configuration parameters
     * @return	CI_Encryption
     */
    public function initialize(array $params)
    {
        if (!empty($params["driver"])) {
            if (isset($this->_drivers[$params["driver"]])) {
                if ($this->_drivers[$params["driver"]]) {
                    $this->_driver = $params["driver"];
                } else {
                    log_message("error", "Encryption: Driver '" . $params["driver"] . "' is not available.");
                }
            } else {
                log_message("error", "Encryption: Unknown driver '" . $params["driver"] . "' cannot be configured.");
            }
        }
        if (empty($this->_driver)) {
            $this->_driver = $this->_drivers["openssl"] === true ? "openssl" : "mcrypt";
            log_message("debug", "Encryption: Auto-configured driver '" . $this->_driver . "'.");
        }
        empty($params["cipher"]) and $params["cipher"] = $this->_cipher;
        empty($params["key"]) or $this->{"_" . $this->_driver . "_initialize"}($params);
        return $this;
    }
    /**
     * Initialize MCrypt
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    protected function _mcrypt_initialize($params)
    {
        if (!empty($params["cipher"])) {
            $params["cipher"] = strtolower($params["cipher"]);
            $this->_cipher_alias($params["cipher"]);
            if (!in_array($params["cipher"], mcrypt_list_algorithms(), true)) {
                log_message("error", "Encryption: MCrypt cipher " . strtoupper($params["cipher"]) . " is not available.");
            } else {
                $this->_cipher = $params["cipher"];
            }
        }
        if (!empty($params["mode"])) {
            $params["mode"] = strtolower($params["mode"]);
            if (!isset($this->_modes["mcrypt"][$params["mode"]])) {
                log_message("error", "Encryption: MCrypt mode " . strtoupper($params["mode"]) . " is not available.");
            } else {
                $this->_mode = $this->_modes["mcrypt"][$params["mode"]];
            }
        }
        if (isset($this->_cipher) && isset($this->_mode)) {
            if (is_resource($this->_handle) && (strtolower(mcrypt_enc_get_algorithms_name($this->_handle)) !== $this->_cipher || strtolower(mcrypt_enc_get_modes_name($this->_handle)) !== $this->_mode)) {
                mcrypt_module_close($this->_handle);
            }
            if ($this->_handle = mcrypt_module_open($this->_cipher, "", $this->_mode, "")) {
                log_message("info", "Encryption: MCrypt cipher " . strtoupper($this->_cipher) . " initialized in " . strtoupper($this->_mode) . " mode.");
            } else {
                log_message("error", "Encryption: Unable to initialize MCrypt with cipher " . strtoupper($this->_cipher) . " in " . strtoupper($this->_mode) . " mode.");
            }
        }
    }
    /**
     * Initialize OpenSSL
     *
     * @param	array	$params	Configuration parameters
     * @return	void
     */
    protected function _openssl_initialize($params)
    {
        if (!empty($params["cipher"])) {
            $params["cipher"] = strtolower($params["cipher"]);
            $this->_cipher_alias($params["cipher"]);
            $this->_cipher = $params["cipher"];
        }
        if (!empty($params["mode"])) {
            $params["mode"] = strtolower($params["mode"]);
            if (!isset($this->_modes["openssl"][$params["mode"]])) {
                log_message("error", "Encryption: OpenSSL mode " . strtoupper($params["mode"]) . " is not available.");
            } else {
                $this->_mode = $this->_modes["openssl"][$params["mode"]];
            }
        }
        if (isset($this->_cipher) && isset($this->_mode)) {
            $handle = empty($this->_mode) ? $this->_cipher : $this->_cipher . "-" . $this->_mode;
            if (!in_array($handle, openssl_get_cipher_methods(), true)) {
                $this->_handle = NULL;
                log_message("error", "Encryption: Unable to initialize OpenSSL with method " . strtoupper($handle) . ".");
            } else {
                $this->_handle = $handle;
                log_message("info", "Encryption: OpenSSL initialized with method " . strtoupper($handle) . ".");
            }
        }
    }
    /**
     * Create a random key
     *
     * @param	int	$length	Output length
     * @return	string
     */
    public function create_key($length)
    {
        if (function_exists("random_bytes")) {
            try {
                return random_bytes((int) $length);
            } catch (Exception $e) {
                log_message("error", $e->getMessage());
                return false;
            }
        } else {
            if (defined("MCRYPT_DEV_URANDOM")) {
                return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
            }
        }
        $is_secure = NULL;
        $key = openssl_random_pseudo_bytes($length, $is_secure);
        return $is_secure === true ? $key : false;
    }
    /**
     * Encrypt
     *
     * @param	string	$data	Input data
     * @param	array	$params	Input parameters
     * @return	string
     */
    public function encrypt($data, array $params = NULL)
    {
        if (($params = $this->_get_params($params)) === false) {
            return false;
        }
        isset($params["key"]) or $params["key"] = $this->hkdf($this->_key, "sha512", NULL, self::strlen($this->_key), "encryption");
        if (($data = $this->{"_" . $this->_driver . "_encrypt"}($data, $params)) === false) {
            return false;
        }
        $params["base64"] and base64_encode($data);
        if (isset($params["hmac_digest"])) {
            isset($params["hmac_key"]) or $params["hmac_key"] = $this->hkdf($this->_key, "sha512", NULL, NULL, "authentication");
            return hash_hmac($params["hmac_digest"], $data, $params["hmac_key"], !$params["base64"]) . $data;
        }
        return $data;
    }
    /**
     * Encrypt via MCrypt
     *
     * @param	string	$data	Input data
     * @param	array	$params	Input parameters
     * @return	string
     */
    protected function _mcrypt_encrypt($data, $params)
    {
        if (!is_resource($params["handle"])) {
            return false;
        }
        $iv = 1 < ($iv_size = mcrypt_enc_get_iv_size($params["handle"])) ? $this->create_key($iv_size) : NULL;
        if (mcrypt_generic_init($params["handle"], $params["key"], $iv) < 0) {
            if ($params["handle"] !== $this->_handle) {
                mcrypt_module_close($params["handle"]);
            }
            return false;
        }
        if (in_array(strtolower(mcrypt_enc_get_modes_name($params["handle"])), array("cbc", "ecb"), true)) {
            $block_size = mcrypt_enc_get_block_size($params["handle"]);
            $pad = $block_size - self::strlen($data) % $block_size;
            $data .= str_repeat(chr($pad), $pad);
        }
        $data = mcrypt_enc_get_modes_name($params["handle"]) !== "ECB" ? $iv . mcrypt_generic($params["handle"], $data) : mcrypt_generic($params["handle"], $data);
        mcrypt_generic_deinit($params["handle"]);
        if ($params["handle"] !== $this->_handle) {
            mcrypt_module_close($params["handle"]);
        }
        return $data;
    }
    /**
     * Encrypt via OpenSSL
     *
     * @param	string	$data	Input data
     * @param	array	$params	Input parameters
     * @return	string
     */
    protected function _openssl_encrypt($data, $params)
    {
        if (empty($params["handle"])) {
            return false;
        }
        $iv = ($iv_size = openssl_cipher_iv_length($params["handle"])) ? $this->create_key($iv_size) : NULL;
        $data = openssl_encrypt($data, $params["handle"], $params["key"], OPENSSL_RAW_DATA, $iv);
        if ($data === false) {
            return false;
        }
        return $iv . $data;
    }
    /**
     * Decrypt
     *
     * @param	string	$data	Encrypted data
     * @param	array	$params	Input parameters
     * @return	string
     */
    public function decrypt($data, array $params = NULL)
    {
        if (($params = $this->_get_params($params)) === false) {
            return false;
        }
        if (isset($params["hmac_digest"])) {
            $digest_size = $params["base64"] ? $this->_digests[$params["hmac_digest"]] * 2 : $this->_digests[$params["hmac_digest"]];
            if (self::strlen($data) <= $digest_size) {
                return false;
            }
            $hmac_input = self::substr($data, 0, $digest_size);
            $data = self::substr($data, $digest_size);
            isset($params["hmac_key"]) or $params["hmac_key"] = $this->hkdf($this->_key, "sha512", NULL, NULL, "authentication");
            $hmac_check = hash_hmac($params["hmac_digest"], $data, $params["hmac_key"], !$params["base64"]);
            $diff = 0;
            for ($i = 0; $i < $digest_size; $i++) {
                $diff |= ord($hmac_input[$i]) ^ ord($hmac_check[$i]);
            }
            if ($diff !== 0) {
                return false;
            }
        }
        if ($params["base64"]) {
            $data = base64_decode($data);
        }
        isset($params["key"]) or $params["key"] = $this->hkdf($this->_key, "sha512", NULL, self::strlen($this->_key), "encryption");
        return $this->{"_" . $this->_driver . "_decrypt"}($data, $params);
    }
    /**
     * Decrypt via MCrypt
     *
     * @param	string	$data	Encrypted data
     * @param	array	$params	Input parameters
     * @return	string
     */
    protected function _mcrypt_decrypt($data, $params)
    {
        if (!is_resource($params["handle"])) {
            return false;
        }
        if (1 < ($iv_size = mcrypt_enc_get_iv_size($params["handle"]))) {
            if (mcrypt_enc_get_modes_name($params["handle"]) !== "ECB") {
                $iv = self::substr($data, 0, $iv_size);
                $data = self::substr($data, $iv_size);
            } else {
                $iv = str_repeat("", $iv_size);
            }
        } else {
            $iv = NULL;
        }
        if (mcrypt_generic_init($params["handle"], $params["key"], $iv) < 0) {
            if ($params["handle"] !== $this->_handle) {
                mcrypt_module_close($params["handle"]);
            }
            return false;
        }
        $data = mdecrypt_generic($params["handle"], $data);
        if (in_array(strtolower(mcrypt_enc_get_modes_name($params["handle"])), array("cbc", "ecb"), true)) {
            $data = self::substr($data, 0, 0 - ord($data[self::strlen($data) - 1]));
        }
        mcrypt_generic_deinit($params["handle"]);
        if ($params["handle"] !== $this->_handle) {
            mcrypt_module_close($params["handle"]);
        }
        return $data;
    }
    /**
     * Decrypt via OpenSSL
     *
     * @param	string	$data	Encrypted data
     * @param	array	$params	Input parameters
     * @return	string
     */
    protected function _openssl_decrypt($data, $params)
    {
        if ($iv_size = openssl_cipher_iv_length($params["handle"])) {
            $iv = self::substr($data, 0, $iv_size);
            $data = self::substr($data, $iv_size);
        } else {
            $iv = NULL;
        }
        return empty($params["handle"]) ? false : openssl_decrypt($data, $params["handle"], $params["key"], OPENSSL_RAW_DATA, $iv);
    }
    /**
     * Get params
     *
     * @param	array	$params	Input parameters
     * @return	array
     */
    protected function _get_params($params)
    {
        if (empty($params)) {
            return isset($this->_cipher) && isset($this->_mode) && isset($this->_key) && isset($this->_handle) ? array("handle" => $this->_handle, "cipher" => $this->_cipher, "mode" => $this->_mode, "key" => NULL, "base64" => true, "hmac_digest" => "sha512", "hmac_key" => NULL) : false;
        }
        if (!(isset($params["cipher"]) && isset($params["mode"]) && isset($params["key"]))) {
            return false;
        }
        if (isset($params["mode"])) {
            $params["mode"] = strtolower($params["mode"]);
            if (!isset($this->_modes[$this->_driver][$params["mode"]])) {
                return false;
            }
            $params["mode"] = $this->_modes[$this->_driver][$params["mode"]];
        }
        if (isset($params["hmac"]) && $params["hmac"] === false) {
            $params["hmac_key"] = NULL;
            $params["hmac_digest"] = $params["hmac_key"];
        } else {
            if (!isset($params["hmac_key"])) {
                return false;
            }
            if (isset($params["hmac_digest"])) {
                $params["hmac_digest"] = strtolower($params["hmac_digest"]);
                if (!isset($this->_digests[$params["hmac_digest"]])) {
                    return false;
                }
            } else {
                $params["hmac_digest"] = "sha512";
            }
        }
        $params = array("handle" => NULL, "cipher" => $params["cipher"], "mode" => $params["mode"], "key" => $params["key"], "base64" => isset($params["raw_data"]) ? !$params["raw_data"] : false, "hmac_digest" => $params["hmac_digest"], "hmac_key" => $params["hmac_key"]);
        $this->_cipher_alias($params["cipher"]);
        $params["handle"] = $params["cipher"] !== $this->_cipher || $params["mode"] !== $this->_mode ? $this->{"_" . $this->_driver . "_get_handle"}($params["cipher"], $params["mode"]) : $this->_handle;
        return $params;
    }
    /**
     * Get MCrypt handle
     *
     * @param	string	$cipher	Cipher name
     * @param	string	$mode	Encryption mode
     * @return	resource
     */
    protected function _mcrypt_get_handle($cipher, $mode)
    {
        return mcrypt_module_open($cipher, "", $mode, "");
    }
    /**
     * Get OpenSSL handle
     *
     * @param	string	$cipher	Cipher name
     * @param	string	$mode	Encryption mode
     * @return	string
     */
    protected function _openssl_get_handle($cipher, $mode)
    {
        return $mode === "stream" ? $cipher : $cipher . "-" . $mode;
    }
    /**
     * Cipher alias
     *
     * Tries to translate cipher names between MCrypt and OpenSSL's "dialects".
     *
     * @param	string	$cipher	Cipher name
     * @return	void
     */
    protected function _cipher_alias(&$cipher)
    {
        static $dictionary = NULL;
        if (empty($dictionary)) {
            $dictionary = array("mcrypt" => array("aes-128" => "rijndael-128", "aes-192" => "rijndael-128", "aes-256" => "rijndael-128", "des3-ede3" => "tripledes", "bf" => "blowfish", "cast5" => "cast-128", "rc4" => "arcfour", "rc4-40" => "arcfour"), "openssl" => array("rijndael-128" => "aes-128", "tripledes" => "des-ede3", "blowfish" => "bf", "cast-128" => "cast5", "arcfour" => "rc4-40", "rc4" => "rc4-40"));
        }
        if (isset($dictionary[$this->_driver][$cipher])) {
            $cipher = $dictionary[$this->_driver][$cipher];
        }
    }
    /**
     * HKDF
     *
     * @link	https://tools.ietf.org/rfc/rfc5869.txt
     * @param	$key	Input key
     * @param	$digest	A SHA-2 hashing algorithm
     * @param	$salt	Optional salt
     * @param	$length	Output length (defaults to the selected digest size)
     * @param	$info	Optional context/application-specific info
     * @return	string	A pseudo-random key
     */
    public function hkdf($key, $digest = "sha512", $salt = NULL, $length = NULL, $info = "")
    {
        if (!isset($this->_digests[$digest])) {
            return false;
        }
        if (empty($length) || !is_int($length)) {
            $length = $this->_digests[$digest];
        } else {
            if (255 * $this->_digests[$digest] < $length) {
                return false;
            }
        }
        self::strlen($salt) or str_repeat("", $this->_digests[$digest]);
        $prk = hash_hmac($digest, $key, $salt, true);
        $key = "";
        $key_block = "";
        for ($block_index = 1; self::strlen($key) < $length; $block_index++) {
            $key_block = hash_hmac($digest, $key_block . $info . chr($block_index), $prk, true);
            $key .= $key_block;
        }
        return self::substr($key, 0, $length);
    }
    /**
     * __get() magic
     *
     * @param	string	$key	Property name
     * @return	mixed
     */
    public function __get($key)
    {
        if ($key === "mode") {
            return array_search($this->_mode, $this->_modes[$this->_driver], true);
        }
        if (in_array($key, array("cipher", "driver", "drivers", "digests"), true)) {
            return $this->{"_" . $key};
        }
    }
    /**
     * Byte-safe strlen()
     *
     * @param	string	$str
     * @return	int
     */
    protected static function strlen($str)
    {
        return self::$func_overload ? mb_strlen($str, "8bit") : strlen($str);
    }
    /**
     * Byte-safe substr()
     *
     * @param	string	$str
     * @param	int	$start
     * @param	int	$length
     * @return	string
     */
    protected static function substr($str, $start, $length = NULL)
    {
        if (self::$func_overload) {
            return mb_substr($str, $start, $length, "8bit");
        }
        return isset($length) ? substr($str, $start, $length) : substr($str, $start);
    }
}

?>