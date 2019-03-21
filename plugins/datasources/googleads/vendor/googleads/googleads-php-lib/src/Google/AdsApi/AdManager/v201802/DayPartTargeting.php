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
namespace Google\AdsApi\AdManager\v201802;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class DayPartTargeting
{
    /**
     * @var \Google\AdsApi\AdManager\v201802\DayPart[] $dayParts
     */
    protected $dayParts = null;
    /**
     * @var string $timeZone
     */
    protected $timeZone = null;
    /**
     * @param \Google\AdsApi\AdManager\v201802\DayPart[] $dayParts
     * @param string $timeZone
     */
    public function __construct(array $dayParts = null, $timeZone = null)
    {
        $this->dayParts = $dayParts;
        $this->timeZone = $timeZone;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201802\DayPart[]
     */
    public function getDayParts()
    {
        return $this->dayParts;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201802\DayPart[] $dayParts
     * @return \Google\AdsApi\AdManager\v201802\DayPartTargeting
     */
    public function setDayParts(array $dayParts)
    {
        $this->dayParts = $dayParts;
        return $this;
    }
    /**
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }
    /**
     * @param string $timeZone
     * @return \Google\AdsApi\AdManager\v201802\DayPartTargeting
     */
    public function setTimeZone($timeZone)
    {
        $this->timeZone = $timeZone;
        return $this;
    }
}

?>