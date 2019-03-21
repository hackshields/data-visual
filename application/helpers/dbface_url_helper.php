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
 * Class to retrieve absolute URL or URI components of the current URL,
 * and handle URL redirection.
 *
 * @package Piwik
 */
class DbFace_Url
{
    /**
     * List of hosts that are never checked for validity.
     */
    private static $alwaysTrustedHosts = array("localhost", "127.0.0.1", "::1", "[::1]");
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     *
     * @return string
     */
    public static function getCurrentUrl()
    {
        return self::getCurrentScheme() . "://" . self::getCurrentHost() . self::getCurrentScriptName() . self::getCurrentQueryString();
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "http://example.org/dir1/dir2/index.php"
     *
     * @param bool $checkTrustedHost Whether to do trusted host check. Should ALWAYS be true,
     *                               except in Piwik_Controller.
     * @return string
     */
    public static function getCurrentUrlWithoutQueryString($checkTrustedHost = true)
    {
        return self::getCurrentScheme() . "://" . self::getCurrentHost($default = "unknown", $checkTrustedHost) . self::getCurrentScriptName();
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "http://example.org/dir1/dir2/"
     *
     * @return string with trailing slash
     */
    public static function getCurrentUrlWithoutFileName()
    {
        return self::getCurrentScheme() . "://" . self::getCurrentHost() . self::getCurrentScriptPath();
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "/dir1/dir2/"
     *
     * @return string with trailing slash
     */
    public static function getCurrentScriptPath()
    {
        $queryString = self::getCurrentScriptName();
        $urlDir = dirname($queryString . "x");
        $urlDir = str_replace("\\", "/", $urlDir);
        if (1 < strlen($urlDir)) {
            $urlDir .= "/";
        }
        return $urlDir;
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "/dir1/dir2/index.php"
     *
     * @return string
     */
    public static function getCurrentScriptName()
    {
        $url = "";
        if (!empty($_SERVER["REQUEST_URI"])) {
            $url = $_SERVER["REQUEST_URI"];
            if (preg_match("~^https?://[^/]+(\$|/.*)~D", $url, $matches)) {
                $url = $matches[1];
            }
            if (($pos = strpos($url, "?")) !== false) {
                $url = substr($url, 0, $pos);
            }
            if (isset($_SERVER["PATH_INFO"])) {
                $url = substr($url, 0, 0 - strlen($_SERVER["PATH_INFO"]));
            }
        }
        if (empty($url)) {
            if (isset($_SERVER["SCRIPT_NAME"])) {
                $url = $_SERVER["SCRIPT_NAME"];
            } else {
                if (isset($_SERVER["SCRIPT_FILENAME"])) {
                    $url = $_SERVER["SCRIPT_FILENAME"];
                } else {
                    if (isset($_SERVER["argv"])) {
                        $url = $_SERVER["argv"][0];
                    }
                }
            }
        }
        if (!isset($url[0]) || $url[0] !== "/") {
            $url = "/" . $url;
        }
        return $url;
    }
    /**
     * If the current URL is 'http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return 'http'
     *
     * @return string 'https' or 'http'
     */
    public static function getCurrentScheme()
    {
        try {
            $assume_secure_protocol = @Piwik_Config::getInstance()->General["assume_secure_protocol"];
        } catch (Exception $e) {
            $assume_secure_protocol = false;
        }
        if ($assume_secure_protocol || isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] === true) || isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER["HTTP_X_FORWARDED_PROTO"] == "https") {
            return "https";
        }
        return "http";
    }
    /**
     * Validate "Host" (untrusted user input)
     *
     * @param string|bool $host Contents of Host: header from Request. If false, gets the
     *                          value from the request.
     *
     * @return bool True if valid; false otherwise
     */
    public static function isValidHost($host = false)
    {
        if (isset(Piwik_Config::getInstance()->General["enable_trusted_host_check"]) && Piwik_Config::getInstance()->General["enable_trusted_host_check"] == 0) {
            return true;
        }
        if ($host === false) {
            $host = $_SERVER["HTTP_HOST"];
            if (empty($host)) {
                return true;
            }
        }
        if (in_array($host, self::$alwaysTrustedHosts)) {
            return true;
        }
        $trustedHosts = @Piwik_Config::getInstance()->General["trusted_hosts"];
        if (empty($trustedHosts)) {
            self::saveTrustedHostnameInConfig($host);
            return true;
        }
        $hostLength = Piwik_Common::strlen($host);
        if ($hostLength !== strcspn($host, "`~!@#\$%^&*()_+={}\\|;\"'<>,?/ ")) {
            return false;
        }
        foreach ($trustedHosts as &$trustedHost) {
            $trustedHost = preg_quote($trustedHost);
        }
        $untrustedHost = Piwik_Common::mb_strtolower($host);
        $untrustedHost = rtrim($untrustedHost, ".");
        $hostRegex = Piwik_Common::mb_strtolower("/(^|.)" . implode("|", $trustedHosts) . "\$/");
        $result = preg_match($hostRegex, $untrustedHost);
        return 0 !== $result;
    }
    /**
     * Records one host, or an array of hosts in the config file,
     * if user is super user
     *
     * @static
     * @param $host string|array
     * @return bool
     */
    public static function saveTrustedHostnameInConfig($host)
    {
        if (Piwik::isUserIsSuperUser() && file_exists(Piwik_Config::getLocalConfigPath())) {
            $general = Piwik_Config::getInstance()->General;
            if (!is_array($host)) {
                $host = array($host);
            }
            $host = array_filter($host);
            if (empty($host)) {
                return false;
            }
            $general["trusted_hosts"] = $host;
            Piwik_Config::getInstance()->General = $general;
            Piwik_Config::getInstance()->forceSave();
            return true;
        }
        return false;
    }
    /**
     * Get host
     *
     * @param bool $checkIfTrusted Whether to do trusted host check. Should ALWAYS be true,
     *                             except in Piwik_Controller.
     * @return string|false
     */
    public static function getHost($checkIfTrusted = true)
    {
        if (isset($_SERVER["HTTP_HOST"]) && strlen($host = $_SERVER["HTTP_HOST"]) && (!$checkIfTrusted || self::isValidHost($host))) {
            return $host;
        }
        if (isset($_SERVER["SERVER_ADDR"])) {
            return $_SERVER["SERVER_ADDR"];
        }
        return false;
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "example.org"
     *
     * @param string $default Default value to return if host unknown
     * @param bool $checkTrustedHost Whether to do trusted host check. Should ALWAYS be true,
     *                               except in Piwik_Controller.
     * @return string
     */
    public static function getCurrentHost($default = "unknown", $checkTrustedHost = true)
    {
        $hostHeaders = @Piwik_Config::getInstance()->General["proxy_host_headers"];
        if (!is_array($hostHeaders)) {
            $hostHeaders = array();
        }
        $host = self::getHost($checkTrustedHost);
        $default = Piwik_Common::sanitizeInputValue($host ? $host : $default);
        return Piwik_IP::getNonProxyIpFromHeader($default, $hostHeaders);
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return "?param1=value1&param2=value2"
     *
     * @return string
     */
    public static function getCurrentQueryString()
    {
        $url = "";
        if (isset($_SERVER["QUERY_STRING"]) && !empty($_SERVER["QUERY_STRING"])) {
            $url .= "?" . $_SERVER["QUERY_STRING"];
        }
        return $url;
    }
    /**
     * If current URL is "http://example.org/dir1/dir2/index.php?param1=value1&param2=value2"
     * will return
     *  array
     *    'param1' => string 'value1'
     *    'param2' => string 'value2'
     *
     * @return array
     */
    public static function getArrayFromCurrentQueryString()
    {
        $queryString = self::getCurrentQueryString();
        $urlValues = DbFace_Common::getArrayFromQueryString($queryString);
        return $urlValues;
    }
    /**
     * Given an array of name-values, it will return the current query string
     * with the new requested parameter key-values;
     * If a parameter wasn't found in the current query string, the new key-value will be added to the returned query string.
     *
     * @param array $params array ( 'param3' => 'value3' )
     * @return string ?param2=value2&param3=value3
     */
    public static function getCurrentQueryStringWithParametersModified($params)
    {
        $urlValues = self::getArrayFromCurrentQueryString();
        foreach ($params as $key => $value) {
            $urlValues[$key] = $value;
        }
        $query = self::getQueryStringFromParameters($urlValues);
        if (0 < strlen($query)) {
            return "?" . $query;
        }
        return "";
    }
    /**
     * Given an array of parameters name->value, returns the query string.
     * Also works with array values using the php array syntax for GET parameters.
     *
     * @param array $parameters eg. array( 'param1' => 10, 'param2' => array(1,2))
     * @return string eg. "param1=10&param2[]=1&param2[]=2"
     */
    public static function getQueryStringFromParameters($parameters)
    {
        $query = "";
        foreach ($parameters as $name => $value) {
            if (is_null($value) || $value === false) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $theValue) {
                    $query .= $name . "[]=" . $theValue . "&";
                }
            } else {
                $query .= $name . "=" . $value . "&";
            }
        }
        $query = substr($query, 0, -1);
        return $query;
    }
    /**
     * Redirects the user to the referrer if found.
     * If the user doesn't have a referrer set, it redirects to the current URL without query string.
     */
    public static function redirectToReferer()
    {
        $referrer = self::getReferer();
        if ($referrer !== false) {
            self::redirectToUrl($referrer);
        }
        self::redirectToUrl(self::getCurrentUrlWithoutQueryString());
    }
    /**
     * Redirects the user to the specified URL
     *
     * @param string $url
     */
    public static function redirectToUrl($url)
    {
        if (DbFace_Common::isLookLikeUrl($url) || strpos($url, "index.php") === 0) {
            @header("Location: " . $url);
        } else {
            echo "Invalid URL to redirect to.";
        }
        exit;
    }
    /**
     * Returns the HTTP_REFERER header, false if not found.
     *
     * @return string|false
     */
    public static function getReferer()
    {
        if (!empty($_SERVER["HTTP_REFERER"])) {
            return $_SERVER["HTTP_REFERER"];
        }
        return false;
    }
    /**
     * Is the URL on the same host?
     *
     * @param string $url
     * @return bool True if local; false otherwise.
     */
    public static function isLocalUrl($url)
    {
        if (empty($url)) {
            return true;
        }
        $requestUri = isset($_SERVER["SCRIPT_URI"]) ? $_SERVER["SCRIPT_URI"] : "";
        $parseRequest = @parse_url($requestUri);
        $hosts = array(self::getHost(), self::getCurrentHost());
        if (!empty($parseRequest["host"])) {
            $hosts[] = $parseRequest["host"];
        }
        $hosts = array_map(array("Piwik_IP", "sanitizeIp"), $hosts);
        $disableHostCheck = Piwik_Config::getInstance()->General["enable_trusted_host_check"] == 0;
        $parsedUrl = @parse_url($url);
        $host = Piwik_IP::sanitizeIp($parsedUrl["host"]);
        return !empty($host) && ($disableHostCheck || in_array($host, $hosts)) && !empty($parsedUrl["scheme"]) && in_array($parsedUrl["scheme"], array("http", "https"));
    }
}

?>