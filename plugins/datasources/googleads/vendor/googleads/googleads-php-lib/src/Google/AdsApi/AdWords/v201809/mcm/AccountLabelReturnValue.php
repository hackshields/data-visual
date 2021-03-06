<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201809\mcm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class AccountLabelReturnValue
{
    /**
     * @var \Google\AdsApi\AdWords\v201809\mcm\AccountLabel[] $labels
     */
    protected $labels = null;
    /**
     * @param \Google\AdsApi\AdWords\v201809\mcm\AccountLabel[] $labels
     */
    public function __construct(array $labels = null)
    {
        $this->labels = $labels;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201809\mcm\AccountLabel[]
     */
    public function getLabels()
    {
        return $this->labels;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201809\mcm\AccountLabel[] $labels
     * @return \Google\AdsApi\AdWords\v201809\mcm\AccountLabelReturnValue
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;
        return $this;
    }
}

?>