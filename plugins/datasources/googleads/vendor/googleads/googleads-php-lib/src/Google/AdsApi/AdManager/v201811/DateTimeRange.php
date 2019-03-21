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
class DateTimeRange
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $startDateTime
     */
    protected $startDateTime = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime $endDateTime
     */
    protected $endDateTime = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $startDateTime
     * @param \Google\AdsApi\AdManager\v201811\DateTime $endDateTime
     */
    public function __construct($startDateTime = null, $endDateTime = null)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $startDateTime
     * @return \Google\AdsApi\AdManager\v201811\DateTimeRange
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime $endDateTime
     * @return \Google\AdsApi\AdManager\v201811\DateTimeRange
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
        return $this;
    }
}

?>