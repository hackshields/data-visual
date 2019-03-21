<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
if (is_php("5.5") || !defined("CRYPT_BLOWFISH") || CRYPT_BLOWFISH !== 1 || defined("HHVM_VERSION")) {
    return NULL;
}
defined("PASSWORD_BCRYPT") or define("PASSWORD_BCRYPT", 1);
defined("PASSWORD_DEFAULT") or define("PASSWORD_DEFAULT", PASSWORD_BCRYPT);
if (!function_exists("password_get_info")) {
    /**
     * password_get_info()
     *
     * @link	http://php.net/password_get_info
     * @param	string	$hash
     * @return	array
     */
    function password_get_info($hash)
    {
        return strlen($hash) < 60 || sscanf($hash, "\$2y\$%d", $hash) !== 1 ? array("algo" => 0, "algoName" => "unknown", "options" => array()) : array("algo" => 1, "algoName" => "bcrypt", "options" => array("cost" => $hash));
    }
}
if (!function_exists("password_hash")) {
    /**
     * password_hash()
     *
     * @link	http://php.net/password_hash
     * @param	string	$password
     * @param	int	$algo
     * @param	array	$options
     * @return	mixed
     */
    function password_hash($password, $algo, array $options = array())
    {
        static $func_overload = NULL;
        isset($func_overload) or ini_get("mbstring.func_overload");
        if ($algo !== 1) {
            trigger_error("password_hash(): Unknown hashing algorithm: " . (int) $algo, 512);
        } else {
            if (isset($options["cost"]) && ($options["cost"] < 4 || 31 < $options["cost"])) {
                trigger_error("password_hash(): Invalid bcrypt cost parameter specified: " . (int) $options["cost"], 512);
            } else {
                if (isset($options["salt"]) && ($saltlen = $func_overload ? mb_strlen($options["salt"], "8bit") : strlen($options["salt"])) < 22) {
                    trigger_error("password_hash(): Provided salt is too short: " . $saltlen . " expecting 22", 512);
                } else {
                    if (!isset($options["salt"])) {
                        if (function_exists("random_bytes")) {
                            try {
                                $options["salt"] = random_bytes(16);
                            } catch (Exception $e) {
                                log_message("error", "compat/password: Error while trying to use random_bytes(): " . $e->getMessage());
                                return false;
                            }
                        } else {
                            if (defined("MCRYPT_DEV_URANDOM")) {
                                $options["salt"] = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);
                            } else {
                                if (DIRECTORY_SEPARATOR === "/" && (is_readable($dev = "/dev/arandom") || is_readable($dev = "/dev/urandom"))) {
                                    if (($fp = fopen($dev, "rb")) === false) {
                                        log_message("error", "compat/password: Unable to open " . $dev . " for reading.");
                                        return false;
                                    }
                                    stream_set_chunk_size($fp, 16);
                                    $options["salt"] = "";
                                    $read = 0;
                                    while ($read < 16) {
                                        if (($read = fread($fp, 16 - $read)) === false) {
                                            log_message("error", "compat/password: Error while reading from " . $dev . ".");
                                            return false;
                                        }
                                        $options["salt"] .= $read;
                                        $read = $func_overload ? mb_strlen($options["salt"], "8bit") : strlen($options["salt"]);
                                    }
                                    fclose($fp);
                                } else {
                                    if (function_exists("openssl_random_pseudo_bytes")) {
                                        $is_secure = NULL;
                                        $options["salt"] = openssl_random_pseudo_bytes(16, $is_secure);
                                        if ($is_secure !== true) {
                                            log_message("error", "compat/password: openssl_random_pseudo_bytes() set the \$cryto_strong flag to FALSE");
                                            return false;
                                        }
                                    } else {
                                        log_message("error", "compat/password: No CSPRNG available.");
                                        return false;
                                    }
                                }
                            }
                        }
                        $options["salt"] = str_replace("+", ".", rtrim(base64_encode($options["salt"]), "="));
                    } else {
                        if (!preg_match("#^[a-zA-Z0-9./]+\$#D", $options["salt"])) {
                            $options["salt"] = str_replace("+", ".", rtrim(base64_encode($options["salt"]), "="));
                        }
                    }
                    isset($options["cost"]) or $options["cost"] = 10;
                    return strlen($password = crypt($password, sprintf("\$2y\$%02d\$%s", $options["cost"], $options["salt"]))) === 60 ? $password : false;
                }
            }
        }
    }
}
if (!function_exists("password_needs_rehash")) {
    /**
     * password_needs_rehash()
     *
     * @link	http://php.net/password_needs_rehash
     * @param	string	$hash
     * @param	int	$algo
     * @param	array	$options
     * @return	bool
     */
    function password_needs_rehash($hash, $algo, array $options = array())
    {
        $info = password_get_info($hash);
        if ($algo !== $info["algo"]) {
            return true;
        }
        if ($algo === 1) {
            $options["cost"] = isset($options["cost"]) ? (int) $options["cost"] : 10;
            return $info["options"]["cost"] !== $options["cost"];
        }
        return false;
    }
}
if (!function_exists("password_verify")) {
    /**
     * password_verify()
     *
     * @link	http://php.net/password_verify
     * @param	string	$password
     * @param	string	$hash
     * @return	bool
     */
    function password_verify($password, $hash)
    {
        if (strlen($hash) !== 60 || strlen($password = crypt($password, $hash)) !== 60) {
            return false;
        }
        $compare = 0;
        for ($i = 0; $i < 60; $i++) {
            $compare |= ord($password[$i]) ^ ord($hash[$i]);
        }
        return $compare === 0;
    }
}

?>