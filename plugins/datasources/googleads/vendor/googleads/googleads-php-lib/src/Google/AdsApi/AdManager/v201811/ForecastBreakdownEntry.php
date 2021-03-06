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
class ForecastBreakdownEntry
{
    /**
     * @var string $name
     */
    protected $name = null;
    /**
     * @var \Google\AdsApi\AdManager\v201811\BreakdownForecast $forecast
     */
    protected $forecast = null;
    /**
     * @param string $name
     * @param \Google\AdsApi\AdManager\v201811\BreakdownForecast $forecast
     */
    public function __construct($name = null, $forecast = null)
    {
        $this->name = $name;
        $this->forecast = $forecast;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @param string $name
     * @return \Google\AdsApi\AdManager\v201811\ForecastBreakdownEntry
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201811\BreakdownForecast
     */
    public function getForecast()
    {
        return $this->forecast;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201811\BreakdownForecast $forecast
     * @return \Google\AdsApi\AdManager\v201811\ForecastBreakdownEntry
     */
    public function setForecast($forecast)
    {
        $this->forecast = $forecast;
        return $this;
    }
}

?>