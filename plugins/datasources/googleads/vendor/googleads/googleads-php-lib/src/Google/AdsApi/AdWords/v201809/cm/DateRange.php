<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\cm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class DateRange
{
    /**
     * @var string $min
     */
    protected $min = null;
    /**
     * @var string $max
     */
    protected $max = null;
    /**
     * @param string $min
     * @param string $max
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
    }
    /**
     * @return string
     */
    public function getMin()
    {
        return $this->min;
    }
    /**
     * @param string $min
     * @return \Google\AdsApi\AdWords\v201809\cm\DateRange
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }
    /**
     * @return string
     */
    public function getMax()
    {
        return $this->max;
    }
    /**
     * @param string $max
     * @return \Google\AdsApi\AdWords\v201809\cm\DateRange
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }
}

?>