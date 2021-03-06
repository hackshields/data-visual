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
class CustomerNegativeCriterion
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\cm\Criterion $criterion
     */
    protected $criterion = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\Criterion $criterion
     */
    public function __construct($criterion = null)
    {
        $this->criterion = $criterion;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\cm\Criterion
     */
    public function getCriterion()
    {
        return $this->criterion;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\cm\Criterion $criterion
     * @return \Google\AdsApi\AdWords\v201809\cm\CustomerNegativeCriterion
     */
    public function setCriterion($criterion)
    {
        $this->criterion = $criterion;
        return $this;
    }
}

?>