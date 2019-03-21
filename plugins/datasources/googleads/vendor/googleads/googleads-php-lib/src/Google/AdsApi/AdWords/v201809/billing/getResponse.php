<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\billing;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class getResponse
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\billing\BudgetOrderPage $rval
     */
    protected $rval = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\billing\BudgetOrderPage $rval
     */
    public function __construct($rval = null)
    {
        $this->rval = $rval;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\billing\BudgetOrderPage
     */
    public function getRval()
    {
        return $this->rval;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\billing\BudgetOrderPage $rval
     * @return \Google\AdsApi\AdWords\v201809\billing\getResponse
     */
    public function setRval($rval)
    {
        $this->rval = $rval;
        return $this;
    }
}

?>