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
namespace Parse;

/**
 * Class ParseFeatures - Representation of server-side features
 *
 * @author Ben Friedman <friedman.benjamin@gmail.com>
 * @package Parse
 */
class ParseServerInfo
{
    /**
     * Reported server features and configs
     *
     * @var array
     */
    private static $serverFeatures = NULL;
    /**
     * Reported server version
     *
     * @var string
     */
    private static $serverVersion = NULL;
    /**
     * Requests, sets and returns server features and version
     *
     * @return array
     * @throws ParseException
     */
    private static function getServerInfo()
    {
        if (!isset($serverFeatures) || !isset($serverVersion)) {
            $info = ParseClient::_request("GET", "serverInfo/", null, null, true);
            if (!isset($info["features"])) {
                throw new ParseException("Missing features in server info.");
            }
            if (!isset($info["parseServerVersion"])) {
                throw new ParseException("Missing version in server info.");
            }
            self::$serverFeatures = $info["features"];
            self::_setServerVersion($info["parseServerVersion"]);
        }
        return array("features" => self::$serverFeatures, "version" => self::$serverVersion);
    }
    /**
     * Sets the current server version.
     * Allows setting the server version to avoid making an additional request
     * if the version is obtained elsewhere.
     *
     * @param string $version   Version to set
     */
    public static function _setServerVersion($version)
    {
        self::$serverVersion = $version;
    }
    /**
     * Get a specific feature set from the server
     *
     * @param string $key   Feature set to get
     * @return mixed
     */
    public static function get($key)
    {
        return self::getServerInfo()["features"][$key];
    }
    /**
     * Gets features for the current server
     *
     * @return array
     */
    public static function getFeatures()
    {
        return self::getServerInfo()["features"];
    }
    /**
     * Gets the reported version of the current server
     *
     * @return string
     */
    public static function getVersion()
    {
        if (!isset($serverVersion)) {
            return self::getServerInfo()["version"];
        }
        return self::$serverVersion;
    }
    /**
     * Gets features available for globalConfig
     *
     * @return array
     */
    public static function getGlobalConfigFeatures()
    {
        return self::get("globalConfig");
    }
    /**
     * Gets features available for hooks
     *
     * @return array
     */
    public static function getHooksFeatures()
    {
        return self::get("hooks");
    }
    /**
     * Gets features available for cloudCode
     *
     * @return array
     */
    public static function getCloudCodeFeatures()
    {
        return self::get("cloudCode");
    }
    /**
     * Gets features available for logs
     *
     * @return array
     */
    public static function getLogsFeatures()
    {
        return self::get("logs");
    }
    /**
     * Gets features available for push
     *
     * @return array
     */
    public static function getPushFeatures()
    {
        return self::get("push");
    }
    /**
     * Gets features available for schemas
     *
     * @return array
     */
    public static function getSchemasFeatures()
    {
        return self::get("schemas");
    }
}

?>