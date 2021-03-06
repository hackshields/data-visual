<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\rm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class RelativeDate
{
    /**
     * @var int $offsetInDays
     */
    protected $offsetInDays = null;
    /**
     * @param int $offsetInDays
     */
    public function __construct($offsetInDays = null)
    {
        $this->offsetInDays = $offsetInDays;
    }
    /**
     * @return int
     */
    public function getOffsetInDays()
    {
        return $this->offsetInDays;
    }
    /**
     * @param int $offsetInDays
     * @return \Google\AdsApi\AdWords\v201806\rm\RelativeDate
     */
    public function setOffsetInDays($offsetInDays)
    {
        $this->offsetInDays = $offsetInDays;
        return $this;
    }
}

?>