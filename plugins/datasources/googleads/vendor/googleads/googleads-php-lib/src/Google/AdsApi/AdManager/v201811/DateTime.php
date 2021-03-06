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
namespace Google\AdsApi\AdManager\v201811;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class DateTime
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\Date $date
     */
    protected $date = null;
    /**
     * @var int $hour
     */
    protected $hour = null;
    /**
     * @var int $minute
     */
    protected $minute = null;
    /**
     * @var int $second
     */
    protected $second = null;
    /**
     * @var string $timeZoneId
     */
    protected $timeZoneId = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\Date $date
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param string $timeZoneId
     */
    public function __construct($date = null, $hour = null, $minute = null, $second = null, $timeZoneId = null)
    {
        $this->date = $date;
        $this->hour = $hour;
        $this->minute = $minute;
        $this->second = $second;
        $this->timeZoneId = $timeZoneId;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\Date
     */
    public function getDate()
    {
        return $this->date;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\Date $date
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }
    /**
     * @return int
     */
    public function getHour()
    {
        return $this->hour;
    }
    /**
     * @param int $hour
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function setHour($hour)
    {
        $this->hour = $hour;
        return $this;
    }
    /**
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }
    /**
     * @param int $minute
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
        return $this;
    }
    /**
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }
    /**
     * @param int $second
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function setSecond($second)
    {
        $this->second = $second;
        return $this;
    }
    /**
     * @return string
     */
    public function getTimeZoneId()
    {
        return $this->timeZoneId;
    }
    /**
     * @param string $timeZoneId
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function setTimeZoneId($timeZoneId)
    {
        $this->timeZoneId = $timeZoneId;
        return $this;
    }
}

?>