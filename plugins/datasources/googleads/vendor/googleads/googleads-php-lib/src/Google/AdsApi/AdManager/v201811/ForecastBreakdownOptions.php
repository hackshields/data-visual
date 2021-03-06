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
class ForecastBreakdownOptions
{
    /**
     * @var \Google\AdsApi\AdManager\v201811\DateTime[] $timeWindows
     */
    protected $timeWindows = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\ForecastBreakdownTarget[] $targets
     */
    protected $targets = null;
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime[] $timeWindows
     * @param \Google\AdsApi\AdManager\v201811\ForecastBreakdownTarget[] $targets
     */
    public function __construct(array $timeWindows = null, array $targets = null)
    {
        $this->timeWindows = $timeWindows;
        $this->targets = $targets;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\DateTime[]
     */
    public function getTimeWindows()
    {
        return $this->timeWindows;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\DateTime[] $timeWindows
     * @return \Google\AdsApi\AdManager\v201811\ForecastBreakdownOptions
     */
    public function setTimeWindows(array $timeWindows)
    {
        $this->timeWindows = $timeWindows;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\ForecastBreakdownTarget[]
     */
    public function getTargets()
    {
        return $this->targets;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\ForecastBreakdownTarget[] $targets
     * @return \Google\AdsApi\AdManager\v201811\ForecastBreakdownOptions
     */
    public function setTargets(array $targets)
    {
        $this->targets = $targets;
        return $this;
    }
}

?>