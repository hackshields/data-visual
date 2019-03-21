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
class DateTimeRangeTargeting
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTimeRange[] $targetedDateTimeRanges
     */
    protected $targetedDateTimeRanges = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTimeRange[] $targetedDateTimeRanges
     */
    public function __construct(array $targetedDateTimeRanges = null)
    {
        $this->targetedDateTimeRanges = $targetedDateTimeRanges;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTimeRange[]
     */
    public function getTargetedDateTimeRanges()
    {
        return $this->targetedDateTimeRanges;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTimeRange[] $targetedDateTimeRanges
     * @return \Google\AdsApi\AdManager\v201811\DateTimeRangeTargeting
     */
    public function setTargetedDateTimeRanges(array $targetedDateTimeRanges)
    {
        $this->targetedDateTimeRanges = $targetedDateTimeRanges;
        return $this;
    }
}

?>