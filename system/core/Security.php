<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

defined("BASEPATH") or exit("No direct script access allowed");
/**
 * Security Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Security
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/libraries/security.html
 */
class CI_Security
{
    /**
     * List of sanitize filename strings
     *
     * @var	array
     */
    public $filename_bad_chars = array("../", "<!--", "-->", "<", ">", "'", "\"", "&", "\$", "#", "{", "}", "[", "]", "=", ";", "?", "%20", "%22", "%3c", "%253c", "%3e", "%0e", "%28", "%29", "%2528", "%26", "%24", "%3f", "%3b", "%3d");
    /**
     * Character set
     *
     * Will be overridden by the constructor.
     *
     * @var	string
     */
    public $charset = "UTF-8";
    /**
     * XSS Hash
     *
     * Random Hash for protecting URLs.
     *
     * @var	string
     */
    protected $_xss_hash = NULL;
    /**
     * CSRF Hash
     *
     * Random hash for Cross Site Request Forgery protection cookie
     *
     * @var	string
     */
    protected $_csrf_hash = NULL;
    /**
     * CSRF Expire time
     *
     * Expiration time for Cross Site Request Forgery protection cookie.
     * Defaults to two hours (in seconds).
     *
     * @var	int
     */
    protected $_csrf_expire = 7200;
    /**
     * CSRF Token name
     *
     * Token name for Cross Site Request Forgery protection cookie.
     *
     * @var	string
     */
    protected $_csrf_token_name = "ci_csrf_token";
    /**
     * CSRF Cookie name
     *
     * Cookie name for Cross Site Request Forgery protection cookie.
     *
     * @var	string
     */
    protected $_csrf_cookie_name = "ci_csrf_token";
    /**
     * List of never allowed strings
     *
     * @var	array
     */
    protected $_never_allowed_str = array("document.cookie" => "[removed]", "document.write" => "[removed]", ".parentNode" => "[removed]", ".innerHTML" => "[removed]", "-moz-binding" => "[removed]", "<!--" => "&lt;!--", "-->" => "--&gt;", "<![CDATA[" => "&lt;![CDATA[", "<comment>" => "&lt;comment&gt;", "<%" => "&lt;&#37;");
    /**
     * List of never allowed regex replacements
     *
     * @var	array
     */
    protected $_never_allowed_regex = array("javascript\\s*:", "(document|(document\\.)?window)\\.(location|on\\w*)", "expression\\s*(\\(|&\\#40;)", "vbscript\\s*:", "wscript\\s*:", "jscript\\s*:", "vbs\\s*:", "Redirect\\s+30\\d", "([\"'])?data\\s*:[^\\1]*?base64[^\\1]*?,[^\\1]*?\\1?");
    /**
     * Class constructor
     *
     * @return	void
     */
    public function __construct($charset)
    {
        $this->charset = $charset;
        if (config_item("csrf_protection") && !is_cli()) {
            foreach (array("csrf_expire", "csrf_token_name", "csrf_cookie_name") as $key) {
                if (NULL !== ($val = config_item($key))) {
                    $this->{"_" . $key} = $val;
                }
            }
            if ($cookie_prefix = config_item("cookie_prefix")) {
                $this->_csrf_cookie_name = $cookie_prefix . $this->_csrf_cookie_name;
            }
            $this->_csrf_set_hash();
            $this->csrf_verify();
        }
        log_message("info", "Security Class Initialized");
    }
    /**
     * CSRF Verify
     *
     * @return	CI_Security
     */
    public function csrf_verify()
    {
        if (strtoupper($_SERVER["REQUEST_METHOD"]) !== "POST") {
            return $this->csrf_set_cookie();
        }
        if ($exclude_uris = config_item("csrf_exclude_uris")) {
            $uri = load_class("URI", "core");
            foreach ($exclude_uris as $excluded) {
                if (preg_match("#^" . $excluded . "\$#i" . (UTF8_ENABLED ? "u" : ""), $uri->uri_string())) {
                    return $this;
                }
            }
        }
        $valid = isset($_POST[$this->_csrf_token_name]) && isset($_COOKIE[$this->_csrf_cookie_name]) && hash_equals($_POST[$this->_csrf_token_name], $_COOKIE[$this->_csrf_cookie_name]);
        unset($_POST[$this->_csrf_token_name]);
        if (config_item("csrf_regenerate")) {
            unset($_COOKIE[$this->_csrf_cookie_name]);
            $this->_csrf_hash = NULL;
        }
        $this->_csrf_set_hash();
        $this->csrf_set_cookie();
        if ($valid !== true) {
            $this->csrf_show_error();
        }
        log_message("info", "CSRF token verified");
        return $this;
    }
    /**
     * CSRF Set Cookie
     *
     * @codeCoverageIgnore
     * @return	CI_Security
     */
    public function csrf_set_cookie()
    {
        $expire = time() + $this->_csrf_expire;
        $secure_cookie = (bool) config_item("cookie_secure");
        if ($secure_cookie && !is_https()) {
            return false;
        }
        setcookie($this->_csrf_cookie_name, $this->_csrf_hash, $expire, config_item("cookie_path"), config_item("cookie_domain"), $secure_cookie, config_item("cookie_httponly"));
        log_message("info", "CSRF cookie sent");
        return $this;
    }
    /**
     * Show CSRF Error
     *
     * @return	void
     */
    public function csrf_show_error()
    {
        show_error("The action you have requested is not allowed.", 403);
    }
    /**
     * Get CSRF Hash
     *
     * @see		CI_Security::$_csrf_hash
     * @return 	string	CSRF hash
     */
    public function get_csrf_hash()
    {
        return $this->_csrf_hash;
    }
    /**
     * Get CSRF Token Name
     *
     * @see		CI_Security::$_csrf_token_name
     * @return	string	CSRF token name
     */
    public function get_csrf_token_name()
    {
        return $this->_csrf_token_name;
    }
    /**
     * XSS Clean
     *
     * Sanitizes data so that Cross Site Scripting Hacks can be
     * prevented.  This method does a fair amount of work but
     * it is extremely thorough, designed to prevent even the
     * most obscure XSS attempts.  Nothing is ever 100% foolproof,
     * of course, but I haven't been able to get anything passed
     * the filter.
     *
     * Note: Should only be used to deal with data upon submission.
     *	 It's not something that should be used for general
     *	 runtime processing.
     *
     * @link	http://channel.bitflux.ch/wiki/XSS_Prevention
     * 		Based in part on some code and ideas from Bitflux.
     *
     * @link	http://ha.ckers.org/xss.html
     * 		To help develop this script I used this great list of
     *		vulnerabilities along with a few other hacks I've
     *		harvested from examining vulnerabilities in other programs.
     *
     * @param	string|string[]	$str		Input data
     * @param 	bool		$is_image	Whether the input is an image
     * @return	string
     */
    public function xss_clean($str, $is_image = false)
    {
        if (is_array($str)) {
            foreach ($str as $key => &$value) {
                $str[$key] = $this->xss_clean($value);
            }
            return $str;
        } else {
            $str = remove_invisible_characters($str);
            if (stripos($str, "%") !== false) {
                do {
                    $oldstr = $str;
                    $str = rawurldecode($str);
                    $str = preg_replace_callback("#%(?:\\s*[0-9a-f]){2,}#i", array($this, "_urldecodespaces"), $str);
                } while ($oldstr !== $str);
                unset($oldstr);
            }
            $str = preg_replace_callback("/[^a-z0-9>]+[a-z0-9]+=([\\'\"]).*?\\1/si", array($this, "_convert_attribute"), $str);
            $str = preg_replace_callback("/<\\w+.*/si", array($this, "_decode_entity"), $str);
            $str = remove_invisible_characters($str);
            $str = str_replace("\t", " ", $str);
            $converted_string = $str;
            $str = $this->_do_never_allowed($str);
            if ($is_image === true) {
                $str = preg_replace("/<\\?(php)/i", "&lt;?\\1", $str);
            } else {
                $str = str_replace(array("<?", "?" . ">"), array("&lt;?", "?&gt;"), $str);
            }
            $words = array("javascript", "expression", "vbscript", "jscript", "wscript", "vbs", "script", "base64", "applet", "alert", "document", "write", "cookie", "window", "confirm", "prompt", "eval");
            foreach ($words as $word) {
                $word = implode("\\s*", str_split($word)) . "\\s*";
                $str = preg_replace_callback("#(" . substr($word, 0, -3) . ")(\\W)#is", array($this, "_compact_exploded_words"), $str);
            }
            do {
                $original = $str;
                if (preg_match("/<a/i", $str)) {
                    $str = preg_replace_callback("#<a(?:rea)?[^a-z0-9>]+([^>]*?)(?:>|\$)#si", array($this, "_js_link_removal"), $str);
                }
                if (preg_match("/<img/i", $str)) {
                    $str = preg_replace_callback("#<img[^a-z0-9]+([^>]*?)(?:\\s?/?>|\$)#si", array($this, "_js_img_removal"), $str);
                }
                if (preg_match("/script|xss/i", $str)) {
                    $str = preg_replace("#</*(?:script|xss).*?>#si", "[removed]", $str);
                }
            } while ($original !== $str);
            unset($original);
            $pattern = "#" . "<((?<slash>/*\\s*)((?<tagName>[a-z0-9]+)(?=[^a-z0-9]|\$)|.+)" . "[^\\s\\042\\047a-z0-9>/=]*" . "(?<attributes>(?:[\\s\\042\\047/=]*" . "[^\\s\\042\\047>/=]+" . "(?:\\s*=" . "(?:[^\\s\\042\\047=><`]+|\\s*\\042[^\\042]*\\042|\\s*\\047[^\\047]*\\047|\\s*(?U:[^\\s\\042\\047=><`]*))" . ")?" . ")*)" . "[^>]*)(?<closeTag>\\>)?#isS";
            do {
                $old_str = $str;
                $str = preg_replace_callback($pattern, array($this, "_sanitize_naughty_html"), $str);
            } while ($old_str !== $str);
            unset($old_str);
            $str = preg_replace("#(alert|prompt|confirm|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\\s*)\\((.*?)\\)#si", "\\1\\2&#40;\\3&#41;", $str);
            $str = $this->_do_never_allowed($str);
            if ($is_image === true) {
                return $str === $converted_string;
            }
            return $str;
        }
    }
    /**
     * XSS Hash
     *
     * Generates the XSS hash if needed and returns it.
     *
     * @see		CI_Security::$_xss_hash
     * @return	string	XSS hash
     */
    public function xss_hash()
    {
        if ($this->_xss_hash === NULL) {
            $rand = $this->get_random_bytes(16);
            $this->_xss_hash = $rand === false ? md5(uniqid(mt_rand(), true)) : bin2hex($rand);
        }
        return $this->_xss_hash;
    }
    /**
     * Get random bytes
     *
     * @param	int	$length	Output length
     * @return	string
     */
    public function get_random_bytes($length)
    {
        if (empty($length) || !ctype_digit((string) $length)) {
            return false;
        }
        if (function_exists("random_bytes")) {
            try {
                return random_bytes((int) $length);
            } catch (Exception $e) {
                log_message("error", $e->getMessage());
                return false;
            }
        }
        if (defined("MCRYPT_DEV_URANDOM") && ($output = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM)) !== false) {
            return $output;
        }
        if (is_readable("/dev/urandom") && ($fp = fopen("/dev/urandom", "rb")) !== false) {
            stream_set_chunk_size($fp, $length);
            $output = fread($fp, $length);
            fclose($fp);
            if ($output !== false) {
                return $output;
            }
        }
        if (function_exists("openssl_random_pseudo_bytes")) {
            return openssl_random_pseudo_bytes($length);
        }
        return false;
    }
    /**
     * HTML Entities Decode
     *
     * A replacement for html_entity_decode()
     *
     * The reason we are not using html_entity_decode() by itself is because
     * while it is not technically correct to leave out the semicolon
     * at the end of an entity most browsers will still interpret the entity
     * correctly. html_entity_decode() does not convert entities without
     * semicolons, so we are left with our own little solution here. Bummer.
     *
     * @link	http://php.net/html-entity-decode
     *
     * @param	string	$str		Input
     * @param	string	$charset	Character set
     * @return	string
     */
    public function entity_decode($str, $charset = NULL)
    {
        if (strpos($str, "&") === false) {
            return $str;
        }
        static $_entities = NULL;
        isset($charset) or isset($_entities) or array_map("strtolower", get_html_translation_table(HTML_ENTITIES, ENT_COMPAT | ENT_HTML5, $charset));
        $str_compare = $str;
        if (preg_match_all("/&[a-z]{2,}(?![a-z;])/i", $str, $matches)) {
            $replace = array();
            $matches = array_unique(array_map("strtolower", $matches[0]));
            foreach ($matches as &$match) {
                if (($char = array_search($match . ";", $_entities, true)) !== false) {
                    $replace[$match] = $char;
                }
            }
            $str = str_replace(array_keys($replace), array_values($replace), $str);
        }
        $str = html_entity_decode(preg_replace("/(&#(?:x0*[0-9a-f]{2,5}(?![0-9a-f;])|(?:0*\\d{2,4}(?![0-9;]))))/iS", "\$1;", $str), ENT_COMPAT | ENT_HTML5, $charset);
        if ($str_compare === $str) {
            return $str;
        }
    }
    /**
     * Sanitize Filename
     *
     * @param	string	$str		Input file name
     * @param 	bool	$relative_path	Whether to preserve paths
     * @return	string
     */
    public function sanitize_filename($str, $relative_path = false)
    {
        $bad = $this->filename_bad_chars;
        if (!$relative_path) {
            $bad[] = "./";
            $bad[] = "/";
        }
        $str = remove_invisible_characters($str, false);
        do {
            $old = $str;
            $str = str_replace($bad, "", $str);
        } while ($old !== $str);
        return stripslashes($str);
    }
    /**
     * Strip Image Tags
     *
     * @param	string	$str
     * @return	string
     */
    public function strip_image_tags($str)
    {
        return preg_replace(array("#<img[\\s/]+.*?src\\s*=\\s*([\"'])([^\\1]+?)\\1.*?\\>#i", "#<img[\\s/]+.*?src\\s*=\\s*?(([^\\s\"'=<>`]+)).*?\\>#i"), "\\2", $str);
    }
    /**
     * URL-decode taking spaces into account
     *
     * @see		https://github.com/bcit-ci/CodeIgniter/issues/4877
     * @param	array	$matches
     * @return	string
     */
    protected function _urldecodespaces($matches)
    {
        $input = $matches[0];
        $nospaces = preg_replace("#\\s+#", "", $input);
        return $nospaces === $input ? $input : rawurldecode($nospaces);
    }
    /**
     * Compact Exploded Words
     *
     * Callback method for xss_clean() to remove whitespace from
     * things like 'j a v a s c r i p t'.
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$matches
     * @return	string
     */
    protected function _compact_exploded_words($matches)
    {
        return preg_replace("/\\s+/s", "", $matches[1]) . $matches[2];
    }
    /**
     * Sanitize Naughty HTML
     *
     * Callback method for xss_clean() to remove naughty HTML elements.
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$matches
     * @return	string
     */
    protected function _sanitize_naughty_html($matches)
    {
        static $naughty_tags = array("alert", "area", "prompt", "confirm", "applet", "audio", "basefont", "base", "behavior", "bgsound", "blink", "body", "embed", "expression", "form", "frameset", "frame", "head", "html", "ilayer", "iframe", "input", "button", "select", "isindex", "layer", "link", "meta", "keygen", "object", "plaintext", "style", "script", "textarea", "title", "math", "video", "svg", "xml", "xss");
        static $evil_attributes = array("on\\w+", "style", "xmlns", "formaction", "form", "xlink:href", "FSCommand", "seekSegmentTime");
        if (empty($matches["closeTag"])) {
            return "&lt;" . $matches[1];
        }
        if (in_array(strtolower($matches["tagName"]), $naughty_tags, true)) {
            return "&lt;" . $matches[1] . "&gt;";
        }
        if (isset($matches["attributes"])) {
            $attributes = array();
            $attributes_pattern = "#" . "(?<name>[^\\s\\042\\047>/=]+)" . "(?:\\s*=(?<value>[^\\s\\042\\047=><`]+|\\s*\\042[^\\042]*\\042|\\s*\\047[^\\047]*\\047|\\s*(?U:[^\\s\\042\\047=><`]*)))" . "#i";
            $is_evil_pattern = "#^(" . implode("|", $evil_attributes) . ")\$#i";
            $matches["attributes"] = preg_replace("#^[^a-z]+#i", "", $matches["attributes"]);
            if (!preg_match($attributes_pattern, $matches["attributes"], $attribute, PREG_OFFSET_CAPTURE)) {
                break;
            }
            if (preg_match($is_evil_pattern, $attribute["name"][0]) || trim($attribute["value"][0]) === "") {
                $attributes[] = "xss=removed";
            } else {
                $attributes[] = $attribute[0][0];
            }
            $matches["attributes"] = substr($matches["attributes"], $attribute[0][1] + strlen($attribute[0][0]));
            if ($matches["attributes"] === "") {
                $attributes = empty($attributes) ? "" : " " . implode(" ", $attributes);
                return "<" . $matches["slash"] . $matches["tagName"] . $attributes . ">";
            }
        } else {
            return $matches[0];
        }
    }
    /**
     * JS Link Removal
     *
     * Callback method for xss_clean() to sanitize links.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on link-heavy strings.
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$match
     * @return	string
     */
    protected function _js_link_removal($match)
    {
        return str_replace($match[1], preg_replace("#href=.*?(?:(?:alert|prompt|confirm)(?:\\(|&\\#40;)|javascript:|livescript:|mocha:|charset=|window\\.|document\\.|\\.cookie|<script|<xss|d\\s*a\\s*t\\s*a\\s*:)#si", "", $this->_filter_attributes($match[1])), $match[0]);
    }
    /**
     * JS Image Removal
     *
     * Callback method for xss_clean() to sanitize image tags.
     *
     * This limits the PCRE backtracks, making it more performance friendly
     * and prevents PREG_BACKTRACK_LIMIT_ERROR from being triggered in
     * PHP 5.2+ on image tag heavy strings.
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$match
     * @return	string
     */
    protected function _js_img_removal($match)
    {
        return str_replace($match[1], preg_replace("#src=.*?(?:(?:alert|prompt|confirm|eval)(?:\\(|&\\#40;)|javascript:|livescript:|mocha:|charset=|window\\.|document\\.|\\.cookie|<script|<xss|base64\\s*,)#si", "", $this->_filter_attributes($match[1])), $match[0]);
    }
    /**
     * Attribute Conversion
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$match
     * @return	string
     */
    protected function _convert_attribute($match)
    {
        return str_replace(array(">", "<", "\\"), array("&gt;", "&lt;", "\\\\"), $match[0]);
    }
    /**
     * Filter Attributes
     *
     * Filters tag attributes for consistency and safety.
     *
     * @used-by	CI_Security::_js_img_removal()
     * @used-by	CI_Security::_js_link_removal()
     * @param	string	$str
     * @return	string
     */
    protected function _filter_attributes($str)
    {
        $out = "";
        if (preg_match_all("#\\s*[a-z\\-]+\\s*=\\s*(\\042|\\047)([^\\1]*?)\\1#is", $str, $matches)) {
            foreach ($matches[0] as $match) {
                $out .= preg_replace("#/\\*.*?\\*/#s", "", $match);
            }
        }
        return $out;
    }
    /**
     * HTML Entity Decode Callback
     *
     * @used-by	CI_Security::xss_clean()
     * @param	array	$match
     * @return	string
     */
    protected function _decode_entity($match)
    {
        $match = preg_replace("|\\&([a-z\\_0-9\\-]+)\\=([a-z\\_0-9\\-/]+)|i", $this->xss_hash() . "\\1=\\2", $match[0]);
        return str_replace($this->xss_hash(), "&", $this->entity_decode($match, $this->charset));
    }
    /**
     * Do Never Allowed
     *
     * @used-by	CI_Security::xss_clean()
     * @param 	string
     * @return 	string
     */
    protected function _do_never_allowed($str)
    {
        $str = str_replace(array_keys($this->_never_allowed_str), $this->_never_allowed_str, $str);
        foreach ($this->_never_allowed_regex as $regex) {
            $str = preg_replace("#" . $regex . "#is", "[removed]", $str);
        }
        return $str;
    }
    /**
     * Set CSRF Hash and Cookie
     *
     * @return	string
     */
    protected function _csrf_set_hash()
    {
        if ($this->_csrf_hash === NULL) {
            if (isset($_COOKIE[$this->_csrf_cookie_name]) && is_string($_COOKIE[$this->_csrf_cookie_name]) && preg_match("#^[0-9a-f]{32}\$#iS", $_COOKIE[$this->_csrf_cookie_name]) === 1) {
                return $this->_csrf_hash = $_COOKIE[$this->_csrf_cookie_name];
            }
            $rand = $this->get_random_bytes(16);
            $this->_csrf_hash = $rand === false ? md5(uniqid(mt_rand(), true)) : bin2hex($rand);
        }
        return $this->_csrf_hash;
    }
}

?>