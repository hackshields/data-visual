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
class PendingInvitationSelector
{
    /**
     * @var int[] $managerCustomerIds
     */
    protected $managerCustomerIds = null;
    /**
     * @var int[] $clientCustomerIds
     */
    protected $clientCustomerIds = null;
    /**
     * @param int[] $managerCustomerIds
     * @param int[] $clientCustomerIds
     */
    public function __construct(array $managerCustomerIds = null, array $clientCustomerIds = null)
    {
        $this->managerCustomerIds = $managerCustomerIds;
        $this->clientCustomerIds = $clientCustomerIds;
    }
    /**
     * @return int[]
     */
    public function getManagerCustomerIds()
    {
        return $this->managerCustomerIds;
    }
    /**
     * @param int[] $managerCustomerIds
     * @return \Google\AdsApi\AdWords\v201809\mcm\PendingInvitationSelector
     */
    public function setManagerCustomerIds(array $managerCustomerIds)
    {
        $this->managerCustomerIds = $managerCustomerIds;
        return $this;
    }
    /**
     * @return int[]
     */
    public function getClientCustomerIds()
    {
        return $this->clientCustomerIds;
    }
    /**
     * @param int[] $clientCustomerIds
     * @return \Google\AdsApi\AdWords\v201809\mcm\PendingInvitationSelector
     */
    public function setClientCustomerIds(array $clientCustomerIds)
    {
        $this->clientCustomerIds = $clientCustomerIds;
        return $this;
    }
}

?>