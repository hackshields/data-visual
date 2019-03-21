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
namespace Google\AdsApi\AdManager\v201808;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class BillableRevenueOverrides
{
    /**
     * @var \Google\AdsApi\AdManager\v201808\Money $netBillableRevenueOverride
     */
    protected $netBillableRevenueOverride = null;
    /**
     * @var \Google\AdsApi\AdManager\v201808\Money $grossBillableRevenueOverride
     */
    protected $grossBillableRevenueOverride = null;
    /**
     * @var \Google\AdsApi\AdManager\v201808\Money $billableRevenueOverride
     */
    protected $billableRevenueOverride = null;
    /**
     * @param \Google\AdsApi\AdManager\v201808\Money $netBillableRevenueOverride
     * @param \Google\AdsApi\AdManager\v201808\Money $grossBillableRevenueOverride
     * @param \Google\AdsApi\AdManager\v201808\Money $billableRevenueOverride
     */
    public function __construct($netBillableRevenueOverride = null, $grossBillableRevenueOverride = null, $billableRevenueOverride = null)
    {
        $this->netBillableRevenueOverride = $netBillableRevenueOverride;
        $this->grossBillableRevenueOverride = $grossBillableRevenueOverride;
        $this->billableRevenueOverride = $billableRevenueOverride;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201808\Money
     */
    public function getNetBillableRevenueOverride()
    {
        return $this->netBillableRevenueOverride;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201808\Money $netBillableRevenueOverride
     * @return \Google\AdsApi\AdManager\v201808\BillableRevenueOverrides
     */
    public function setNetBillableRevenueOverride($netBillableRevenueOverride)
    {
        $this->netBillableRevenueOverride = $netBillableRevenueOverride;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201808\Money
     */
    public function getGrossBillableRevenueOverride()
    {
        return $this->grossBillableRevenueOverride;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201808\Money $grossBillableRevenueOverride
     * @return \Google\AdsApi\AdManager\v201808\BillableRevenueOverrides
     */
    public function setGrossBillableRevenueOverride($grossBillableRevenueOverride)
    {
        $this->grossBillableRevenueOverride = $grossBillableRevenueOverride;
        return $this;
    }
    /**
     * @return \Google\AdsApi\AdManager\v201808\Money
     */
    public function getBillableRevenueOverride()
    {
        return $this->billableRevenueOverride;
    }
    /**
     * @param \Google\AdsApi\AdManager\v201808\Money $billableRevenueOverride
     * @return \Google\AdsApi\AdManager\v201808\BillableRevenueOverrides
     */
    public function setBillableRevenueOverride($billableRevenueOverride)
    {
        $this->billableRevenueOverride = $billableRevenueOverride;
        return $this;
    }
}

?>