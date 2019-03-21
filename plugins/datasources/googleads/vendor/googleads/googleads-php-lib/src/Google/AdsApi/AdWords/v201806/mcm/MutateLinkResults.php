<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

namespace Google\AdsApi\AdWords\v201806\mcm;

/**
 * This file was generated from WSDL. DO NOT EDIT.
 */
class MutateLinkResults
{
    /**
     * @var \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerLink[] $links
     */
    protected $links = null;
    /**
     * @param \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerLink[] $links
     */
    public function __construct(array $links = null)
    {
        $this->links = $links;
    }
    /**
     * @return \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerLink[]
     */
    public function getLinks()
    {
        return $this->links;
    }
    /**
     * @param \Google\AdsApi\AdWords\v201806\mcm\ManagedCustomerLink[] $links
     * @return \Google\AdsApi\AdWords\v201806\mcm\MutateLinkResults
     */
    public function setLinks(array $links)
    {
        $this->links = $links;
        return $this;
    }
}

?>