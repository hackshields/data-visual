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
class OfflineData
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\rm\StoreSalesTransaction $StoreSalesTransaction
     */
    protected $StoreSalesTransaction = null;
    /**
     * @param \Google\AdsApi\AdWords\v201806\rm\StoreSalesTransaction $StoreSalesTransaction
     */
    public function __construct($StoreSalesTransaction = null)
    {
        $this->StoreSalesTransaction = $StoreSalesTransaction;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\rm\StoreSalesTransaction
     */
    public function getStoreSalesTransaction()
    {
        return $this->StoreSalesTransaction;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\rm\StoreSalesTransaction $StoreSalesTransaction
     * @return \Google\AdsApi\AdWords\v201806\rm\OfflineData
     */
    public function setStoreSalesTransaction($StoreSalesTransaction)
    {
        $this->StoreSalesTransaction = $StoreSalesTransaction;
        return $this;
    }
}

?>