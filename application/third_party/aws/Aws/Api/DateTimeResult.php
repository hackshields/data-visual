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
namespace Aws\Api;

/**
 * DateTime overrides that make DateTime work more seamlessly as a string,
 * with JSON documents, and with JMESPath.
 */
class DateTimeResult extends \DateTime implements \JsonSerializable
{
    /**
     * Create a new DateTimeResult from a unix timestamp.
     *
     * @param $unixTimestamp
     *
     * @return DateTimeResult
     */
    public static function fromEpoch($unixTimestamp)
    {
        return new self(gmdate('c', $unixTimestamp));
    }
    /**
     * Serialize the DateTimeResult as an ISO 8601 date string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format('c');
    }
    /**
     * Serialize the date as an ISO 8601 date when serializing as JSON.
     *
     * @return mixed|string
     */
    public function jsonSerialize()
    {
        return (string) $this;
    }
}

?>