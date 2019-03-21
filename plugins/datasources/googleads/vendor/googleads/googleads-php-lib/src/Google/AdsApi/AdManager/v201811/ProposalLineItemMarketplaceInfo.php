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
class ProposalLineItemMarketplaceInfo
{
    /**
     * @var string $adExchangeEnvironment
     */
    protected $adExchangeEnvironment = null;
    /**
     * @param string $adExchangeEnvironment
     */
    public function __construct($adExchangeEnvironment = null)
    {
        $this->adExchangeEnvironment = $adExchangeEnvironment;
    }
    /**
     * @return string
     */
    public function getAdExchangeEnvironment()
    {
        return $this->adExchangeEnvironment;
    }
    /**
     * @param string $adExchangeEnvironment
     * @return \Google\AdsApi\AdManager\v201811\ProposalLineItemMarketplaceInfo
     */
    public function setAdExchangeEnvironment($adExchangeEnvironment)
    {
        $this->adExchangeEnvironment = $adExchangeEnvironment;
        return $this;
    }
}

?>